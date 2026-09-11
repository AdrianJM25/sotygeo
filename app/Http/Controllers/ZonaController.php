<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use App\Models\Vehiculo;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ZonaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Aplicar scoping Multi-Tenant
        $query = Zona::select(
            'id', 'empresa_id', 'user_id', 'nombre', 'color_hex', 
            DB::raw('ST_AsGeoJSON(poligono) as geojson'),
            'created_at'
        )->with(['vehiculos']);

        if ($user->hasRole('Cliente Individual')) {
            $query->where('user_id', $user->id);
            $vehiculos = Vehiculo::where('user_id', $user->id)->get();
            $empresas = collect();
        } elseif (!$user->hasRole('Super Administrador')) {
            $query->where('empresa_id', $user->empresa_id);
            $vehiculos = Vehiculo::where('empresa_id', $user->empresa_id)->get();
            $empresas = collect([$user->empresa]);
        } else {
            $vehiculos = Vehiculo::all();
            $empresas = Empresa::orderBy('nombre')->get();
        }

        $zonas = $query->latest()->get();

        return view('zonas.index', compact('zonas', 'vehiculos', 'empresas'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nombre' => 'required|string|max:255',
            'color_hex' => 'required|string|max:7',
            'coordenadas' => 'required|string', // Array JSON desde el mapa [ {lat, lng}, ... ]
            'empresa_id' => $user->hasRole('Super Administrador') ? 'nullable|exists:empresas,id' : 'nullable',
            'user_id' => $user->hasRole('Super Administrador') ? 'nullable|exists:users,id' : 'nullable',
            'vehiculos' => 'nullable|array',
            'vehiculos.*.id' => 'required|exists:vehiculos,id',
            'vehiculos.*.tipo_regla' => 'required|in:permitida,restringida,informativa',
            'vehiculos.*.notificar_entrada' => 'nullable|boolean',
            'vehiculos.*.notificar_salida' => 'nullable|boolean',
            'vehiculos.*.permanencia_minima_minutos' => 'nullable|integer|min:1',
            'vehiculos.*.permanencia_maxima_minutos' => 'nullable|integer|min:1',
        ]);

        // 1. Determinar pertenencia Multi-Tenant
        $empresa_id = $user->hasRole('Super Administrador') ? $request->empresa_id : ($user->hasRole('Cliente Individual') ? null : $user->empresa_id);
        $user_id = $user->hasRole('Super Administrador') ? $request->user_id : ($user->hasRole('Cliente Individual') ? $user->id : null);

        // 2. Formatear Polígono Spatial (Lng Lat)
        $stringPoligono = $this->parsearCoordenadasAPolígono($request->coordenadas);
        if (!$stringPoligono) {
            return back()->withErrors(['coordenadas' => 'Debes dibujar un polígono válido con al menos 3 puntos.'])->withInput();
        }

        // 3. Crear Geocerca
        $zona = Zona::create([
            'empresa_id' => $empresa_id,
            'user_id' => $user_id,
            'nombre' => $request->nombre,
            'color_hex' => $request->color_hex,
            'poligono' => DB::raw("ST_GeomFromText('$stringPoligono', 4326)")
        ]);

        // 4. Asignar Vehículos con reglas pivote (si se seleccionaron)
        if ($request->has('vehiculos')) {
            $this->sincronizarVehiculos($zona, $request->input('vehiculos'));
        }

        return redirect()->route('zonas.index')->with('success', 'Geocerca creada correctamente.');
    }

    public function update(Request $request, Zona $zona)
    {
        $this->verificarPropiedadZona($zona);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'color_hex' => 'required|string|max:7',
            'coordenadas' => 'nullable|string', // Opcional si solo edita nombre/vehículos
            'vehiculos' => 'nullable|array',
            'vehiculos.*.id' => 'required|exists:vehiculos,id',
            'vehiculos.*.tipo_regla' => 'required|in:permitida,restringida,informativa',
            'vehiculos.*.notificar_entrada' => 'nullable|boolean',
            'vehiculos.*.notificar_salida' => 'nullable|boolean',
            'vehiculos.*.permanencia_minima_minutos' => 'nullable|integer|min:1',
            'vehiculos.*.permanencia_maxima_minutos' => 'nullable|integer|min:1',
        ]);

        $datosActualizar = [
            'nombre' => $request->nombre,
            'color_hex' => $request->color_hex,
        ];

        // Si se volvió a dibujar la geocerca
        if ($request->filled('coordenadas')) {
            $stringPoligono = $this->parsearCoordenadasAPolígono($request->coordenadas);
            if ($stringPoligono) {
                $datosActualizar['poligono'] = DB::raw("ST_GeomFromText('$stringPoligono', 4326)");
            }
        }

        $zona->update($datosActualizar);

        // Sincronizar vehículos vinculados
        $this->sincronizarVehiculos($zona, $request->input('vehiculos', []));

        return redirect()->route('zonas.index')->with('success', 'Geocerca actualizada correctamente.');
    }

    public function destroy(Zona $zona)
    {
        $this->verificarPropiedadZona($zona);
        $zona->delete();

        return redirect()->route('zonas.index')->with('success', 'Geocerca eliminada correctamente.');
    }

    /**
     * Auxiliar para formatear el array de coordenadas a WKT POLYGON
     */
    private function parsearCoordenadasAPolígono(string $jsonCoordenadas): ?string
    {
        $coordenadas = json_decode($jsonCoordenadas, true);

        if (!$coordenadas || count($coordenadas) < 3) {
            return null;
        }

        $puntos = [];
        foreach ($coordenadas as $coord) {
            $puntos[] = $coord['lng'] . ' ' . $coord['lat'];
        }

        // El polígono debe cerrarse (primer punto igual al último)
        if ($puntos[0] !== end($puntos)) {
            $puntos[] = $puntos[0];
        }

        return 'POLYGON((' . implode(',', $puntos) . '))';
    }

    /**
     * Sincroniza los vehículos en la tabla pivote vehiculo_zona
     */
    private function sincronizarVehiculos(Zona $zona, array $vehiculosInput)
    {
        $syncData = [];

        foreach ($vehiculosInput as $v) {
            $syncData[$v['id']] = [
                'tipo_regla' => $v['tipo_regla'] ?? 'informativa',
                'notificar_entrada' => filter_var($v['notificar_entrada'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'notificar_salida' => filter_var($v['notificar_salida'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'permanencia_minima_minutos' => $v['permanencia_minima_minutos'] ?? null,
                'permanencia_maxima_minutos' => $v['permanencia_maxima_minutos'] ?? null,
            ];
        }

        $zona->vehiculos()->sync($syncData);
    }

    /**
     * Validar permisos Multi-Tenant
     */
    private function verificarPropiedadZona(Zona $zona)
    {
        $user = auth()->user();
        if ($user->hasRole('Super Administrador')) return;

        if ($user->hasRole('Cliente Individual')) {
            if ($zona->user_id !== $user->id) abort(403, 'Acceso denegado a esta geocerca.');
        } else {
            if ($zona->empresa_id !== $user->empresa_id) abort(403, 'La geocerca pertenece a otra empresa.');
        }
    }
}