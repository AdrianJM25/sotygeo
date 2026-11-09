<?php

namespace App\Http\Controllers;

use App\Exports\ReporteAlertasExport;
use App\Models\Alerta;
use App\Models\Empresa;
use App\Models\Flotilla;
use App\Models\Ubicacion;
use App\Models\Vehiculo;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteAlertasController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FN.16 - REPORTE DE EXCESOS DE VELOCIDAD Y ALERTAS
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $datos =
            $this->generarDatosReporte(
                $request
            );

        return view(
            'reportes.alertas',
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
            'reporte_alertas_'
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
                'reportes.alertas-pdf',
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
            'reporte_alertas_'
            .
            $datos['fechaInicio']->format('Ymd')
            .
            '_'
            .
            $datos['fechaFin']->format('Ymd')
            .
            '.xlsx';


        return Excel::download(
            new ReporteAlertasExport(
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

            $flotillasQuery->where(
                'empresa_id',
                $empresaId
            );
        }


        if (
            !$user->hasRole(
                'Super Administrador'
            )
        ) {

            $flotillasQuery->where(
                'empresa_id',
                $user->empresa_id
            );
        }


        $flotillas =
            $flotillasQuery->get();


        /*
        |--------------------------------------------------------------------------
        | VEHÍCULOS DISPONIBLES EN FILTRO
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

                $vehiculosQuery->where(
                    'empresa_id',
                    $empresaId
                );
            }

        } else {

            $vehiculosQuery->where(
                'empresa_id',
                $user->empresa_id
            );
        }


        if (
            $request->filled(
                'flotilla_id'
            )
        ) {

            $vehiculosQuery->where(
                'flotilla_id',
                $request->flotilla_id
            );
        }


        $vehiculos =
            $vehiculosQuery->get();


        /*
        |--------------------------------------------------------------------------
        | VEHÍCULOS DEL REPORTE
        |--------------------------------------------------------------------------
        */

        $vehiculosReporteQuery =
            Vehiculo::query()
                ->with([
                    'flotilla',
                ])
                ->orderBy(
                    'nombre'
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

                $vehiculosReporteQuery
                    ->where(
                        'empresa_id',
                        $empresaId
                    );
            }

        } else {

            $vehiculosReporteQuery
                ->where(
                    'empresa_id',
                    $user->empresa_id
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

            $vehiculosReporteQuery
                ->where(
                    'flotilla_id',
                    $request->flotilla_id
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

            $vehiculosReporteQuery
                ->where(
                    'id',
                    $request->vehiculo_id
                );
        }


        $vehiculosReporte =
            $vehiculosReporteQuery
                ->get();


        $vehiculosIds =
            $vehiculosReporte
                ->pluck(
                    'id'
                );


        /*
        |--------------------------------------------------------------------------
        | COLECCIÓN GENERAL DE EVENTOS
        |--------------------------------------------------------------------------
        */

        $eventos =
            collect();


        /*
        |--------------------------------------------------------------------------
        | EXCESOS DE VELOCIDAD
        |--------------------------------------------------------------------------
        */

        if (
            $vehiculosIds->isNotEmpty()
        ) {

            $ubicaciones =
                Ubicacion::query()
                    ->with([
                        'vehiculo.flotilla',
                    ])
                    ->whereIn(
                        'vehiculo_id',
                        $vehiculosIds
                    )
                    ->whereBetween(
                        'fecha_gps',
                        [
                            $fechaInicio,
                            $fechaFin,
                        ]
                    )
                    ->where(
                        'velocidad',
                        '>',
                        0
                    )
                    ->orderBy(
                        'fecha_gps',
                        'desc'
                    )
                    ->get();


            foreach (
                $ubicaciones
                as $ubicacion
            ) {

                $vehiculo =
                    $ubicacion->vehiculo;


                if (
                    !$vehiculo
                ) {

                    continue;
                }


                $limite =
                    (float)
                    (
                        $vehiculo
                            ->limite_velocidad_kmh
                        ??
                        90
                    );


                $velocidad =
                    (float)
                    $ubicacion->velocidad;


                if (
                    $velocidad
                    <=
                    $limite
                ) {

                    continue;
                }


                $excedente =
                    round(
                        $velocidad
                        -
                        $limite,
                        2
                    );


                $fechaEvento =
                    $ubicacion->fecha_gps
                        instanceof Carbon

                        ?

                        $ubicacion->fecha_gps

                        :

                        Carbon::parse(
                            $ubicacion->fecha_gps,
                            $zonaLocal
                        );


                $eventos->push([

                    'id' =>
                        'gps-'
                        .
                        $ubicacion->id,

                    'origen' =>
                        'gps',

                    'tipo' =>
                        'Exceso de velocidad',

                    'vehiculo_id' =>
                        $vehiculo->id,

                    'vehiculo' =>
                        $vehiculo->nombre,

                    'placas' =>
                        $vehiculo->placas,

                    'flotilla' =>
                        $vehiculo
                            ->flotilla
                            ?->nombre,

                    'fecha' =>
                        $fechaEvento,

                    'velocidad' =>
                        round(
                            $velocidad,
                            2
                        ),

                    'limite' =>
                        round(
                            $limite,
                            2
                        ),

                    'excedente' =>
                        $excedente,

                    'mensaje' =>
                        'El vehículo registró '
                        .
                        number_format(
                            $velocidad,
                            2
                        )
                        .
                        ' km/h, superando el límite de '
                        .
                        number_format(
                            $limite,
                            2
                        )
                        .
                        ' km/h.',

                    'leida' =>
                        null,

                    'latitud' =>
                        $ubicacion->latitud,

                    'longitud' =>
                        $ubicacion->longitud,

                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | DEMÁS ALERTAS REGISTRADAS
        |--------------------------------------------------------------------------
        */

        if (
            $vehiculosIds->isNotEmpty()
        ) {

            $alertas =
                Alerta::query()
                    ->with([
                        'vehiculo.flotilla',
                    ])
                    ->whereIn(
                        'vehiculo_id',
                        $vehiculosIds
                    )
                    ->whereBetween(
                        'created_at',
                        [
                            $fechaInicio,
                            $fechaFin,
                        ]
                    )
                    ->orderBy(
                        'created_at',
                        'desc'
                    )
                    ->get();


            foreach (
                $alertas
                as $alerta
            ) {

                $vehiculo =
                    $alerta->vehiculo;


                if (
                    !$vehiculo
                ) {

                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | EVITAR DUPLICAR EXCESOS
                |--------------------------------------------------------------------------
                */

                $tipoNormalizado =
                    mb_strtolower(
                        (string)
                        $alerta->tipo
                    );


                if (
                    str_contains(
                        $tipoNormalizado,
                        'velocidad'
                    )
                ) {

                    continue;
                }


                $fechaEvento =
                    $alerta->created_at
                        instanceof Carbon

                        ?

                        $alerta->created_at

                        :

                        Carbon::parse(
                            $alerta->created_at,
                            $zonaLocal
                        );


                $eventos->push([

                    'id' =>
                        'alerta-'
                        .
                        $alerta->id,

                    'origen' =>
                        'alerta',

                    'tipo' =>
                        $this->formatearTipoAlerta(
                            $alerta->tipo
                        ),

                    'vehiculo_id' =>
                        $vehiculo->id,

                    'vehiculo' =>
                        $vehiculo->nombre,

                    'placas' =>
                        $vehiculo->placas,

                    'flotilla' =>
                        $vehiculo
                            ->flotilla
                            ?->nombre,

                    'fecha' =>
                        $fechaEvento,

                    'velocidad' =>
                        null,

                    'limite' =>
                        null,

                    'excedente' =>
                        null,

                    'mensaje' =>
                        $alerta->mensaje,

                    'leida' =>
                        (bool)
                        $alerta->leida,

                    'latitud' =>
                        null,

                    'longitud' =>
                        null,

                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ORDENAR EVENTOS
        |--------------------------------------------------------------------------
        */

        $eventos =
            $eventos
                ->sortByDesc(
                    function ($evento) {

                        return
                            $evento[
                                'fecha'
                            ]
                            ->timestamp;
                    }
                )
                ->values();


        /*
        |--------------------------------------------------------------------------
        | TOTALES
        |--------------------------------------------------------------------------
        */

        $totalEventos =
            $eventos->count();


        $totalExcesos =
            $eventos
                ->where(
                    'origen',
                    'gps'
                )
                ->count();


        $totalAlertas =
            $eventos
                ->where(
                    'origen',
                    'alerta'
                )
                ->count();


        $alertasNoLeidas =
            $eventos
                ->filter(
                    function ($evento) {

                        return
                            $evento[
                                'origen'
                            ]
                            ===
                            'alerta'
                            &&
                            $evento[
                                'leida'
                            ]
                            ===
                            false;
                    }
                )
                ->count();


        /*
        |--------------------------------------------------------------------------
        | VELOCIDAD MÁXIMA
        |--------------------------------------------------------------------------
        */

        $velocidadMaxima =
            (float)
            (
                $eventos
                    ->where(
                        'origen',
                        'gps'
                    )
                    ->max(
                        'velocidad'
                    )
                ??
                0
            );


        /*
        |--------------------------------------------------------------------------
        | GRÁFICA POR VEHÍCULO
        |--------------------------------------------------------------------------
        */

        $grafica =
            $vehiculosReporte
                ->map(
                    function ($vehiculo) use (
                        $eventos
                    ) {

                        $eventosVehiculo =
                            $eventos
                                ->where(
                                    'vehiculo_id',
                                    $vehiculo->id
                                );


                        return [

                            'vehiculo' =>
                                $vehiculo->nombre,

                            'placas' =>
                                $vehiculo->placas,

                            'total' =>
                                $eventosVehiculo
                                    ->count(),

                            'excesos' =>
                                $eventosVehiculo
                                    ->where(
                                        'origen',
                                        'gps'
                                    )
                                    ->count(),

                            'alertas' =>
                                $eventosVehiculo
                                    ->where(
                                        'origen',
                                        'alerta'
                                    )
                                    ->count(),

                        ];
                    }
                )
                ->values();


        /*
        |--------------------------------------------------------------------------
        | TIPOS DE ALERTA
        |--------------------------------------------------------------------------
        */

        $tiposAlerta =
            $eventos
                ->where(
                    'origen',
                    'alerta'
                )
                ->pluck(
                    'tipo'
                )
                ->unique()
                ->sort()
                ->values();


        /*
        |--------------------------------------------------------------------------
        | DATOS PARA VISTA, PDF Y EXCEL
        |--------------------------------------------------------------------------
        */

        return [

            'empresas' =>
                $empresas,

            'flotillas' =>
                $flotillas,

            'vehiculos' =>
                $vehiculos,

            'eventos' =>
                $eventos,

            'fechaInicio' =>
                $fechaInicio,

            'fechaFin' =>
                $fechaFin,

            'totalEventos' =>
                $totalEventos,

            'totalExcesos' =>
                $totalExcesos,

            'totalAlertas' =>
                $totalAlertas,

            'alertasNoLeidas' =>
                $alertasNoLeidas,

            'velocidadMaxima' =>
                $velocidadMaxima,

            'grafica' =>
                $grafica,

            'tiposAlerta' =>
                $tiposAlerta,

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | FORMATEAR TIPO DE ALERTA
    |--------------------------------------------------------------------------
    */

    private function formatearTipoAlerta(
        ?string $tipo
    ): string {

        if (
            !$tipo
        ) {

            return 'Alerta';
        }


        $tipo =
            str_replace(
                [
                    '_',
                    '-',
                ],
                ' ',
                $tipo
            );


        return
            mb_convert_case(
                $tipo,
                MB_CASE_TITLE,
                'UTF-8'
            );
    }
}