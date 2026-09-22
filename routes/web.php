<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DispositivoController;
use App\Http\Controllers\ZonaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\FlotillaController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\EventoGeocercaController;

// Landing page comercial (welcome.blade.php)
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/geocercas/historial', [EventoGeocercaController::class, 'index'])->name('geocercas.historial');
    
    // ==========================================
    // API INTERNA (Dashboard y Mapas)
    // ==========================================
    // Utilizamos 'prefix' y 'name' para agruparlas limpiamente y evitar escribir '/api/' a mano.
    Route::prefix('api-interno')->name('api.')->group(function () {
        Route::get('/vehiculos/en-vivo', [DashboardApiController::class, 'vehiculosEnVivo'])->name('vehiculos.en-vivo');
        Route::get('/zonas/en-vivo', [DashboardApiController::class, 'zonasEnVivo'])->name('zonas.en-vivo');
        Route::get('/alertas/recientes', [DashboardApiController::class, 'alertasRecientes'])->name('alertas.recientes');
        
    });

    // ==========================================
    // PERFIL DE USUARIO
    // ==========================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==========================================
    // EXCLUSIVO SOTYTECH (SaaS)
    // ==========================================
    Route::middleware(['role:Super Administrador'])->group(function () {
        Route::resource('empresas', EmpresaController::class);
    });

    // ==========================================
    // ADMINISTRACIÓN DE PERSONAL Y GRUPOS
    // ==========================================
    Route::middleware(['role:Super Administrador|Administrador de Empresa'])->group(function () {
        Route::resource('usuarios', UserController::class);
        Route::delete('usuarios/{usuario}/eliminar', [UserController::class, 'eliminar'])->name('usuarios.eliminar');
        Route::resource('flotillas', FlotillaController::class);
    });

    // ==========================================
    // OPERACIÓN LOGÍSTICA Y ACTIVOS
    // ==========================================
    Route::middleware(['role:Super Administrador|Administrador de Empresa|Gestor de flotilla|Cliente Individual'])->group(function () {
        // Vehículos y Rutas
        Route::resource('vehiculos', VehiculoController::class);
        Route::get('/vehiculos/{vehiculo}/ruta', [RutaController::class, 'historial'])->name('vehiculos.ruta');
        Route::patch('/vehiculos/{vehiculo}/actualizar-corte', [VehiculoController::class, 'actualizarCorteRuta'])->name('vehiculos.actualizar-corte');
        
        // Dispositivos y Geocercas
        Route::resource('dispositivos', DispositivoController::class);
        Route::resource('zonas', ZonaController::class);
    });
});

// Bloquear acceso al registro público (Sobrescribe las rutas de auth.php)
Route::match(['get', 'post'], '/register', function () {
    return redirect('/');
});

require __DIR__.'/auth.php';