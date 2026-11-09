<?php

namespace App\Http\Controllers;

use App\Exports\ReporteZonasExport;
use App\Models\Empresa;
use App\Models\EventoGeocerca;
use App\Models\Flotilla;
use App\Models\Vehiculo;
use App\Models\Zona;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteZonasController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FN.17 - REPORTE DE VISITAS Y TIEMPOS EN ZONAS
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $datos =
            $this->generarDatosReporte(
                $request
            );

        return view(
            'reportes.zonas',
            $datos
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORTAR PDF
    |--------------------------------------------------------------------------
    */

    public function exportarPdf(Request $request)
    {
        $datos =
            $this->generarDatosReporte(
                $request
            );


        $nombreArchivo =
            'reporte_zonas_'
            .
            $datos['fechaInicio']->format('Ymd')
            .
            '_'
            .
            $datos['fechaFin']->format('Ymd')
            .
            '.pdf';


        $pdf =
            Pdf::loadView(
                'reportes.zonas-pdf',
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

    public function exportarExcel(Request $request)
    {
        $datos =
            $this->generarDatosReporte(
                $request
            );


        $nombreArchivo =
            'reporte_zonas_'
            .
            $datos['fechaInicio']->format('Ymd')
            .
            '_'
            .
            $datos['fechaFin']->format('Ymd')
            .
            '.xlsx';


        return Excel::download(
            new ReporteZonasExport(
                $datos['eventos']
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

            'flotilla_id' => [
                'nullable',
                'integer',
                'exists:flotillas,id',
            ],

            'vehiculo_id' => [
                'nullable',
                'integer',
                'exists:vehiculos,id',
            ],

            'zona_id' => [
                'nullable',
                'integer',
                'exists:zonas,id',
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
        | FECHA INICIAL
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'fecha_inicio'
            )
        ) {

            $fechaInicio =
                Carbon::createFromFormat(
                    'Y-m-d',
                    $request->fecha_inicio,
                    $zonaLocal
                )
                ->startOfDay();

        } else {

            $fechaInicio =
                Carbon::now(
                    $zonaLocal
                )
                ->startOfMonth()
                ->startOfDay();
        }


        /*
        |--------------------------------------------------------------------------
        | FECHA FINAL
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'fecha_fin'
            )
        ) {

            $fechaFin =
                Carbon::createFromFormat(
                    'Y-m-d',
                    $request->fecha_fin,
                    $zonaLocal
                )
                ->endOfDay();

        } else {

            $fechaFin =
                Carbon::now(
                    $zonaLocal
                )
                ->endOfDay();
        }


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
        | FLOTILLAS DISPONIBLES
        |--------------------------------------------------------------------------
        */

        $flotillasQuery =
            Flotilla::query()
                ->orderBy(
                    'nombre'
                );


        if (
            $empresaId
        ) {

            $flotillasQuery
                ->where(
                    'empresa_id',
                    $empresaId
                );
        }


        if (
            !$user->hasRole(
                'Super Administrador'
            )
        ) {

            $flotillasQuery
                ->where(
                    'empresa_id',
                    $user->empresa_id
                );
        }


        $flotillas =
            $flotillasQuery
                ->get();


        /*
        |--------------------------------------------------------------------------
        | VEHÍCULOS DISPONIBLES
        |--------------------------------------------------------------------------
        */

        $vehiculosQuery =
            Vehiculo::query()
                ->orderBy(
                    'nombre'
                );


        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {

            if (
                $empresaId
            ) {

                $vehiculosQuery
                    ->where(
                        'empresa_id',
                        $empresaId
                    );
            }

        } else {

            $vehiculosQuery
                ->where(
                    'empresa_id',
                    $user->empresa_id
                );
        }


        if (
            $request->filled(
                'flotilla_id'
            )
        ) {

            $vehiculosQuery
                ->where(
                    'flotilla_id',
                    $request->flotilla_id
                );
        }


        $vehiculos =
            $vehiculosQuery
                ->get();


        /*
        |--------------------------------------------------------------------------
        | ZONAS DISPONIBLES
        |--------------------------------------------------------------------------
        */

        $zonasQuery =
            Zona::query()
                ->orderBy(
                    'nombre'
                );


        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {

            if (
                $empresaId
            ) {

                $zonasQuery
                    ->where(
                        'empresa_id',
                        $empresaId
                    );
            }

        } else {

            $zonasQuery
                ->where(
                    'empresa_id',
                    $user->empresa_id
                );
        }


        $zonas =
            $zonasQuery
                ->get();


        /*
        |--------------------------------------------------------------------------
        | CONSULTAR EVENTOS DE GEOCERCA
        |--------------------------------------------------------------------------
        */

        $eventosQuery =
            EventoGeocerca::query()
                ->with([
                    'vehiculo.flotilla',
                    'zona',
                ])
                ->whereBetween(
                    'fecha_entrada',
                    [
                        $fechaInicio,
                        $fechaFin,
                    ]
                );


        /*
        |--------------------------------------------------------------------------
        | SEGURIDAD POR EMPRESA
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {

            if (
                $empresaId
            ) {

                $eventosQuery
                    ->whereHas(
                        'vehiculo',
                        function ($query) use (
                            $empresaId
                        ) {

                            $query->where(
                                'empresa_id',
                                $empresaId
                            );
                        }
                    );
            }

        } else {

            $eventosQuery
                ->whereHas(
                    'vehiculo',
                    function ($query) use (
                        $user
                    ) {

                        $query->where(
                            'empresa_id',
                            $user->empresa_id
                        );
                    }
                );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO POR FLOTILLA
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'flotilla_id'
            )
        ) {

            $eventosQuery
                ->whereHas(
                    'vehiculo',
                    function ($query) use (
                        $request
                    ) {

                        $query->where(
                            'flotilla_id',
                            $request->flotilla_id
                        );
                    }
                );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO POR VEHÍCULO
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'vehiculo_id'
            )
        ) {

            $eventosQuery
                ->where(
                    'vehiculo_id',
                    $request->vehiculo_id
                );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO POR ZONA
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'zona_id'
            )
        ) {

            $eventosQuery
                ->where(
                    'zona_id',
                    $request->zona_id
                );
        }


        /*
        |--------------------------------------------------------------------------
        | OBTENER EVENTOS
        |--------------------------------------------------------------------------
        */

        $eventosBase =
            $eventosQuery
                ->orderBy(
                    'fecha_entrada',
                    'desc'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | CONSTRUIR RESULTADOS
        |--------------------------------------------------------------------------
        */

        $eventos =
            $eventosBase
                ->filter(
                    function ($evento) {

                        return
                            $evento->vehiculo
                            &&
                            $evento->zona;
                    }
                )
                ->map(
                    function ($evento) use (
                        $zonaLocal,
                        $fechaFin
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | FECHA DE ENTRADA
                        |--------------------------------------------------------------------------
                        */

                        $fechaEntrada =
                            $evento->fecha_entrada
                                instanceof Carbon

                                ?

                                $evento->fecha_entrada

                                :

                                Carbon::parse(
                                    $evento->fecha_entrada,
                                    $zonaLocal
                                );


                        /*
                        |--------------------------------------------------------------------------
                        | FECHA DE SALIDA
                        |--------------------------------------------------------------------------
                        */

                        $fechaSalida =
                            null;


                        if (
                            $evento->fecha_salida
                        ) {

                            $fechaSalida =
                                $evento->fecha_salida
                                    instanceof Carbon

                                    ?

                                    $evento->fecha_salida

                                    :

                                    Carbon::parse(
                                        $evento->fecha_salida,
                                        $zonaLocal
                                    );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | DURACIÓN
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $evento->duracion_minutos
                            !==
                            null
                        ) {

                            $duracionMinutos =
                                max(
                                    0,
                                    (int)
                                    $evento->duracion_minutos
                                );

                        } else {

                            $finCalculo =
                                $fechaSalida
                                ??
                                Carbon::now(
                                    $zonaLocal
                                );


                            if (
                                $finCalculo->gt(
                                    $fechaFin
                                )
                            ) {

                                $finCalculo =
                                    $fechaFin->copy();
                            }


                            $segundos =
                                $finCalculo->timestamp
                                -
                                $fechaEntrada->timestamp;


                            $duracionMinutos =
                                (int)
                                floor(
                                    max(
                                        0,
                                        $segundos
                                    )
                                    /
                                    60
                                );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | ESTADO
                        |--------------------------------------------------------------------------
                        */

                        $estado =
                            $fechaSalida
                                ?
                                'Finalizada'
                                :
                                'En zona';


                        return [

                            'id' =>
                                $evento->id,

                            'vehiculo_id' =>
                                $evento->vehiculo_id,

                            'vehiculo' =>
                                $evento
                                    ->vehiculo
                                    ->nombre,

                            'placas' =>
                                $evento
                                    ->vehiculo
                                    ->placas,

                            'flotilla' =>
                                $evento
                                    ->vehiculo
                                    ->flotilla
                                    ?->nombre,

                            'zona_id' =>
                                $evento->zona_id,

                            'zona' =>
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
                                $fechaEntrada,

                            'fecha_salida' =>
                                $fechaSalida,

                            'duracion_minutos' =>
                                $duracionMinutos,

                            'duracion_formateada' =>
                                $this->formatearDuracion(
                                    $duracionMinutos
                                ),

                            'estado' =>
                                $estado,

                            'tipo_evento' =>
                                $evento->tipo_evento,

                            'alerta_generada' =>
                                (bool)
                                $evento->alerta_generada,

                            'alerta_permanencia_generada' =>
                                (bool)
                                $evento
                                    ->alerta_permanencia_generada,

                            'latitud_entrada' =>
                                $evento->latitud_entrada,

                            'longitud_entrada' =>
                                $evento->longitud_entrada,

                            'latitud_salida' =>
                                $evento->latitud_salida,

                            'longitud_salida' =>
                                $evento->longitud_salida,

                        ];
                    }
                )
                ->values();


        /*
        |--------------------------------------------------------------------------
        | TOTAL DE VISITAS
        |--------------------------------------------------------------------------
        */

        $totalVisitas =
            $eventos
                ->count();


        /*
        |--------------------------------------------------------------------------
        | TIEMPO TOTAL
        |--------------------------------------------------------------------------
        */

        $totalMinutos =
            (int)
            $eventos
                ->sum(
                    'duracion_minutos'
                );


        $tiempoTotalFormateado =
            $this->formatearDuracion(
                $totalMinutos
            );


        /*
        |--------------------------------------------------------------------------
        | PROMEDIO
        |--------------------------------------------------------------------------
        */

        $promedioMinutos =
            $totalVisitas > 0

                ?

                (int)
                round(
                    $totalMinutos
                    /
                    $totalVisitas
                )

                :

                0;


        $promedioFormateado =
            $this->formatearDuracion(
                $promedioMinutos
            );


        /*
        |--------------------------------------------------------------------------
        | VISITAS FINALIZADAS / ACTIVAS
        |--------------------------------------------------------------------------
        */

        $visitasFinalizadas =
            $eventos
                ->where(
                    'estado',
                    'Finalizada'
                )
                ->count();


        $visitasActivas =
            $eventos
                ->where(
                    'estado',
                    'En zona'
                )
                ->count();


        /*
        |--------------------------------------------------------------------------
        | ZONAS VISITADAS
        |--------------------------------------------------------------------------
        */

        $zonasVisitadas =
            $eventos
                ->pluck(
                    'zona_id'
                )
                ->unique()
                ->count();


        /*
        |--------------------------------------------------------------------------
        | GRÁFICA POR ZONA
        |--------------------------------------------------------------------------
        */

        $grafica =
            $eventos
                ->groupBy(
                    'zona_id'
                )
                ->map(
                    function ($grupo) {

                        $primerEvento =
                            $grupo->first();


                        $minutos =
                            (int)
                            $grupo
                                ->sum(
                                    'duracion_minutos'
                                );


                        return [

                            'zona_id' =>
                                $primerEvento[
                                    'zona_id'
                                ],

                            'zona' =>
                                $primerEvento[
                                    'zona'
                                ],

                            'color' =>
                                $primerEvento[
                                    'zona_color'
                                ],

                            'visitas' =>
                                $grupo->count(),

                            'minutos' =>
                                $minutos,

                            'tiempo' =>
                                $this->formatearDuracion(
                                    $minutos
                                ),

                        ];
                    }
                )
                ->sortByDesc(
                    'visitas'
                )
                ->values();


        /*
        |--------------------------------------------------------------------------
        | ZONA MÁS VISITADA
        |--------------------------------------------------------------------------
        */

        $zonaMasVisitada =
            $grafica
                ->sortByDesc(
                    'visitas'
                )
                ->first();


        /*
        |--------------------------------------------------------------------------
        | GRÁFICA POR VEHÍCULO
        |--------------------------------------------------------------------------
        */

        $graficaVehiculos =
            $eventos
                ->groupBy(
                    'vehiculo_id'
                )
                ->map(
                    function ($grupo) {

                        $primerEvento =
                            $grupo->first();


                        $minutos =
                            (int)
                            $grupo
                                ->sum(
                                    'duracion_minutos'
                                );


                        return [

                            'vehiculo_id' =>
                                $primerEvento[
                                    'vehiculo_id'
                                ],

                            'vehiculo' =>
                                $primerEvento[
                                    'vehiculo'
                                ],

                            'placas' =>
                                $primerEvento[
                                    'placas'
                                ],

                            'visitas' =>
                                $grupo->count(),

                            'minutos' =>
                                $minutos,

                            'tiempo' =>
                                $this->formatearDuracion(
                                    $minutos
                                ),

                        ];
                    }
                )
                ->sortByDesc(
                    'minutos'
                )
                ->values();


        /*
        |--------------------------------------------------------------------------
        | RETORNAR DATOS
        |--------------------------------------------------------------------------
        */

        return [

            'empresas' =>
                $empresas,

            'flotillas' =>
                $flotillas,

            'vehiculos' =>
                $vehiculos,

            'zonas' =>
                $zonas,

            'eventos' =>
                $eventos,

            'fechaInicio' =>
                $fechaInicio,

            'fechaFin' =>
                $fechaFin,

            'totalVisitas' =>
                $totalVisitas,

            'totalMinutos' =>
                $totalMinutos,

            'tiempoTotalFormateado' =>
                $tiempoTotalFormateado,

            'promedioMinutos' =>
                $promedioMinutos,

            'promedioFormateado' =>
                $promedioFormateado,

            'visitasFinalizadas' =>
                $visitasFinalizadas,

            'visitasActivas' =>
                $visitasActivas,

            'zonasVisitadas' =>
                $zonasVisitadas,

            'zonaMasVisitada' =>
                $zonaMasVisitada,

            'grafica' =>
                $grafica,

            'graficaVehiculos' =>
                $graficaVehiculos,

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | FORMATEAR DURACIÓN
    |--------------------------------------------------------------------------
    */

    private function formatearDuracion(
        int $minutos
    ): string {

        $minutos =
            max(
                0,
                $minutos
            );


        $horas =
            intdiv(
                $minutos,
                60
            );


        $minutosRestantes =
            $minutos
            %
            60;


        if (
            $horas === 0
        ) {

            return
                $minutosRestantes
                .
                ' min';
        }


        if (
            $minutosRestantes === 0
        ) {

            return
                $horas
                .
                ' h';
        }


        return
            $horas
            .
            ' h '
            .
            $minutosRestantes
            .
            ' min';
    }
}