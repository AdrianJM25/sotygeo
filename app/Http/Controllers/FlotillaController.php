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

        // Iniciamos la consulta base
        $query = Flotilla::with(['usuario', 'activos', 'empresa'])->latest();

        if (!$userActual->hasRole('Super Administrador')) {
            // Un cliente solo ve sus propias flotillas
            $query->where('empresa_id', $userActual->empresa_id);
            
            // Y solo puede asignar usuarios de su propia empresa
            $users = User::where('empresa_id', $userActual->empresa_id)->get();
            $empresas = collect([$userActual->empresa]); 
        } else {
            // Tu equipo ve todo el panorama
            $users = User::with('empresa')->get();
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
            'user_id' => 'nullable|exists:users,id', // Opcional: Una flotilla puede no tener gestor al inicio
            'empresa_id' => $userActual->hasRole('Super Administrador') ? 'required|exists:empresas,id' : 'nullable',
        ]);

        $empresa_id = $userActual->hasRole('Super Administrador') 
                        ? $request->empresa_id 
                        : $userActual->empresa_id;

        // Validación de seguridad: El usuario asignado DEBE pertenecer a la empresa de la flotilla
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

        // Protección de ruta: Si un usuario adivina el ID en la URL de una flotilla ajena, lo bloqueamos
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
                        : $flotilla->empresa_id; // Se mantiene en su empresa original si es un cliente editando

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

        // Misma protección de ruta para la eliminación
        if (!$userActual->hasRole('Super Administrador') && $flotilla->empresa_id !== $userActual->empresa_id) {
            abort(403, 'Acceso denegado a esta flotilla.');
        }

        $flotilla->delete();
        return redirect()->route('flotillas.index')->with('success', 'Flotilla eliminada correctamente.');
    }
}