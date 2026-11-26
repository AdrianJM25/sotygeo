<?php

namespace App\Services;

use App\Models\Conductor;
use App\Models\User;
use App\Models\Vehiculo;
use App\Notifications\EventoSistemaNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class NotificacionSistemaService
{
    /**
     * Envía una notificación a los usuarios relacionados con un vehículo.
     */
    public function notificarVehiculo(
        Vehiculo $vehiculo,
        string $tipo,
        string $titulo,
        string $mensaje,
        ?string $url = null,
        array $datos = [],
        bool $enviarCorreo = false,
    ): void {
        $this->enviar(
            $this->destinatariosVehiculo($vehiculo),
            $tipo,
            $titulo,
            $mensaje,
            $url,
            array_merge(
                [
                    'vehiculo_id' => $vehiculo->id,
                    'empresa_id' => $vehiculo->empresa_id,
                ],
                $datos
            ),
            $enviarCorreo,
        );
    }

    /**
     * Envía una notificación relacionada con un conductor.
     */
    public function notificarConductor(
        Conductor $conductor,
        string $tipo,
        string $titulo,
        string $mensaje,
        ?string $url = null,
        array $datos = [],
        bool $enviarCorreo = false,
    ): void {
        $this->enviar(
            $this->destinatariosConductor($conductor),
            $tipo,
            $titulo,
            $mensaje,
            $url,
            array_merge(
                [
                    'conductor_id' => $conductor->id,
                    'empresa_id' => $conductor->empresa_id,
                    'vehiculo_id' => $conductor->vehiculo_id,
                ],
                $datos
            ),
            $enviarCorreo,
        );
    }

    /**
     * Usuarios que deben recibir eventos asociados a un vehículo.
     */
    public function destinatariosVehiculo(
        Vehiculo $vehiculo
    ): Collection {
        $usuarios = collect();

        $usuarios = $usuarios->merge(
            $this->superAdministradores()
        );

        if ($vehiculo->empresa_id) {
            $usuarios = $usuarios->merge(
                $this->usuariosOperativosEmpresa(
                    (int) $vehiculo->empresa_id
                )
            );
        }

        if ($vehiculo->user_id) {
            $cliente = User::query()
                ->where('id', $vehiculo->user_id)
                ->where('activo', true)
                ->first();

            if ($cliente) {
                $usuarios->push($cliente);
            }
        }

        $correosConductores = Conductor::query()
            ->where('vehiculo_id', $vehiculo->id)
            ->where('activo', true)
            ->whereNotNull('email')
            ->pluck('email')
            ->filter()
            ->values();

        if ($correosConductores->isNotEmpty()) {
            $choferes = User::query()
                ->where('activo', true)
                ->whereIn(
                    'email',
                    $correosConductores
                )
                ->whereHas(
                    'roles',
                    function ($query) {
                        $query->where(
                            'name',
                            'Chofer'
                        );
                    }
                )
                ->get();

            $usuarios = $usuarios->merge(
                $choferes
            );
        }

        return $this->limpiarDestinatarios(
            $usuarios
        );
    }

    /**
     * Usuarios que deben recibir eventos asociados a un conductor.
     */
    public function destinatariosConductor(
        Conductor $conductor
    ): Collection {
        $usuarios = collect();

        $usuarios = $usuarios->merge(
            $this->superAdministradores()
        );

        if ($conductor->empresa_id) {
            $usuarios = $usuarios->merge(
                $this->usuariosOperativosEmpresa(
                    (int) $conductor->empresa_id
                )
            );
        }

        if ($conductor->email) {
            $chofer = User::query()
                ->where('activo', true)
                ->where(
                    'email',
                    $conductor->email
                )
                ->whereHas(
                    'roles',
                    function ($query) {
                        $query->where(
                            'name',
                            'Chofer'
                        );
                    }
                )
                ->first();

            if ($chofer) {
                $usuarios->push(
                    $chofer
                );
            }
        }

        return $this->limpiarDestinatarios(
            $usuarios
        );
    }

    private function enviar(
        Collection $destinatarios,
        string $tipo,
        string $titulo,
        string $mensaje,
        ?string $url,
        array $datos,
        bool $enviarCorreo,
    ): void {
        if ($destinatarios->isEmpty()) {
            return;
        }

        Notification::send(
            $destinatarios,
            new EventoSistemaNotification(
                $tipo,
                $titulo,
                $mensaje,
                $url,
                $datos,
                $enviarCorreo,
            )
        );
    }

    private function superAdministradores(): Collection
    {
        return User::query()
            ->where('activo', true)
            ->whereHas(
                'roles',
                function ($query) {
                    $query->where(
                        'name',
                        'Super Administrador'
                    );
                }
            )
            ->get();
    }

    private function usuariosOperativosEmpresa(
        int $empresaId
    ): Collection {
        return User::query()
            ->where('activo', true)
            ->where(
                'empresa_id',
                $empresaId
            )
            ->whereHas(
                'roles',
                function ($query) {
                    $query->whereIn(
                        'name',
                        [
                            'Administrador de Empresa',
                            'Gestor de flotilla',
                        ]
                    );
                }
            )
            ->get();
    }

    private function limpiarDestinatarios(
        Collection $usuarios
    ): Collection {
        return $usuarios
            ->filter()
            ->unique('id')
            ->values();
    }
}