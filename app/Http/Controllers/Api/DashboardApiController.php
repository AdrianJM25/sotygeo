<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alerta;
use App\Models\Conductor;
use App\Models\EventoGeocerca;
use App\Models\Vehiculo;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | VEHÍCULOS EN VIVO
    |--------------------------------------------------------------------------
    */

    public function vehiculosEnVivo(Request $request)
    {
        $user = auth()->user();

        $query = Vehiculo::with([
            'dispositivo.ubicaciones' => function ($q) {

                $q->latest('fecha_gps')
                    ->limit(1);
            }
        ]);


        /*
        |--------------------------------------------------------------------------
        | FILTRAR VEHÍCULOS SEGÚN ROL
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Cliente Individual'
            )
        ) {

            $query->where(
                'user_id',
                $user->id
            );

        } elseif (
            $user->hasRole(
                'Conductor'
            )
        ) {

            $vehiculoId =
                $this->vehiculoAsignadoConductor(
                    $user
                );


            if ($vehiculoId) {

                $query->where(
                    'id',
                    $vehiculoId
                );

            } else {

                $query->whereRaw(
                    '1 = 0'
                );
            }

        } elseif (
            !$user->hasRole(
                'Super Administrador'
            )
        ) {

            $query->where(
                'empresa_id',
                $user->empresa_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | OBTENER VEHÍCULOS
        |--------------------------------------------------------------------------
        */

        $vehiculos = $query
            ->orderBy(
                'nombre'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | DETERMINAR ESTADO OPERATIVO
        |--------------------------------------------------------------------------
        |
        | SIN CONEXIÓN:
        | No existe posición GPS o la última posición
        | tiene más de 5 minutos.
        |
        | EN MOVIMIENTO:
        | Reporta durante los últimos 5 minutos
        | y su velocidad es mayor a 3 km/h.
        |
        | DETENIDO:
        | Reporta durante los últimos 5 minutos
        | y su velocidad es de 0 a 3 km/h.
        |
        */

        $vehiculos->each(
            function ($vehiculo) {

                $ultimaUbicacion =
                    $vehiculo
                        ->dispositivo
                        ?->ubicaciones
                        ?->first();


                /*
                |--------------------------------------------------------------------------
                | VALORES PREDETERMINADOS
                |--------------------------------------------------------------------------
                */

                $vehiculo->en_linea =
                    false;

                $vehiculo->estado_operativo =
                    'sin_conexion';

                $vehiculo->velocidad_actual =
                    0;

                $vehiculo->minutos_sin_reporte =
                    null;


                /*
                |--------------------------------------------------------------------------
                | SIN INFORMACIÓN GPS
                |--------------------------------------------------------------------------
                */

                if (
                    !$ultimaUbicacion
                    ||
                    !$ultimaUbicacion->fecha_gps
                ) {

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | TIEMPO DESDE EL ÚLTIMO REPORTE
                |--------------------------------------------------------------------------
                */

                $segundos =
                    now()->timestamp
                    -
                    $ultimaUbicacion
                        ->fecha_gps
                        ->timestamp;


                $segundos =
                    max(
                        0,
                        $segundos
                    );


                $minutos =
                    (int) floor(
                        $segundos / 60
                    );


                $vehiculo->minutos_sin_reporte =
                    $minutos;


                /*
                |--------------------------------------------------------------------------
                | MÁS DE 5 MINUTOS = SIN CONEXIÓN
                |--------------------------------------------------------------------------
                */

                if (
                    $segundos > 300
                ) {

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | ESTÁ EN LÍNEA
                |--------------------------------------------------------------------------
                */

                $vehiculo->en_linea =
                    true;


                $velocidad =
                    (float) (
                        $ultimaUbicacion
                            ->velocidad
                        ??
                        0
                    );


                $vehiculo->velocidad_actual =
                    $velocidad;


                /*
                |--------------------------------------------------------------------------
                | MOVIMIENTO / DETENIDO
                |--------------------------------------------------------------------------
                |
                | Se utiliza un pequeño margen de 3 km/h
                | para evitar interpretar ruido GPS como movimiento.
                |
                */

                if (
                    $velocidad > 3
                ) {

                    $vehiculo->estado_operativo =
                        'movimiento';

                } else {

                    $vehiculo->estado_operativo =
                        'detenido';
                }
            }
        );


        return response()->json(
            $vehiculos
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ZONAS EN VIVO
    |--------------------------------------------------------------------------
    */

    public function zonasEnVivo(Request $request)
    {
        $user = auth()->user();


        $query = Zona::select(
            'id',
            'nombre',
            'color_hex',
            'empresa_id',
            'user_id',
            DB::raw(
                'ST_AsGeoJSON(poligono) as geojson'
            )
        );


        /*
        |--------------------------------------------------------------------------
        | FILTRO SEGÚN ROL
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Cliente Individual'
            )
        ) {

            $query->where(
                'user_id',
                $user->id
            );

        } elseif (
            $user->hasRole(
                'Conductor'
            )
        ) {

            $vehiculoId =
                $this->vehiculoAsignadoConductor(
                    $user
                );


            if ($vehiculoId) {

                $query->whereHas(
                    'vehiculos',
                    function ($q) use ($vehiculoId) {

                        $q->where(
                            'vehiculos.id',
                            $vehiculoId
                        );
                    }
                );

            } else {

                $query->whereRaw(
                    '1 = 0'
                );
            }

        } elseif (
            !$user->hasRole(
                'Super Administrador'
            )
        ) {

            $query->where(
                'empresa_id',
                $user->empresa_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | OBTENER ZONAS
        |--------------------------------------------------------------------------
        */

        $zonas = $query
            ->orderBy(
                'nombre'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | GENERAR GEOJSON
        |--------------------------------------------------------------------------
        */

        $features = $zonas
            ->map(
                function ($zona) {

                    return [

                        'type' =>
                            'Feature',

                        'properties' => [

                            'id' =>
                                $zona->id,

                            'nombre' =>
                                $zona->nombre,

                            'color' =>
                                $zona->color_hex
                                ??
                                '#0056b3',
                        ],

                        'geometry' =>
                            $zona->geojson
                                ?
                                json_decode(
                                    $zona->geojson
                                )
                                :
                                null,
                    ];
                }
            )
            ->filter(
                fn ($feature) =>
                    $feature['geometry']
                    !==
                    null
            )
            ->values();


        return response()->json([

            'type' =>
                'FeatureCollection',

            'features' =>
                $features,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | ALERTAS RECIENTES
    |--------------------------------------------------------------------------
    */

    public function alertasRecientes(Request $request)
    {
        $user = auth()->user();


        $query = Alerta::with(
            'vehiculo'
        )
            ->latest(
                'created_at'
            );


        /*
        |--------------------------------------------------------------------------
        | FILTRO SEGÚN ROL
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Cliente Individual'
            )
        ) {

            $query->whereHas(
                'vehiculo',
                function ($q) use ($user) {

                    $q->where(
                        'user_id',
                        $user->id
                    );
                }
            );

        } elseif (
            $user->hasRole(
                'Conductor'
            )
        ) {

            $vehiculoId =
                $this->vehiculoAsignadoConductor(
                    $user
                );


            if ($vehiculoId) {

                $query->where(
                    'vehiculo_id',
                    $vehiculoId
                );

            } else {

                $query->whereRaw(
                    '1 = 0'
                );
            }

        } elseif (
            !$user->hasRole(
                'Super Administrador'
            )
        ) {

            $query->whereHas(
                'vehiculo',
                function ($q) use ($user) {

                    $q->where(
                        'empresa_id',
                        $user->empresa_id
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ÚLTIMAS 15 ALERTAS
        |--------------------------------------------------------------------------
        */

        $alertas = $query
            ->limit(
                15
            )
            ->get();


        return response()->json(
            $alertas
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VEHÍCULOS ACTUALMENTE DENTRO DE GEOCERCAS
    |--------------------------------------------------------------------------
    */

    public function estadoGeocercas(Request $request)
    {
        $user = auth()->user();


        $query = EventoGeocerca::with([
            'vehiculo',
            'zona',
        ])
            ->whereNull(
                'fecha_salida'
            );


        /*
        |--------------------------------------------------------------------------
        | FILTRO SEGÚN ROL
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Cliente Individual'
            )
        ) {

            $query->whereHas(
                'vehiculo',
                function ($q) use ($user) {

                    $q->where(
                        'user_id',
                        $user->id
                    );
                }
            );

        } elseif (
            $user->hasRole(
                'Conductor'
            )
        ) {

            $vehiculoId =
                $this->vehiculoAsignadoConductor(
                    $user
                );


            if ($vehiculoId) {

                $query->where(
                    'vehiculo_id',
                    $vehiculoId
                );

            } else {

                $query->whereRaw(
                    '1 = 0'
                );
            }

        } elseif (
            !$user->hasRole(
                'Super Administrador'
            )
        ) {

            $query->whereHas(
                'vehiculo',
                function ($q) use ($user) {

                    $q->where(
                        'empresa_id',
                        $user->empresa_id
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CONSTRUIR ESTADO
        |--------------------------------------------------------------------------
        */

        $eventos = $query
            ->get()
            ->filter(
                function ($evento) {

                    return
                        $evento->vehiculo
                        &&
                        $evento->zona;
                }
            )
            ->map(
                function ($evento) {

                    /*
                    |--------------------------------------------------------------------------
                    | REGLA VEHÍCULO - ZONA
                    |--------------------------------------------------------------------------
                    */

                    $regla =
                        $evento
                            ->zona
                            ->vehiculos()
                            ->where(
                                'vehiculo_id',
                                $evento->vehiculo_id
                            )
                            ->first()
                            ?->pivot;


                    /*
                    |--------------------------------------------------------------------------
                    | MINUTOS DENTRO
                    |--------------------------------------------------------------------------
                    */

                    $segundos =
                        now()->timestamp
                        -
                        $evento
                            ->fecha_entrada
                            ->timestamp;


                    $segundos =
                        max(
                            0,
                            $segundos
                        );


                    $minutosDentro =
                        (int) floor(
                            $segundos / 60
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | PERMANENCIA
                    |--------------------------------------------------------------------------
                    */

                    $permanenciaMaxima =
                        $regla
                            ?->permanencia_maxima_minutos;


                    $permanenciaExcedida =
                        $permanenciaMaxima
                        !==
                        null
                        &&
                        $minutosDentro
                        >=
                        (int) $permanenciaMaxima;


                    return [

                        'vehiculo_id' =>
                            $evento->vehiculo_id,

                        'vehiculo_nombre' =>
                            $evento
                                ->vehiculo
                                ->nombre,

                        'zona_id' =>
                            $evento->zona_id,

                        'zona_nombre' =>
                            $evento
                                ->zona
                                ->nombre,

                        'zona_color' =>
                            $evento
                                ->zona
                                ->color_hex
                            ??
                            '#0056b3',

                        'fecha_entrada' =>
                            $evento
                                ->fecha_entrada,

                        'minutos_dentro' =>
                            $minutosDentro,

                        'permanencia_maxima_minutos' =>
                            $permanenciaMaxima,

                        'permanencia_excedida' =>
                            $permanenciaExcedida,

                        'tipo_regla' =>
                            $regla
                                ?->tipo_regla,

                        'alerta_generada' =>
                            (bool)
                            $evento
                                ->alerta_generada,

                        'alerta_permanencia_generada' =>
                            (bool)
                            $evento
                                ->alerta_permanencia_generada,
                    ];
                }
            )
            ->values();


        return response()->json(
            $eventos
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VEHÍCULO ASIGNADO AL CONDUCTOR
    |--------------------------------------------------------------------------
    |
    | Actualmente la cuenta de usuario y el registro
    | de conductor se relacionan por correo electrónico.
    |
    */

    private function vehiculoAsignadoConductor(
        $user
    ): ?int {

        $conductor = Conductor::query()
            ->where(
                'email',
                $user->email
            )
            ->where(
                'activo',
                true
            )
            ->first();


        if (
            !$conductor
            ||
            !$conductor->vehiculo_id
        ) {

            return null;
        }


        return (int)
            $conductor->vehiculo_id;
    }
}