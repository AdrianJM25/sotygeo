<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Mostrar la lista de empresas corporativas.
     */
    public function index()
    {
        // Traemos las empresas junto con el conteo de usuarios y ahora VEHICULOS (en lugar de activos)
        $empresas = Empresa::withCount(['usuarios', 'vehiculos'])->latest()->paginate(10);
        
        return view('empresas.index', compact('empresas'));
    }

    /**
     * Guardar una nueva empresa en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'rfc' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
        ]);

        Empresa::create([
            'nombre' => $request->nombre,
            'rfc' => $request->rfc,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('empresas.index')->with('success', 'Empresa registrada correctamente.');
    }

    /**
     * Actualizar los datos de una empresa existente.
     */
    public function update(Request $request, Empresa $empresa)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'rfc' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
        ]);

        $empresa->update([
            'nombre' => $request->nombre,
            'rfc' => $request->rfc,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('empresas.index')->with('success', 'Empresa actualizada correctamente.');
    }

    /**
     * Eliminar una empresa (esto también eliminará en cascada usuarios y vehículos vinculados).
     */
    public function destroy(Empresa $empresa)
    {
        $empresa->delete();
        return redirect()->route('empresas.index')->with('success', 'Empresa y todos sus registros vinculados eliminados correctamente.');
    }
}