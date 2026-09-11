<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Vehiculo;
use App\Models\Zona;

class GeocercaAlertaNotification extends Notification
{
    use Queueable;

    public $vehiculo;
    public $zona;
    public $tipoAccion; // 'entrada' o 'salida'
    public $detalles;

    public function __construct(Vehiculo $vehiculo, Zona $zona, string $tipoAccion, string $detalles = '')
    {
        $this->vehiculo = $vehiculo;
        $this->zona = $zona;
        $this->tipoAccion = $tipoAccion;
        $this->detalles = $detalles;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $asunto = $this->tipoAccion === 'entrada' 
            ? "🚨 Alerta: {$this->vehiculo->nombre} ingresó a {$this->zona->nombre}"
            : "ℹ️ Notificación: {$this->vehiculo->nombre} salió de {$this->zona->nombre}";

        return (new MailMessage)
                    ->subject($asunto)
                    ->greeting('Hola,')
                    ->line("Se ha detectado un evento de geocerca en el sistema.")
                    ->line("Vehículo: **{$this->vehiculo->nombre}** (Placas: {$this->vehiculo->placas})")
                    ->line("Zona: **{$this->zona->nombre}**")
                    ->line("Acción: **" . strtoupper($this->tipoAccion) . "**")
                    ->line("Fecha / Hora: " . now()->format('d/m/Y H:i:s'))
                    ->lineIf(!empty($this->detalles), "Detalles adicionales: {$this->detalles}")
                    ->action('Ver en el Mapa', url('/vehiculos/' . $this->vehiculo->id))
                    ->salutation('Atentamente, Sistema SOTyTECH GPS');
    }
}