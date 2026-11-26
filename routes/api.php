<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\Api\GpsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ==========================================
// RECEPCIÓN DE GPS (Traccar Client)
// ==========================================

Route::any('/traccar', [GpsController::class, 'traccar']);

Route::any('/gps-receptor', [GpsController::class, 'traccar']);

Route::post('/rutas', [RutaController::class, 'store']);