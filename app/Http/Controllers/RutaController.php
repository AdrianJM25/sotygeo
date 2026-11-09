<?php

namespace App\Http\Controllers;

use App\Models\Conductor;
use App\Models\Ubicacion;
use App\Models\Vehiculo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RutaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO DE VEHÍCULOS PARA HISTORIAL
    |--------------------------------------------------------------------------
    |
    | Permite que los cinco perfiles autorizados accedan al historial
    | sin necesidad de tener permiso para administrar vehículos.
    |
    */

    public function index()
    {
        $user = auth()->user();

        $query = Vehiculo::query();


        /*
        |--------------------------------------------------------------------------
        | SUPER ADMINISTRADOR
        |--------------------------------------------------------------------------
        |
        | Puede consultar todos los vehículos.
        |
        */

        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {

            // Sin filtro adicional.

        }


        /*
        |--------------------------------------------------------------------------
        | CLIENTE INDIVIDUAL
        |--------------------------------------------------------------------------
        |
        | Solo puede consultar sus propios vehículos.
        |
        */

        elseif (
            $user->hasRole(
                'Cliente Individual'
            )
        ) {

            $query->where(
                'user_id',
                $user->id
            );

        }


        /*
        |--------------------------------------------------------------------------
        | CONDUCTOR
        |--------------------------------------------------------------------------
        |
        | Solo puede consultar el vehículo que tenga asignado.
        |
        | Actualmente relacionamos la cuenta de usuario con el registro
        | del conductor mediante el correo electrónico.
        |
        */

        elseif (
            $user->hasRole(
                'Conductor'
            )
        ) {

            $conductor = Conductor::where(
                'email',
                $user->email
            )
                ->where(
                    'activo',
                    true
                )
                ->first();


            if (
                $conductor
                &&
                $conductor->vehiculo_id
            ) {

                $query->where(
                    'id',
                    $conductor->vehiculo_id
                );

            } else {

                /*
                |--------------------------------------------------------------------------
                | SIN VEHÍCULO ASIGNADO
                |--------------------------------------------------------------------------
                |
                | No mostramos vehículos de otros usuarios o de toda la empresa.
                |
                */

                $query->whereRaw(
                    '1 = 0'
                );
            }

        }


        /*
        |--------------------------------------------------------------------------
        | ADMINISTRADOR DE EMPRESA / GESTOR DE FLOTILLA
        |--------------------------------------------------------------------------
        |
        | Consultan los vehículos pertenecientes a su empresa.
        |
        */

        else {

            $query->where(
                'empresa_id',
                $user->empresa_id
            );
        }


        $vehiculos = $query
            ->orderBy(
                'nombre'
            )
            ->get();


        return view(
            'rutas.index',
            compact(
                'vehiculos'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | HISTORIAL DE RUTA
    |--------------------------------------------------------------------------
    */

    public function historial(
        Request $request,
        Vehiculo $vehiculo
    ) {

        /*
        |--------------------------------------------------------------------------
        | VERIFICAR ACCESO
        |--------------------------------------------------------------------------
        */

        $this->verificarPropiedadVehiculo(
            $vehiculo
        );


        /*
        |--------------------------------------------------------------------------
        | VALIDACIÓN
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'fecha' => [
                'nullable',
                'date_format:Y-m-d',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | ZONA HORARIA
        |--------------------------------------------------------------------------
        */

        $zonaLocal = config(
            'app.timezone',
            'America/Mexico_City'
        );


        /*
        |--------------------------------------------------------------------------
        | FECHA CONSULTADA
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'fecha'
            )
        ) {

            $fechaConsulta = Carbon::createFromFormat(
                'Y-m-d',
                $request->fecha,
                $zonaLocal
            );

        } else {

            $fechaConsulta = Carbon::now(
                $zonaLocal
            );
        }


        /*
        |--------------------------------------------------------------------------
        | INICIO Y FIN DEL DÍA
        |--------------------------------------------------------------------------
        */

        $inicio = $fechaConsulta
            ->copy()
            ->startOfDay();


        $fin = $fechaConsulta
            ->copy()
            ->endOfDay();


        /*
        |--------------------------------------------------------------------------
        | CONSULTAR UBICACIONES DEL VEHÍCULO
        |--------------------------------------------------------------------------
        */

        $ubicaciones = Ubicacion::query()
            ->where(
                'vehiculo_id',
                $vehiculo->id
            )
            ->whereNotNull(
                'latitud'
            )
            ->whereNotNull(
                'longitud'
            )
            ->whereBetween(
                'fecha_gps',
                [
                    $inicio,
                    $fin,
                ]
            )
            ->orderBy(
                'fecha_gps',
                'asc'
            )
            ->orderBy(
                'id',
                'asc'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | CONSTRUIR RUTA AJUSTADA A CALLES
        |--------------------------------------------------------------------------
        */

        [
            $rutaAjustada,
            $modoRuta
        ] = $this->construirRuta(
            $ubicaciones
        );


        /*
        |--------------------------------------------------------------------------
        | MOSTRAR REPRODUCTOR
        |--------------------------------------------------------------------------
        */

        return view(
            'vehiculos.ruta',
            compact(
                'vehiculo',
                'ubicaciones',
                'fechaConsulta',
                'rutaAjustada',
                'modoRuta'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CONSTRUIR RUTA
    |--------------------------------------------------------------------------
    |
    | 1. Si tenemos suficientes puntos intentamos Map Matching.
    | 2. Si falla, calculamos ruta por calles pasando por los puntos.
    | 3. Si OSRM no responde, usamos los puntos GPS originales.
    |
    */

    private function construirRuta(
        Collection $ubicaciones
    ): array {

        /*
        |--------------------------------------------------------------------------
        | RUTA ORIGINAL
        |--------------------------------------------------------------------------
        */

        $rutaOriginal = $ubicaciones
            ->map(function ($ubicacion) {

                return [
                    (float) $ubicacion->latitud,
                    (float) $ubicacion->longitud,
                ];

            })
            ->values()
            ->all();


        /*
        |--------------------------------------------------------------------------
        | MENOS DE DOS PUNTOS
        |--------------------------------------------------------------------------
        */

        if (
            $ubicaciones->count() < 2
        ) {

            return [
                $rutaOriginal,
                'gps',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | REDUCIR PUNTOS
        |--------------------------------------------------------------------------
        |
        | Evitamos mandar cientos o miles
        | de coordenadas en una sola petición.
        |
        */

        $puntos = $this->muestrearUbicaciones(
            $ubicaciones,
            80
        );


        /*
        |--------------------------------------------------------------------------
        | COORDENADAS PARA OSRM
        |--------------------------------------------------------------------------
        |
        | OSRM utiliza:
        |
        | longitud,latitud
        |
        */

        $coordenadas = collect(
            $puntos
        )
            ->map(function ($ubicacion) {

                return sprintf(
                    '%.7F,%.7F',
                    (float) $ubicacion->longitud,
                    (float) $ubicacion->latitud
                );

            })
            ->implode(
                ';'
            );


        /*
        |--------------------------------------------------------------------------
        | SERVIDOR OSRM
        |--------------------------------------------------------------------------
        */

        $servidor =
            'https://router.project-osrm.org';


        /*
        |--------------------------------------------------------------------------
        | INTENTAR MAP MATCHING
        |--------------------------------------------------------------------------
        */

        if (
            count(
                $puntos
            ) >= 3
        ) {

            try {

                $respuesta = Http::acceptJson()
                    ->timeout(
                        10
                    )
                    ->get(
                        $servidor
                        .
                        '/match/v1/driving/'
                        .
                        $coordenadas,
                        [
                            'geometries' =>
                                'geojson',

                            'overview' =>
                                'full',

                            'steps' =>
                                'false',

                            'tidy' =>
                                'true',

                            'gaps' =>
                                'ignore',
                        ]
                    );


                if (
                    $respuesta->successful()
                ) {

                    $datos =
                        $respuesta->json();


                    if (
                        ($datos['code'] ?? null)
                        ===
                        'Ok'
                        &&
                        !empty(
                            $datos['matchings']
                        )
                    ) {

                        $ruta =
                            [];


                        foreach (
                            $datos['matchings']
                            as $matching
                        ) {

                            $coordenadasMatching =
                                $matching[
                                    'geometry'
                                ][
                                    'coordinates'
                                ]
                                ??
                                [];


                            foreach (
                                $coordenadasMatching
                                as $coordenada
                            ) {

                                if (
                                    !isset(
                                        $coordenada[0],
                                        $coordenada[1]
                                    )
                                ) {

                                    continue;
                                }


                                /*
                                |--------------------------------------------------------------------------
                                | GeoJSON:
                                | [longitud, latitud]
                                |
                                | Leaflet:
                                | [latitud, longitud]
                                |--------------------------------------------------------------------------
                                */

                                $ruta[] = [
                                    (float) $coordenada[1],
                                    (float) $coordenada[0],
                                ];
                            }
                        }


                        if (
                            count(
                                $ruta
                            ) >= 2
                        ) {

                            return [
                                $ruta,
                                'match',
                            ];
                        }
                    }
                }

            } catch (\Throwable $e) {

                Log::warning(
                    'OSRM Map Matching no disponible.',
                    [
                        'mensaje' =>
                            $e->getMessage(),
                    ]
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | RESPALDO: RUTA POR CALLES
        |--------------------------------------------------------------------------
        |
        | Con pocos puntos no sabemos exactamente
        | qué camino tomó el vehículo.
        |
        | Este modo genera una trayectoria vial
        | razonable pasando por los puntos GPS.
        |
        */

        try {

            $respuesta = Http::acceptJson()
                ->timeout(
                    10
                )
                ->get(
                    $servidor
                    .
                    '/route/v1/driving/'
                    .
                    $coordenadas,
                    [
                        'geometries' =>
                            'geojson',

                        'overview' =>
                            'full',

                        'steps' =>
                            'false',

                        'continue_straight' =>
                            'true',
                    ]
                );


            if (
                $respuesta->successful()
            ) {

                $datos =
                    $respuesta->json();


                if (
                    ($datos['code'] ?? null)
                    ===
                    'Ok'
                    &&
                    !empty(
                        $datos[
                            'routes'
                        ][0][
                            'geometry'
                        ][
                            'coordinates'
                        ]
                    )
                ) {

                    $ruta =
                        collect(
                            $datos[
                                'routes'
                            ][0][
                                'geometry'
                            ][
                                'coordinates'
                            ]
                        )
                            ->map(
                                function (
                                    $coordenada
                                ) {

                                    return [
                                        (float) $coordenada[1],
                                        (float) $coordenada[0],
                                    ];

                                }
                            )
                            ->values()
                            ->all();


                    if (
                        count(
                            $ruta
                        ) >= 2
                    ) {

                        return [
                            $ruta,
                            'route',
                        ];
                    }
                }
            }

        } catch (\Throwable $e) {

            Log::warning(
                'OSRM Route no disponible.',
                [
                    'mensaje' =>
                        $e->getMessage(),
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ÚLTIMO RESPALDO
        |--------------------------------------------------------------------------
        */

        return [
            $rutaOriginal,
            'gps',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | REDUCIR CANTIDAD DE PUNTOS
    |--------------------------------------------------------------------------
    |
    | Conservamos el inicio, fin y puntos intermedios
    | distribuidos uniformemente.
    |
    */

    private function muestrearUbicaciones(
        Collection $ubicaciones,
        int $maximo
    ): array {

        $cantidad =
            $ubicaciones->count();


        /*
        |--------------------------------------------------------------------------
        | NO NECESITA REDUCCIÓN
        |--------------------------------------------------------------------------
        */

        if (
            $cantidad <= $maximo
        ) {

            return $ubicaciones
                ->values()
                ->all();
        }


        /*
        |--------------------------------------------------------------------------
        | MUESTREO UNIFORME
        |--------------------------------------------------------------------------
        */

        $resultado =
            [];


        for (
            $i = 0;
            $i < $maximo;
            $i++
        ) {

            $indice = (int) round(
                $i
                *
                ($cantidad - 1)
                /
                ($maximo - 1)
            );


            $resultado[
                $indice
            ] =
                $ubicaciones[
                    $indice
                ];
        }


        ksort(
            $resultado
        );


        return array_values(
            $resultado
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFICAR ACCESO AL VEHÍCULO
    |--------------------------------------------------------------------------
    |
    | Se aplica también cuando alguien intenta escribir directamente:
    |
    | /vehiculos/ID/ruta
    |
    | De esta manera no basta con ocultar vehículos en la interfaz.
    |
    */

    private function verificarPropiedadVehiculo(
        Vehiculo $vehiculo
    ): void {

        $user =
            auth()->user();


        /*
        |--------------------------------------------------------------------------
        | SUPER ADMINISTRADOR
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CLIENTE INDIVIDUAL
        |--------------------------------------------------------------------------
        |
        | Solo puede consultar vehículos asociados directamente a su cuenta.
        |
        */

        if (
            $user->hasRole(
                'Cliente Individual'
            )
        ) {

            if (
                (int) $vehiculo->user_id
                !==
                (int) $user->id
            ) {

                abort(
                    403,
                    'Acceso denegado a las rutas de este vehículo.'
                );
            }


            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CONDUCTOR
        |--------------------------------------------------------------------------
        |
        | Solo puede consultar el historial del vehículo
        | que tenga asignado actualmente.
        |
        */

        if (
            $user->hasRole(
                'Conductor'
            )
        ) {

            $conductor = Conductor::where(
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
                ||
                (int) $conductor->vehiculo_id
                !==
                (int) $vehiculo->id
            ) {

                abort(
                    403,
                    'Solo puedes consultar las rutas del vehículo que tienes asignado.'
                );
            }


            return;
        }


        /*
        |--------------------------------------------------------------------------
        | ADMINISTRADOR DE EMPRESA / GESTOR DE FLOTILLA
        |--------------------------------------------------------------------------
        |
        | Solo pueden consultar vehículos de su empresa.
        |
        */

        if (
            (int) $vehiculo->empresa_id
            !==
            (int) $user->empresa_id
        ) {

            abort(
                403,
                'El vehículo pertenece a otra empresa.'
            );
        }
    }
}