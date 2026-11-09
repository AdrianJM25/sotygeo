<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DispositivoController;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\FlotillaController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ConductorController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\EventoGeocercaController;
use App\Http\Controllers\BusquedaModuloController;
use App\Http\Controllers\EnlaceRastreoController;
use App\Http\Controllers\InspeccionController;
use App\Http\Controllers\ReporteKilometrajeController;
use App\Http\Controllers\ReporteAlertasController;
use App\Http\Controllers\ReporteZonasController;
use App\Http\Controllers\ReporteInspeccionesController;
use App\Http\Controllers\ReporteFlotillasController;
use App\Http\Controllers\ReporteConductoresController;
use App\Http\Controllers\BackupController;

use App\Http\Controllers\Api\DashboardApiController;


// ==========================================
// LANDING PAGE
// ==========================================

Route::get('/', function () {
    return view('welcome');
});


// ==========================================
// DASHBOARD
// ==========================================

Route::get('/dashboard', function () {
    return view('dashboard');
})
    ->middleware([
        'auth',
        'verified',
        'permission:ver mapas',
    ])
    ->name('dashboard');


// ==========================================
// RUTAS PROTEGIDAS
// ==========================================

Route::middleware('auth')
    ->group(function () {


        // ==========================================
        // BUSCADOR DE MÓDULOS
        // ==========================================

        Route::get(
            '/buscar-modulos',
            [
                BusquedaModuloController::class,
                'index',
            ]
        )->name(
            'modulos.buscar'
        );


        // ==========================================
        // API INTERNA
        // DASHBOARD Y MAPAS
        // ==========================================

        Route::middleware([
            'permission:ver mapas',
        ])
            ->prefix('api-interno')
            ->name('api.')
            ->group(function () {


                Route::get(
                    '/vehiculos/en-vivo',
                    [
                        DashboardApiController::class,
                        'vehiculosEnVivo',
                    ]
                )->name(
                    'vehiculos.en-vivo'
                );


                Route::get(
                    '/zonas/en-vivo',
                    [
                        DashboardApiController::class,
                        'zonasEnVivo',
                    ]
                )->name(
                    'zonas.en-vivo'
                );


                Route::get(
                    '/alertas/recientes',
                    [
                        DashboardApiController::class,
                        'alertasRecientes',
                    ]
                )->name(
                    'alertas.recientes'
                );


                Route::get(
                    '/geocercas/estado',
                    [
                        DashboardApiController::class,
                        'estadoGeocercas',
                    ]
                )->name(
                    'geocercas.estado'
                );

            });


        // ==========================================
        // PERFIL DE USUARIO
        // ==========================================

        Route::get(
            '/profile',
            [
                ProfileController::class,
                'edit',
            ]
        )->name(
            'profile.edit'
        );


        Route::patch(
            '/profile',
            [
                ProfileController::class,
                'update',
            ]
        )->name(
            'profile.update'
        );


        Route::delete(
            '/profile',
            [
                ProfileController::class,
                'destroy',
            ]
        )->name(
            'profile.destroy'
        );


        // ==========================================
        // EMPRESAS
        // ==========================================

        Route::middleware([
            'permission:gestionar empresas',
        ])
            ->group(function () {

                Route::resource(
                    'empresas',
                    EmpresaController::class
                );

            });


        // ==========================================
        // USUARIOS
        // ==========================================

        Route::middleware([
            'permission:gestionar usuarios',
        ])
            ->group(function () {


                Route::resource(
                    'usuarios',
                    UserController::class
                );


                Route::delete(
                    'usuarios/{usuario}/eliminar',
                    [
                        UserController::class,
                        'eliminar',
                    ]
                )->name(
                    'usuarios.eliminar'
                );

            });


        // ==========================================
        // FLOTILLAS
        // ==========================================

        Route::middleware([
            'permission:gestionar flotillas',
        ])
            ->group(function () {


                Route::resource(
                    'flotillas',
                    FlotillaController::class
                );

            });


        // ==========================================
        // CONDUCTORES
        // ==========================================

        Route::middleware([
            'permission:gestionar conductores',
        ])
            ->group(function () {


                // ======================================
                // LISTADO
                // ======================================

                Route::get(
                    '/conductores',
                    [
                        ConductorController::class,
                        'index',
                    ]
                )->name(
                    'conductores.index'
                );


                // ======================================
                // REGISTRAR
                // ======================================

                Route::post(
                    '/conductores',
                    [
                        ConductorController::class,
                        'store',
                    ]
                )->name(
                    'conductores.store'
                );


                // ======================================
                // ACTUALIZAR
                // ======================================

                Route::put(
                    '/conductores/{conductor}',
                    [
                        ConductorController::class,
                        'update',
                    ]
                )->name(
                    'conductores.update'
                );


                // ======================================
                // ACTIVAR / DESACTIVAR
                // ======================================

                Route::patch(
                    '/conductores/{conductor}/estado',
                    [
                        ConductorController::class,
                        'cambiarEstado',
                    ]
                )->name(
                    'conductores.estado'
                );


                // ======================================
                // VER LICENCIA PRIVADA
                // ======================================

                Route::get(
                    '/conductores/{conductor}/licencia/{lado}',
                    [
                        ConductorController::class,
                        'verLicencia',
                    ]
                )
                    ->where(
                        'lado',
                        'frente|reverso'
                    )
                    ->name(
                        'conductores.licencia'
                    );

            });


        // ==========================================
        // VEHÍCULOS
        // ==========================================

        Route::middleware([
            'permission:gestionar vehiculos',
        ])
            ->group(function () {


                Route::resource(
                    'vehiculos',
                    VehiculoController::class
                );


                // ======================================
                // ACTUALIZAR CORTE DE RUTA
                // ======================================

                Route::patch(
                    '/vehiculos/{vehiculo}/actualizar-corte',
                    [
                        VehiculoController::class,
                        'actualizarCorteRuta',
                    ]
                )->name(
                    'vehiculos.actualizar-corte'
                );

            });


        // ==========================================
        // HISTORIAL DE RUTAS
        // ==========================================

        Route::middleware([
            'permission:ver historial de rutas',
        ])
            ->group(function () {


                // ======================================
                // LISTADO DE VEHÍCULOS DISPONIBLES
                // ======================================

                Route::get(
                    '/historial-rutas',
                    [
                        RutaController::class,
                        'index',
                    ]
                )->name(
                    'rutas.index'
                );


                // ======================================
                // REPRODUCTOR DE RUTA
                // ======================================

                Route::get(
                    '/vehiculos/{vehiculo}/ruta',
                    [
                        RutaController::class,
                        'historial',
                    ]
                )->name(
                    'vehiculos.ruta'
                );

            });


        // ==========================================
        // DISPOSITIVOS GPS
        // ==========================================

        Route::middleware([
            'permission:gestionar dispositivos',
        ])
            ->group(function () {


                Route::resource(
                    'dispositivos',
                    DispositivoController::class
                )->only([
                    'index',
                    'store',
                    'update',
                    'destroy',
                ]);


                // ======================================
                // ACTIVAR / DESACTIVAR GPS
                // ======================================

                Route::patch(
                    '/dispositivos/{dispositivo}/estado',
                    [
                        DispositivoController::class,
                        'cambiarEstado',
                    ]
                )->name(
                    'dispositivos.estado'
                );

            });


        // ==========================================
        // ZONAS / GEOCERCAS
        // ==========================================

        Route::middleware([
            'permission:gestionar zonas',
        ])
            ->group(function () {


                Route::resource(
                    'zonas',
                    ZonaController::class
                );


                // ======================================
                // HISTORIAL DE GEOCERCAS
                // ======================================

                Route::get(
                    '/geocercas/historial',
                    [
                        EventoGeocercaController::class,
                        'index',
                    ]
                )->name(
                    'geocercas.historial'
                );

            });


        // ==========================================
        // INSPECCIONES VEHICULARES
        // ==========================================

        Route::middleware([
            'permission:gestionar inspecciones',
        ])
            ->group(function () {


                // ======================================
                // LISTADO / HISTORIAL
                // ======================================

                Route::get(
                    '/inspecciones',
                    [
                        InspeccionController::class,
                        'index',
                    ]
                )->name(
                    'inspecciones.index'
                );


                // ======================================
                // REGISTRAR INSPECCIÓN
                // ======================================

                Route::post(
                    '/inspecciones',
                    [
                        InspeccionController::class,
                        'store',
                    ]
                )->name(
                    'inspecciones.store'
                );

            });


        // ==========================================
        // REPORTES
        // ==========================================

        Route::middleware([
            'permission:gestionar reportes',
        ])
            ->group(function () {


                // ======================================
                // FN.15 - KILOMETRAJE Y RECORRIDOS
                // ======================================

                Route::get(
                    '/reportes/kilometraje',
                    [
                        ReporteKilometrajeController::class,
                        'index',
                    ]
                )->name(
                    'reportes.kilometraje'
                );


                // ======================================
                // FN.15 - EXPORTAR PDF
                // ======================================

                Route::get(
                    '/reportes/kilometraje/pdf',
                    [
                        ReporteKilometrajeController::class,
                        'exportarPdf',
                    ]
                )->name(
                    'reportes.kilometraje.pdf'
                );


                // ======================================
                // FN.15 - EXPORTAR EXCEL
                // ======================================

                Route::get(
                    '/reportes/kilometraje/excel',
                    [
                        ReporteKilometrajeController::class,
                        'exportarExcel',
                    ]
                )->name(
                    'reportes.kilometraje.excel'
                );


                // ======================================
                // FN.16 - EXCESOS DE VELOCIDAD Y ALERTAS
                // ======================================

                Route::get(
                    '/reportes/alertas',
                    [
                        ReporteAlertasController::class,
                        'index',
                    ]
                )->name(
                    'reportes.alertas'
                );


                // ======================================
                // FN.16 - EXPORTAR PDF
                // ======================================

                Route::get(
                    '/reportes/alertas/pdf',
                    [
                        ReporteAlertasController::class,
                        'exportarPdf',
                    ]
                )->name(
                    'reportes.alertas.pdf'
                );


                // ======================================
                // FN.16 - EXPORTAR EXCEL
                // ======================================

                Route::get(
                    '/reportes/alertas/excel',
                    [
                        ReporteAlertasController::class,
                        'exportarExcel',
                    ]
                )->name(
                    'reportes.alertas.excel'
                );


                // ======================================
                // FN.17 - VISITAS Y TIEMPOS EN ZONAS
                // ======================================

                Route::get(
                    '/reportes/zonas',
                    [
                        ReporteZonasController::class,
                        'index',
                    ]
                )->name(
                    'reportes.zonas'
                );


                // ======================================
                // FN.17 - EXPORTAR PDF
                // ======================================

                Route::get(
                    '/reportes/zonas/pdf',
                    [
                        ReporteZonasController::class,
                        'exportarPdf',
                    ]
                )->name(
                    'reportes.zonas.pdf'
                );


                // ======================================
                // FN.17 - EXPORTAR EXCEL
                // ======================================

                Route::get(
                    '/reportes/zonas/excel',
                    [
                        ReporteZonasController::class,
                        'exportarExcel',
                    ]
                )->name(
                    'reportes.zonas.excel'
                );


                // ======================================
                // FN.18 - HISTÓRICO DE INSPECCIONES
                // ======================================

                Route::get(
                    '/reportes/inspecciones',
                    [
                        ReporteInspeccionesController::class,
                        'index',
                    ]
                )->name(
                    'reportes.inspecciones'
                );


                // ======================================
                // FN.18 - EXPORTAR PDF
                // ======================================

                Route::get(
                    '/reportes/inspecciones/pdf',
                    [
                        ReporteInspeccionesController::class,
                        'exportarPdf',
                    ]
                )->name(
                    'reportes.inspecciones.pdf'
                );


                // ======================================
                // FN.18 - EXPORTAR EXCEL
                // ======================================

                Route::get(
                    '/reportes/inspecciones/excel',
                    [
                        ReporteInspeccionesController::class,
                        'exportarExcel',
                    ]
                )->name(
                    'reportes.inspecciones.excel'
                );


                // ======================================
                // FN.19 - UTILIZACIÓN DE LA FLOTILLA
                // ======================================

                Route::get(
                    '/reportes/flotillas',
                    [
                        ReporteFlotillasController::class,
                        'index',
                    ]
                )->name(
                    'reportes.flotillas'
                );


                // ======================================
                // FN.19 - EXPORTAR PDF
                // ======================================

                Route::get(
                    '/reportes/flotillas/pdf',
                    [
                        ReporteFlotillasController::class,
                        'exportarPdf',
                    ]
                )->name(
                    'reportes.flotillas.pdf'
                );


                // ======================================
                // FN.19 - EXPORTAR EXCEL
                // ======================================

                Route::get(
                    '/reportes/flotillas/excel',
                    [
                        ReporteFlotillasController::class,
                        'exportarExcel',
                    ]
                )->name(
                    'reportes.flotillas.excel'
                );


                // ======================================
                // FN.20 - ACTIVIDAD POR CONDUCTOR
                // ======================================

                Route::get(
                    '/reportes/conductores',
                    [
                        ReporteConductoresController::class,
                        'index',
                    ]
                )->name(
                    'reportes.conductores'
                );


                // ======================================
                // FN.20 - EXPORTAR PDF
                // ======================================

                Route::get(
                    '/reportes/conductores/pdf',
                    [
                        ReporteConductoresController::class,
                        'exportarPdf',
                    ]
                )->name(
                    'reportes.conductores.pdf'
                );


                // ======================================
                // FN.20 - EXPORTAR EXCEL
                // ======================================

                Route::get(
                    '/reportes/conductores/excel',
                    [
                        ReporteConductoresController::class,
                        'exportarExcel',
                    ]
                )->name(
                    'reportes.conductores.excel'
                );

            });


        // ==========================================
        // FN.21 - RESPALDO DE BASE DE DATOS
        // ==========================================

        Route::get(
            '/respaldos',
            [
                BackupController::class,
                'index',
            ]
        )->name(
            'backups.index'
        );


        Route::post(
            '/respaldos',
            [
                BackupController::class,
                'store',
            ]
        )->name(
            'backups.store'
        );


        Route::get(
            '/respaldos/{archivo}/descargar',
            [
                BackupController::class,
                'download',
            ]
        )->name(
            'backups.download'
        );


        // ==========================================
        // ENLACES DE SEGUIMIENTO TEMPORAL
        // ==========================================

        Route::middleware([
            'permission:gestionar enlaces temporales',
        ])
            ->group(function () {


                // ======================================
                // LISTADO
                // ======================================

                Route::get(
                    '/enlaces-rastreo',
                    [
                        EnlaceRastreoController::class,
                        'index',
                    ]
                )->name(
                    'enlaces.index'
                );


                // ======================================
                // GENERAR ENLACE
                // ======================================

                Route::post(
                    '/enlaces-rastreo',
                    [
                        EnlaceRastreoController::class,
                        'store',
                    ]
                )->name(
                    'enlaces.store'
                );


                // ======================================
                // CANCELAR / ELIMINAR ENLACE
                // ======================================

                Route::delete(
                    '/enlaces-rastreo/{enlace}',
                    [
                        EnlaceRastreoController::class,
                        'destroy',
                    ]
                )->name(
                    'enlaces.destroy'
                );

            });

    });


// ==========================================
// SEGUIMIENTO PÚBLICO
// ==========================================
//
// Estas rutas no utilizan middleware auth.
// Una persona externa puede abrir el enlace
// temporal sin iniciar sesión.
//

Route::get(
    '/seguimiento/{token}',
    [
        EnlaceRastreoController::class,
        'publico',
    ]
)->name(
    'seguimiento.publico'
);


Route::get(
    '/seguimiento/{token}/ubicacion',
    [
        EnlaceRastreoController::class,
        'ubicacionPublica',
    ]
)->name(
    'seguimiento.ubicacion'
);


// ==========================================
// RUTAS DE AUTENTICACIÓN
// ==========================================

require __DIR__.'/auth.php';


// ==========================================
// BLOQUEAR REGISTRO PÚBLICO
// ==========================================
//
// Se coloca después de auth.php para
// sobrescribir las rutas públicas
// de registro de Laravel Breeze.
//

Route::match(
    [
        'get',
        'post',
    ],
    '/register',
    function () {

        return redirect('/');

    }
);