<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            
            // Relación directa con el vehículo
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade');
            
            // Opcional: si quieres saber qué dispositivo GPS disparó la alerta
            $table->foreignId('dispositivo_id')->nullable()->constrained('dispositivos')->onDelete('set null');
            
            $table->string('tipo'); // Ej: bateria_baja, exceso_velocidad, desconexion, geocerca
            $table->text('mensaje'); // Ej: "Exceso de velocidad registrado: 110 km/h"
            $table->boolean('leida')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('alertas');
    }
};