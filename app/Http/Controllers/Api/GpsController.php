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
use Carbon\Carbon;

class GpsController extends Controller
{
    public function traccar(Request $request)
    {
        $imei = $request->query('id');

        if (!$imei) {
            return response('Falta ID (IMEI)', 400);
        }

        $dispositivo = Dispositivo::with('vehiculo')->where('imei', $imei)->first();

        if (!$dispositivo) {
            return response('Dispositivo no registrado en SotyGeo', 404);
        }

        $lat = $request->query('lat');
        $lon = $request->query('lon');
        $velocidadKmh = $request->query('speed', 0) * 1.852;
        
        $fechaGps = $request->query('timestamp') 
            ? Carbon::createFromTimestamp($request->query('timestamp')) 
            : now();

        Ubicacion::create([
            'dispositivo_id' => $dispositivo->id,
            'vehiculo_id' => $dispositivo->vehiculo_id,
            'latitud' => $lat,
            'longitud' => $lon,
            'punto' => DB::raw("ST_GeomFromText('POINT($lon $lat)', 4326)"),
            'velocidad' => $velocidadKmh,
            'altitud' => $request->query('altitude', 0),
            'rumbo' => $request->query('bearing', 0),
            'porcentaje_bateria' => $request->query('batt'),
            'fecha_gps' => $fechaGps,
        ]);

        if ($dispositivo->vehiculo_id) {
            $this->procesarGeocercas($dispositivo->vehiculo, $lat, $lon, $fechaGps);
        }

        return response('OK', 200);
    }

    private function procesarGeocercas(Vehiculo $vehiculo, $lat, $lng, Carbon $fechaGps)
    {
        $puntoActual = "POINT($lng $lat)";
        $zonasDentro = $vehiculo->zonas()->whereRaw(
            "ST_Contains(poligono, ST_GeomFromText(?, 4326))", 
            [$puntoActual]
        )->get();
        $idsZonasDentro = $zonasDentro->pluck('id')->toArray();

        $eventosAbiertos = EventoGeocerca::where('vehiculo_id', $vehiculo->id)
                                         ->whereNull('fecha_salida')
                                         ->get();
        $idsZonasAbiertas = $eventosAbiertos->pluck('zona_id')->toArray();

        foreach ($eventosAbiertos as $evento) {
            if (!in_array($evento->zona_id, $idsZonasDentro)) {
                $duracion = $fechaGps->diffInMinutes($evento->fecha_entrada);
                
                $evento->update([
                    'fecha_salida' => $fechaGps,
                    'latitud_salida' => $lat,
                    'longitud_salida' => $lng,
                    'duracion_minutos' => $duracion,
                ]);

                $regla = $evento->zona->vehiculos()->where('vehiculo_id', $vehiculo->id)->first()->pivot;
                if ($regla && $regla->notificar_salida) {
                    Alerta::create([
                        'vehiculo_id' => $vehiculo->id,
                        'tipo' => 'geocerca_salida',
                        'mensaje' => "El vehículo {$vehiculo->nombre} salió de la zona: {$evento->zona->nombre}. Duración: {$duracion} min.",
                    ]);
                }
            }
        }

        foreach ($zonasDentro as $zona) {
            if (!in_array($zona->id, $idsZonasAbiertas)) {
                $regla = $zona->pivot;
                $tipoEvento = $regla->tipo_regla === 'restringida' ? 'violacion_restringida' : 'normal';

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