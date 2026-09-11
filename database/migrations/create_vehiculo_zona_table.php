<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculo_zona', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade');
            $table->foreignId('zona_id')->constrained('zonas')->onDelete('cascade');

            // Tipo de Regla para esta zona
            // permitida: Debe estar aquí / Seguro
            // restringida: Prohibido ingresar
            // informativa: Solo monitoreo de paso/control
            $table->enum('tipo_regla', ['permitida', 'restringida', 'informativa'])->default('informativa');

            // Configuración de Notificaciones
            $table->boolean('notificar_entrada')->default(true);
            $table->boolean('notificar_salida')->default(true);

            // Tiempo de permanencia mínima/máxima en minutos (opcional)
            // Ej: Si dura más de X minutos en una zona restringida o si dura menos de X en una permitida
            $table->integer('permanencia_minima_minutos')->nullable()->comment('Tiempo mínimo que debe permanecer antes de alertar');
            $table->integer('permanencia_maxima_minutos')->nullable()->comment('Tiempo máximo permitido en la zona');

            $table->timestamps();

            $table->unique(['vehiculo_id', 'zona_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculo_zona');
    }
};