<?php

namespace App\Http\Controllers;

use App\Models\EventoGeocerca;
use App\Models\Vehiculo;
use App\Models\Zona;
use Illuminate\Http\Request;

class EventoGeocercaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = EventoGeocerca::with(['vehiculo', 'zona'])->latest('fecha_entrada');

        // Scoping Multi-Tenant
        if ($user->hasRole('Cliente Individual')) {
            $query->whereHas('vehiculo', fn($q) => $q->where('user_id', $user->id));
            $vehiculos = Vehiculo::where('user_id', $user->id)->get();
            $zonas = Zona::where('user_id', $user->id)->get();
        } elseif (!$user->hasRole('Super Administrador')) {
            $query->whereHas('vehiculo', fn($q) => $q->where('empresa_id', $user->empresa_id));
            $vehiculos = Vehiculo::where('empresa_id', $user->empresa_id)->get();
            $zonas = Zona::where('empresa_id', $user->empresa_id)->get();
        } else {
            $vehiculos = Vehiculo::all();
            $zonas = Zona::all();
        }

        // Filtros opcionales
        if ($request->filled('vehiculo_id')) {
            $query->where('vehiculo_id', $request->vehiculo_id);
        }

        if ($request->filled('zona_id')) {
            $query->where('zona_id', $request->zona_id);
        }

        if ($request->filled('tipo_evento')) {
            $query->where('tipo_evento', $request->tipo_evento);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_entrada', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_entrada', '<=', $request->fecha_fin);
        }

        $eventos = $query->paginate(20);

        return view('geocercas.historial', compact('eventos', 'vehiculos', 'zonas'));
    }
}