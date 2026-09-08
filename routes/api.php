<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UbicacionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ==========================================
// RECEPCIÓN DE GPS (Traccar Client)
// ==========================================
// Usamos "any" para permitir tanto POST como GET sin que dé el error 405
Route::any('/traccar', [UbicacionController::class, 'traccar']);

// Nota: Las rutas antiguas de "ReceptorGpsController" y "activos/en-vivo" 
// ya las eliminamos porque ahora usas Vehiculos y el DashboardApiController en web.php