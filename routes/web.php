<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DispositivoController;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\FlotillaController;
use App\Http\Controllers\EmpresaController; // <-- Nuevo controlador maestro
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GpsController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\RutaController; // <-- AQUÍ ESTABA EL ERROR: Apunta a la raíz, no a Api

// Esta es la URL que pondrás en tu celular
Route::get('/traccar', [GpsController::class, 'traccar']);


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    
Route::get('/api/vehiculos/en-vivo', [DashboardApiController::class, 'vehiculosEnVivo'])->name('api.vehiculos.en-vivo');
Route::get('/api/zonas-en-vivo', [\App\Http\Controllers\Api\DashboardApiController::class, 'zonasEnVivo'])->name('api.zonas.en-vivo');
    // ==========================================
    // PERFIL (Accesible para todos los logueados)
    // ==========================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==========================================
    // RUTAS EXCLUSIVAS DE SOTYTECH
    // (Gestión global de clientes/empresas)
    // ==========================================
    Route::middleware(['role:Super Administrador'])->group(function () {
        Route::resource('empresas', EmpresaController::class);
    });

    // ==========================================
    // ADMINISTRACIÓN DE PERSONAL Y GRUPOS
    // (No accesible para Gestores ni Clientes Individuales)
    // ==========================================
    Route::middleware(['role:Super Administrador|Administrador de Empresa'])->group(function () {
        // Usuarios
        Route::resource('usuarios', UserController::class);
        Route::delete('usuarios/{usuario}/eliminar', [UserController::class, 'eliminar'])->name('usuarios.eliminar');
        
        // Flotillas
        Route::resource('flotillas', FlotillaController::class);
    });

    // ==========================================
    // OPERACIÓN LOGÍSTICA Y ACTIVOS
    // (Accesible para operativos y clientes particulares)
    // ==========================================
    Route::middleware(['role:Super Administrador|Administrador de Empresa|Gestor de flotilla|Cliente Individual'])->group(function () {
        Route::resource('vehiculos', VehiculoController::class);
        Route::get('/vehiculos/{vehiculo}/ruta', [App\Http\Controllers\VehiculoController::class, 'historialRuta'])->name('vehiculos.ruta');
        Route::resource('dispositivos', DispositivoController::class);
        Route::resource('zonas', ZonaController::class);
        

        
    });Route::get('vehiculos/{vehiculo}/ruta', [RutaController::class, 'historial'])->name('vehiculos.ruta');



    // En el grupo de OPERACIÓN LOGÍSTICA Y ACTIVOS:
Route::middleware(['role:Super Administrador|Administrador de Empresa|Gestor de flotilla|Cliente Individual'])->group(function () {
    Route::resource('vehiculos', VehiculoController::class);
    Route::resource('dispositivos', DispositivoController::class);
    Route::resource('zonas', ZonaController::class);
});

});

require __DIR__.'/auth.php';