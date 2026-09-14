<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GpsController;
use App\Http\Controllers\Api\RutaController;
use App\Http\Controllers\Api\ReceptorGpsController;
use App\Http\Controllers\Api\DashboardApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ==========================================
// RECEPCIÓN DE GPS (Traccar App / Hardware)
// ==========================================

// Ruta original para la app cliente
Route::any('/traccar', [GpsController::class, 'traccar']);

// Nueva ruta unificada para el hardware físico (TCP parseado a HTTP)
Route::any('/receptor', [ReceptorGpsController::class, 'recibirTramas']);

// ==========================================
// ENDPOINTS DEL DASHBOARD EN VIVO
// ==========================================
// Nota: Si usas auth:sanctum para proteger tu API, puedes envolver este grupo. 
// Por ahora los dejamos abiertos para que tu fetch() local pase sin problemas.
Route::name('api.')->group(function () {
    Route::get('/vehiculos/en-vivo', [DashboardApiController::class, 'vehiculosEnVivo'])->name('vehiculos.en-vivo');
    Route::get('/zonas/en-vivo', [DashboardApiController::class, 'zonasEnVivo'])->name('zonas.en-vivo');
    Route::get('/alertas/recientes', [DashboardApiController::class, 'alertasRecientes'])->name('alertas.recientes');
});

// ==========================================
// RUTAS ADICIONALES
// ==========================================
Route::post('/rutas', [RutaController::class, 'store']);