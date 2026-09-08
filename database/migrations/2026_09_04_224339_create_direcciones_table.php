<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id();

            // Relación polimórfica: hoy Users, mañana Vehiculos, Activos, Flotillas, etc.
            $table->unsignedBigInteger('direccionable_id');
            $table->string('direccionable_type');

            $table->enum('tipo', ['domicilio', 'trabajo', 'envio', 'facturacion', 'operacion'])
                  ->default('domicilio');

            $table->string('calle');
            $table->string('numero_exterior', 20)->nullable();
            $table->string('numero_interior', 20)->nullable();
            $table->string('colonia')->nullable();
            $table->string('ciudad');
            $table->string('estado');
            $table->string('codigo_postal', 10);
            $table->string('pais')->default('México');
            $table->text('referencias')->nullable();

            // Punto geocodificado (PostGIS) para pintar en el mapa
            $table->geometry('punto', subtype: 'point', srid: 4326)->nullable();

            $table->boolean('es_principal')->default(false);
            $table->boolean('activo')->default(true);

            $table->timestamps();

            $table->index(['direccionable_id', 'direccionable_type'], 'idx_direccionable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};