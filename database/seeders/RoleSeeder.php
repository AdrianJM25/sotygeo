<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos de Spatie (evita errores al reconstruir la base de datos)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Crear Roles de la nueva jerarquía SaaS
        $roleSuperAdmin = Role::create(['name' => 'Super Administrador']); // Personal de SOTyTECH
        $roleAdminEmpresa = Role::create(['name' => 'Administrador de Empresa']); // Gerentes de corporativos
        $roleGestor = Role::create(['name' => 'Gestor de flotilla']); // Empleados de corporativos
        $roleCliente = Role::create(['name' => 'Cliente Individual']); // Clientes particulares (personas físicas)
        $roleConductor = Role::create(['name' => 'Conductor']); // Nivel más bajo, solo visualización

        // 2. Crear Permisos (Reflejando todos los módulos del sistema)
        Permission::create(['name' => 'ver mapas']);
        Permission::create(['name' => 'gestionar empresas']); // Permiso maestro para crear clientes corporativos
        Permission::create(['name' => 'gestionar usuarios']);
        Permission::create(['name' => 'gestionar flotillas']);
        Permission::create(['name' => 'gestionar vehiculos']);
        Permission::create(['name' => 'gestionar dispositivos']);
        Permission::create(['name' => 'gestionar zonas']);

        // 3. Asignación Estratégica de Permisos

        // Super Administrador: Acceso absoluto a la plataforma SaaS
        $roleSuperAdmin->givePermissionTo(Permission::all());
        
        // Administrador de Empresa: Gestiona toda su compañía interna (empleados, vehículos, flotillas)
        $roleAdminEmpresa->givePermissionTo([
            'ver mapas', 
            'gestionar usuarios', 
            'gestionar flotillas', 
            'gestionar vehiculos', 
            'gestionar dispositivos', 
            'gestionar zonas'
        ]);
        
        // Gestor de flotilla: Operación logística diaria (sin control sobre creación de empleados)
        $roleGestor->givePermissionTo([
            'ver mapas', 
            'gestionar vehiculos', 
            'gestionar dispositivos', 
            'gestionar zonas'
        ]);
        
        // Cliente Individual: Usuario particular, interfaz simplificada directa a su auto y geocercas
        $roleCliente->givePermissionTo([
            'ver mapas', 
            'gestionar vehiculos', 
            'gestionar zonas'
        ]);

        // Conductor: Acceso mínimo, proyectado para uso futuro en app móvil
        $roleConductor->givePermissionTo([
            'ver mapas'
        ]);
    }
}