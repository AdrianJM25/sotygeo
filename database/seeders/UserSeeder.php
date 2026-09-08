<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Empresa;
use App\Models\Vehiculo;
use App\Models\Dispositivo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Empresa Corporativa de Prueba
        $empresa = Empresa::create([
            'nombre' => 'Logística Jiutepec S.A. de C.V.',
            'rfc' => 'LOGJ260905ABC',
            'telefono' => '7770001122',
            'correo' => 'contacto@logisticajiutepec.com',
            'is_active' => true,
        ]);

        // 2. Definir Usuarios para todos los roles de SotyGeo
        $usuarios = [
            [
                'nombre' => 'Ismael',
                'apellido_paterno' => 'Figueroa',
                'apellido_materno' => 'Soto',
                'email' => 'admin@sotygeo.com',
                'telefono' => '7771234567',
                'role' => 'Super Administrador',
                'empresa_id' => null, // SOTyTECH controla todo el SaaS
            ],
            [
                'nombre' => 'Carlos',
                'apellido_paterno' => 'Ramírez',
                'apellido_materno' => 'Torres',
                'email' => 'gerente@logistica.com',
                'telefono' => '7772233445',
                'role' => 'Administrador de Empresa',
                'empresa_id' => $empresa->id,
            ],
            [
                'nombre' => 'Laura',
                'apellido_paterno' => 'Gómez',
                'apellido_materno' => 'Sánchez',
                'email' => 'gestor@logistica.com',
                'telefono' => '7777654321',
                'role' => 'Gestor de flotilla',
                'empresa_id' => $empresa->id,
            ],
            [
                'nombre' => 'Miguel',
                'apellido_paterno' => 'Hernández',
                'apellido_materno' => 'López',
                'email' => 'conductor@logistica.com',
                'telefono' => '7779988776',
                'role' => 'Conductor',
                'empresa_id' => $empresa->id,
            ],
            [
                'nombre' => 'Juan',
                'apellido_paterno' => 'Pérez',
                'apellido_materno' => 'Martínez',
                'email' => 'juan@particular.com',
                'telefono' => '7775556677',
                'role' => 'Cliente Individual',
                'empresa_id' => null, // Particular independiente
            ],
        ];

        $usersCreados = [];
        foreach ($usuarios as $datos) {
            $user = User::create([
                'empresa_id' => $datos['empresa_id'],
                'nombre' => $datos['nombre'],
                'apellido_paterno' => $datos['apellido_paterno'],
                'apellido_materno' => $datos['apellido_materno'],
                'email' => $datos['email'],
                'telefono' => $datos['telefono'],
                'password' => Hash::make('12345678'),
                'activo' => true,
            ]);

            $user->assignRole($datos['role']);
            $usersCreados[$datos['role']] = $user;
        }

        // 3. Crear Vehículos y Dispositivos GPS (Sin capa de Activos)

        // A. Ecosistema Corporativo (Propiedad de la Empresa)
        $vehiculoCorp = Vehiculo::create([
            'empresa_id' => $empresa->id,
            'user_id' => null,
            'flotilla_id' => null,
            'nombre' => 'Unidad 01 - Reparto Cuernavaca',
            'tipo_vehiculo' => 'camioneta',
            'marca' => 'Nissan',
            'modelo' => 'NP300',
            'anio' => 2024,
            'placas' => 'NV-1234',
            'color' => 'Blanco',
            'vin' => '3N1AB7AP0KY123456',
            'rendimiento_km_litro' => 10.50,
            'vencimiento_seguro' => '2027-01-15',
        ]);

        Dispositivo::create([
            'empresa_id' => $empresa->id,
            'user_id' => null,
            'vehiculo_id' => $vehiculoCorp->id, // Conectado directo al auto
            'imei' => '863123456789012',
            'numero_sim' => '7771112233',
            'modelo' => 'TK905',
            'capacidad_bateria_mah' => 5000,
            'modo_reposo' => false,
        ]);

        // B. Ecosistema Particular (Propiedad del Cliente Individual)
        $clienteParticular = $usersCreados['Cliente Individual'];

        $vehiculoPart = Vehiculo::create([
            'empresa_id' => null,
            'user_id' => $clienteParticular->id,
            'flotilla_id' => null,
            'nombre' => 'Auto Familiar',
            'tipo_vehiculo' => 'automovil',
            'marca' => 'Volkswagen',
            'modelo' => 'Jetta',
            'anio' => 2022,
            'placas' => 'PYZ-9876',
            'color' => 'Gris',
            'vin' => '3VW2B7AJ7NM987654',
            'rendimiento_km_litro' => 13.20,
            'vencimiento_seguro' => '2026-11-30',
        ]);

        Dispositivo::create([
            'empresa_id' => null,
            'user_id' => $clienteParticular->id,
            'vehiculo_id' => $vehiculoPart->id, // Conectado directo al auto
            'imei' => '863987654321098',
            'numero_sim' => '7779998877',
            'modelo' => 'Sinotrack ST901',
            'capacidad_bateria_mah' => 350,
            'modo_reposo' => false,
        ]);
    }
}