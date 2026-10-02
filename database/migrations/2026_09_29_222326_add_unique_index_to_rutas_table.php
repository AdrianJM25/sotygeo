<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rutas', function (Blueprint $table) {
            $table->unique(['vehiculo_id', 'fecha_inicio'], 'rutas_vehiculo_ventana_unique');
        });
    }

    public function down(): void
    {
        Schema::table('rutas', function (Blueprint $table) {
            $table->dropUnique('rutas_vehiculo_ventana_unique');
        });
    }
};