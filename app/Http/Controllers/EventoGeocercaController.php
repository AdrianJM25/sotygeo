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

        // CORRECCIÓN 1: Llamamos a la relación 'zona' en lugar de 'geocerca'
        $query = EventoGeocerca::with(['vehiculo', 'zona'])->latest('fecha_entrada');

        // Scoping Multi-Tenant
        if ($user->hasRole('Cliente Individual')) {
            $query->whereHas('vehiculo', fn($q) => $q->where('user_id', $user->id));
            $vehiculos = Vehiculo::where('user_id', $user->id)->get();
            $geocercas = Zona::where('user_id', $user->id)->get();
        } elseif (!$user->hasRole('Super Administrador')) {
            $query->whereHas('vehiculo', fn($q) => $q->where('empresa_id', $user->empresa_id));
            $vehiculos = Vehiculo::where('empresa_id', $user->empresa_id)->get();
            $geocercas = Zona::where('empresa_id', $user->empresa_id)->get();
        } else {
            $vehiculos = Vehiculo::all();
            $geocercas = Zona::all();
        }

        // Filtros opcionales (Alineados con el formulario de la vista)
        if ($request->filled('vehiculo_id')) {
            $query->where('vehiculo_id', $request->vehiculo_id);
        }

        if ($request->filled('geocerca_id')) {
            // CORRECCIÓN 2: Apuntamos a la columna correcta 'zona_id'
            $query->where('zona_id', $request->geocerca_id); 
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

        // Se envía $geocercas (como lo espera el @foreach de la vista)
        return view('geocercas.historial', compact('eventos', 'vehiculos', 'geocercas'));
    }
}