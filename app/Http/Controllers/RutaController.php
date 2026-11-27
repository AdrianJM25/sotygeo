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
    private const OSRM_SERVIDOR =
        'https://router.project-osrm.org';

    private const MAXIMO_PUNTOS_OSRM = 80;

    private const MINUTOS_SIN_SENAL = 10;

    private const VELOCIDAD_MAXIMA_KMH = 160;


    /*
    |--------------------------------------------------------------------------
    | LISTADO DE VEHÍCULOS PARA HISTORIAL
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = auth()->user();

        $query = Vehiculo::query();


        if (
            $user->hasRole('Super Administrador')
        ) {

            // Puede consultar todos los vehículos.

        }

        elseif (
            $user->hasRole('Cliente Individual')
        ) {

            $query->where(
                'user_id',
                $user->id
            );

        }

        elseif (
            $user->hasRole('Conductor')
        ) {

            $conductor = Conductor::where(
                'email',
                $user->email
            )
                ->where('activo', true)
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

                $query->whereRaw('1 = 0');
            }

        }

        else {

            $query->where(
                'empresa_id',
                $user->empresa_id
            );
        }


        $vehiculos = $query
            ->orderBy('nombre')
            ->get();


        return view(
            'rutas.index',
            compact('vehiculos')
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

        $this->verificarPropiedadVehiculo(
            $vehiculo
        );


        $request->validate([
            'fecha' => [
                'nullable',
                'date_format:Y-m-d',
            ],
        ]);


        $zonaLocal = config(
            'app.timezone',
            'America/Mexico_City'
        );


        if (
            $request->filled('fecha')
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


        $inicio = $fechaConsulta
            ->copy()
            ->startOfDay();


        $fin = $fechaConsulta
            ->copy()
            ->endOfDay();


        /*
        |--------------------------------------------------------------------------
        | CONSULTAR UBICACIONES GPS
        |--------------------------------------------------------------------------
        |
        | Se descartan coordenadas nulas o fuera de rango.
        |
        */

        $ubicaciones = Ubicacion::query()

            ->where(
                'vehiculo_id',
                $vehiculo->id
            )

            ->whereNotNull('latitud')

            ->whereNotNull('longitud')

            ->whereNotNull('fecha_gps')

            ->whereBetween(
                'latitud',
                [-90, 90]
            )

            ->whereBetween(
                'longitud',
                [-180, 180]
            )

            ->whereBetween(
                'fecha_gps',
                [
                    $inicio,
                    $fin,
                ]
            )

            ->orderBy('fecha_gps', 'asc')

            ->orderBy('id', 'asc')

            ->get();


        /*
        |--------------------------------------------------------------------------
        | CONSTRUIR RECORRIDOS
        |--------------------------------------------------------------------------
        */

        [
            $rutaAjustada,
            $segmentosRuta,
            $puntosReproduccion,
            $segmentosReproduccion,
            $modoRuta
        ] = $this->construirRuta(
            $ubicaciones
        );


        return view(
            'vehiculos.ruta',
            compact(
                'vehiculo',
                'ubicaciones',
                'fechaConsulta',
                'rutaAjustada',
                'segmentosRuta',
                'puntosReproduccion',
                'segmentosReproduccion',
                'modoRuta'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CONSTRUIR RECORRIDO COMPLETO
    |--------------------------------------------------------------------------
    |
    | 1. Separar recorridos independientes.
    | 2. Reducir coordenadas para OSRM.
    | 3. Intentar Map Matching.
    | 4. Utilizar Route como alternativa.
    | 5. Conservar GPS original como último recurso.
    |
    */

    private function construirRuta(
        Collection $ubicaciones
    ): array {

        $segmentosGps = $this->segmentarUbicaciones(
            $ubicaciones
        );


        $segmentosRuta = [];

        $segmentosReproduccion = [];

        $modos = [];

        $numeroSegmento = 0;


        foreach (
            $segmentosGps as $segmentoGps
        ) {

            $numeroSegmento++;


            $muestras = $this->muestrearUbicaciones(
                $segmentoGps,
                self::MAXIMO_PUNTOS_OSRM
            );


            if (empty($muestras)) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | PRIMER INTENTO: MAP MATCHING
            |--------------------------------------------------------------------------
            */

            $resultadoMatch = $this->obtenerMapMatching(
                $muestras,
                $numeroSegmento
            );


            if ($resultadoMatch !== null) {

                foreach (
                    $resultadoMatch['segmentosRuta']
                    as $indice => $geometria
                ) {

                    $segmentosRuta[] = $geometria;

                    $segmentosReproduccion[] =
                        $resultadoMatch['segmentosReproduccion'][$indice]
                        ?? [];
                }


                $modos[] = 'match';

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | SEGUNDO INTENTO: RUTA POR CALLES
            |--------------------------------------------------------------------------
            */

            $rutaEstimada = $this->obtenerRutaPorCalles(
                $muestras
            );


            if ($rutaEstimada !== null) {

                $segmentosRuta[] = $rutaEstimada;

                $segmentosReproduccion[] =
                    $this->convertirMuestrasAReproduccion(
                        $muestras,
                        $numeroSegmento
                    );

                $modos[] = 'route';

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | ÚLTIMO RECURSO: GPS ORIGINAL
            |--------------------------------------------------------------------------
            */

            $segmentosRuta[] =
                $this->convertirUbicacionesAGeometria(
                    $segmentoGps
                );


            $segmentosReproduccion[] =
                $this->convertirMuestrasAReproduccion(
                    $muestras,
                    $numeroSegmento
                );


            $modos[] = 'gps';
        }


        /*
        |--------------------------------------------------------------------------
        | CONSERVAR COMPATIBILIDAD CON LA VISTA ACTUAL
        |--------------------------------------------------------------------------
        */

        $rutaAjustada = [];

        foreach ($segmentosRuta as $segmento) {

            foreach ($segmento as $punto) {

                $rutaAjustada[] = $punto;
            }
        }


        $puntosReproduccion = [];

        foreach (
            $segmentosReproduccion as $segmento
        ) {

            foreach ($segmento as $punto) {

                $puntosReproduccion[] = $punto;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | DETERMINAR MODO GENERAL
        |--------------------------------------------------------------------------
        */

        if (
            in_array('match', $modos, true)
        ) {

            $modoRuta = 'match';

        }

        elseif (
            in_array('route', $modos, true)
        ) {

            $modoRuta = 'route';

        }

        else {

            $modoRuta = 'gps';
        }


        return [
            $rutaAjustada,
            $segmentosRuta,
            $puntosReproduccion,
            $segmentosReproduccion,
            $modoRuta,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | SEPARAR RECORRIDOS
    |--------------------------------------------------------------------------
    |
    | Se inicia otro segmento cuando:
    |
    | - Existe una interrupción GPS superior a 10 minutos.
    | - Se detecta una velocidad calculada superior a 160 km/h
    |   durante un intervalo de al menos 30 segundos.
    |
    | Esto evita conectar posiciones que probablemente
    | pertenecen a recorridos independientes.
    |
    */

    private function segmentarUbicaciones(
        Collection $ubicaciones
    ): array {

        $ubicaciones = $ubicaciones->values();


        if ($ubicaciones->isEmpty()) {
            return [];
        }


        $segmentos = [];

        $actual = collect([
            $ubicaciones->first()
        ]);


        for (
            $i = 1;
            $i < $ubicaciones->count();
            $i++
        ) {

            $anterior = $ubicaciones[$i - 1];

            $puntoActual = $ubicaciones[$i];


            $fechaAnterior = Carbon::parse(
                $anterior->fecha_gps
            );


            $fechaActual = Carbon::parse(
                $puntoActual->fecha_gps
            );


            $segundos = max(
                0,
                $fechaAnterior->diffInSeconds(
                    $fechaActual,
                    false
                )
            );


            $distancia = $this->distanciaMetros(
                (float) $anterior->latitud,
                (float) $anterior->longitud,
                (float) $puntoActual->latitud,
                (float) $puntoActual->longitud
            );


            $velocidadCalculada = 0;


            if ($segundos > 0) {

                $velocidadCalculada =
                    ($distancia / $segundos) * 3.6;
            }


            $interrupcionGps =
                $segundos >
                (self::MINUTOS_SIN_SENAL * 60);


            $saltoIrreal =
                $segundos >= 30
                &&
                $velocidadCalculada >
                self::VELOCIDAD_MAXIMA_KMH;


            if (
                $interrupcionGps
                ||
                $saltoIrreal
            ) {

                $segmentos[] = $actual->values();

                $actual = collect();
            }


            $actual->push(
                $puntoActual
            );
        }


        if ($actual->isNotEmpty()) {

            $segmentos[] = $actual->values();
        }


        return $segmentos;
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULAR DISTANCIA ENTRE DOS COORDENADAS
    |--------------------------------------------------------------------------
    |
    | Fórmula de Haversine.
    | Devuelve metros.
    |
    */

    private function distanciaMetros(
        float $latitud1,
        float $longitud1,
        float $latitud2,
        float $longitud2
    ): float {

        $radioTierra = 6371000;


        $lat1 = deg2rad($latitud1);

        $lat2 = deg2rad($latitud2);

        $dLat = deg2rad(
            $latitud2 - $latitud1
        );

        $dLon = deg2rad(
            $longitud2 - $longitud1
        );


        $a =
            sin($dLat / 2) ** 2
            +
            cos($lat1)
            *
            cos($lat2)
            *
            sin($dLon / 2) ** 2;


        return 2
            *
            $radioTierra
            *
            asin(
                min(
                    1,
                    sqrt($a)
                )
            );
    }


    /*
    |--------------------------------------------------------------------------
    | REDUCIR CANTIDAD DE PUNTOS
    |--------------------------------------------------------------------------
    |
    | Se conservan:
    | - Punto inicial.
    | - Punto final.
    | - Puntos intermedios distribuidos uniformemente.
    |
    */

    private function muestrearUbicaciones(
        Collection $ubicaciones,
        int $maximo
    ): array {

        $ubicaciones = $ubicaciones->values();

        $cantidad = $ubicaciones->count();

        $maximo = max(2, $maximo);


        if ($cantidad <= $maximo) {

            return $ubicaciones->all();
        }


        $resultado = [];


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


            $resultado[$indice] =
                $ubicaciones->get($indice);
        }


        ksort($resultado);


        return array_values(
            $resultado
        );
    }


    /*
    |--------------------------------------------------------------------------
    | OSRM MAP MATCHING
    |--------------------------------------------------------------------------
    |
    | Ajusta los puntos GPS a la red vial.
    |
    | También recupera tracepoints para relacionar
    | las posiciones ajustadas con sus registros GPS.
    |
    */

    private function obtenerMapMatching(
        array $muestras,
        int $numeroSegmento
    ): ?array {

        if (count($muestras) < 3) {
            return null;
        }


        $coordenadas = $this->construirCadenaCoordenadas(
            $muestras
        );


        $radios = implode(
            ';',
            array_fill(
                0,
                count($muestras),
                '35'
            )
        );


        try {

            $respuesta = Http::acceptJson()

                ->timeout(15)

                ->get(
                    self::OSRM_SERVIDOR
                    .
                    '/match/v1/driving/'
                    .
                    $coordenadas,
                    [
                        'geometries' => 'geojson',

                        'overview' => 'full',

                        'steps' => 'false',

                        'tidy' => 'true',

                        'gaps' => 'split',

                        'radiuses' => $radios,
                    ]
                );


            if (!$respuesta->successful()) {

                Log::warning(
                    'OSRM Map Matching respondió con error.',
                    [
                        'status' => $respuesta->status(),

                        'segmento' => $numeroSegmento,
                    ]
                );

                return null;
            }


            $datos = $respuesta->json();


            if (
                ($datos['code'] ?? null) !== 'Ok'
                ||
                empty($datos['matchings'])
            ) {

                return null;
            }


            /*
            |--------------------------------------------------------------------------
            | RELACIONAR TRACEPOINTS CON GPS ORIGINAL
            |--------------------------------------------------------------------------
            */

            $reproduccionPorMatching = [];


            $tracepoints = $datos['tracepoints'] ?? [];


            foreach (
                $tracepoints as $indice => $tracepoint
            ) {

                if (
                    !is_array($tracepoint)
                    ||
                    !isset(
                        $tracepoint['location'][0],
                        $tracepoint['location'][1],
                        $tracepoint['matchings_index']
                    )
                ) {

                    continue;
                }


                $ubicacionOriginal =
                    $muestras[$indice]
                    ?? null;


                if (!$ubicacionOriginal) {
                    continue;
                }


                $indiceMatching = (int)
                    $tracepoint['matchings_index'];


                /*
                | OSRM devuelve longitud, latitud.
                | Convertimos a latitud, longitud.
                */

                $latitud = (float)
                    $tracepoint['location'][1];


                $longitud = (float)
                    $tracepoint['location'][0];


                $reproduccionPorMatching[
                    $indiceMatching
                ][] = $this->crearPuntoReproduccion(
                    $ubicacionOriginal,
                    $latitud,
                    $longitud,
                    $numeroSegmento
                );
            }


            /*
            |--------------------------------------------------------------------------
            | GEOMETRÍAS DE CADA MATCHING
            |--------------------------------------------------------------------------
            */

            $segmentosRuta = [];

            $segmentosReproduccion = [];


            foreach (
                $datos['matchings'] as $indice => $matching
            ) {

                $coordenadasMatching =
                    $matching['geometry']['coordinates']
                    ?? [];


                $geometria = $this->convertirCoordenadasGeoJson(
                    $coordenadasMatching
                );


                if (count($geometria) < 2) {
                    continue;
                }


                $segmentosRuta[] = $geometria;


                $puntosReproduccion =
                    $reproduccionPorMatching[$indice]
                    ?? [];


                /*
                | Si existe una sola coincidencia pero
                | OSRM no devuelve tracepoints utilizables,
                | conservamos las muestras GPS originales.
                */

                if (
                    empty($puntosReproduccion)
                    &&
                    count($datos['matchings']) === 1
                ) {

                    $puntosReproduccion =
                        $this->convertirMuestrasAReproduccion(
                            $muestras,
                            $numeroSegmento
                        );
                }


                $segmentosReproduccion[] =
                    $puntosReproduccion;
            }


            if (empty($segmentosRuta)) {
                return null;
            }


            return [
                'segmentosRuta' => $segmentosRuta,

                'segmentosReproduccion' =>
                    $segmentosReproduccion,
            ];

        } catch (\Throwable $e) {

            Log::warning(
                'OSRM Map Matching no disponible.',
                [
                    'mensaje' => $e->getMessage(),

                    'segmento' => $numeroSegmento,
                ]
            );

            return null;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | OSRM ROUTE: ALTERNATIVA
    |--------------------------------------------------------------------------
    |
    | Genera una ruta vial estimada cuando Map Matching
    | no consigue reconstruir correctamente el recorrido.
    |
    */

    private function obtenerRutaPorCalles(
        array $muestras
    ): ?array {

        if (count($muestras) < 2) {
            return null;
        }


        $coordenadas = $this->construirCadenaCoordenadas(
            $muestras
        );


        try {

            $respuesta = Http::acceptJson()

                ->timeout(15)

                ->get(
                    self::OSRM_SERVIDOR
                    .
                    '/route/v1/driving/'
                    .
                    $coordenadas,
                    [
                        'geometries' => 'geojson',

                        'overview' => 'full',

                        'steps' => 'false',

                        'continue_straight' => 'true',
                    ]
                );


            if (!$respuesta->successful()) {
                return null;
            }


            $datos = $respuesta->json();


            if (
                ($datos['code'] ?? null) !== 'Ok'
                ||
                empty(
                    $datos['routes'][0]['geometry']['coordinates']
                )
            ) {

                return null;
            }


            $ruta = $this->convertirCoordenadasGeoJson(
                $datos['routes'][0]['geometry']['coordinates']
            );


            return count($ruta) >= 2
                ? $ruta
                : null;

        } catch (\Throwable $e) {

            Log::warning(
                'OSRM Route no disponible.',
                [
                    'mensaje' => $e->getMessage(),
                ]
            );

            return null;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CONSTRUIR CADENA DE COORDENADAS
    |--------------------------------------------------------------------------
    |
    | OSRM utiliza longitud,latitud.
    |
    */

    private function construirCadenaCoordenadas(
        array $ubicaciones
    ): string {

        return collect($ubicaciones)

            ->map(function ($ubicacion) {

                return sprintf(
                    '%.7F,%.7F',
                    (float) $ubicacion->longitud,
                    (float) $ubicacion->latitud
                );

            })

            ->implode(';');
    }


    /*
    |--------------------------------------------------------------------------
    | CONVERTIR GEOMETRÍA GEOJSON
    |--------------------------------------------------------------------------
    |
    | GeoJSON: [longitud, latitud]
    | Leaflet: [latitud, longitud]
    |
    */

    private function convertirCoordenadasGeoJson(
        array $coordenadas
    ): array {

        $resultado = [];


        foreach ($coordenadas as $coordenada) {

            if (
                !isset(
                    $coordenada[0],
                    $coordenada[1]
                )
            ) {

                continue;
            }


            $resultado[] = [
                (float) $coordenada[1],
                (float) $coordenada[0],
            ];
        }


        return $resultado;
    }


    /*
    |--------------------------------------------------------------------------
    | CONVERTIR GPS ORIGINAL A GEOMETRÍA
    |--------------------------------------------------------------------------
    */

    private function convertirUbicacionesAGeometria(
        Collection $ubicaciones
    ): array {

        return $ubicaciones

            ->map(function ($ubicacion) {

                return [
                    (float) $ubicacion->latitud,
                    (float) $ubicacion->longitud,
                ];

            })

            ->values()

            ->all();
    }


    /*
    |--------------------------------------------------------------------------
    | PREPARAR PUNTOS PARA REPRODUCCIÓN
    |--------------------------------------------------------------------------
    |
    | Cada posición conserva:
    |
    | - Identificador GPS.
    | - Latitud.
    | - Longitud.
    | - Fecha y hora original.
    | - Velocidad original.
    | - Número de segmento.
    |
    */

    private function convertirMuestrasAReproduccion(
        array $muestras,
        int $numeroSegmento
    ): array {

        $resultado = [];


        foreach ($muestras as $ubicacion) {

            $resultado[] = $this->crearPuntoReproduccion(
                $ubicacion,
                (float) $ubicacion->latitud,
                (float) $ubicacion->longitud,
                $numeroSegmento
            );
        }


        return $resultado;
    }


    /*
    |--------------------------------------------------------------------------
    | CREAR REGISTRO PARA REPRODUCTOR
    |--------------------------------------------------------------------------
    */

    private function crearPuntoReproduccion(
        Ubicacion $ubicacion,
        float $latitud,
        float $longitud,
        int $numeroSegmento
    ): array {

        $fecha = $ubicacion->fecha_gps
            ? Carbon::parse(
                $ubicacion->fecha_gps
            )->toIso8601String()
            : null;


        $velocidad = is_numeric(
            $ubicacion->velocidad
        )
            ? (float) $ubicacion->velocidad
            : 0.0;


        return [

            'id' => (int) $ubicacion->id,

            'latitud' => $latitud,

            'longitud' => $longitud,

            'fecha_gps' => $fecha,

            'velocidad' => $velocidad,

            'segmento' => $numeroSegmento,

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFICAR ACCESO AL VEHÍCULO
    |--------------------------------------------------------------------------
    */

    private function verificarPropiedadVehiculo(
        Vehiculo $vehiculo
    ): void {

        $user = auth()->user();


        /*
        |--------------------------------------------------------------------------
        | SUPER ADMINISTRADOR
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole('Super Administrador')
        ) {

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CLIENTE INDIVIDUAL
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole('Cliente Individual')
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
        */

        if (
            $user->hasRole('Conductor')
        ) {

            $conductor = Conductor::where(
                'email',
                $user->email
            )
                ->where('activo', true)
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
