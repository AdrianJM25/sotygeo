<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dispositivo;
use App\Models\Ubicacion;
use App\Models\Vehiculo;
use App\Models\EventoGeocerca;
use App\Models\Alerta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GpsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | RECEPCIÓN DE DATOS GPS
    |--------------------------------------------------------------------------
    */

    public function traccar(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | IMEI
        |--------------------------------------------------------------------------
        */

        $imei = $request->input('id');

        if (!$imei) {

            return response(
                'Falta ID (IMEI)',
                400
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BUSCAR DISPOSITIVO
        |--------------------------------------------------------------------------
        */

        $dispositivo = Dispositivo::with('vehiculo')
            ->where('imei', $imei)
            ->first();


        if (!$dispositivo) {

            Log::warning(
                "Traccar: Dispositivo con IMEI {$imei} no registrado."
            );


            return response(
                'Dispositivo no registrado en SotyGeo',
                404
            );
        }


        /*
        |--------------------------------------------------------------------------
        | COMPROBAR QUE ESTÉ ACTIVO
        |--------------------------------------------------------------------------
        */

        if (!$dispositivo->activo) {

            Log::warning(
                "Traccar: El dispositivo {$imei} está inactivo."
            );


            return response(
                'Dispositivo inactivo',
                403
            );
        }


        /*
        |--------------------------------------------------------------------------
        | COORDENADAS
        |--------------------------------------------------------------------------
        */

        $lat = $request->input('lat');
        $lon = $request->input('lon');


        if (
            !is_numeric($lat)
            ||
            !is_numeric($lon)
        ) {

            return response(
                'Coordenadas inválidas',
                422
            );
        }


        $lat = (float) $lat;
        $lon = (float) $lon;


        if (
            $lat < -90
            ||
            $lat > 90
            ||
            $lon < -180
            ||
            $lon > 180
        ) {

            return response(
                'Coordenadas fuera de rango',
                422
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VELOCIDAD
        |--------------------------------------------------------------------------
        |
        | Traccar normalmente envía speed en nudos.
        | Se convierte a km/h.
        |
        */

        $velocidadKnots =
            (float) $request->input(
                'speed',
                0
            );


        $velocidadKmh =
            $velocidadKnots * 1.852;


        /*
        |--------------------------------------------------------------------------
        | FECHA GPS
        |--------------------------------------------------------------------------
        */

        $fechaGps =
            $this->obtenerFechaGps(
                $request
            );


        /*
        |--------------------------------------------------------------------------
        | GUARDAR UBICACIÓN
        |--------------------------------------------------------------------------
        */

        $ubicacion = Ubicacion::create([
            'dispositivo_id' =>
                $dispositivo->id,

            'vehiculo_id' =>
                $dispositivo->vehiculo_id,

            'latitud' =>
                $lat,

            'longitud' =>
                $lon,

            'punto' =>
                DB::raw(
                    "ST_GeomFromText('POINT({$lon} {$lat})', 4326)"
                ),

            'velocidad' =>
                $velocidadKmh,

            'altitud' =>
                $request->input(
                    'altitude',
                    0
                ),

            'rumbo' =>
                $request->input(
                    'bearing',
                    0
                ),

            'porcentaje_bateria' =>
                $request->input(
                    'batt'
                ),

            'fecha_gps' =>
                $fechaGps,
        ]);


        /*
        |--------------------------------------------------------------------------
        | PROCESAR GEOCERCAS
        |--------------------------------------------------------------------------
        */

        if (
            $dispositivo->vehiculo_id
            &&
            $dispositivo->vehiculo
        ) {

            Log::info(
                "Traccar: Evaluando geocercas para vehículo ID {$dispositivo->vehiculo_id}. Lon: {$lon}, Lat: {$lat}"
            );


            try {

                $this->procesarGeocercas(
                    $dispositivo->vehiculo,
                    $lat,
                    $lon,
                    $fechaGps
                );

            } catch (\Throwable $e) {

                /*
                | La ubicación ya quedó guardada.
                | Si falla una geocerca, no perdemos
                | el reporte GPS completo.
                */

                Log::error(
                    'Error procesando geocercas: '
                    . $e->getMessage(),
                    [
                        'vehiculo_id' =>
                            $dispositivo->vehiculo_id,

                        'ubicacion_id' =>
                            $ubicacion->id,

                        'trace' =>
                            $e->getTraceAsString(),
                    ]
                );
            }

        } else {

            Log::warning(
                "Traccar: El dispositivo {$imei} llegó correctamente, pero no tiene vehículo asignado."
            );
        }


        return response(
            'OK',
            200
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PROCESAR GEOCERCAS
    |--------------------------------------------------------------------------
    */

    private function procesarGeocercas(
        Vehiculo $vehiculo,
        float $lat,
        float $lng,
        Carbon $fechaGps
    ): void {

        DB::transaction(
            function () use (
                $vehiculo,
                $lat,
                $lng,
                $fechaGps
            ) {

                $puntoActual =
                    "POINT({$lng} {$lat})";


                /*
                |--------------------------------------------------------------------------
                | ZONAS EN LAS QUE ESTÁ EL VEHÍCULO
                |--------------------------------------------------------------------------
                |
                | ST_Covers también considera el borde del polígono.
                |
                */

                $zonasDentro =
                    $vehiculo
                        ->zonas()
                        ->whereRaw(
                            "
                            ST_Covers(
                                zonas.poligono,
                                ST_GeomFromText(?, 4326)
                            )
                            ",
                            [
                                $puntoActual
                            ]
                        )
                        ->get();


                $idsZonasDentro =
                    $zonasDentro
                        ->pluck('id')
                        ->map(
                            fn ($id) =>
                                (int) $id
                        )
                        ->values()
                        ->all();


                /*
                |--------------------------------------------------------------------------
                | EVENTOS ABIERTOS
                |--------------------------------------------------------------------------
                */

                $eventosAbiertos =
                    EventoGeocerca::with(
                        'zona'
                    )
                    ->where(
                        'vehiculo_id',
                        $vehiculo->id
                    )
                    ->whereNull(
                        'fecha_salida'
                    )
                    ->get();


                $idsZonasAbiertas =
                    $eventosAbiertos
                        ->pluck('zona_id')
                        ->map(
                            fn ($id) =>
                                (int) $id
                        )
                        ->values()
                        ->all();


                /*
                |--------------------------------------------------------------------------
                | SALIDAS
                |--------------------------------------------------------------------------
                */

                foreach (
                    $eventosAbiertos
                    as $evento
                ) {

                    if (
                        in_array(
                            (int) $evento->zona_id,
                            $idsZonasDentro,
                            true
                        )
                    ) {

                        continue;
                    }


                    /*
                    | Duración correcta:
                    | entrada -> reporte actual
                    */

                    $duracion =
                        $this->calcularDuracionMinutos(
                            $evento->fecha_entrada,
                            $fechaGps
                        );


                    /*
                    | Obtener regla vehículo-zona
                    */

                    $regla = null;


                    if ($evento->zona) {

                        $regla =
                            $evento
                                ->zona
                                ->vehiculos()
                                ->where(
                                    'vehiculo_id',
                                    $vehiculo->id
                                )
                                ->first()
                                ?->pivot;
                    }


                    $notificarSalida =
                        (bool) (
                            $regla
                                ?->notificar_salida
                            ??
                            false
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | PERMANENCIA MÍNIMA
                    |--------------------------------------------------------------------------
                    */

                    $incumplioMinima =
                        $regla
                        &&
                        $regla
                            ->permanencia_minima_minutos
                        &&
                        $duracion
                            <
                        (int) $regla
                            ->permanencia_minima_minutos;


                    /*
                    |--------------------------------------------------------------------------
                    | TIPO DE EVENTO
                    |--------------------------------------------------------------------------
                    */

                    $tipoEvento =
                        $evento->tipo_evento;


                    /*
                    | No sobrescribimos una violación
                    | restringida con salida anticipada.
                    */

                    if (
                        $incumplioMinima
                        &&
                        $tipoEvento === 'normal'
                    ) {

                        $tipoEvento =
                            'salida_anticipada';
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CERRAR EVENTO
                    |--------------------------------------------------------------------------
                    */

                    $evento->update([
                        'fecha_salida' =>
                            $fechaGps,

                        'latitud_salida' =>
                            $lat,

                        'longitud_salida' =>
                            $lng,

                        'duracion_minutos' =>
                            $duracion,

                        'tipo_evento' =>
                            $tipoEvento,

                        'alerta_generada' =>
                            $evento->alerta_generada
                            ||
                            $notificarSalida
                            ||
                            $incumplioMinima,
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | ALERTA DE SALIDA
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $notificarSalida
                        &&
                        $evento->zona
                    ) {

                        Alerta::create([
                            'vehiculo_id' =>
                                $vehiculo->id,

                            'tipo' =>
                                'geocerca_salida',

                            'mensaje' =>
                                "El vehículo {$vehiculo->nombre} salió de la zona {$evento->zona->nombre}. Permanencia: {$duracion} min.",
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | ALERTA PERMANENCIA MÍNIMA
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $incumplioMinima
                        &&
                        $evento->zona
                    ) {

                        Alerta::create([
                            'vehiculo_id' =>
                                $vehiculo->id,

                            'tipo' =>
                                'permanencia_minima',

                            'mensaje' =>
                                "El vehículo {$vehiculo->nombre} salió de la zona {$evento->zona->nombre} después de {$duracion} min. El mínimo requerido era de {$regla->permanencia_minima_minutos} min.",
                        ]);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | ENTRADAS
                |--------------------------------------------------------------------------
                */

                foreach (
                    $zonasDentro
                    as $zona
                ) {

                    if (
                        in_array(
                            (int) $zona->id,
                            $idsZonasAbiertas,
                            true
                        )
                    ) {

                        continue;
                    }


                    /*
                    | Obtener configuración de la
                    | relación vehículo-zona.
                    */

                    $regla =
                        $zona
                            ->vehiculos()
                            ->where(
                                'vehiculo_id',
                                $vehiculo->id
                            )
                            ->first()
                            ?->pivot;


                    $esRestringida =
                        $regla
                        &&
                        $regla->tipo_regla
                            ===
                        'restringida';


                    $notificarEntrada =
                        (bool) (
                            $regla
                                ?->notificar_entrada
                            ??
                            false
                        );


                    /*
                    | Una zona restringida siempre
                    | se considera una alerta.
                    */

                    $debeGenerarAlerta =
                        $notificarEntrada
                        ||
                        $esRestringida;


                    $tipoEvento =
                        $esRestringida
                            ?
                            'violacion_restringida'
                            :
                            'normal';


                    /*
                    |--------------------------------------------------------------------------
                    | CREAR EVENTO
                    |--------------------------------------------------------------------------
                    */

                    EventoGeocerca::create([
                        'vehiculo_id' =>
                            $vehiculo->id,

                        'zona_id' =>
                            $zona->id,

                        'fecha_entrada' =>
                            $fechaGps,

                        'fecha_salida' =>
                            null,

                        'duracion_minutos' =>
                            null,

                        'latitud_entrada' =>
                            $lat,

                        'longitud_entrada' =>
                            $lng,

                        'latitud_salida' =>
                            null,

                        'longitud_salida' =>
                            null,

                        'tipo_evento' =>
                            $tipoEvento,

                        'alerta_generada' =>
                            $debeGenerarAlerta,

                        'alerta_permanencia_generada' =>
                            false,
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | ALERTA ENTRADA
                    |--------------------------------------------------------------------------
                    */

                    if ($debeGenerarAlerta) {

                        if ($esRestringida) {

                            $tipoAlerta =
                                'geocerca_restringida';

                            $mensaje =
                                "ALERTA: El vehículo {$vehiculo->nombre} ingresó a la zona restringida {$zona->nombre}.";

                        } else {

                            $tipoAlerta =
                                'geocerca_entrada';

                            $mensaje =
                                "El vehículo {$vehiculo->nombre} ingresó a la zona {$zona->nombre}.";
                        }


                        Alerta::create([
                            'vehiculo_id' =>
                                $vehiculo->id,

                            'tipo' =>
                                $tipoAlerta,

                            'mensaje' =>
                                $mensaje,
                        ]);
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | PERMANENCIA MÁXIMA
                |--------------------------------------------------------------------------
                |
                | Después de procesar entradas y salidas,
                | revisamos todos los eventos que siguen
                | abiertos.
                |
                */

                $eventosDentro =
                    EventoGeocerca::with(
                        'zona'
                    )
                    ->where(
                        'vehiculo_id',
                        $vehiculo->id
                    )
                    ->whereNull(
                        'fecha_salida'
                    )
                    ->whereIn(
                        'zona_id',
                        $idsZonasDentro
                    )
                    ->get();


                foreach (
                    $eventosDentro
                    as $evento
                ) {

                    if (!$evento->zona) {
                        continue;
                    }


                    /*
                    | Ya se generó esta alerta.
                    | No volver a generarla.
                    */

                    if (
                        $evento
                            ->alerta_permanencia_generada
                    ) {

                        continue;
                    }


                    $regla =
                        $evento
                            ->zona
                            ->vehiculos()
                            ->where(
                                'vehiculo_id',
                                $vehiculo->id
                            )
                            ->first()
                            ?->pivot;


                    if (
                        !$regla
                        ||
                        !$regla
                            ->permanencia_maxima_minutos
                    ) {

                        continue;
                    }


                    $duracion =
                        $this->calcularDuracionMinutos(
                            $evento->fecha_entrada,
                            $fechaGps
                        );


                    if (
                        $duracion
                        <
                        (int) $regla
                            ->permanencia_maxima_minutos
                    ) {

                        continue;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | MARCAR ALERTA DE PERMANENCIA
                    |--------------------------------------------------------------------------
                    */

                    $tipoEvento =
                        $evento->tipo_evento;


                    if (
                        $tipoEvento ===
                        'normal'
                    ) {

                        $tipoEvento =
                            'permanencia_excedida';
                    }


                    $evento->update([
                        'alerta_permanencia_generada' =>
                            true,

                        'alerta_generada' =>
                            true,

                        'tipo_evento' =>
                            $tipoEvento,
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | GENERAR ALERTA
                    |--------------------------------------------------------------------------
                    */

                    Alerta::create([
                        'vehiculo_id' =>
                            $vehiculo->id,

                        'tipo' =>
                            'permanencia_maxima',

                        'mensaje' =>
                            "El vehículo {$vehiculo->nombre} excedió el tiempo máximo permitido en la zona {$evento->zona->nombre}. Tiempo actual: {$duracion} min. Máximo permitido: {$regla->permanencia_maxima_minutos} min.",
                    ]);
                }
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULAR DURACIÓN
    |--------------------------------------------------------------------------
    */

    private function calcularDuracionMinutos(
        Carbon $inicio,
        Carbon $fin
    ): int {

        /*
        | Usamos timestamps para evitar
        | diferencias negativas de Carbon 3.
        */

        $segundos =
            $fin->timestamp
            -
            $inicio->timestamp;


        if ($segundos <= 0) {
            return 0;
        }


        return (int) floor(
            $segundos / 60
        );
    }


    /*
    |--------------------------------------------------------------------------
    | OBTENER FECHA GPS
    |--------------------------------------------------------------------------
    */

    private function obtenerFechaGps(
        Request $request
    ): Carbon {

        if (
            !$request->filled(
                'timestamp'
            )
        ) {

            return now();
        }


        $timestamp =
            $request->input(
                'timestamp'
            );


        /*
        | Unix timestamp
        */

        if (
            is_numeric(
                $timestamp
            )
        ) {

            $timestamp =
                (float) $timestamp;


            /*
            | Si llega en milisegundos,
            | convertir a segundos.
            */

            if (
                $timestamp
                >
                1000000000000
            ) {

                $timestamp =
                    $timestamp / 1000;
            }


            return Carbon::createFromTimestampUTC(
                (int) $timestamp
            )
            ->setTimezone(
                config(
                    'app.timezone'
                )
            );
        }


        /*
        | Fecha en formato texto
        */

        return Carbon::parse(
            $timestamp
        )
        ->setTimezone(
            config(
                'app.timezone'
            )
        );
    }
}