<?php

namespace App\Http\Controllers;

use App\Models\Dispositivo;
use App\Models\Vehiculo;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;

class DispositivoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Iniciar consultas
        $query = Dispositivo::with(['vehiculo', 'empresa', 'usuario'])->latest();
        $vehiculosQuery = Vehiculo::query();

        // 2. Aplicar filtros Multi-tenant por Rol
        if ($user->hasRole('Cliente Individual')) {
            // Cliente particular: Solo ve sus propios GPS y Vehículos
            $query->where('user_id', $user->id);
            $vehiculosQuery->where('user_id', $user->id);
            
            $empresas = collect();
            $clientes = collect();

        } elseif (!$user->hasRole('Super Administrador')) {
            // Operativos de Empresa: Ven los GPS de su corporativo
            $query->where('empresa_id', $user->empresa_id);
            $vehiculosQuery->where('empresa_id', $user->empresa_id);
            
            $empresas = collect([$user->empresa]);
            $clientes = collect();
        } else {
            // SOTyTECH: Ve todo
            $empresas = Empresa::orderBy('nombre')->get();
            $clientes = User::role('Cliente Individual')->get();
        }

        $dispositivos = $query->paginate(10);
        
        // Traemos vehículos que AÚN NO tienen un dispositivo asignado para evitar errores de Unique
        $vehiculos = $vehiculosQuery->whereDoesntHave('dispositivo')->get();

        return view('dispositivos.index', compact('dispositivos', 'vehiculos', 'empresas', 'clientes'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'imei' => 'required|string|max:20|unique:dispositivos,imei',
            'numero_sim' => 'nullable|string|max:20',
            'modelo' => 'nullable|string|max:255',
            'capacidad_bateria_mah' => 'nullable|integer|min:0',
            'vehiculo_id' => 'nullable|exists:vehiculos,id|unique:dispositivos,vehiculo_id',
            'empresa_id' => $user->hasRole('Super Administrador') ? 'nullable|exists:empresas,id' : 'nullable',
            'user_id' => $user->hasRole('Super Administrador') ? 'nullable|exists:users,id' : 'nullable',
        ]);

        // Lógica de Doble Propietario
        $empresa_id = null;
        $user_id = null;

        if ($user->hasRole('Super Administrador')) {
            $empresa_id = $request->empresa_id;
            $user_id = $request->user_id;
        } elseif ($user->hasRole('Cliente Individual')) {
            $user_id = $user->id;
        } else {
            $empresa_id = $user->empresa_id;
        }

        // Validar que el vehículo seleccionado pertenezca al mismo dueño del GPS
        if ($request->filled('vehiculo_id')) {
            $this->validarPropietarioVehiculo($request->vehiculo_id, $empresa_id, $user_id);
        }

        Dispositivo::create(array_merge($request->all(), [
            'empresa_id' => $empresa_id,
            'user_id' => $user_id,
            'modo_reposo' => $request->has('modo_reposo'),
        ]));

        return redirect()->route('dispositivos.index')->with('success', 'Dispositivo registrado correctamente.');
    }

    public function update(Request $request, Dispositivo $dispositivo)
    {
        $this->verificarAccesoDispositivo($dispositivo);
        $user = auth()->user();

        $request->validate([
            'imei' => 'required|string|max:20|unique:dispositivos,imei,' . $dispositivo->id,
            'numero_sim' => 'nullable|string|max:20',
            'modelo' => 'nullable|string|max:255',
            'capacidad_bateria_mah' => 'nullable|integer|min:0',
            'vehiculo_id' => 'nullable|exists:vehiculos,id|unique:dispositivos,vehiculo_id,' . $dispositivo->id,
            'empresa_id' => $user->hasRole('Super Administrador') ? 'nullable|exists:empresas,id' : 'nullable',
            'user_id' => $user->hasRole('Super Administrador') ? 'nullable|exists:users,id' : 'nullable',
        ]);

        $empresa_id = $user->hasRole('Super Administrador') ? $request->empresa_id : $dispositivo->empresa_id;
        $user_id = $user->hasRole('Super Administrador') ? $request->user_id : $dispositivo->user_id;

        if ($request->filled('vehiculo_id') && $request->vehiculo_id != $dispositivo->vehiculo_id) {
            $this->validarPropietarioVehiculo($request->vehiculo_id, $empresa_id, $user_id);
        }

        $dispositivo->update(array_merge($request->all(), [
            'empresa_id' => $empresa_id,
            'user_id' => $user_id,
            'modo_reposo' => $request->has('modo_reposo'),
        ]));

        return redirect()->route('dispositivos.index')->with('success', 'Dispositivo actualizado correctamente.');
    }

    public function destroy(Dispositivo $dispositivo)
    {
        $this->verificarAccesoDispositivo($dispositivo);
        
        $dispositivo->delete();
        return redirect()->route('dispositivos.index')->with('success', 'Dispositivo eliminado correctamente.');
    }

    /**
     * Helper: Verifica que el usuario tenga permiso para editar o borrar ESTE dispositivo.
     */
    private function verificarAccesoDispositivo(Dispositivo $dispositivo)
    {
        $user = auth()->user();
        if ($user->hasRole('Super Administrador')) return;

        if ($user->hasRole('Cliente Individual')) {
            if ($dispositivo->user_id !== $user->id) abort(403, 'Acceso denegado a este GPS.');
        } else {
            if ($dispositivo->empresa_id !== $user->empresa_id) abort(403, 'El GPS pertenece a otra empresa.');
        }
    }

    /**
     * Helper: Al asignar un GPS a un Vehículo, verifica que ambos sean del mismo dueño.
     */
    private function validarPropietarioVehiculo($vehiculo_id, $empresa_id, $user_id)
    {
        $vehiculo = Vehiculo::findOrFail($vehiculo_id);
        
        if ($empresa_id && $vehiculo->empresa_id != $empresa_id) {
            abort(403, 'No puedes asignar un GPS a un vehículo que pertenece a otra empresa.');
        }
        if ($user_id && $vehiculo->user_id != $user_id) {
            abort(403, 'No puedes asignar un GPS a un vehículo que no te pertenece.');
        }
    }
}