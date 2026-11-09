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
        Schema::table('conductores', function (Blueprint $table) {

            $table
                ->string('licencia_frente_path')
                ->nullable()
                ->after('vigencia_licencia');

            $table
                ->string('licencia_reverso_path')
                ->nullable()
                ->after('licencia_frente_path');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conductores', function (Blueprint $table) {

            $table->dropColumn([
                'licencia_frente_path',
                'licencia_reverso_path',
            ]);

        });
    }
};