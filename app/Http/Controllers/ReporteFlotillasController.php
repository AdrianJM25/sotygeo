<?php

namespace App\Http\Controllers;

use App\Exports\ReporteFlotillasExport;
use App\Models\Empresa;
use App\Models\Flotilla;
use App\Models\Ubicacion;
use App\Models\Vehiculo;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteFlotillasController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FN.19 - REPORTE DE UTILIZACIÓN DE LA FLOTILLA
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $datos = $this->generarDatosReporte($request);

        return view('reportes.flotillas', $datos);
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORTAR PDF
    |--------------------------------------------------------------------------
    */

    public function exportarPdf(Request $request)
    {
        $datos = $this->generarDatosReporte($request);

        $nombreArchivo =
            'reporte_utilizacion_flotilla_'
            . $datos['fechaInicio']->format('Ymd')
            . '_'
            . $datos['fechaFin']->format('Ymd')
            . '.pdf';

        $pdf = Pdf::loadView(
            'reportes.flotillas-pdf',
            $datos
        )->setPaper('a4', 'landscape');

        return $pdf->download($nombreArchivo);
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORTAR EXCEL
    |--------------------------------------------------------------------------
    */

    public function exportarExcel(Request $request)
    {
        $datos = $this->generarDatosReporte($request);

        $nombreArchivo =
            'reporte_utilizacion_flotilla_'
            . $datos['fechaInicio']->format('Ymd')
            . '_'
            . $datos['fechaFin']->format('Ymd')
            . '.xlsx';

        return Excel::download(
            new ReporteFlotillasExport(
                $datos['resultados'],
                $datos['totalVehiculos'],
                $datos['totalActivos'],
                $datos['totalInactivos'],
                $datos['porcentajeUtilizacion']
            ),
            $nombreArchivo
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GENERAR DATOS DEL REPORTE
    |--------------------------------------------------------------------------
    */

    private function generarDatosReporte(Request $request): array
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | VALIDAR ROL
        |--------------------------------------------------------------------------
        */

        if (!$user->hasAnyRole([
            'Super Administrador',
            'Administrador de Empresa',
        ])) {
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
        ]);

        /*
        |--------------------------------------------------------------------------
        | ZONA HORARIA Y PERIODO
        |--------------------------------------------------------------------------
        */

        $zonaLocal = config(
            'app.timezone',
            'America/Mexico_City'
        );

        $fechaInicio = $request->filled('fecha_inicio')
            ? Carbon::createFromFormat(
                'Y-m-d',
                $request->fecha_inicio,
                $zonaLocal
            )->startOfDay()
            : Carbon::now($zonaLocal)
                ->startOfMonth()
                ->startOfDay();

        $fechaFin = $request->filled('fecha_fin')
            ? Carbon::createFromFormat(
                'Y-m-d',
                $request->fecha_fin,
                $zonaLocal
            )->endOfDay()
            : Carbon::now($zonaLocal)
                ->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | EMPRESAS DISPONIBLES
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Super Administrador')) {

            $empresas = Empresa::query()
                ->orderBy('nombre')
                ->get();

        } else {

            $empresas = Empresa::query()
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

        $empresaId = null;

        if ($user->hasRole('Super Administrador')) {

            if ($request->filled('empresa_id')) {
                $empresaId = (int) $request->empresa_id;
            }

        } else {

            $empresaId = (int) $user->empresa_id;
        }

        /*
        |--------------------------------------------------------------------------
        | FLOTILLAS DISPONIBLES
        |--------------------------------------------------------------------------
        */

        $flotillasQuery = Flotilla::query()
            ->orderBy('nombre');

        if ($empresaId) {

            $flotillasQuery->where(
                'empresa_id',
                $empresaId
            );
        }

        if (!$user->hasRole('Super Administrador')) {

            $flotillasQuery->where(
                'empresa_id',
                $user->empresa_id
            );
        }

        $flotillas = $flotillasQuery->get();

        /*
        |--------------------------------------------------------------------------
        | VEHÍCULOS A ANALIZAR
        |--------------------------------------------------------------------------
        */

        $vehiculosQuery = Vehiculo::query()
            ->with([
                'flotilla',
            ])
            ->whereNotNull('flotilla_id')
            ->orderBy('nombre');

        if (!$user->hasRole('Super Administrador')) {

            $vehiculosQuery->where(
                'empresa_id',
                $user->empresa_id
            );

        } elseif ($empresaId) {

            $vehiculosQuery->where(
                'empresa_id',
                $empresaId
            );
        }

        if ($request->filled('flotilla_id')) {

            $vehiculosQuery->where(
                'flotilla_id',
                $request->flotilla_id
            );
        }

        $vehiculosReporte = $vehiculosQuery->get();

        /*
        |--------------------------------------------------------------------------
        | RESULTADOS POR VEHÍCULO
        |--------------------------------------------------------------------------
        */

        $resultados = collect();

        foreach ($vehiculosReporte as $vehiculo) {

            $ubicaciones = Ubicacion::query()
                ->where(
                    'vehiculo_id',
                    $vehiculo->id
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
                ->get([
                    'id',
                    'vehiculo_id',
                    'velocidad',
                    'en_movimiento',
                    'fecha_gps',
                ]);

            /*
            |--------------------------------------------------------------------------
            | DETECTAR MOVIMIENTO
            |--------------------------------------------------------------------------
            */

            $ubicacionesMovimiento = $ubicaciones
                ->filter(
                    function ($ubicacion) {

                        return
                            (bool) $ubicacion->en_movimiento
                            ||
                            (float) $ubicacion->velocidad > 0;
                    }
                )
                ->values();

            $registrosGps =
                $ubicaciones->count();

            $registrosMovimiento =
                $ubicacionesMovimiento->count();

            $conActividad =
                $registrosMovimiento > 0;

            /*
            |--------------------------------------------------------------------------
            | PORCENTAJE DE ACTIVIDAD GPS
            |--------------------------------------------------------------------------
            */

            $porcentajeActividad =
                $registrosGps > 0
                    ? round(
                        (
                            $registrosMovimiento
                            /
                            $registrosGps
                        )
                        * 100,
                        2
                    )
                    : 0;

            /*
            |--------------------------------------------------------------------------
            | PRIMER Y ÚLTIMO MOVIMIENTO
            |--------------------------------------------------------------------------
            */

            $primerMovimiento =
                $ubicacionesMovimiento
                    ->first()
                    ?->fecha_gps;

            $ultimoMovimiento =
                $ubicacionesMovimiento
                    ->last()
                    ?->fecha_gps;

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

                'flotilla_id' =>
                    $vehiculo->flotilla_id,

                'flotilla' =>
                    $vehiculo->flotilla?->nombre,

                'estado' =>
                    $conActividad
                        ? 'Con actividad'
                        : 'Sin actividad',

                'con_actividad' =>
                    $conActividad,

                'registros_gps' =>
                    $registrosGps,

                'registros_movimiento' =>
                    $registrosMovimiento,

                'porcentaje_actividad' =>
                    $porcentajeActividad,

                'primer_movimiento' =>
                    $primerMovimiento,

                'ultimo_movimiento' =>
                    $ultimoMovimiento,

            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | TOTALES DEL REPORTE
        |--------------------------------------------------------------------------
        */

        $totalVehiculos =
            $resultados->count();

        $totalActivos =
            $resultados
                ->where(
                    'con_actividad',
                    true
                )
                ->count();

        $totalInactivos =
            $totalVehiculos
            -
            $totalActivos;

        $porcentajeUtilizacion =
            $totalVehiculos > 0
                ? round(
                    (
                        $totalActivos
                        /
                        $totalVehiculos
                    )
                    * 100,
                    2
                )
                : 0;

        $totalRegistrosGps =
            (int)
            $resultados
                ->sum(
                    'registros_gps'
                );

        $totalRegistrosMovimiento =
            (int)
            $resultados
                ->sum(
                    'registros_movimiento'
                );

        /*
        |--------------------------------------------------------------------------
        | FLOTILLA SELECCIONADA
        |--------------------------------------------------------------------------
        */

        $flotillaSeleccionada = null;

        if ($request->filled('flotilla_id')) {

            $flotillaSeleccionada =
                $flotillas
                    ->firstWhere(
                        'id',
                        (int) $request->flotilla_id
                    );
        }

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

            'flotillaSeleccionada' =>
                $flotillaSeleccionada,

            'resultados' =>
                $resultados,

            'fechaInicio' =>
                $fechaInicio,

            'fechaFin' =>
                $fechaFin,

            'totalVehiculos' =>
                $totalVehiculos,

            'totalActivos' =>
                $totalActivos,

            'totalInactivos' =>
                $totalInactivos,

            'porcentajeUtilizacion' =>
                $porcentajeUtilizacion,

            'totalRegistrosGps' =>
                $totalRegistrosGps,

            'totalRegistrosMovimiento' =>
                $totalRegistrosMovimiento,

        ];
    }
}