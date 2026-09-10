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

        // Permite filtrar por fecha mediante request (por defecto hoy)
        $fechaConsulta = $request->filled('fecha') 
            ? Carbon::parse($request->fecha) 
            : today();

        $ubicaciones = $vehiculo->dispositivo->ubicaciones()
            ->whereDate('fecha_gps', $fechaConsulta)
            ->orderBy('fecha_gps', 'asc')
            ->get();

        return view('vehiculos.ruta', compact('vehiculo', 'ubicaciones', 'fechaConsulta'));
    }

    /**
     * Verifica que el usuario actual tenga permisos sobre el vehículo consultado.
     */
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