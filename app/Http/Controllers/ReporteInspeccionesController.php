<?php

namespace App\Http\Controllers;

use App\Exports\ReporteInspeccionesExport;
use App\Models\Conductor;
use App\Models\Empresa;
use App\Models\Vehiculo;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteInspeccionesController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FN.18 - REPORTE HISTÓRICO DE INSPECCIONES VEHICULARES
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $datos =
            $this->generarDatosReporte(
                $request
            );

        return view(
            'reportes.inspecciones',
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

        $pdf =
            Pdf::loadView(
                'reportes.inspecciones-pdf',
                $datos
            )
            ->setPaper(
                'a4',
                'landscape'
            );

        return $pdf->download(
            'reporte_inspecciones_'
            .
            $datos['fechaInicio']->format('Ymd')
            .
            '_'
            .
            $datos['fechaFin']->format('Ymd')
            .
            '.pdf'
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

        return Excel::download(
            new ReporteInspeccionesExport(
                $datos['inspecciones']
            ),
            'reporte_inspecciones_'
            .
            $datos['fechaInicio']->format('Ymd')
            .
            '_'
            .
            $datos['fechaFin']->format('Ymd')
            .
            '.xlsx'
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

            'vehiculo_id' => [
                'nullable',
                'integer',
                'exists:vehiculos,id',
            ],

            'conductor_id' => [
                'nullable',
                'integer',
                'exists:conductores,id',
            ],

            'estado' => [
                'nullable',
                'in:correcto,con_incidencias',
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
        | EMPRESAS
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
        | VEHÍCULOS DISPONIBLES
        |--------------------------------------------------------------------------
        */

        $vehiculosQuery =
            Vehiculo::query()
                ->orderBy(
                    'nombre'
                );


        if (
            $empresaId
        ) {

            $vehiculosQuery
                ->where(
                    'empresa_id',
                    $empresaId
                );
        }


        $vehiculos =
            $vehiculosQuery
                ->get();


        /*
        |--------------------------------------------------------------------------
        | CONDUCTORES DISPONIBLES
        |--------------------------------------------------------------------------
        */

        $conductoresQuery =
            Conductor::query()
                ->orderBy(
                    'nombre'
                );


        if (
            $empresaId
        ) {

            $conductoresQuery
                ->where(
                    'empresa_id',
                    $empresaId
                );
        }


        $conductores =
            $conductoresQuery
                ->get();


        /*
        |--------------------------------------------------------------------------
        | CONSULTAR INSPECCIONES
        |--------------------------------------------------------------------------
        */

        $inspeccionesQuery =
            DB::table(
                'inspecciones as i'
            )
            ->join(
                'vehiculos as v',
                'v.id',
                '=',
                'i.vehiculo_id'
            )
            ->leftJoin(
                'conductores as c',
                'c.id',
                '=',
                'i.conductor_id'
            )
            ->select([

                'i.id',

                'i.vehiculo_id',

                'i.user_id',

                'i.conductor_id',

                'i.fecha_inspeccion',

                'i.resultado_general',

                'i.observaciones',

                'v.nombre as vehiculo_nombre',

                'v.placas as vehiculo_placas',

                'v.empresa_id',

                'c.nombre as conductor_nombre',

                'c.apellido_paterno as conductor_apellido_paterno',

                'c.apellido_materno as conductor_apellido_materno',

            ])
            ->whereBetween(
                'i.fecha_inspeccion',
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
            $empresaId
        ) {

            $inspeccionesQuery
                ->where(
                    'v.empresa_id',
                    $empresaId
                );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO VEHÍCULO
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'vehiculo_id'
            )
        ) {

            $inspeccionesQuery
                ->where(
                    'i.vehiculo_id',
                    $request->vehiculo_id
                );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO CONDUCTOR
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'conductor_id'
            )
        ) {

            $inspeccionesQuery
                ->where(
                    'i.conductor_id',
                    $request->conductor_id
                );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO ESTADO
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'estado'
            )
        ) {

            $inspeccionesQuery
                ->where(
                    'i.resultado_general',
                    $request->estado
                );
        }


        /*
        |--------------------------------------------------------------------------
        | OBTENER INSPECCIONES
        |--------------------------------------------------------------------------
        */

        $inspeccionesBase =
            $inspeccionesQuery
                ->orderBy(
                    'i.fecha_inspeccion',
                    'desc'
                )
                ->orderBy(
                    'i.id',
                    'desc'
                )
                ->get();


        /*
        |--------------------------------------------------------------------------
        | IDS DE INSPECCIONES
        |--------------------------------------------------------------------------
        */

        $inspeccionIds =
            $inspeccionesBase
                ->pluck(
                    'id'
                );


        /*
        |--------------------------------------------------------------------------
        | CONSULTAR INCIDENCIAS
        |--------------------------------------------------------------------------
        */

        $incidenciasPorInspeccion =
            collect();


        if (
            $inspeccionIds->isNotEmpty()
        ) {

            $incidencias =
                DB::table(
                    'detalles_inspeccion as d'
                )
                ->join(
                    'elementos_inspeccion as e',
                    'e.id',
                    '=',
                    'd.elemento_id'
                )
                ->whereIn(
                    'd.inspeccion_id',
                    $inspeccionIds
                )
                ->where(
                    'd.resultado',
                    'incidencia'
                )
                ->select([

                    'd.inspeccion_id',

                    'd.resultado',

                    'd.observaciones',

                    'e.nombre as elemento',

                    'e.categoria',

                ])
                ->orderBy(
                    'e.nombre'
                )
                ->get();


            $incidenciasPorInspeccion =
                $incidencias
                    ->groupBy(
                        'inspeccion_id'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | CONSTRUIR RESULTADOS
        |--------------------------------------------------------------------------
        */

        $inspecciones =
            $inspeccionesBase
                ->map(
                    function ($inspeccion) use (
                        $incidenciasPorInspeccion,
                        $zonaLocal
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | FECHA
                        |--------------------------------------------------------------------------
                        */

                        $fecha =
                            Carbon::parse(
                                $inspeccion
                                    ->fecha_inspeccion,
                                $zonaLocal
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | NOMBRE DEL CONDUCTOR
                        |--------------------------------------------------------------------------
                        */

                        $conductor =
                            trim(
                                implode(
                                    ' ',
                                    array_filter([
                                        $inspeccion
                                            ->conductor_nombre,

                                        $inspeccion
                                            ->conductor_apellido_paterno,

                                        $inspeccion
                                            ->conductor_apellido_materno,
                                    ])
                                )
                            );


                        if (
                            $conductor === ''
                        ) {

                            $conductor =
                                'Sin conductor';
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | INCIDENCIAS
                        |--------------------------------------------------------------------------
                        */

                        $incidencias =
                            $incidenciasPorInspeccion
                                ->get(
                                    $inspeccion->id,
                                    collect()
                                )
                                ->map(
                                    function ($detalle) {

                                        return [

                                            'elemento' =>
                                                $detalle
                                                    ->elemento,

                                            'categoria' =>
                                                $detalle
                                                    ->categoria,

                                            'observaciones' =>
                                                $detalle
                                                    ->observaciones,

                                        ];
                                    }
                                )
                                ->values();


                        /*
                        |--------------------------------------------------------------------------
                        | ESTADO
                        |--------------------------------------------------------------------------
                        */

                        $estadoTexto =
                            $inspeccion
                                ->resultado_general
                            ===
                            'con_incidencias'

                                ?

                                'Con incidencias'

                                :

                                'Correcta';


                        return [

                            'id' =>
                                $inspeccion->id,

                            'vehiculo_id' =>
                                $inspeccion
                                    ->vehiculo_id,

                            'vehiculo' =>
                                $inspeccion
                                    ->vehiculo_nombre,

                            'placas' =>
                                $inspeccion
                                    ->vehiculo_placas,

                            'conductor_id' =>
                                $inspeccion
                                    ->conductor_id,

                            'conductor' =>
                                $conductor,

                            'fecha' =>
                                $fecha,

                            'resultado' =>
                                $inspeccion
                                    ->resultado_general,

                            'resultado_texto' =>
                                $estadoTexto,

                            'observaciones' =>
                                $inspeccion
                                    ->observaciones,

                            'incidencias' =>
                                $incidencias,

                            'total_incidencias' =>
                                $incidencias
                                    ->count(),

                        ];
                    }
                )
                ->values();


        /*
        |--------------------------------------------------------------------------
        | KPIs
        |--------------------------------------------------------------------------
        */

        $totalInspecciones =
            $inspecciones
                ->count();


        $totalCorrectas =
            $inspecciones
                ->where(
                    'resultado',
                    'correcto'
                )
                ->count();


        $totalConIncidencias =
            $inspecciones
                ->where(
                    'resultado',
                    'con_incidencias'
                )
                ->count();


        $vehiculosInspeccionados =
            $inspecciones
                ->pluck(
                    'vehiculo_id'
                )
                ->unique()
                ->count();


        $totalIncidencias =
            (int)
            $inspecciones
                ->sum(
                    'total_incidencias'
                );


        /*
        |--------------------------------------------------------------------------
        | GRÁFICA GENERAL
        |--------------------------------------------------------------------------
        */

        $graficaResultados =
            collect([

                [
                    'nombre' =>
                        'Correctas',

                    'cantidad' =>
                        $totalCorrectas,

                    'porcentaje' =>
                        $totalInspecciones > 0

                            ?

                            round(
                                (
                                    $totalCorrectas
                                    /
                                    $totalInspecciones
                                )
                                *
                                100,
                                1
                            )

                            :

                            0,
                ],

                [
                    'nombre' =>
                        'Con incidencias',

                    'cantidad' =>
                        $totalConIncidencias,

                    'porcentaje' =>
                        $totalInspecciones > 0

                            ?

                            round(
                                (
                                    $totalConIncidencias
                                    /
                                    $totalInspecciones
                                )
                                *
                                100,
                                1
                            )

                            :

                            0,
                ],

            ]);


        /*
        |--------------------------------------------------------------------------
        | INCIDENCIAS POR VEHÍCULO
        |--------------------------------------------------------------------------
        */

        $graficaVehiculos =
            $inspecciones
                ->groupBy(
                    'vehiculo_id'
                )
                ->map(
                    function ($grupo) {

                        $primera =
                            $grupo->first();


                        return [

                            'vehiculo_id' =>
                                $primera[
                                    'vehiculo_id'
                                ],

                            'vehiculo' =>
                                $primera[
                                    'vehiculo'
                                ],

                            'placas' =>
                                $primera[
                                    'placas'
                                ],

                            'inspecciones' =>
                                $grupo->count(),

                            'con_incidencias' =>
                                $grupo
                                    ->where(
                                        'resultado',
                                        'con_incidencias'
                                    )
                                    ->count(),

                            'incidencias' =>
                                (int)
                                $grupo
                                    ->sum(
                                        'total_incidencias'
                                    ),

                        ];
                    }
                )
                ->sortByDesc(
                    'incidencias'
                )
                ->values();


        /*
        |--------------------------------------------------------------------------
        | VEHÍCULO CON MÁS INCIDENCIAS
        |--------------------------------------------------------------------------
        */

        $vehiculoMasIncidencias =
            $graficaVehiculos
                ->sortByDesc(
                    'incidencias'
                )
                ->first();


        /*
        |--------------------------------------------------------------------------
        | RETORNAR DATOS
        |--------------------------------------------------------------------------
        */

        return [

            'empresas' =>
                $empresas,

            'vehiculos' =>
                $vehiculos,

            'conductores' =>
                $conductores,

            'inspecciones' =>
                $inspecciones,

            'fechaInicio' =>
                $fechaInicio,

            'fechaFin' =>
                $fechaFin,

            'totalInspecciones' =>
                $totalInspecciones,

            'totalCorrectas' =>
                $totalCorrectas,

            'totalConIncidencias' =>
                $totalConIncidencias,

            'vehiculosInspeccionados' =>
                $vehiculosInspeccionados,

            'totalIncidencias' =>
                $totalIncidencias,

            'graficaResultados' =>
                $graficaResultados,

            'graficaVehiculos' =>
                $graficaVehiculos,

            'vehiculoMasIncidencias' =>
                $vehiculoMasIncidencias,

        ];
    }
}