<?php

namespace App\Http\Controllers;

use App\Models\Flotilla;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Vehiculo; // <-- No olvides importar el modelo Vehiculo
use Illuminate\Http\Request;

class FlotillaController extends Controller
{
    public function index()
    {
        $userActual = auth()->user();

        $query = Flotilla::with(['usuario', 'vehiculos', 'empresa'])->latest();
        
        // Ajusta los campos ('nombre', 'placas') si en tu BD se llaman diferente (ej. 'marca', 'modelo')
        $queryVehiculos = Vehiculo::select('id', 'empresa_id', 'flotilla_id', 'nombre', 'placas');

        if (!$userActual->hasRole('Super Administrador')) {
            $query->where('empresa_id', $userActual->empresa_id);
            $queryVehiculos->where('empresa_id', $userActual->empresa_id);
            
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
        $vehiculos = $queryVehiculos->get();

        return view('flotillas.index', compact('flotillas', 'users', 'empresas', 'vehiculos'));
    }

    public function store(Request $request)
    {
        $userActual = auth()->user();

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id', 
            'empresa_id' => $userActual->hasRole('Super Administrador') ? 'required|exists:empresas,id' : 'nullable',
            'vehiculos' => 'nullable|array',
            'vehiculos.*' => 'exists:vehiculos,id' // Validación del array de vehículos
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

        $flotilla = Flotilla::create([
            'empresa_id' => $empresa_id,
            'user_id' => $request->user_id,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        // Asignar los vehículos seleccionados a esta nueva flotilla
        if ($request->has('vehiculos') && count($request->vehiculos) > 0) {
            Vehiculo::whereIn('id', $request->vehiculos)
                    ->where('empresa_id', $empresa_id) // Capa extra de seguridad multi-tenant
                    ->update(['flotilla_id' => $flotilla->id]);
        }

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
            'vehiculos' => 'nullable|array',
            'vehiculos.*' => 'exists:vehiculos,id'
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

        // 1. Liberar todos los vehículos que actualmente pertenecen a esta flotilla
        Vehiculo::where('flotilla_id', $flotilla->id)->update(['flotilla_id' => null]);

        // 2. Asignar los nuevos vehículos seleccionados en el checkbox
        if ($request->has('vehiculos') && count($request->vehiculos) > 0) {
            Vehiculo::whereIn('id', $request->vehiculos)
                    ->where('empresa_id', $empresa_id) // Medida de seguridad
                    ->update(['flotilla_id' => $flotilla->id]);
        }

        return redirect()->route('flotillas.index')->with('success', 'Flotilla actualizada correctamente.');
    }

    public function destroy(Flotilla $flotilla)
    {
        $userActual = auth()->user();

        if (!$userActual->hasRole('Super Administrador') && $flotilla->empresa_id !== $userActual->empresa_id) {
            abort(403, 'Acceso denegado a esta flotilla.');
        }

        // Si en la base de datos no tienes "onDelete('set null')" en la llave foránea,
        // esto asegurará que los vehículos no se borren, solo se queden sin flotilla.
        Vehiculo::where('flotilla_id', $flotilla->id)->update(['flotilla_id' => null]);

        $flotilla->delete();
        
        return redirect()->route('flotillas.index')->with('success', 'Flotilla eliminada correctamente.');
    }
}