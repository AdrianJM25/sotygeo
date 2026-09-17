<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    public function vehiculosEnVivo(Request $request)
    {
        $user = auth()->user();

        $query = Vehiculo::with(['dispositivo.ubicaciones' => function ($q) {
            $q->latest('fecha_gps')->limit(1);
        }]);

        if ($user->hasRole('Cliente Individual')) {
            $query->where('user_id', $user->id);
        } elseif (!$user->hasRole('Super Administrador')) {
            $query->where('empresa_id', $user->empresa_id);
        }

        return response()->json($query->get());
    }

    public function zonasEnVivo(Request $request)
    {
        $user = auth()->user();

        $query = Zona::select(
            'id', 'nombre', 'color_hex',
            DB::raw('ST_AsGeoJSON(poligono) as geojson')
        );

        if ($user->hasRole('Cliente Individual')) {
            $query->where('user_id', $user->id);
        } elseif (!$user->hasRole('Super Administrador')) {
            $query->where('empresa_id', $user->empresa_id);
        }

        $zonas = $query->get();

        $features = $zonas->map(fn ($zona) => [
            'type' => 'Feature',
            'properties' => [
                'id' => $zona->id,
                'nombre' => $zona->nombre,
                'color' => $zona->color_hex,
            ],
            'geometry' => json_decode($zona->geojson),
        ]);

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }



    public function alertasRecientes(Request $request)
{
    $user = auth()->user();

    $query = \App\Models\Alerta::with('vehiculo')->latest();

    if ($user->hasRole('Cliente Individual')) {
        $query->whereHas('vehiculo', fn($q) => $q->where('user_id', $user->id));
    } elseif (!$user->hasRole('Super Administrador')) {
        $query->whereHas('vehiculo', fn($q) => $q->where('empresa_id', $user->empresa_id));
    }

    return response()->json($query->take(15)->get()); // Retorna las últimas 15 notificaciones
}

public function estadoGeocercas(Request $request)
{
    $user = auth()->user();

    $query = \App\Models\EventoGeocerca::with(['vehiculo', 'zona'])
        ->whereNull('fecha_salida');

    if ($user->hasRole('Cliente Individual')) {
        $query->whereHas('vehiculo', fn ($q) => $q->where('user_id', $user->id));
    } elseif (!$user->hasRole('Super Administrador')) {
        $query->whereHas('vehiculo', fn ($q) => $q->where('empresa_id', $user->empresa_id));
    }

    $eventos = $query->get()->map(function ($evento) {
        $regla = $evento->zona->vehiculos()
            ->where('vehiculo_id', $evento->vehiculo_id)
            ->first()?->pivot;

        return [
            'vehiculo_id' => $evento->vehiculo_id,
            'vehiculo_nombre' => $evento->vehiculo->nombre,
            'zona_id' => $evento->zona_id,
            'zona_nombre' => $evento->zona->nombre,
            'zona_color' => $evento->zona->color_hex,
            'fecha_entrada' => $evento->fecha_entrada,
            'minutos_dentro' => now()->diffInMinutes($evento->fecha_entrada),
            'permanencia_maxima_minutos' => $regla->permanencia_maxima_minutos ?? null,
            'tipo_regla' => $regla->tipo_regla ?? null,
        ];
    });

    return response()->json($eventos);
}
}