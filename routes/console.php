<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command(
    'inspire',
    function () {
        $this->comment(
            Inspiring::quote()
        );
    }
)->purpose(
    'Display an inspiring quote'
);

/*
|--------------------------------------------------------------------------
| FN.23 - NOTIFICACIONES AUTOMÁTICAS
|--------------------------------------------------------------------------
|
| Verifica periódicamente:
| - Equipos GPS sin conexión.
| - Licencias de conductores próximas a vencer.
|
| El comando incorpora control anti-spam y el scheduler
| evita solapamientos.
|
*/

Schedule::command(
    'sotygeo:verificar-notificaciones'
)
    ->everyFiveMinutes()
    ->withoutOverlapping();