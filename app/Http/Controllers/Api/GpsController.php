<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dispositivo;
use App\Models\Ubicacion;
use App\Models\Vehiculo;
use App\Models\EventoGeocerca;
use App\Models\Alerta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GpsController extends Controller
{
    public function traccar(Request $request)
    {
        // Cambiado de query() a input() para aceptar peticiones GET y POST sin error 400
        $imei = $request->input('id');

        if (!$imei) {
            return response('Falta ID (IMEI)', 400);
        }

        $dispositivo = Dispositivo::with('vehiculo')->where('imei', $imei)->first();

        if (!$dispositivo) {
            Log::warning("Traccar: Dispositivo con IMEI $imei no registrado.");
            return response('Dispositivo no registrado en SotyGeo', 404);
        }

        $lat = $request->input('lat');
        $lon = $request->input('lon');
        $velocidadKmh = $request->input('speed', 0) * 1.852;
        
        $fechaGps = $request->input('timestamp') 
            ? Carbon::createFromTimestamp($request->input('timestamp')) 
            : now();

        // Guardar historial de ruta
        Ubicacion::create([
            'dispositivo_id' => $dispositivo->id,
            'vehiculo_id' => $dispositivo->vehiculo_id,
            'latitud' => $lat,
            'longitud' => $lon,
            'punto' => DB::raw("ST_GeomFromText('POINT($lon $lat)', 4326)"),
            'velocidad' => $velocidadKmh,
            'altitud' => $request->input('altitude', 0),
            'rumbo' => $request->input('bearing', 0),
            'porcentaje_bateria' => $request->input('batt'),
            'fecha_gps' => $fechaGps,
        ]);

        // Verificar si el dispositivo está asignado a un vehículo
        if ($dispositivo->vehiculo_id && $dispositivo->vehiculo) {
            Log::info("Traccar: Evaluando geocercas para vehículo ID: {$dispositivo->vehiculo_id} en Lon: $lon, Lat: $lat");
            $this->procesarGeocercas($dispositivo->vehiculo, $lat, $lon, $fechaGps);
        } else {
            Log::warning("Traccar: El dispositivo $imei llegó bien, pero NO tiene un vehículo asignado en la BD.");
        }

        return response('OK', 200);
    }

    private function procesarGeocercas(Vehiculo $vehiculo, $lat, $lng, Carbon $fechaGps)
    {
        $puntoActual = "POINT($lng $lat)";
        
        $zonasDentro = $vehiculo->zonas()->whereRaw(
            "ST_Contains(zonas.poligono, ST_GeomFromText(?, 4326))", 
            [$puntoActual]
        )->get();
        
        $idsZonasDentro = $zonasDentro->pluck('id')->toArray();

        $eventosAbiertos = EventoGeocerca::where('vehiculo_id', $vehiculo->id)
                                         ->whereNull('fecha_salida')
                                         ->get();
        $idsZonasAbiertas = $eventosAbiertos->pluck('zona_id')->toArray();

        // Procesar Salidas
        foreach ($eventosAbiertos as $evento) {
            if (!in_array($evento->zona_id, $idsZonasDentro)) {
                $duracion = $fechaGps->diffInMinutes($evento->fecha_entrada);
                
                $evento->update([
                    'fecha_salida' => $fechaGps,
                    'latitud_salida' => $lat,
                    'longitud_salida' => $lng,
                    'duracion_minutos' => $duracion,
                ]);

                $regla = $evento->zona->vehiculos()->where('vehiculo_id', $vehiculo->id)->first()?->pivot;
                if ($regla && $regla->notificar_salida) {
                    Alerta::create([
                        'vehiculo_id' => $vehiculo->id,
                        'tipo' => 'geocerca_salida',
                        'mensaje' => "El vehículo {$vehiculo->nombre} salió de la zona: {$evento->zona->nombre}. Duración: {$duracion} min.",
                    ]);
                }
            }
        }

        // Procesar Entradas
        foreach ($zonasDentro as $zona) {
            if (!in_array($zona->id, $idsZonasAbiertas)) {
                $regla = $zona->pivot;
                $tipoEvento = ($regla && $regla->tipo_regla === 'restringida') ? 'violacion_restringida' : 'normal';

                EventoGeocerca::create([
                    'vehiculo_id' => $vehiculo->id,
                    'zona_id' => $zona->id,
                    'fecha_entrada' => $fechaGps,
                    'latitud_entrada' => $lat,
                    'longitud_entrada' => $lng,
                    'tipo_evento' => $tipoEvento,
                ]);

                if ($regla && $regla->notificar_entrada) {
                    Alerta::create([
                        'vehiculo_id' => $vehiculo->id,
                        'tipo' => 'geocerca_entrada',
                        'mensaje' => "El vehículo {$vehiculo->nombre} ingresó a la zona: {$zona->nombre}.",
                    ]);
                }
            }
        }
    }
}