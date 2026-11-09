<?php

namespace App\Http\Controllers;

use App\Exports\ReporteConductoresExport;
use App\Models\Conductor;
use App\Models\Empresa;
use App\Models\Ubicacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class ReporteConductoresController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FN.20 - REPORTE DE ACTIVIDAD POR CONDUCTOR
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $datos =
            $this->generarDatosReporte(
                $request
            );

        return view(
            'reportes.conductores',
            $datos
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORTAR PDF
    |--------------------------------------------------------------------------
    */

    public function exportarPdf(
        Request $request
    ) {

        $datos =
            $this->generarDatosReporte(
                $request
            );


        $nombreArchivo =
            'reporte_actividad_conductores_'
            .
            $datos[
                'fechaInicio'
            ]->format(
                'Ymd'
            )
            .
            '_'
            .
            $datos[
                'fechaFin'
            ]->format(
                'Ymd'
            )
            .
            '.pdf';


        $pdf =
            Pdf::loadView(
                'reportes.conductores-pdf',
                $datos
            )
            ->setPaper(
                'a4',
                'landscape'
            );


        return $pdf->download(
            $nombreArchivo
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORTAR EXCEL
    |--------------------------------------------------------------------------
    */

    public function exportarExcel(
        Request $request
    ) {

        $datos =
            $this->generarDatosReporte(
                $request
            );


        $nombreArchivo =
            'reporte_actividad_conductores_'
            .
            $datos[
                'fechaInicio'
            ]->format(
                'Ymd'
            )
            .
            '_'
            .
            $datos[
                'fechaFin'
            ]->format(
                'Ymd'
            )
            .
            '.xlsx';


        return Excel::download(

            new ReporteConductoresExport(

                $datos[
                    'resultados'
                ],

                $datos[
                    'totalConductores'
                ],

                $datos[
                    'conductoresConActividad'
                ],

                $datos[
                    'conductoresSinActividad'
                ],

                $datos[
                    'totalRecorridos'
                ],

                $datos[
                    'distanciaTotal'
                ]

            ),

            $nombreArchivo
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERAR DATOS DEL REPORTE
    |--------------------------------------------------------------------------
    */

    private function generarDatosReporte(
        Request $request
    ): array {

        $user =
            auth()->user();


        /*
        |--------------------------------------------------------------------------
        | VALIDAR ROL
        |--------------------------------------------------------------------------
        */

        if (
            !$user->hasAnyRole([
                'Super Administrador',
                'Administrador de Empresa',
            ])
        ) {

            abort(
                403,
                'No tienes permiso para consultar este reporte.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR FILTROS
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'fecha_inicio' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'fecha_fin' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:fecha_inicio',
            ],

            'empresa_id' => [
                'nullable',
                'integer',
                'exists:empresas,id',
            ],

            'conductor_id' => [
                'nullable',
                'integer',
                'exists:conductores,id',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | ZONA HORARIA
        |--------------------------------------------------------------------------
        */

        $zonaLocal =
            config(
                'app.timezone',
                'America/Mexico_City'
            );


        /*
        |--------------------------------------------------------------------------
        | PERIODO
        |--------------------------------------------------------------------------
        */

        $fechaInicio =
            $request->filled(
                'fecha_inicio'
            )
                ? Carbon::createFromFormat(
                    'Y-m-d',
                    $request->fecha_inicio,
                    $zonaLocal
                )->startOfDay()

                : Carbon::now(
                    $zonaLocal
                )
                    ->startOfMonth()
                    ->startOfDay();


        $fechaFin =
            $request->filled(
                'fecha_fin'
            )
                ? Carbon::createFromFormat(
                    'Y-m-d',
                    $request->fecha_fin,
                    $zonaLocal
                )->endOfDay()

                : Carbon::now(
                    $zonaLocal
                )->endOfDay();


        /*
        |--------------------------------------------------------------------------
        | EMPRESAS DISPONIBLES
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {

            $empresas =
                Empresa::query()
                    ->orderBy(
                        'nombre'
                    )
                    ->get();

        } else {

            $empresas =
                Empresa::query()
                    ->where(
                        'id',
                        $user->empresa_id
                    )
                    ->get();
        }


        /*
        |--------------------------------------------------------------------------
        | EMPRESA APLICADA
        |--------------------------------------------------------------------------
        */

        $empresaId =
            null;


        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {

            if (
                $request->filled(
                    'empresa_id'
                )
            ) {

                $empresaId =
                    (int)
                    $request->empresa_id;
            }

        } else {

            $empresaId =
                (int)
                $user->empresa_id;
        }


        /*
        |--------------------------------------------------------------------------
        | CONDUCTORES DISPONIBLES
        |--------------------------------------------------------------------------
        |
        | Solamente se consideran conductores activos.
        |
        */

        $conductoresQuery =
            Conductor::query()
                ->with([
                    'empresa',
                    'vehiculo',
                ])
                ->where(
                    'activo',
                    true
                )
                ->orderBy(
                    'nombre'
                )
                ->orderBy(
                    'apellido_paterno'
                );


        /*
        |--------------------------------------------------------------------------
        | SEGURIDAD POR EMPRESA
        |--------------------------------------------------------------------------
        */

        if ($empresaId) {

            $conductoresQuery
                ->where(
                    'empresa_id',
                    $empresaId
                );
        }


        $conductores =
            (clone $conductoresQuery)
                ->get();


        /*
        |--------------------------------------------------------------------------
        | CONDUCTORES DEL REPORTE
        |--------------------------------------------------------------------------
        */

        $conductoresReporteQuery =
            clone $conductoresQuery;


        if (
            $request->filled(
                'conductor_id'
            )
        ) {

            $conductoresReporteQuery
                ->where(
                    'id',
                    $request->conductor_id
                );
        }


        $conductoresReporte =
            $conductoresReporteQuery
                ->get();


        /*
        |--------------------------------------------------------------------------
        | RESULTADOS
        |--------------------------------------------------------------------------
        */

        $resultados =
            collect();


        $resumenConductores =
            collect();


        foreach (
            $conductoresReporte
            as
            $conductor
        ) {

            /*
            |--------------------------------------------------------------------------
            | NOMBRE COMPLETO
            |--------------------------------------------------------------------------
            */

            $nombreConductor =
                trim(
                    $conductor->nombre
                    .
                    ' '
                    .
                    (
                        $conductor
                            ->apellido_paterno
                        ??
                        ''
                    )
                    .
                    ' '
                    .
                    (
                        $conductor
                            ->apellido_materno
                        ??
                        ''
                    )
                );


            /*
            |--------------------------------------------------------------------------
            | CONDUCTOR SIN VEHÍCULO
            |--------------------------------------------------------------------------
            */

            if (
                !$conductor->vehiculo_id
                ||
                !$conductor->vehiculo
            ) {

                $resultados->push([

                    'conductor_id' =>
                        $conductor->id,

                    'conductor' =>
                        $nombreConductor,

                    'empresa' =>
                        $conductor
                            ->empresa
                            ?->nombre
                        ??
                        'Sin empresa',

                    'vehiculo' =>
                        'Sin vehículo asignado',

                    'placas' =>
                        '—',

                    'ruta_id' =>
                        null,

                    'fecha_inicio' =>
                        null,

                    'fecha_fin' =>
                        null,

                    'estado' =>
                        'Sin vehículo',

                    'distancia_km' =>
                        0,

                    'duracion' =>
                        '—',

                    'tiene_actividad' =>
                        false,

                ]);


                $resumenConductores
                    ->push([

                        'conductor_id' =>
                            $conductor->id,

                        'conductor' =>
                            $nombreConductor,

                        'vehiculo' =>
                            'Sin vehículo asignado',

                        'recorridos' =>
                            0,

                        'distancia_km' =>
                            0,

                        'tiene_actividad' =>
                            false,

                    ]);


                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | CONSULTAR UBICACIONES GPS
            |--------------------------------------------------------------------------
            */

            $ubicaciones =
                Ubicacion::query()

                    ->where(
                        'vehiculo_id',
                        $conductor->vehiculo_id
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
                            $fechaInicio,
                            $fechaFin,
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
            | SIN REGISTROS GPS
            |--------------------------------------------------------------------------
            */

            if (
                $ubicaciones
                    ->isEmpty()
            ) {

                $this
                    ->agregarSinActividad(

                        $resultados,

                        $resumenConductores,

                        $conductor,

                        $nombreConductor,

                        'Sin registros GPS'
                    );


                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | AGRUPAR UBICACIONES POR DÍA
            |--------------------------------------------------------------------------
            |
            | Cada día con movimiento se considera
            | un recorrido de actividad.
            |
            | Esto coincide con la forma en que
            | el historial de rutas consulta
            | el recorrido de un vehículo por fecha.
            |
            */

            $ubicacionesPorDia =
                $ubicaciones
                    ->groupBy(
                        function (
                            $ubicacion
                        ) use (
                            $zonaLocal
                        ) {

                            return
                                $ubicacion
                                    ->fecha_gps
                                    ->copy()
                                    ->timezone(
                                        $zonaLocal
                                    )
                                    ->format(
                                        'Y-m-d'
                                    );
                        }
                    );


            /*
            |--------------------------------------------------------------------------
            | CONTADORES DEL CONDUCTOR
            |--------------------------------------------------------------------------
            */

            $recorridosConductor =
                0;


            $distanciaConductor =
                0.0;


            /*
            |--------------------------------------------------------------------------
            | PROCESAR CADA DÍA
            |--------------------------------------------------------------------------
            */

            foreach (
                $ubicacionesPorDia
                as
                $fecha
                =>
                $ubicacionesDia
            ) {

                /*
                |--------------------------------------------------------------------------
                | UBICACIONES CON MOVIMIENTO
                |--------------------------------------------------------------------------
                */

                $ubicacionesMovimiento =
                    $ubicacionesDia
                        ->filter(
                            function (
                                $ubicacion
                            ) {

                                return
                                    (bool)
                                    $ubicacion
                                        ->en_movimiento

                                    ||

                                    (float)
                                    $ubicacion
                                        ->velocidad
                                    >
                                    0;
                            }
                        )
                        ->values();


                /*
                |--------------------------------------------------------------------------
                | VALIDAR RECORRIDO REAL
                |--------------------------------------------------------------------------
                |
                | Para considerar un recorrido deben existir
                | al menos dos registros GPS de movimiento.
                | Un solo punto aislado no representa un recorrido.
                |
                */

                if (
                    $ubicacionesMovimiento
                        ->count()
                    <
                    2
                ) {

                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | PRIMER Y ÚLTIMO MOVIMIENTO
                |--------------------------------------------------------------------------
                */

                $primerMovimiento =
                    $ubicacionesMovimiento
                        ->first();


                $ultimoMovimiento =
                    $ubicacionesMovimiento
                        ->last();


                /*
                |--------------------------------------------------------------------------
                | PUNTOS DEL RECORRIDO
                |--------------------------------------------------------------------------
                |
                | Conservamos todos los puntos GPS
                | comprendidos entre el primer y
                | último movimiento del día.
                |
                */

                $puntosRecorrido =
                    $ubicacionesDia
                        ->filter(
                            function (
                                $ubicacion
                            ) use (
                                $primerMovimiento,
                                $ultimoMovimiento
                            ) {

                                return
                                    $ubicacion
                                        ->fecha_gps
                                    >=
                                    $primerMovimiento
                                        ->fecha_gps

                                    &&

                                    $ubicacion
                                        ->fecha_gps
                                    <=
                                    $ultimoMovimiento
                                        ->fecha_gps;
                            }
                        )
                        ->values();


                /*
                |--------------------------------------------------------------------------
                | DISTANCIA DEL RECORRIDO
                |--------------------------------------------------------------------------
                */

                $distanciaKm =
                    $this
                        ->calcularDistanciaTotal(
                            $puntosRecorrido
                        );


                /*
                |--------------------------------------------------------------------------
                | DURACIÓN
                |--------------------------------------------------------------------------
                */

                $minutos =
                    (int)
                    $primerMovimiento
                        ->fecha_gps
                        ->diffInMinutes(
                            $ultimoMovimiento
                                ->fecha_gps
                        );


                /*
                |--------------------------------------------------------------------------
                | ACUMULAR
                |--------------------------------------------------------------------------
                */

                $recorridosConductor++;


                $distanciaConductor +=
                    $distanciaKm;


                /*
                |--------------------------------------------------------------------------
                | AGREGAR RECORRIDO
                |--------------------------------------------------------------------------
                */

                $resultados->push([

                    'conductor_id' =>
                        $conductor->id,

                    'conductor' =>
                        $nombreConductor,

                    'empresa' =>
                        $conductor
                            ->empresa
                            ?->nombre
                        ??
                        'Sin empresa',

                    'vehiculo' =>
                        $conductor
                            ->vehiculo
                            ->nombre,

                    'placas' =>
                        $conductor
                            ->vehiculo
                            ->placas
                        ?:
                        'Sin placas',

                    /*
                    | No existe una fila física
                    | en rutas para este recorrido.
                    | Utilizamos la fecha como
                    | identificador lógico.
                    */

                    'ruta_id' =>
                        $fecha,

                    'fecha_inicio' =>
                        $primerMovimiento
                            ->fecha_gps
                            ->copy()
                            ->timezone(
                                $zonaLocal
                            ),

                    'fecha_fin' =>
                        $ultimoMovimiento
                            ->fecha_gps
                            ->copy()
                            ->timezone(
                                $zonaLocal
                            ),

                    'estado' =>
                        'Finalizado',

                    'distancia_km' =>
                        round(
                            $distanciaKm,
                            2
                        ),

                    'duracion' =>
                        $this
                            ->formatearDuracion(
                                $minutos
                            ),

                    'tiene_actividad' =>
                        true,

                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | CONDUCTOR SIN MOVIMIENTO
            |--------------------------------------------------------------------------
            */

            if (
                $recorridosConductor
                ===
                0
            ) {

                $this
                    ->agregarSinActividad(

                        $resultados,

                        $resumenConductores,

                        $conductor,

                        $nombreConductor,

                        'Sin recorridos'
                    );


                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | RESUMEN DEL CONDUCTOR
            |--------------------------------------------------------------------------
            */

            $resumenConductores
                ->push([

                    'conductor_id' =>
                        $conductor->id,

                    'conductor' =>
                        $nombreConductor,

                    'vehiculo' =>
                        $conductor
                            ->vehiculo
                            ->nombre,

                    'recorridos' =>
                        $recorridosConductor,

                    'distancia_km' =>
                        round(
                            $distanciaConductor,
                            2
                        ),

                    'tiene_actividad' =>
                        true,

                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | MÉTRICAS GENERALES
        |--------------------------------------------------------------------------
        */

        $totalConductores =
            $resumenConductores
                ->count();


        $conductoresConActividad =
            $resumenConductores
                ->where(
                    'tiene_actividad',
                    true
                )
                ->count();


        $conductoresSinActividad =
            $totalConductores
            -
            $conductoresConActividad;


        $totalRecorridos =
            (int)
            $resumenConductores
                ->sum(
                    'recorridos'
                );


        $distanciaTotal =
            round(
                (float)
                $resumenConductores
                    ->sum(
                        'distancia_km'
                    ),
                2
            );


        /*
        |--------------------------------------------------------------------------
        | VEHÍCULOS UTILIZADOS
        |--------------------------------------------------------------------------
        */

        $vehiculosUtilizados =
            $resumenConductores

                ->where(
                    'tiene_actividad',
                    true
                )

                ->pluck(
                    'vehiculo'
                )

                ->filter()

                ->unique()

                ->count();


        /*
        |--------------------------------------------------------------------------
        | CONDUCTOR SELECCIONADO
        |--------------------------------------------------------------------------
        */

        $conductorSeleccionado =
            null;


        if (
            $request->filled(
                'conductor_id'
            )
        ) {

            $conductorSeleccionado =
                $conductores
                    ->firstWhere(
                        'id',
                        (int)
                        $request
                            ->conductor_id
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | DATOS PARA VISTA / PDF / EXCEL
        |--------------------------------------------------------------------------
        */

        return [

            'empresas' =>
                $empresas,

            'conductores' =>
                $conductores,

            'conductorSeleccionado' =>
                $conductorSeleccionado,

            'resultados' =>
                $resultados,

            'resumenConductores' =>
                $resumenConductores,

            'fechaInicio' =>
                $fechaInicio,

            'fechaFin' =>
                $fechaFin,

            'totalConductores' =>
                $totalConductores,

            'conductoresConActividad' =>
                $conductoresConActividad,

            'conductoresSinActividad' =>
                $conductoresSinActividad,

            'totalRecorridos' =>
                $totalRecorridos,

            'distanciaTotal' =>
                $distanciaTotal,

            'vehiculosUtilizados' =>
                $vehiculosUtilizados,

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | AGREGAR CONDUCTOR SIN ACTIVIDAD
    |--------------------------------------------------------------------------
    */

    private function agregarSinActividad(
        Collection $resultados,
        Collection $resumenConductores,
        Conductor $conductor,
        string $nombreConductor,
        string $estado
    ): void {

        $vehiculo =
            $conductor->vehiculo;


        $resultados->push([

            'conductor_id' =>
                $conductor->id,

            'conductor' =>
                $nombreConductor,

            'empresa' =>
                $conductor
                    ->empresa
                    ?->nombre
                ??
                'Sin empresa',

            'vehiculo' =>
                $vehiculo
                    ?->nombre
                ??
                'Sin vehículo asignado',

            'placas' =>
                $vehiculo
                    ?->placas
                ??
                '—',

            'ruta_id' =>
                null,

            'fecha_inicio' =>
                null,

            'fecha_fin' =>
                null,

            'estado' =>
                $estado,

            'distancia_km' =>
                0,

            'duracion' =>
                '—',

            'tiene_actividad' =>
                false,

        ]);


        $resumenConductores
            ->push([

                'conductor_id' =>
                    $conductor->id,

                'conductor' =>
                    $nombreConductor,

                'vehiculo' =>
                    $vehiculo
                        ?->nombre
                    ??
                    'Sin vehículo asignado',

                'recorridos' =>
                    0,

                'distancia_km' =>
                    0,

                'tiene_actividad' =>
                    false,

            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULAR DISTANCIA TOTAL
    |--------------------------------------------------------------------------
    |
    | Se utiliza la misma lógica de distancia
    | empleada en el reporte de kilometraje.
    |
    */

    private function calcularDistanciaTotal(
        Collection $ubicaciones
    ): float {

        if (
            $ubicaciones
                ->count()
            <
            2
        ) {

            return 0;
        }


        $distanciaTotal =
            0.0;


        $anterior =
            null;


        foreach (
            $ubicaciones
            as
            $ubicacion
        ) {

            if (
                !$anterior
            ) {

                $anterior =
                    $ubicacion;

                continue;
            }


            $distanciaTotal +=
                $this
                    ->distanciaEntrePuntos(

                        (float)
                        $anterior
                            ->latitud,

                        (float)
                        $anterior
                            ->longitud,

                        (float)
                        $ubicacion
                            ->latitud,

                        (float)
                        $ubicacion
                            ->longitud

                    );


            $anterior =
                $ubicacion;
        }


        return
            $distanciaTotal;
    }


    /*
    |--------------------------------------------------------------------------
    | FÓRMULA DE HAVERSINE
    |--------------------------------------------------------------------------
    */

    private function distanciaEntrePuntos(
        float $latitud1,
        float $longitud1,
        float $latitud2,
        float $longitud2
    ): float {

        /*
        | Radio aproximado de la Tierra
        | expresado en kilómetros.
        */

        $radioTierra =
            6371;


        $lat1 =
            deg2rad(
                $latitud1
            );


        $lat2 =
            deg2rad(
                $latitud2
            );


        $dLat =
            deg2rad(
                $latitud2
                -
                $latitud1
            );


        $dLon =
            deg2rad(
                $longitud2
                -
                $longitud1
            );


        $a =
            sin(
                $dLat
                /
                2
            )
            **
            2

            +

            cos(
                $lat1
            )

            *

            cos(
                $lat2
            )

            *

            (
                sin(
                    $dLon
                    /
                    2
                )
                **
                2
            );


        $c =
            2
            *
            atan2(
                sqrt(
                    $a
                ),
                sqrt(
                    1
                    -
                    $a
                )
            );


        return
            $radioTierra
            *
            $c;
    }


    /*
    |--------------------------------------------------------------------------
    | FORMATEAR DURACIÓN
    |--------------------------------------------------------------------------
    */

    private function formatearDuracion(
        int $minutos
    ): string {

        $horas =
            intdiv(
                $minutos,
                60
            );


        $resto =
            $minutos
            %
            60;


        if (
            $horas
            >
            0
        ) {

            return
                $horas
                .
                ' h '
                .
                $resto
                .
                ' min';
        }


        return
            $resto
            .
            ' min';
    }
}