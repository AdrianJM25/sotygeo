<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Dispositivo;
use App\Models\Ubicacion;

class UbicacionController extends Controller
{
    public function traccar(Request $request)
    {
        $imei = $request->input('id');

        if (!$imei) {
            return response()->json(['error' => 'Falta ID (IMEI)'], 400);
        }

        $dispositivo = Dispositivo::where('imei', $imei)->first();

        if (!$dispositivo) {
            Log::warning("Traccar Webhook: Intento de conexión de dispositivo no registrado (IMEI: {$imei})");
            return response()->json(['error' => 'Dispositivo no registrado en la plataforma'], 404);
        }

        $velocidadKmh = $request->input('speed', 0) * 1.852; // Nudos a km/h
        $enMovimiento = $velocidadKmh > 2;

        $fechaHora = $request->input('timestamp') 
            ? date('Y-m-d H:i:s', $request->input('timestamp')) 
            : now();

        Ubicacion::create([
            'dispositivo_id' => $dispositivo->id,
            'vehiculo_id' => $dispositivo->vehiculo_id,
            'latitud' => $request->input('lat'),
            'longitud' => $request->input('lon'),
            'velocidad' => $velocidadKmh,
            'altitud' => $request->input('altitude', 0),
            'rumbo' => $request->input('bearing', 0),
            'porcentaje_bateria' => $request->input('batt', null),
            'esta_cargando' => $request->input('charge') === 'true' || $request->input('charge') === true,
            'en_movimiento' => $enMovimiento,
            'estado_reposo' => $dispositivo->modo_reposo ?? false,
            'fecha_gps' => $fechaHora,
        ]);

        return response('OK', 200);
    }
}