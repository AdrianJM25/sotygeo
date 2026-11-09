<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {

            $table
                ->integer('limite_velocidad_kmh')
                ->default(90);

        });
    }


    public function down(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {

            $table->dropColumn(
                'limite_velocidad_kmh'
            );

        });
    }
};