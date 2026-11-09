<?php

namespace App\Http\Controllers;

use App\Exports\ReporteKilometrajeExport;
use App\Models\Empresa;
use App\Models\Flotilla;
use App\Models\Ubicacion;
use App\Models\Vehiculo;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReporteKilometrajeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | REPORTE DE KILOMETRAJE Y RECORRIDOS
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $datos =
            $this->generarDatosReporte(
                $request
            );


        return view(
            'reportes.kilometraje',
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
            'reporte_kilometraje_'
            .
            $datos[
                'fechaInicio'
            ]
            ->format(
                'Ymd'
            )
            .
            '_'
            .
            $datos[
                'fechaFin'
            ]
            ->format(
                'Ymd'
            )
            .
            '.pdf';


        $pdf =
            Pdf::loadView(
                'reportes.kilometraje-pdf',
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
            'reporte_kilometraje_'
            .
            $datos[
                'fechaInicio'
            ]
            ->format(
                'Ymd'
            )
            .
            '_'
            .
            $datos[
                'fechaFin'
            ]
            ->format(
                'Ymd'
            )
            .
            '.xlsx';


        return Excel::download(
            new ReporteKilometrajeExport(
                $datos[
                    'resultados'
                ]
            ),
            $nombreArchivo
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERAR DATOS DEL REPORTE
    |--------------------------------------------------------------------------
    |
    | Este método se reutiliza para:
    |
    | - Vista web
    | - PDF
    | - Excel
    |
    | De esta manera los tres resultados utilizan
    | exactamente los mismos filtros y cálculos.
    |
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

        $consultaVehiculos =
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
            !$user->hasRole(
                'Super Administrador'
            )
        ) {

            $consultaVehiculos->where(
                'empresa_id',
                $user->empresa_id
            );

        } elseif (
            $empresaId
        ) {

            $consultaVehiculos->where(
                'empresa_id',
                $empresaId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO FLOTILLA
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'flotilla_id'
            )
        ) {

            $consultaVehiculos->where(
                'flotilla_id',
                $request->flotilla_id
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

            $consultaVehiculos->where(
                'id',
                $request->vehiculo_id
            );
        }


        $vehiculosReporte =
            $consultaVehiculos->get();


        /*
        |--------------------------------------------------------------------------
        | RESULTADOS
        |--------------------------------------------------------------------------
        */

        $resultados =
            collect();


        foreach (
            $vehiculosReporte
            as $vehiculo
        ) {

            /*
            |--------------------------------------------------------------------------
            | UBICACIONES GPS
            |--------------------------------------------------------------------------
            */

            $ubicaciones =
                Ubicacion::query()
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
            | DISTANCIA TOTAL
            |--------------------------------------------------------------------------
            */

            $distanciaKm =
                $this->calcularDistanciaTotal(
                    $ubicaciones
                );


            /*
            |--------------------------------------------------------------------------
            | RECORRIDOS REGISTRADOS
            |--------------------------------------------------------------------------
            */

            $recorridos =
                DB::table(
                    'rutas'
                )
                    ->where(
                        'vehiculo_id',
                        $vehiculo->id
                    )
                    ->whereBetween(
                        'fecha_inicio',
                        [
                            $fechaInicio,
                            $fechaFin,
                        ]
                    )
                    ->count();


            /*
            |--------------------------------------------------------------------------
            | PRIMER Y ÚLTIMO REPORTE
            |--------------------------------------------------------------------------
            */

            $primeraUbicacion =
                $ubicaciones->first();


            $ultimaUbicacion =
                $ubicaciones->last();


            /*
            |--------------------------------------------------------------------------
            | AGREGAR RESULTADO
            |--------------------------------------------------------------------------
            */

            $resultados->push([
                'vehiculo_id' =>
                    $vehiculo->id,

                'vehiculo' =>
                    $vehiculo->nombre,

                'placas' =>
                    $vehiculo->placas,

                'marca' =>
                    $vehiculo->marca,

                'modelo' =>
                    $vehiculo->modelo,

                'flotilla' =>
                    $vehiculo
                        ->flotilla
                        ?->nombre,

                'kilometros' =>
                    round(
                        $distanciaKm,
                        2
                    ),

                'recorridos' =>
                    $recorridos,

                'registros_gps' =>
                    $ubicaciones->count(),

                'primer_reporte' =>
                    $primeraUbicacion
                        ?->fecha_gps,

                'ultimo_reporte' =>
                    $ultimaUbicacion
                        ?->fecha_gps,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | TOTALES
        |--------------------------------------------------------------------------
        */

        $totalKilometros =
            round(
                $resultados->sum(
                    'kilometros'
                ),
                2
            );


        $totalRecorridos =
            $resultados->sum(
                'recorridos'
            );


        $totalRegistros =
            $resultados->sum(
                'registros_gps'
            );


        /*
        |--------------------------------------------------------------------------
        | GRÁFICA
        |--------------------------------------------------------------------------
        */

        $grafica =
            $resultados
                ->map(
                    function ($resultado) {

                        return [
                            'vehiculo' =>
                                $resultado[
                                    'vehiculo'
                                ],

                            'kilometros' =>
                                $resultado[
                                    'kilometros'
                                ],
                        ];
                    }
                )
                ->values();


        /*
        |--------------------------------------------------------------------------
        | DATOS PARA VISTAS Y EXPORTACIONES
        |--------------------------------------------------------------------------
        */

        return [
            'empresas' =>
                $empresas,

            'flotillas' =>
                $flotillas,

            'vehiculos' =>
                $vehiculos,

            'resultados' =>
                $resultados,

            'fechaInicio' =>
                $fechaInicio,

            'fechaFin' =>
                $fechaFin,

            'totalKilometros' =>
                $totalKilometros,

            'totalRecorridos' =>
                $totalRecorridos,

            'totalRegistros' =>
                $totalRegistros,

            'grafica' =>
                $grafica,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULAR DISTANCIA TOTAL
    |--------------------------------------------------------------------------
    */

    private function calcularDistanciaTotal(
        Collection $ubicaciones
    ): float {

        if (
            $ubicaciones->count()
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
            as $ubicacion
        ) {

            if (
                !$anterior
            ) {

                $anterior =
                    $ubicacion;

                continue;
            }


            $distanciaTotal +=
                $this->distanciaEntrePuntos(
                    (float)
                    $anterior->latitud,

                    (float)
                    $anterior->longitud,

                    (float)
                    $ubicacion->latitud,

                    (float)
                    $ubicacion->longitud
                );


            $anterior =
                $ubicacion;
        }


        return $distanciaTotal;
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
                $dLat / 2
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
                    $dLon / 2
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
                    1 - $a
                )
            );


        return
            $radioTierra
            *
            $c;
    }
}