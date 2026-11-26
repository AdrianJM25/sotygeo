<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventoSistemaNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $tipo,
        private readonly string $titulo,
        private readonly string $mensaje,
        private readonly ?string $url = null,
        private readonly array $datos = [],
        private readonly bool $enviarCorreo = false,
    ) {
        //
    }

    /**
     * Canales de envío.
     */
    public function via(object $notifiable): array
    {
        $canales = ['database'];

        if ($this->enviarCorreo) {
            $canales[] = 'mail';
        }

        return $canales;
    }

    /**
     * Convierte las URL internas en rutas relativas.
     *
     * Permite utilizar las notificaciones tanto en
     * localhost como en Laravel Cloud.
     *
     * Las URL externas se conservan.
     */
    private function urlRelativa(): ?string
    {
        if ($this->url === null || trim($this->url) === '') {
            return null;
        }

        $url = trim($this->url);

        $bases = array_unique([
            rtrim((string) config('app.url'), '/'),
            rtrim(url('/'), '/'),
        ]);

        foreach ($bases as $base) {

            if ($base === '') {
                continue;
            }

            if ($url === $base) {
                return '/';
            }

            if (str_starts_with($url, $base.'/')) {
                return substr($url, strlen($base));
            }
        }

        return $url;
    }

    /**
     * Genera una URL absoluta para los correos.
     *
     * Los enlaces enviados por correo necesitan
     * incluir el dominio de la aplicación.
     */
    private function urlParaCorreo(): ?string
    {
        $ruta = $this->urlRelativa();

        if ($ruta === null) {
            return null;
        }

        if (
            str_starts_with($ruta, '/')
            && !str_starts_with($ruta, '//')
        ) {
            return rtrim(
                (string) config('app.url'),
                '/'
            ).$ruta;
        }

        return $ruta;
    }

    /**
     * Información almacenada en PostgreSQL.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'tipo' => $this->tipo,
            'titulo' => $this->titulo,
            'mensaje' => $this->mensaje,
            'url' => $this->urlRelativa(),
            'datos' => $this->datos,
        ];
    }

    /**
     * Correo electrónico.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $correo = (new MailMessage)
            ->subject($this->titulo)
            ->greeting('Hola, '.$notifiable->name)
            ->line($this->mensaje);

        $urlCorreo = $this->urlParaCorreo();

        if ($urlCorreo) {
            $correo->action(
                'Ver en SotyGeo',
                $urlCorreo
            );
        }

        return $correo
            ->line('Este mensaje fue generado automáticamente por SotyGeo.');
    }

    /**
     * Representación general.
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
