<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class DashboardApiController extends Controller
{
    public function vehiculosEnVivo(Request $request)
    {
        $user = auth()->user(); // Solo obtiene los vehículos a los que el usuario tiene acceso

        $query = Vehiculo::with(['dispositivo.ubicaciones' => function ($q) {
            // Traemos solo la ÚLTIMA ubicación (la más reciente) de cada GPS
            $q->latest('fecha_gps')->limit(1);
        }]);

        // Aplicamos la misma seguridad Multi-tenant de siempre
        if ($user->hasRole('Cliente Individual')) {
            $query->where('user_id', $user->id);
        } elseif (!$user->hasRole('Super Administrador')) {
            $query->where('empresa_id', $user->empresa_id);
        }

        $vehiculos = $query->get();

        return response()->json($vehiculos);
    }
}