<?php

namespace App\Observers;

use App\Models\Inspeccion;
use App\Services\NotificacionSistemaService;

class InspeccionObserver
{
    public function __construct(
        private readonly NotificacionSistemaService $notificaciones
    ) {
        //
    }

    /**
     * Notificar solamente inspecciones con incidencia.
     */
    public function created(Inspeccion $inspeccion): void
    {
        if ($inspeccion->resultado_general !== 'con_incidencias') {
            return;
        }

        $vehiculo = $inspeccion->vehiculo()->first();

        if (!$vehiculo) {
            return;
        }

        $mensaje =
            'La inspección del vehículo '
            .$vehiculo->nombre
            .' registró una o más incidencias.';

        if ($inspeccion->observaciones) {
            $mensaje .= ' Observaciones: '.$inspeccion->observaciones;
        }

        $this->notificaciones->notificarVehiculo(
            $vehiculo,
            'inspeccion_incidencia',
            'Inspección con incidencia',
            $mensaje,
            route('inspecciones.index'),
            [
                'inspeccion_id' => $inspeccion->id,
                'conductor_id' => $inspeccion->conductor_id,
            ]
        );
    }
}
