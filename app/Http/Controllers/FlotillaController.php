<?php

namespace App\Http\Controllers;

use App\Models\Flotilla;
use App\Models\User;
use App\Models\Empresa;
use Illuminate\Http\Request;

class FlotillaController extends Controller
{
    public function index()
    {
        $userActual = auth()->user();

        // SOLUCIÓN AL ERROR: Cambiamos 'activos' por 'vehiculos'
        $query = Flotilla::with(['usuario', 'vehiculos', 'empresa'])->latest();

        if (!$userActual->hasRole('Super Administrador')) {
            $query->where('empresa_id', $userActual->empresa_id);
            
            // Solo usuarios activos de su propia empresa
            $users = User::where('empresa_id', $userActual->empresa_id)
                         ->where('activo', true)
                         ->get(['id', 'nombre', 'apellido_paterno', 'empresa_id']);
                         
            $empresas = collect([$userActual->empresa]); 
        } else {
            // Super Admin obtiene todos los usuarios activos para que Alpine.js los filtre en la vista
            $users = User::where('activo', true)
                         ->get(['id', 'nombre', 'apellido_paterno', 'empresa_id']);
                         
            $empresas = Empresa::orderBy('nombre')->get();
        }

        $flotillas = $query->paginate(10);

        return view('flotillas.index', compact('flotillas', 'users', 'empresas'));
    }

    public function store(Request $request)
    {
        $userActual = auth()->user();

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id', 
            'empresa_id' => $userActual->hasRole('Super Administrador') ? 'required|exists:empresas,id' : 'nullable',
        ]);

        $empresa_id = $userActual->hasRole('Super Administrador') 
                        ? $request->empresa_id 
                        : $userActual->empresa_id;

        if ($request->filled('user_id')) {
            $gestor = User::findOrFail($request->user_id);
            if ($gestor->empresa_id != $empresa_id) {
                return back()->withErrors(['user_id' => 'El responsable seleccionado pertenece a otro corporativo.'])->withInput();
            }
        }

        Flotilla::create([
            'empresa_id' => $empresa_id,
            'user_id' => $request->user_id,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('flotillas.index')->with('success', 'Flotilla registrada correctamente.');
    }

    public function update(Request $request, Flotilla $flotilla)
    {
        $userActual = auth()->user();

        if (!$userActual->hasRole('Super Administrador') && $flotilla->empresa_id !== $userActual->empresa_id) {
            abort(403, 'Acceso denegado a esta flotilla.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'empresa_id' => $userActual->hasRole('Super Administrador') ? 'required|exists:empresas,id' : 'nullable',
        ]);

        $empresa_id = $userActual->hasRole('Super Administrador') 
                        ? $request->empresa_id 
                        : $flotilla->empresa_id; 

        if ($request->filled('user_id')) {
            $gestor = User::findOrFail($request->user_id);
            if ($gestor->empresa_id != $empresa_id) {
                return back()->withErrors(['user_id' => 'El responsable seleccionado pertenece a otro corporativo.'])->withInput();
            }
        }

        $flotilla->update([
            'empresa_id' => $empresa_id,
            'user_id' => $request->user_id,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('flotillas.index')->with('success', 'Flotilla actualizada correctamente.');
    }

    public function destroy(Flotilla $flotilla)
    {
        $userActual = auth()->user();

        if (!$userActual->hasRole('Super Administrador') && $flotilla->empresa_id !== $userActual->empresa_id) {
            abort(403, 'Acceso denegado a esta flotilla.');
        }

        $flotilla->delete();
        return redirect()->route('flotillas.index')->with('success', 'Flotilla eliminada correctamente.');
    }
}