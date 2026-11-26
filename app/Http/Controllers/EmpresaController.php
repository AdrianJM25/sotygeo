<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmpresaController extends Controller
{
    /**
     * Mostrar la lista de empresas.
     */
    public function index()
    {
        $empresas = Empresa::query()
            ->withCount([
                'usuarios',
                'vehiculos',
                'dispositivos',
                'flotillas',
                'zonas',
            ])
            ->latest()
            ->paginate(10);

        return view(
            'empresas.index',
            compact('empresas')
        );
    }

    /**
     * Registrar una nueva empresa.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],
            'rfc' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('empresas', 'rfc'),
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],
            'correo' => [
                'nullable',
                'email',
                'max:255',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['rfc'] = filled($validated['rfc'] ?? null)
            ? strtoupper(trim($validated['rfc']))
            : null;

        $validated['nombre'] = trim($validated['nombre']);
        $validated['telefono'] = filled($validated['telefono'] ?? null)
            ? trim($validated['telefono'])
            : null;
        $validated['correo'] = filled($validated['correo'] ?? null)
            ? strtolower(trim($validated['correo']))
            : null;
        $validated['is_active'] = $request->boolean('is_active');

        Empresa::create($validated);

        return redirect()
            ->route('empresas.index')
            ->with(
                'success',
                'Empresa registrada correctamente.'
            );
    }

    /**
     * Actualizar una empresa existente.
     */
    public function update(
        Request $request,
        Empresa $empresa
    ) {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],
            'rfc' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('empresas', 'rfc')
                    ->ignore($empresa->id),
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],
            'correo' => [
                'nullable',
                'email',
                'max:255',
            ],
            'is_active' => [
                'nullable',
                'boolean',
            ],
            'is_edit' => [
                'nullable',
                'integer',
            ],
        ]);

        $validated['rfc'] = filled($validated['rfc'] ?? null)
            ? strtoupper(trim($validated['rfc']))
            : null;

        $validated['nombre'] = trim($validated['nombre']);
        $validated['telefono'] = filled($validated['telefono'] ?? null)
            ? trim($validated['telefono'])
            : null;
        $validated['correo'] = filled($validated['correo'] ?? null)
            ? strtolower(trim($validated['correo']))
            : null;
        $validated['is_active'] = $request->boolean('is_active');

        unset($validated['is_edit']);

        $empresa->update($validated);

        return redirect()
            ->route('empresas.index')
            ->with(
                'success',
                'Empresa actualizada correctamente.'
            );
    }

    /**
     * Eliminar una empresa.
     */
    public function destroy(Empresa $empresa)
    {
        DB::transaction(function () use ($empresa) {
            $empresa->delete();
        });

        return redirect()
            ->route('empresas.index')
            ->with(
                'success',
                'Empresa eliminada correctamente.'
            );
    }
}
