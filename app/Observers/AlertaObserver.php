<?php

namespace App\Observers;

use App\Models\Alerta;
use App\Services\NotificacionSistemaService;

class AlertaObserver
{
    public function __construct(
        private readonly NotificacionSistemaService $notificaciones
    ) {
        //
    }

    /**
     * Al crear una alerta operativa, generar la notificación FN.23.
     */
    public function created(Alerta $alerta): void
    {
        $vehiculo = $alerta->vehiculo()->first();

        if (!$vehiculo) {
            return;
        }

        $tipoOriginal = strtolower((string) $alerta->tipo);

        $tipoNotificacion = 'alerta_seguridad';
        $titulo = 'Alerta de seguridad';

        if (str_contains($tipoOriginal, 'velocidad')) {
            $tipoNotificacion = 'exceso_velocidad';
            $titulo = 'Exceso de velocidad';
        } elseif (
            str_contains($tipoOriginal, 'gps')
            || str_contains($tipoOriginal, 'conexion')
            || str_contains($tipoOriginal, 'conexión')
        ) {
            $tipoNotificacion = 'gps_desconectado';
            $titulo = 'GPS sin conexión';
        }

        $this->notificaciones->notificarVehiculo(
            $vehiculo,
            $tipoNotificacion,
            $titulo,
            (string) $alerta->mensaje,
            route('dashboard'),
            [
                'alerta_id' => $alerta->id,
                'tipo_alerta' => $alerta->tipo,
            ]
        );
    }
}
