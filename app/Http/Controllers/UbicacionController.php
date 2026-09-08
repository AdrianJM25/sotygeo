<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dispositivo;
use App\Models\Ubicacion;

class UbicacionController extends Controller
{
    public function traccar(Request $request)
    {
        // 1. Usamos "input" para que lea los datos sin importar si llegan por GET o POST
        $imei = $request->input('id');

        if (!$imei) {
            return response('Error: Falta ID (IMEI)', 400);
        }

        $dispositivo = Dispositivo::where('imei', $imei)->first();

        if (!$dispositivo) {
            return response('Error: Dispositivo no registrado en la plataforma', 404);
        }

        // Conversiones
        $velocidadKmh = $request->input('speed', 0) * 1.852;
        $enMovimiento = $velocidadKmh > 2;

        $fechaHora = $request->input('timestamp') 
            ? date('Y-m-d H:i:s', $request->input('timestamp')) 
            : now();

        // Guardar la coordenada
        Ubicacion::create([
            'dispositivo_id' => $dispositivo->id,
            'vehiculo_id' => $dispositivo->vehiculo_id,
            'latitud' => $request->input('lat'),
            'longitud' => $request->input('lon'),
            'velocidad' => $velocidadKmh,
            'altitud' => $request->input('altitude'),
            'rumbo' => $request->input('bearing'),
            'porcentaje_bateria' => $request->input('batt'),
            'esta_cargando' => $request->input('charge') === 'true',
            'en_movimiento' => $enMovimiento,
            'estado_reposo' => $dispositivo->modo_reposo,
            'fecha_gps' => $fechaHora,
        ]);

        return response('OK', 200);
    }
}