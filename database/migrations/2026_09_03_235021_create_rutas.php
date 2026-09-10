<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('rutas', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade');
            
            // Tiempos
            $table->timestamp('fecha_inicio');
            $table->timestamp('fecha_fin')->nullable();
            
            // Métricas
            $table->decimal('distancia_total_km', 8, 2)->default(0);
            
            // Estado ('abierta' = todavía está en su periodo de 24h; 'cerrada' = ya se completó)
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            
            // Para renderizar en el mapa a máxima velocidad
            $table->longText('trazado_polilinea')->nullable(); 

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('rutas');
    }
};