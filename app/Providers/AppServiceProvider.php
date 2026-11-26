<?php

namespace App\Providers;

use App\Models\Alerta;
use App\Models\EventoGeocerca;
use App\Models\Inspeccion;

use App\Observers\AlertaObserver;
use App\Observers\EventoGeocercaObserver;
use App\Observers\InspeccionObserver;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | FN.23 - NOTIFICACIONES AUTOMÁTICAS
        |--------------------------------------------------------------------------
        |
        | Los observers permiten detectar cuando SotyGeo
        | crea o actualiza eventos importantes y generar
        | automáticamente las notificaciones correspondientes.
        |
        */

        Alerta::observe(
            AlertaObserver::class
        );

        EventoGeocerca::observe(
            EventoGeocercaObserver::class
        );

        Inspeccion::observe(
            InspeccionObserver::class
        );
    }
}