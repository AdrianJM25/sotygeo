<?php

namespace App\Console\Commands;

use App\Models\Alerta;
use App\Models\Conductor;
use App\Models\Vehiculo;
use App\Services\NotificacionSistemaService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class VerificarNotificacionesSistema extends Command
{
    protected $signature = 'sotygeo:verificar-notificaciones';

    protected $description = 'FN.23: verifica GPS sin conexión y licencias próximas a vencer.';

    public function handle(
        NotificacionSistemaService $notificaciones
    ): int {
        $gpsGeneradas = $this->verificarGpsSinConexion();

        $licenciasGeneradas = $this->verificarLicencias(
            $notificaciones
        );

        $this->info(
            'FN.23 verificado. '
            .'GPS: '.$gpsGeneradas
            .' | Licencias: '.$licenciasGeneradas
        );

        return self::SUCCESS;
    }

    /**
     * Detecta vehículos que llevan más de cinco minutos sin reportar.
     *
     * Se crea una Alerta normal. El AlertaObserver ya registrado se encarga
     * de generar las notificaciones para los usuarios correspondientes.
     *
     * Anti-spam:
     * - Mientras el vehículo siga desconectado, solo se genera una alerta.
     * - Cuando vuelve a reportar, se elimina el bloqueo.
     * - Si vuelve a desconectarse después, se genera una nueva alerta.
     */
    private function verificarGpsSinConexion(): int
    {
        $generadas = 0;

        $vehiculos = Vehiculo::query()
            ->with([
                'dispositivo.ubicaciones' => function ($query) {
                    $query
                        ->latest('fecha_gps')
                        ->limit(1);
                },
            ])
            ->whereHas('dispositivo')
            ->get();

        foreach ($vehiculos as $vehiculo) {
            $dispositivo = $vehiculo->dispositivo;

            if (!$dispositivo) {
                continue;
            }

            $ultimaUbicacion = $dispositivo
                ->ubicaciones
                ?->first();

            $claveCache =
                'fn23:gps_sin_conexion:vehiculo:'
                .$vehiculo->id;

            $sinConexion = false;
            $minutosSinReporte = null;

            if (
                !$ultimaUbicacion
                || !$ultimaUbicacion->fecha_gps
            ) {
                $sinConexion = true;
            } else {
                $fechaGps = Carbon::parse(
                    $ultimaUbicacion->fecha_gps
                );

                $segundos = max(
                    0,
                    now()->timestamp - $fechaGps->timestamp
                );

                $minutosSinReporte = (int) floor(
                    $segundos / 60
                );

                $sinConexion = $segundos > 300;
            }

            if (!$sinConexion) {
                Cache::forget($claveCache);
                continue;
            }

            if (Cache::has($claveCache)) {
                continue;
            }

            $mensaje =
                'El GPS del vehículo '
                .$vehiculo->nombre
                .' se encuentra sin conexión.';

            if ($minutosSinReporte !== null) {
                $mensaje .=
                    ' Último reporte hace '
                    .$minutosSinReporte
                    .' min.';
            } else {
                $mensaje .=
                    ' El dispositivo aún no registra una ubicación reciente.';
            }

            Alerta::create([
                'vehiculo_id' => $vehiculo->id,
                'dispositivo_id' => $dispositivo->id,
                'tipo' => 'gps_sin_conexion',
                'mensaje' => $mensaje,
                'leida' => false,
            ]);

            Cache::forever(
                $claveCache,
                true
            );

            $generadas++;
        }

        return $generadas;
    }

    /**
     * Notifica licencias próximas a vencer en días clave.
     *
     * Umbrales: 30, 15, 7, 1 y 0 días.
     * Cada umbral se envía una sola vez para la vigencia actual.
     */
    private function verificarLicencias(
        NotificacionSistemaService $notificaciones
    ): int {
        $generadas = 0;

        $umbrales = [
            30,
            15,
            7,
            1,
            0,
        ];

        $conductores = Conductor::query()
            ->where('activo', true)
            ->whereNotNull('vigencia_licencia')
            ->get();

        $hoy = now()->startOfDay();

        foreach ($conductores as $conductor) {
            $vencimiento = Carbon::parse(
                $conductor->vigencia_licencia
            )->startOfDay();

            $diasRestantes = (int) $hoy->diffInDays(
                $vencimiento,
                false
            );

            if (!in_array($diasRestantes, $umbrales, true)) {
                continue;
            }

            $fechaVencimiento = $vencimiento->format('Y-m-d');

            $claveCache =
                'fn23:licencia:'
                .$conductor->id
                .':'.$fechaVencimiento
                .':'.$diasRestantes;

            if (Cache::has($claveCache)) {
                continue;
            }

            $nombreConductor = trim(
                collect([
                    $conductor->nombre,
                    $conductor->apellido_paterno,
                    $conductor->apellido_materno,
                ])
                    ->filter()
                    ->implode(' ')
            );

            if ($diasRestantes === 0) {
                $titulo = 'Licencia vence hoy';

                $mensaje =
                    'La licencia de '.$nombreConductor
                    .' vence hoy.';
            } elseif ($diasRestantes === 1) {
                $titulo = 'Licencia próxima a vencer';

                $mensaje =
                    'La licencia de '.$nombreConductor
                    .' vence mañana.';
            } else {
                $titulo = 'Licencia próxima a vencer';

                $mensaje =
                    'La licencia de '.$nombreConductor
                    .' vence en '.$diasRestantes.' días.';
            }

            $notificaciones->notificarConductor(
                $conductor,
                'licencia_por_vencer',
                $titulo,
                $mensaje,
                route('conductores.index'),
                [
                    'dias_restantes' => $diasRestantes,
                    'vigencia_licencia' => $fechaVencimiento,
                ]
            );

            Cache::forever(
                $claveCache,
                true
            );

            $generadas++;
        }

        return $generadas;
    }
}