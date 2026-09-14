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

class ReceptorGpsController extends Controller
{
    public function recibirTramas(Request $request)
    {
        // Aceptamos el formato de nuestra API o el formato universal OsmAnd (Traccar)
        $imei = $request->input('imei', $request->input('id'));
        $lat = $request->input('lat');
        $lng = $request->input('lng', $request->input('lon'));
        
        if (!$imei || !$lat || !$lng) {
            return response()->json(['error' => 'Faltan datos obligatorios (imei, lat, lng)'], 400);
        }

        // Cargamos el dispositivo junto con la relación del vehículo
        $dispositivo = Dispositivo::with('vehiculo')->where('imei', $imei)->first();

        if (!$dispositivo) {
            Log::warning("Receptor: Dispositivo con IMEI $imei no registrado.");
            return response()->json(['error' => 'Dispositivo no registrado en SotyGeo'], 404);
        }

        // Convertir el timestamp de UNIX a Fecha/Hora de Postgres o usar Carbon
        $fecha_gps = $request->input('fecha_gps');
        if (!$fecha_gps && $request->has('timestamp')) {
            $fecha_gps = Carbon::createFromTimestamp($request->input('timestamp'))->toDateTimeString();
        }

        $fechaCarbon = $fecha_gps ? Carbon::parse($fecha_gps) : now();

        $ubicacion = Ubicacion::create([
            'dispositivo_id' => $dispositivo->id,
            'vehiculo_id' => $dispositivo->vehiculo_id, // Aseguramos guardar la relación en la ubicación
            'punto' => DB::raw("ST_SetSRID(ST_GeomFromText('POINT({$lng} {$lat})'), 4326)"),
            'velocidad' => $request->input('velocidad', $request->input('speed', 0)),
            'porcentaje_bateria' => $request->input('bateria', $request->input('batt', null)),
            'fecha_gps' => $fechaCarbon,
        ]);

        // ==========================================
        // EVALUACIÓN DE GEOCERCAS
        // ==========================================
        if ($dispositivo->vehiculo_id && $dispositivo->vehiculo) {
            $this->procesarGeocercas($dispositivo->vehiculo, $lat, $lng, $fechaCarbon);
        }

        return response()->json(['status' => 'success'], 201);
    }

    public function obtenerActivosEnVivo()
    {
        // Buscamos los activos con su dispositivo y la última coordenada registrada
        $activos = \App\Models\Activo::with(['dispositivo.ubicaciones' => function($query) {
            $query->select(
                'id', 'dispositivo_id', 'velocidad', 'porcentaje_bateria', 'fecha_gps',
                \Illuminate\Support\Facades\DB::raw('ST_X(punto) as lng'),
                \Illuminate\Support\Facades\DB::raw('ST_Y(punto) as lat')
            )->latest('fecha_gps')->limit(1);
        }])->get();

        return response()->json($activos);
    }

    // ==========================================
    // LÓGICA DE GEOCERCAS (Solo BD local, SIN Correos)
    // ==========================================
    private function procesarGeocercas(Vehiculo $vehiculo, $lat, $lng, Carbon $fechaGps)
    {
        $puntoActual = "POINT($lng $lat)";
        
        // Determinar en qué zonas se encuentra actualmente la coordenada
        $zonasDentro = $vehiculo->zonas()->whereRaw(
            "ST_Contains(zonas.poligono, ST_GeomFromText(?, 4326))", 
            [$puntoActual]
        )->get();
        
        $idsZonasDentro = $zonasDentro->pluck('id')->toArray();

        // Buscar eventos de geocerca que estén "abiertos" (sin fecha de salida)
        $eventosAbiertos = EventoGeocerca::where('vehiculo_id', $vehiculo->id)
                                         ->whereNull('fecha_salida')
                                         ->get();
        $idsZonasAbiertas = $eventosAbiertos->pluck('zona_id')->toArray();

        // 1. Procesar SALIDAS
        foreach ($eventosAbiertos as $evento) {
            // Si el evento abierto no está en las zonas actuales, significa que salió
            if (!in_array($evento->zona_id, $idsZonasDentro)) {
                $duracion = $fechaGps->diffInMinutes($evento->fecha_entrada);
                
                $evento->update([
                    'fecha_salida' => $fechaGps,
                    'latitud_salida' => $lat,
                    'longitud_salida' => $lng,
                    'duracion_minutos' => $duracion,
                ]);

                $regla = $evento->zona->vehiculos()->where('vehiculo_id', $vehiculo->id)->first()?->pivot;
                
                // Guardar la Alerta en BD para el Dashboard (Sin disparo de email)
                if ($regla && $regla->notificar_salida) {
                    Alerta::create([
                        'vehiculo_id' => $vehiculo->id,
                        'tipo' => 'geocerca_salida',
                        'mensaje' => "El vehículo {$vehiculo->nombre} salió de la zona: {$evento->zona->nombre}. Duración: {$duracion} min.",
                    ]);
                }
            }
        }

        // 2. Procesar ENTRADAS
        foreach ($zonasDentro as $zona) {
            // Si la zona en la que está no tiene un evento abierto, significa que acaba de entrar
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

                // Guardar la Alerta en BD para el Dashboard (Sin disparo de email)
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