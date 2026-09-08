<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('suscripciones', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->foreignId('plan_id')->constrained('planes')->onDelete('cascade');
    $table->enum('estatus', ['activa', 'suspendida', 'vencida'])->default('activa');
    $table->timestamp('fecha_inicio')->nullable();
    $table->timestamp('fecha_fin')->nullable(); // Para el corte mensual
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suscripcions');
    }
};
