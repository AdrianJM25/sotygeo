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

        // La fecha que pide el usuario se interpreta en hora de México,
        // no en UTC (así "26/09" significa el día 26 completo en Morelos/CDMX).
        $fechaConsulta = $request->filled('fecha')
            ? Carbon::parse($request->fecha, $zonaLocal)
            : Carbon::now($zonaLocal);

        // Convertimos el día completo en hora local a un rango UTC,
        // que es como está guardado fecha_gps en la base de datos.
        $inicioUTC = $fechaConsulta->copy()->startOfDay()->setTimezone('UTC');
        $finUTC    = $fechaConsulta->copy()->endOfDay()->setTimezone('UTC');

        $ubicaciones = $vehiculo->dispositivo->ubicaciones()
            ->whereBetween('fecha_gps', [$inicioUTC, $finUTC])
            ->orderBy('fecha_gps', 'asc')
            ->get();

        return view('vehiculos.ruta', compact('vehiculo', 'ubicaciones', 'fechaConsulta'));
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