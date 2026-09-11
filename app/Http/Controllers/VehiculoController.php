<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Flotilla;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class VehiculoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Vehiculo::with(['empresa', 'usuario', 'flotilla', 'zonas'])->latest();

        if ($user->hasRole('Cliente Individual')) {
            $query->where('user_id', $user->id);
            $flotillas = collect();
            $empresas = collect();
            $clientes = collect();
            $zonas = Zona::where('user_id', $user->id)->get();
        } elseif (!$user->hasRole('Super Administrador')) {
            $query->where('empresa_id', $user->empresa_id);
            $flotillas = Flotilla::where('empresa_id', $user->empresa_id)->get();
            $empresas = collect([$user->empresa]);
            $clientes = collect();
            $zonas = Zona::where('empresa_id', $user->empresa_id)->get();
        } else {
            $flotillas = Flotilla::all();
            $empresas = Empresa::orderBy('nombre')->get();
            $clientes = User::role('Cliente Individual')->get();
            $zonas = Zona::all();
        }

        $vehiculos = $query->paginate(10);

        return view('vehiculos.index', compact('vehiculos', 'flotillas', 'empresas', 'clientes', 'zonas'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_vehiculo' => 'required|string',
            'empresa_id' => $user->hasRole('Super Administrador') ? 'nullable|exists:empresas,id' : 'nullable',
            'user_id' => $user->hasRole('Super Administrador') ? 'nullable|exists:users,id' : 'nullable',
            'flotilla_id' => 'nullable|exists:flotillas,id',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'anio' => 'nullable|integer|min:1900|max:2100',
            'placas' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
            'vin' => 'nullable|string|max:50|unique:vehiculos,vin',
            'rendimiento_km_litro' => 'nullable|numeric|min:0',
            'vencimiento_seguro' => 'nullable|date',

            'icono' => ['nullable', Rule::in(array_column(Vehiculo::iconosDisponibles(), 'path'))],
            'icono_file' => 'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:1024',
            'color_icono' => 'nullable|string|max:7',
        ]);

        $empresa_id = $user->hasRole('Super Administrador') ? $request->empresa_id : ($user->hasRole('Cliente Individual') ? null : $user->empresa_id);
        $user_id = $user->hasRole('Super Administrador') ? $request->user_id : ($user->hasRole('Cliente Individual') ? $user->id : null);

        if ($request->filled('flotilla_id') && $empresa_id) {
            $flotilla = Flotilla::findOrFail($request->flotilla_id);
            if ($flotilla->empresa_id != $empresa_id) {
                return back()->withErrors(['flotilla_id' => 'La flotilla pertenece a otro corporativo.'])->withInput();
            }
        }

        if ($request->hasFile('icono_file')) {
            $rutaIcono = $request->file('icono_file')->store('icons_vehiculos/personalizados', 'public');
        } else {
            $rutaIcono = $validated['icono'] ?? (Vehiculo::iconosDisponibles()[0]['path'] ?? null);
        }

        Vehiculo::create(array_merge($request->except('icono_file'), [
            'empresa_id' => $empresa_id,
            'user_id' => $user_id,
            'icono' => $rutaIcono,
            'color_icono' => $request->input('color_icono', '#111827'),
        ]));

        return redirect()->route('vehiculos.index')->with('success', 'Vehículo registrado correctamente.');
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $this->verificarPropiedadVehiculo($vehiculo);
        $user = auth()->user();

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_vehiculo' => 'required|string',
            'empresa_id' => $user->hasRole('Super Administrador') ? 'nullable|exists:empresas,id' : 'nullable',
            'user_id' => $user->hasRole('Super Administrador') ? 'nullable|exists:users,id' : 'nullable',
            'flotilla_id' => 'nullable|exists:flotillas,id',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'anio' => 'nullable|integer|min:1900|max:2100',
            'placas' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:50',
            'vin' => 'nullable|string|max:50|unique:vehiculos,vin,' . $vehiculo->id,
            'rendimiento_km_litro' => 'nullable|numeric|min:0',
            'vencimiento_seguro' => 'nullable|date',

            'icono' => ['nullable', Rule::in(array_column(Vehiculo::iconosDisponibles(), 'path'))],
            'icono_file' => 'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:1024',
            'color_icono' => 'nullable|string|max:7',
        ]);

        $empresa_id = $user->hasRole('Super Administrador') ? $request->empresa_id : $vehiculo->empresa_id;
        $user_id = $user->hasRole('Super Administrador') ? $request->user_id : $vehiculo->user_id;

        if ($request->filled('flotilla_id') && $empresa_id) {
            $flotilla = Flotilla::findOrFail($request->flotilla_id);
            if ($flotilla->empresa_id != $empresa_id) {
                return back()->withErrors(['flotilla_id' => 'La flotilla pertenece a otro corporativo.'])->withInput();
            }
        }

        if ($request->hasFile('icono_file')) {
            if ($vehiculo->icono && str_contains($vehiculo->icono, 'icons_vehiculos/personalizados/')) {
                Storage::disk('public')->delete($vehiculo->icono);
            }
            $rutaIcono = $request->file('icono_file')->store('icons_vehiculos/personalizados', 'public');
        } else {
            $rutaIcono = $validated['icono'] ?? $vehiculo->icono;
        }

        $vehiculo->update(array_merge($request->except('icono_file'), [
            'empresa_id' => $empresa_id,
            'user_id' => $user_id,
            'icono' => $rutaIcono,
            'color_icono' => $request->input('color_icono', $vehiculo->color_icono),
        ]));

        return redirect()->route('vehiculos.index')->with('success', 'Vehículo actualizado correctamente.');
    }

    public function destroy(Vehiculo $vehiculo)
    {
        $this->verificarPropiedadVehiculo($vehiculo);

        if ($vehiculo->icono && str_contains($vehiculo->icono, 'icons_vehiculos/personalizados/')) {
            Storage::disk('public')->delete($vehiculo->icono);
        }

        $vehiculo->delete();

        return redirect()->route('vehiculos.index')->with('success', 'Vehículo eliminado correctamente.');
    }

    public function actualizarCorteRuta(Request $request, Vehiculo $vehiculo)
    {
        $this->verificarPropiedadVehiculo($vehiculo);

        $request->validate([
            'horas_corte_ruta' => 'required|integer|min:1|max:168',
        ]);

        $vehiculo->update([
            'horas_corte_ruta' => $request->horas_corte_ruta,
        ]);

        return redirect()->back()->with('success', 'Periodo de corte de ruta actualizado correctamente.');
    }

    /**
     * Asignar geocercas directamente desde el modal/vista de un vehículo.
     */
    public function asignarZonas(Request $request, Vehiculo $vehiculo)
    {
        $this->verificarPropiedadVehiculo($vehiculo);

        $request->validate([
            'zonas' => 'nullable|array',
            'zonas.*.id' => 'required|exists:zonas,id',
            'zonas.*.tipo_regla' => 'required|in:permitida,restringida,informativa',
            'zonas.*.notificar_entrada' => 'nullable|boolean',
            'zonas.*.notificar_salida' => 'nullable|boolean',
            'zonas.*.permanencia_minima_minutos' => 'nullable|integer|min:1',
            'zonas.*.permanencia_maxima_minutos' => 'nullable|integer|min:1',
        ]);

        $syncData = [];
        if ($request->has('zonas')) {
            foreach ($request->input('zonas') as $z) {
                $syncData[$z['id']] = [
                    'tipo_regla' => $z['tipo_regla'] ?? 'informativa',
                    'notificar_entrada' => filter_var($z['notificar_entrada'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'notificar_salida' => filter_var($z['notificar_salida'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'permanencia_minima_minutos' => $z['permanencia_minima_minutos'] ?? null,
                    'permanencia_maxima_minutos' => $z['permanencia_maxima_minutos'] ?? null,
                ];
            }
        }

        $vehiculo->zonas()->sync($syncData);

        return redirect()->back()->with('success', 'Geocercas y reglas asignadas correctamente al vehículo.');
    }

    private function verificarPropiedadVehiculo(Vehiculo $vehiculo)
    {
        $user = auth()->user();
        if ($user->hasRole('Super Administrador')) return;

        if ($user->hasRole('Cliente Individual')) {
            if ($vehiculo->user_id !== $user->id) abort(403, 'Acceso denegado a este vehículo.');
        } else {
            if ($vehiculo->empresa_id !== $user->empresa_id) abort(403, 'El vehículo pertenece a otra empresa.');
        }
    }
}