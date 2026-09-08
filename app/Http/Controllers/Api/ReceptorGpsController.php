<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dispositivo;
use App\Models\Ubicacion;
use Illuminate\Support\Facades\DB;

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

        $dispositivo = Dispositivo::where('imei', $imei)->first();

        if (!$dispositivo) {
            return response()->json(['error' => 'Dispositivo no registrado en SotyGeo'], 404);
        }

        // Convertir el timestamp de UNIX (que mandan las apps) a Fecha/Hora de Postgres
        $fecha_gps = $request->input('fecha_gps');
        if (!$fecha_gps && $request->has('timestamp')) {
            $fecha_gps = date('Y-m-d H:i:s', $request->input('timestamp'));
        }

        $ubicacion = Ubicacion::create([
            'dispositivo_id' => $dispositivo->id,
            'punto' => DB::raw("ST_SetSRID(ST_GeomFromText('POINT({$lng} {$lat})'), 4326)"),
            'velocidad' => $request->input('velocidad', $request->input('speed', 0)),
            'porcentaje_bateria' => $request->input('bateria', $request->input('batt', null)),
            'fecha_gps' => $fecha_gps ?? now(), // Si no trae fecha, usamos la actual
        ]);

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
}