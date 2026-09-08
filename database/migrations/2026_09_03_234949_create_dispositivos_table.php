<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispositivos', function (Blueprint $table) {
            $table->id();
            
            // ==========================================
            // Doble Propietario (SaaS)
            // ==========================================
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            
            // ==========================================
            // Relación Directa con el Vehículo
            // ==========================================
            // Unique: Un vehículo solo debe tener 1 GPS a la vez en el sistema
            $table->foreignId('vehiculo_id')->nullable()->unique()->constrained('vehiculos')->onDelete('set null');
            
            // ==========================================
            // Datos del Hardware
            // ==========================================
            $table->string('imei', 20)->unique();
            $table->string('numero_sim', 20)->nullable();
            $table->string('modelo')->nullable(); // Ej: Skstar, TK905
            $table->integer('capacidad_bateria_mah')->nullable(); // Ej: 5000
            $table->boolean('modo_reposo')->default(false); // Para saber si duerme para ahorrar batería
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispositivos');
    }
};