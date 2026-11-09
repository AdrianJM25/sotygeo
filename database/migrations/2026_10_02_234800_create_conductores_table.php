<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conductores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')
                ->nullable()
                ->constrained('empresas')
                ->onDelete('cascade');

            $table->foreignId('vehiculo_id')
                ->nullable()
                ->constrained('vehiculos')
                ->onDelete('set null');

            $table->string('nombre');
            $table->string('apellido_paterno')->nullable();
            $table->string('apellido_materno')->nullable();

            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();

            $table->string('numero_licencia', 50)->nullable();
            $table->date('vigencia_licencia')->nullable();

            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conductores');
    }
};