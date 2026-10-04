<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RutaController extends Controller
{
    public function historial(Request $request, Vehiculo $vehiculo)
    {
        $this->verificarPropiedadVehiculo($vehiculo);

        if (!$vehiculo->dispositivo) {
            return back()->with('error', 'Este vehículo no tiene un dispositivo GPS asignado.');
        }

        $zonaLocal = 'America/Mexico_City';

        if ($request->filled('inicio') && $request->filled('fin')) {
            // El usuario eligió un rango exacto de fecha y hora (interpretado en hora local)
            $inicioLocal = Carbon::parse($request->inicio, $zonaLocal);
            $finLocal    = Carbon::parse($request->fin, $zonaLocal);
        } else {
            // Por defecto: el día de hoy completo, en hora local
            $hoy = Carbon::now($zonaLocal);
            $inicioLocal = $hoy->copy()->startOfDay();
            $finLocal    = $hoy->copy()->endOfDay();
        }

        // Si por error el usuario invierte las fechas, las intercambiamos
        if ($finLocal->lt($inicioLocal)) {
            [$inicioLocal, $finLocal] = [$finLocal, $inicioLocal];
        }

        // Convertimos el rango de hora local a UTC, que es como está guardado fecha_gps
        $inicioUTC = $inicioLocal->copy()->setTimezone('UTC');
        $finUTC    = $finLocal->copy()->setTimezone('UTC');

        $ubicaciones = $vehiculo->dispositivo->ubicaciones()
            ->whereBetween('fecha_gps', [$inicioUTC, $finUTC])
            ->orderBy('fecha_gps', 'asc')
            ->get();

        return view('vehiculos.ruta', [
            'vehiculo'    => $vehiculo,
            'ubicaciones' => $ubicaciones,
            'inicioLocal' => $inicioLocal,
            'finLocal'    => $finLocal,
        ]);
    }

    private function verificarPropiedadVehiculo(Vehiculo $vehiculo)
    {
        $user = auth()->user();

        if ($user->hasRole('Super Administrador')) {
            return;
        }

        if ($user->hasRole('Cliente Individual')) {
            if ($vehiculo->user_id !== $user->id) {
                abort(403, 'Acceso denegado a las rutas de este vehículo.');
            }
        } else {
            if ($vehiculo->empresa_id !== $user->empresa_id) {
                abort(403, 'El vehículo pertenece a otra empresa.');
            }
        }
    }
}