<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GpsController;
use App\Http\Controllers\Api\RutaController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ==========================================
// RECEPCIÓN DE GPS (Traccar / App Cliente)
// ==========================================
// Usamos Route::any para que acepte tanto GET como POST sin errores 405
Route::any('/traccar', [GpsController::class, 'traccar']);

Route::post('/rutas', [RutaController::class, 'store']);