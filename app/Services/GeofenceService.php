<?php
namespace App\Services;

use App\Models\Vehiculo;
use App\Models\Zona;
use App\Models\EventoGeocerca;
use App\Models\Alerta;
use App\Notifications\GeocercaAlertaNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class GeofenceService
{
    public function procesarUbicacion(Vehiculo $vehiculo, float $lat, float $lng)
    {
        // 1. Obtener todas las zonas asignadas a este vehículo
        $zonasAsignadas = $vehiculo->zonas;

        foreach ($zonasAsignadas as $zona) {
            // 2. Comprobar si las coordenadas actuales están DENTRO del polígono (MySQL Spatial Query)
            $estaDentro = $this->puntoEnPoligono($lat, $lng, $zona->id);

            // 3. Buscar si hay un evento de entrada activo (sin fecha de salida)
            $eventoActivo = EventoGeocerca::where('vehiculo_id', $vehiculo->id)
                ->where('zona_id', $zona->id)
                ->whereNull('fecha_salida')
                ->first();

            $configPivote = $zona->pivot;

            // CASO A: Entró a la zona y no estaba dentro previamente
            if ($estaDentro && !$eventoActivo) {
                $this->registrarEntrada($vehiculo, $zona, $configPivote, $lat, $lng);
            }
            
            // CASO B: Salió de la zona y estaba dentro previamente
            elseif (!$estaDentro && $eventoActivo) {
                $this->registrarSalida($vehiculo, $zona, $configPivote, $eventoActivo, $lat, $lng);
            }

            // CASO C: Sigue dentro -> Validar permanencia mínima/máxima
            elseif ($estaDentro && $eventoActivo) {
                $this->validarPermanencia($vehiculo, $zona, $configPivote, $eventoActivo);
            }
        }
    }

    private function puntoEnPoligono(float $lat, float $lng, int $zonaId): bool
    {
        // Usa funciones espaciales ST_Contains de MySQL
        return DB::table('zonas')
            ->where('id', $zonaId)
            ->whereRaw("ST_Contains(poligono, ST_GeomFromText(?))", ["POINT({$lng} {$lat})"])
            ->exists();
    }

    private function registrarEntrada(Vehiculo $vehiculo, Zona $zona, $configPivote, float $lat, float $lng)
    {
        $tipoEvento = $configPivote->tipo_regla === 'restringida' ? 'violacion_restringida' : 'normal';

        $evento = EventoGeocerca::create([
            'vehiculo_id' => $vehiculo->id,
            'zona_id' => $zona->id,
            'fecha_entrada' => now(),
            'latitud_entrada' => $lat,
            'longitud_entrada' => $lng,
            'tipo_evento' => $tipoEvento
        ]);

        // Crear registro en la tabla de Alertas
        $mensajeAlerta = "El vehículo {$vehiculo->nombre} ingresó a la zona '{$zona->nombre}'";
        if ($configPivote->tipo_regla === 'restringida') {
            $mensajeAlerta = "🚨 VIOLACIÓN: El vehículo ingresó a ZONA RESTRINGIDA '{$zona->nombre}'";
        }

        Alerta::create([
            'vehiculo_id' => $vehiculo->id,
            'dispositivo_id' => $vehiculo->dispositivo?->id,
            'tipo' => 'geocerca_entrada',
            'mensaje' => $mensajeAlerta,
            'leida' => false,
        ]);

        // Enviar notificación por Mail si está activada
        if ($configPivote->notificar_entrada) {
            $notificable = $vehiculo->user ?? $vehiculo->empresa?->user; // Propietario del vehículo
            if ($notificable) {
                $notificable->notify(new GeocercaAlertaNotification($vehiculo, $zona, 'entrada', $mensajeAlerta));
            }
        }
    }

    private function registrarSalida(Vehiculo $vehiculo, Zona $zona, $configPivote, EventoGeocerca $evento, float $lat, float $lng)
    {
        $fechaSalida = now();
        $duracionMinutos = $evento->fecha_entrada->diffInMinutes($fechaSalida);

        $evento->update([
            'fecha_salida' => $fechaSalida,
            'duracion_minutos' => $duracionMinutos,
            'latitud_salida' => $lat,
            'longitud_salida' => $lng,
        ]);

        $mensajeAlerta = "El vehículo {$vehiculo->nombre} salió de la zona '{$zona->nombre}'. Permanencia: {$duracionMinutos} min.";

        Alerta::create([
            'vehiculo_id' => $vehiculo->id,
            'dispositivo_id' => $vehiculo->dispositivo?->id,
            'tipo' => 'geocerca_salida',
            'mensaje' => $mensajeAlerta,
            'leida' => false,
        ]);

        // Enviar mail de salida
        if ($configPivote->notificar_salida) {
            $notificable = $vehiculo->user ?? $vehiculo->empresa?->user;
            if ($notificable) {
                $notificable->notify(new GeocercaAlertaNotification($vehiculo, $zona, 'salida', $mensajeAlerta));
            }
        }
    }

    private function validarPermanencia(Vehiculo $vehiculo, Zona $zona, $configPivote, EventoGeocerca $evento)
    {
        $minutosActuales = $evento->fecha_entrada->diffInMinutes(now());

        // Validar exceso de tiempo de permanencia si está configurado
        if ($configPivote->permanencia_maxima_minutos && 
            $minutosActuales > $configPivote->permanencia_maxima_minutos && 
            !$evento->alerta_generada) {

            $mensaje = "El vehículo ha superado el tiempo máximo permitido ({$configPivote->permanencia_maxima_minutos} min) en la zona {$zona->nombre}. Tiempo actual: {$minutosActuales} min.";

            Alerta::create([
                'vehiculo_id' => $vehiculo->id,
                'dispositivo_id' => $vehiculo->dispositivo?->id,
                'tipo' => 'exceso_permanencia',
                'mensaje' => $mensaje,
                'leida' => false,
            ]);

            $evento->update([
                'alerta_generada' => true,
                'tipo_evento' => 'exceso_permanencia'
            ]);

            $notificable = $vehiculo->user ?? $vehiculo->empresa?->user;
            if ($notificable) {
                $notificable->notify(new GeocercaAlertaNotification($vehiculo, $zona, 'entrada', $mensaje));
            }
        }
    }
}