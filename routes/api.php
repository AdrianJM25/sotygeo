<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GpsController;
use App\Http\Controllers\Api\RutaController;
use App\Http\Controllers\Api\ReceptorGpsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ==========================================
// RECEPCIÓN DE GPS (Traccar App / Hardware)
// ==========================================
Route::any('/traccar', [GpsController::class, 'traccar']);
Route::any('/receptor', [ReceptorGpsController::class, 'recibirTramas']);
Route::post('/rutas', [RutaController::class, 'store']);