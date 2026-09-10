<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('ubicaciones', function (Blueprint $table) {
            $table->id();
            
            // ==========================================
            // Relaciones
            // ==========================================
            $table->foreignId('dispositivo_id')->constrained('dispositivos')->onDelete('cascade');
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos')->onDelete('cascade');
            // Modifica tu migración de ubicaciones. Agrega esto debajo de vehiculo_id:
$table->foreignId('ruta_id')->nullable()->constrained('rutas')->onDelete('cascade');

// IMPORTANTE PARA EL RENDIMIENTO: Agrega índices a las fechas y relaciones, 
// ya que las tablas de GPS crecen a millones de registros muy rápido.
$table->index(['vehiculo_id', 'fecha_gps']);
            // ==========================================
            // Datos Espaciales y Coordenadas
            // ==========================================
            // Guardamos lat/lon por separado (Ideal para cálculos rápidos y APIs)
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            
            // Dato espacial PostGIS (Lo dejamos nullable por si en algún momento falla la conversión geométrica)
            $table->geometry('punto', 'point')->nullable(); 

            // ==========================================
            // Métricas de Movimiento
            // ==========================================
            $table->decimal('velocidad', 8, 2)->default(0); // km/h
            $table->decimal('altitud', 8, 2)->nullable(); // Metros sobre el nivel del mar
            $table->decimal('rumbo', 5, 2)->nullable(); // Grados de dirección (0-360)
            
            // ==========================================
            // Estados del Dispositivo (Hardware)
            // ==========================================
            $table->integer('porcentaje_bateria')->nullable();
            $table->boolean('esta_cargando')->nullable()->default(false);
            $table->boolean('en_movimiento')->nullable()->default(false); 
            $table->boolean('estado_reposo')->nullable()->default(false); 
            
            // ==========================================
            // Tiempos
            // ==========================================
            $table->timestamp('fecha_gps'); // Hora que dictó el satélite
            

                   
            // created_at y updated_at = Hora en la que llegó a tu servidor
            $table->timestamps(); 
        });
    }

    public function down(): void {
        // Corregido el nombre de la tabla para que no falle al hacer rollback
        Schema::dropIfExists('ubicaciones'); 
    }
};