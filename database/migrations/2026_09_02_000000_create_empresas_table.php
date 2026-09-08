<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            
            // Datos fiscales / Identificación
            $table->string('nombre'); // Ej: "Transportes Castores" o "SOTyTECH"
            $table->string('rfc', 20)->nullable();
            
            // Contacto
            $table->string('telefono', 20)->nullable();
            $table->string('correo')->nullable(); // <-- Campo añadido
            
            // Control
            $table->boolean('is_active')->default(true); // Para suspender servicio a toda la empresa
            
            $table->timestamps();
        });

        // Inyectamos la relación en la tabla users que ya existe
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
        });
        Schema::dropIfExists('empresas');
    }
};