<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos_geocerca', function (Blueprint $table) {
            // Separado de 'alerta_generada' (que ya se usa para entrada/salida)
            // para poder marcar específicamente si ya se avisó del exceso de
            // permanencia y así no repetir la alerta en cada corrida del cron.
            $table->boolean('alerta_permanencia_generada')->default(false)->after('alerta_generada');
        });
    }

    public function down(): void
    {
        Schema::table('eventos_geocerca', function (Blueprint $table) {
            $table->dropColumn('alerta_permanencia_generada');
        });
    }
};