<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dispositivo;
use App\Models\Ubicacion;

class GpsController extends Controller
{
    /**
     * Recibe las tramas de Traccar Client (Protocolo OsmAnd)
     */
    public function traccar(Request $request)
    {
        // 1. Traccar manda el IMEI en el parámetro 'id'
        $imei = $request->query('id');

        if (!$imei) {
            return response('Falta ID (IMEI)', 400);
        }

        // 2. Buscamos si ese IMEI existe en nuestro sistema
        $dispositivo = Dispositivo::where('imei', $imei)->first();

        if (!$dispositivo) {
            return response('Dispositivo no registrado en SotyGeo', 404);
        }

        // 3. Traccar manda la velocidad en "Nudos" (knots). La convertimos a Km/h.
        $velocidadNudos = $request->query('speed', 0);
        $velocidadKmh = $velocidadNudos * 1.852;

        // 4. Traccar manda el timestamp en formato Unix (segundos). Lo pasamos a fecha legible.
        $fechaHora = $request->query('timestamp') 
            ? date('Y-m-d H:i:s', $request->query('timestamp')) 
            : now();

        // 5. Guardar la coordenada en la base de datos
        Ubicacion::create([
            'dispositivo_id' => $dispositivo->id,
            'vehiculo_id' => $dispositivo->vehiculo_id, // Si tiene vehículo asignado, lo hereda
            'latitud' => $request->query('lat'),
            'longitud' => $request->query('lon'),
            'velocidad' => $velocidadKmh,
            'altitud' => $request->query('altitude', 0),
            'rumbo' => $request->query('bearing', 0),
            'bateria' => $request->query('batt'),
            'fecha_hora' => $fechaHora,
        ]);

        // 6. Respondemos con un HTTP 200 OK para que la app sepa que llegó bien
        return response('OK', 200);
    }
}