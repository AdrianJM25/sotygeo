<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Flotilla;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = Vehiculo::with(['empresa', 'usuario', 'flotilla'])->latest();

        if ($user->hasRole('Cliente Individual')) {
            $query->where('user_id', $user->id);
            $flotillas = collect();
            $empresas = collect();
            $clientes = collect();
        } elseif (!$user->hasRole('Super Administrador')) {
            $query->where('empresa_id', $user->empresa_id);
            $flotillas = Flotilla::where('empresa_id', $user->empresa_id)->get();
            $empresas = collect([$user->empresa]);
            $clientes = collect();
        } else {
            $flotillas = Flotilla::all();
            $empresas = Empresa::orderBy('nombre')->get();
            $clientes = User::role('Cliente Individual')->get();
        }

        $vehiculos = $query->paginate(10);

        return view('vehiculos.index', compact('vehiculos', 'flotillas', 'empresas', 'clientes'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
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
            'icono' => 'nullable|in:' . implode(',', array_keys(Vehiculo::ICONOS)),
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

        Vehiculo::create(array_merge($request->all(), [
            'empresa_id' => $empresa_id,
            'user_id' => $user_id,
            'icono' => $request->input('icono', 'sedan'),
            'color_icono' => $request->input('color_icono', '#111827'),
        ]));

        return redirect()->route('vehiculos.index')->with('success', 'Vehículo registrado correctamente.');
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $this->verificarPropiedadVehiculo($vehiculo);
        $user = auth()->user();

        $request->validate([
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
            'icono' => 'nullable|in:' . implode(',', array_keys(Vehiculo::ICONOS)),
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

        $vehiculo->update(array_merge($request->all(), [
            'empresa_id' => $empresa_id,
            'user_id' => $user_id,
            'icono' => $request->input('icono', $vehiculo->icono),
            'color_icono' => $request->input('color_icono', $vehiculo->color_icono),
        ]));

        return redirect()->route('vehiculos.index')->with('success', 'Vehículo actualizado correctamente.');
    }

    public function destroy(Vehiculo $vehiculo)
    {
        $this->verificarPropiedadVehiculo($vehiculo);
        $vehiculo->delete();
        
        return redirect()->route('vehiculos.index')->with('success', 'Vehículo eliminado correctamente.');
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