<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ElementoInspeccionSeeder extends Seeder
{
    public function run(): void
    {
        $elementos = [

            [
                'nombre' =>
                    'Llantas',

                'categoria' =>
                    'Seguridad',
            ],

            [
                'nombre' =>
                    'Luces',

                'categoria' =>
                    'Seguridad',
            ],

            [
                'nombre' =>
                    'Líquidos',

                'categoria' =>
                    'Mantenimiento',
            ],

            [
                'nombre' =>
                    'Limpieza general',

                'categoria' =>
                    'Condición general',
            ],
        ];


        foreach (
            $elementos
            as $elemento
        ) {

            DB::table(
                'elementos_inspeccion'
            )
                ->updateOrInsert(
                    [
                        'nombre' =>
                            $elemento[
                                'nombre'
                            ],
                    ],
                    [
                        'categoria' =>
                            $elemento[
                                'categoria'
                            ],

                        'activo' =>
                            true,

                        'created_at' =>
                            now(),

                        'updated_at' =>
                            now(),
                    ]
                );
        }
    }
}