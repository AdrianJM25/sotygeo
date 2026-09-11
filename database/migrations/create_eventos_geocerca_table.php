<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos_geocerca', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade');
            $table->foreignId('zona_id')->constrained('zonas')->onDelete('cascade');

            // Registro de Entrada y Salida
            $table->timestamp('fecha_entrada');
            $table->timestamp('fecha_salida')->nullable(); // Permanece NULL mientras el vehículo esté dentro

            // Métricas calculadas al salir
            $table->integer('duracion_minutos')->nullable();

            // Coordenadas de entrada y salida
            $table->decimal('latitud_entrada', 10, 7);
            $table->decimal('longitud_entrada', 10, 7);
            $table->decimal('latitud_salida', 10, 7)->nullable();
            $table->decimal('longitud_salida', 10, 7)->nullable();

            // Estado de alerta
            $table->boolean('alerta_generada')->default(false);
            $table->string('tipo_evento')->default('normal'); // normal, violacion_restringida, exceso_permanencia

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos_geocerca');
    }
};