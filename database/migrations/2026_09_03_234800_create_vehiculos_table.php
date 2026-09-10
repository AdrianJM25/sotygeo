<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            
            // ==========================================
            // 1. PROPIEDAD MULTI-TENANT (SaaS)
            // ==========================================
            // Si pertenece a un corporativo:
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onDelete('cascade');
            // Si pertenece a un cliente particular (persona física):
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            
            // ==========================================
            // 2. LOGÍSTICA
            // ==========================================
            $table->foreignId('flotilla_id')->nullable()->constrained('flotillas')->onDelete('set null');

            // ==========================================
            // 3. IDENTIFICACIÓN ESENCIAL
            // ==========================================
            $table->string('nombre'); // El Alias. Ej: "Unidad 01", "Reparto Sur"
            $table->string('tipo_vehiculo', 50)->default('automovil'); // auto, moto, camion, caja_seca, etc.
            
            // Características físicas
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->integer('anio')->nullable();
            $table->string('placas', 20)->nullable();
            $table->string('color', 50)->nullable();
            
            // ==========================================
            // 4. LEGALES / SEGURIDAD
            // ==========================================
            $table->string('vin', 50)->nullable()->unique();
            
            // ==========================================
            // 5. DATOS OPERATIVOS
            // ==========================================
            $table->decimal('rendimiento_km_litro', 5, 2)->nullable();
            $table->date('vencimiento_seguro')->nullable();
            // En tu migración de vehiculos, agrega esto antes del timestamps:
$table->integer('horas_corte_ruta')->default(24)->comment('Frecuencia en horas para dividir el historial de rutas');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};