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

        // Si en tu modelo EventoGeocerca la relación se llama 'zona', cambia 'geocerca' por 'zona' 
        // y en tu vista blade cambia $evento->geocerca->nombre por $evento->zona->nombre.
        $query = EventoGeocerca::with(['vehiculo', 'geocerca'])->latest('created_at');

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
            // Cambia 'geocerca_id' por 'zona_id' si así se llama la columna en tu base de datos
            $query->where('geocerca_id', $request->geocerca_id); 
        }

        if ($request->filled('tipo_evento')) {
            $query->where('tipo_evento', $request->tipo_evento);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $eventos = $query->paginate(20);

        // Se envía $geocercas (como lo espera el @foreach de la vista)
        return view('geocercas.historial', compact('eventos', 'vehiculos', 'geocercas'));
    }
}