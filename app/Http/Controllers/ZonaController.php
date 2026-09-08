<?php

namespace App\Http\Controllers;

use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ZonaController extends Controller
{
    public function index()
    {
        // Traemos las zonas usando ST_AsGeoJSON para que el mapa las pueda dibujar fácilmente
        $zonas = Zona::select(
            'id', 'nombre', 'color_hex', 
            DB::raw('ST_AsGeoJSON(poligono) as geojson')
        )->where('user_id', Auth::id())->get();

        return view('zonas.index', compact('zonas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'color_hex' => 'required|string|max:7',
            'coordenadas' => 'required|string' // Llegará como JSON desde el frontend
        ]);

        // 1. Decodificar las coordenadas enviadas por el mapa
        $coordenadas_array = json_decode($request->coordenadas, true);
        
        if (!$coordenadas_array || count($coordenadas_array) < 3) {
            return back()->withErrors(['coordenadas' => 'Debes dibujar un polígono de al menos 3 puntos.']);
        }

        // 2. Formatear a sintaxis Lng Lat de PostGIS
        $puntos = [];
        foreach ($coordenadas_array as $coord) {
            $puntos[] = $coord['lng'] . ' ' . $coord['lat'];
        }

        // 3. PostGIS exige que un polígono esté cerrado (el último punto debe ser igual al primero)
        if ($puntos[0] !== end($puntos)) {
            $puntos[] = $puntos[0];
        }

        $stringPoligono = 'POLYGON((' . implode(',', $puntos) . '))';

        // 4. Guardar usando DB::raw
        Zona::create([
            'user_id' => Auth::id(),
            'nombre' => $request->nombre,
            'color_hex' => $request->color_hex,
            'poligono' => DB::raw("ST_GeomFromText('$stringPoligono', 4326)")
        ]);

        return redirect()->route('zonas.index')->with('success', 'Zona creada correctamente.');
    }

    public function destroy($id)
    {
        $zona = Zona::where('user_id', Auth::id())->findOrFail($id);
        $zona->delete();
        
        return redirect()->route('zonas.index')->with('success', 'Zona eliminada.');
    }
}