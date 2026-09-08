<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('enlaces_rastreo', function (Blueprint $table) {
            $table->id();
            
            // Relación directa al vehículo
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade');
            
            $table->string('token', 64)->unique(); // Token seguro para la URL compartida
            $table->timestamp('fecha_expiracion'); // Momento en que caduca el acceso
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('enlaces_rastreo');
    }
};