<?php

namespace App\Observers;

use App\Models\EventoGeocerca;
use App\Services\NotificacionSistemaService;

class EventoGeocercaObserver
{
    public function __construct(
        private readonly NotificacionSistemaService $notificaciones
    ) {
        //
    }

    /**
     * Entrada a una geocerca.
     */
    public function created(EventoGeocerca $evento): void
    {
        $vehiculo = $evento->vehiculo()->first();
        $zona = $evento->zona()->first();

        if (!$vehiculo || !$zona) {
            return;
        }

        $regla = $zona
            ->vehiculos()
            ->where('vehiculo_id', $vehiculo->id)
            ->first()
            ?->pivot;

        if ($regla && !$regla->notificar_entrada) {
            return;
        }

        $this->notificaciones->notificarVehiculo(
            $vehiculo,
            'zona_entrada',
            'Entrada a zona',
            'El vehículo '.$vehiculo->nombre.' ingresó a la zona '.$zona->nombre.'.',
            route('dashboard'),
            [
                'evento_geocerca_id' => $evento->id,
                'zona_id' => $zona->id,
                'accion' => 'entrada',
            ]
        );
    }

    /**
     * Salida de una geocerca.
     */
    public function updated(EventoGeocerca $evento): void
    {
        if (
            !$evento->wasChanged('fecha_salida')
            || !$evento->fecha_salida
        ) {
            return;
        }

        $vehiculo = $evento->vehiculo()->first();
        $zona = $evento->zona()->first();

        if (!$vehiculo || !$zona) {
            return;
        }

        $regla = $zona
            ->vehiculos()
            ->where('vehiculo_id', $vehiculo->id)
            ->first()
            ?->pivot;

        if ($regla && !$regla->notificar_salida) {
            return;
        }

        $mensaje =
            'El vehículo '.$vehiculo->nombre
            .' salió de la zona '.$zona->nombre.'.';

        if ($evento->duracion_minutos !== null) {
            $mensaje .=
                ' Permanencia: '.(int) $evento->duracion_minutos.' min.';
        }

        $this->notificaciones->notificarVehiculo(
            $vehiculo,
            'zona_salida',
            'Salida de zona',
            $mensaje,
            route('dashboard'),
            [
                'evento_geocerca_id' => $evento->id,
                'zona_id' => $zona->id,
                'accion' => 'salida',
            ]
        );
    }
}
