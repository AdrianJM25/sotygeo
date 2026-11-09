<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ELEMENTOS DE INSPECCIÓN
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'elementos_inspeccion',
            function (Blueprint $table) {

                $table->id();

                $table->string(
                    'nombre',
                    150
                );

                $table->string(
                    'categoria',
                    80
                )->nullable();

                $table->boolean(
                    'activo'
                )->default(
                    true
                );

                $table->timestamps();


                $table->unique([
                    'nombre',
                    'categoria',
                ]);
            }
        );


        /*
        |--------------------------------------------------------------------------
        | INSPECCIONES
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'inspecciones',
            function (Blueprint $table) {

                $table->id();


                /*
                |--------------------------------------------------------------------------
                | VEHÍCULO
                |--------------------------------------------------------------------------
                */

                $table->foreignId(
                    'vehiculo_id'
                )
                    ->constrained(
                        'vehiculos'
                    )
                    ->restrictOnDelete();


                /*
                |--------------------------------------------------------------------------
                | USUARIO RESPONSABLE
                |--------------------------------------------------------------------------
                */

                $table->foreignId(
                    'user_id'
                )
                    ->constrained(
                        'users'
                    )
                    ->restrictOnDelete();


                /*
                |--------------------------------------------------------------------------
                | CONDUCTOR
                |--------------------------------------------------------------------------
                |
                | Se utiliza cuando la inspección es realizada
                | por un usuario con rol Conductor.
                |
                */

                $table->foreignId(
                    'conductor_id'
                )
                    ->nullable()
                    ->constrained(
                        'conductores'
                    )
                    ->nullOnDelete();


                /*
                |--------------------------------------------------------------------------
                | FECHA
                |--------------------------------------------------------------------------
                */

                $table->dateTime(
                    'fecha_inspeccion'
                );


                /*
                |--------------------------------------------------------------------------
                | RESULTADO GENERAL
                |--------------------------------------------------------------------------
                |
                | correcto
                | con_incidencias
                |
                */

                $table->string(
                    'resultado_general',
                    30
                );


                /*
                |--------------------------------------------------------------------------
                | OBSERVACIONES
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'observaciones'
                )->nullable();


                $table->timestamps();


                /*
                |--------------------------------------------------------------------------
                | ÍNDICES
                |--------------------------------------------------------------------------
                */

                $table->index([
                    'vehiculo_id',
                    'fecha_inspeccion',
                ]);

                $table->index(
                    'resultado_general'
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | DETALLES DE INSPECCIÓN
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'detalles_inspeccion',
            function (Blueprint $table) {

                $table->id();


                /*
                |--------------------------------------------------------------------------
                | INSPECCIÓN
                |--------------------------------------------------------------------------
                */

                $table->foreignId(
                    'inspeccion_id'
                )
                    ->constrained(
                        'inspecciones'
                    )
                    ->cascadeOnDelete();


                /*
                |--------------------------------------------------------------------------
                | ELEMENTO REVISADO
                |--------------------------------------------------------------------------
                */

                $table->foreignId(
                    'elemento_id'
                )
                    ->constrained(
                        'elementos_inspeccion'
                    )
                    ->restrictOnDelete();


                /*
                |--------------------------------------------------------------------------
                | RESULTADO
                |--------------------------------------------------------------------------
                |
                | correcto
                | incidencia
                |
                */

                $table->string(
                    'resultado',
                    30
                );


                /*
                |--------------------------------------------------------------------------
                | OBSERVACIÓN DEL ELEMENTO
                |--------------------------------------------------------------------------
                */

                $table->text(
                    'observaciones'
                )->nullable();


                $table->timestamps();


                /*
                |--------------------------------------------------------------------------
                | EVITAR DUPLICAR EL MISMO ELEMENTO
                |--------------------------------------------------------------------------
                */

                $table->unique([
                    'inspeccion_id',
                    'elemento_id',
                ]);
            }
        );
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'detalles_inspeccion'
        );

        Schema::dropIfExists(
            'inspecciones'
        );

        Schema::dropIfExists(
            'elementos_inspeccion'
        );
    }
};