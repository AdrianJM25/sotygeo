<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */

        $roleSuperAdmin = Role::firstOrCreate([
            'name' => 'Super Administrador',
            'guard_name' => 'web',
        ]);

        $roleAdminEmpresa = Role::firstOrCreate([
            'name' => 'Administrador de Empresa',
            'guard_name' => 'web',
        ]);

        $roleGestor = Role::firstOrCreate([
            'name' => 'Gestor de flotilla',
            'guard_name' => 'web',
        ]);

        $roleCliente = Role::firstOrCreate([
            'name' => 'Cliente Individual',
            'guard_name' => 'web',
        ]);

        $roleConductor = Role::firstOrCreate([
            'name' => 'Conductor',
            'guard_name' => 'web',
        ]);

        /*
        |--------------------------------------------------------------------------
        | PERMISOS DEL SISTEMA
        |--------------------------------------------------------------------------
        */

        $permisos = [
            'ver mapas',

            'gestionar empresas',
            'gestionar usuarios',
            'gestionar flotillas',
            'gestionar vehiculos',
            'gestionar dispositivos',
            'gestionar conductores',
            'gestionar zonas',

            'ver historial de rutas',
            'ver dashboard',

            'gestionar enlaces temporales',
            'gestionar inspecciones',

            'gestionar reportes',
            'gestionar notificaciones',

            'administrar respaldos',
        ];

        foreach ($permisos as $permiso) {

            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMINISTRADOR
        |--------------------------------------------------------------------------
        |
        | Acceso completo a SotyGeo.
        |
        */

        $roleSuperAdmin->syncPermissions(
            $permisos
        );

        /*
        |--------------------------------------------------------------------------
        | ADMINISTRADOR DE EMPRESA
        |--------------------------------------------------------------------------
        |
        | FN.3  Gestionar usuarios
        | FN.5  Gestionar vehículos
        | FN.6  Gestionar equipos GPS
        | FN.7  Gestionar conductores
        | FN.8  Gestionar zonas
        | FN.9  Gestionar flotillas
        | FN.10 Monitoreo en vivo
        | FN.11 Historial de rutas
        | FN.12 Dashboard
        | FN.13 Enlaces temporales
        | FN.14 Inspecciones
        | FN.15 - FN.20 Reportes
        | FN.23 Notificaciones
        |
        */

        $roleAdminEmpresa->syncPermissions([
            'ver mapas',

            'gestionar usuarios',
            'gestionar flotillas',
            'gestionar vehiculos',
            'gestionar dispositivos',
            'gestionar conductores',
            'gestionar zonas',

            'ver historial de rutas',
            'ver dashboard',

            'gestionar enlaces temporales',
            'gestionar inspecciones',

            'gestionar reportes',
            'gestionar notificaciones',
        ]);

        /*
        |--------------------------------------------------------------------------
        | GESTOR DE FLOTILLA
        |--------------------------------------------------------------------------
        |
        | FN.5  Gestionar vehículos
        | FN.6  Gestionar equipos GPS
        | FN.7  Gestionar conductores
        | FN.8  Gestionar zonas
        | FN.9  Gestionar flotillas
        | FN.10 Monitoreo en vivo
        | FN.11 Historial de rutas
        | FN.12 Dashboard
        | FN.13 Enlaces temporales
        | FN.14 Inspecciones
        | FN.23 Notificaciones
        |
        */

        $roleGestor->syncPermissions([
            'ver mapas',

            'gestionar flotillas',
            'gestionar vehiculos',
            'gestionar dispositivos',
            'gestionar conductores',
            'gestionar zonas',

            'ver historial de rutas',
            'ver dashboard',

            'gestionar enlaces temporales',
            'gestionar inspecciones',

            'gestionar notificaciones',
        ]);

        /*
        |--------------------------------------------------------------------------
        | CLIENTE INDIVIDUAL
        |--------------------------------------------------------------------------
        |
        | FN.5  Gestionar vehículos
        | FN.6  Gestionar equipos GPS
        | FN.8  Gestionar zonas
        | FN.10 Monitoreo en vivo
        | FN.11 Historial de rutas
        | FN.12 Dashboard
        | FN.13 Enlaces temporales
        | FN.23 Notificaciones
        |
        | IMPORTANTE:
        | FN.14 NO incluye al Cliente para inspecciones.
        |
        */

        $roleCliente->syncPermissions([
            'ver mapas',

            'gestionar vehiculos',
            'gestionar dispositivos',
            'gestionar zonas',

            'ver historial de rutas',
            'ver dashboard',

            'gestionar enlaces temporales',

            'gestionar notificaciones',
        ]);

        /*
        |--------------------------------------------------------------------------
        | CONDUCTOR
        |--------------------------------------------------------------------------
        |
        | En los requisitos aparece como "Chofer".
        |
        | FN.10 Monitoreo en vivo
        | FN.11 Historial de rutas
        | FN.12 Dashboard
        | FN.13 Enlaces temporales
        | FN.14 Inspecciones
        | FN.23 Notificaciones
        |
        */

        $roleConductor->syncPermissions([
            'ver mapas',

            'ver historial de rutas',
            'ver dashboard',

            'gestionar enlaces temporales',
            'gestionar inspecciones',

            'gestionar notificaciones',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}