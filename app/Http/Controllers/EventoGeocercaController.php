<?php

namespace App\Http\Controllers;

use App\Models\EventoGeocerca;
use App\Models\Vehiculo;
use App\Models\Zona;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventoGeocercaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        /*
        |--------------------------------------------------------------------------
        | VALIDAR FILTROS
        |--------------------------------------------------------------------------
        */

        $filtros = $request->validate([
            'fecha_inicio' => [
                'nullable',
                'date',
            ],

            'fecha_fin' => [
                'nullable',
                'date',
                'after_or_equal:fecha_inicio',
            ],

            'vehiculo_id' => [
                'nullable',
                'integer',
                'exists:vehiculos,id',
            ],

            'geocerca_id' => [
                'nullable',
                'integer',
                'exists:zonas,id',
            ],

            'estado' => [
                'nullable',
                'in:dentro,finalizado,alerta',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | CONSULTAS BASE
        |--------------------------------------------------------------------------
        */

        $query = EventoGeocerca::with([
            'vehiculo',
            'zona',
        ])
        ->orderByDesc('fecha_entrada');


        $vehiculosQuery =
            Vehiculo::query()
                ->orderBy('nombre');


        $zonasQuery =
            Zona::query()
                ->orderBy('nombre');


        /*
        |--------------------------------------------------------------------------
        | MULTI-TENANT
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Cliente Individual'
            )
        ) {

            $query->whereHas(
                'vehiculo',
                function ($q) use ($user) {

                    $q->where(
                        'user_id',
                        $user->id
                    );
                }
            );


            $vehiculosQuery->where(
                'user_id',
                $user->id
            );


            $zonasQuery->where(
                'user_id',
                $user->id
            );

        } elseif (
            !$user->hasRole(
                'Super Administrador'
            )
        ) {

            $query->whereHas(
                'vehiculo',
                function ($q) use ($user) {

                    $q->where(
                        'empresa_id',
                        $user->empresa_id
                    );
                }
            );


            $vehiculosQuery->where(
                'empresa_id',
                $user->empresa_id
            );


            $zonasQuery->where(
                'empresa_id',
                $user->empresa_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO VEHÍCULO
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filtros['vehiculo_id']
            )
        ) {

            $query->where(
                'vehiculo_id',
                $filtros['vehiculo_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTRO ZONA
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filtros['geocerca_id']
            )
        ) {

            $query->where(
                'zona_id',
                $filtros['geocerca_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FECHA INICIO
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filtros['fecha_inicio']
            )
        ) {

            $fechaInicio =
                Carbon::parse(
                    $filtros['fecha_inicio']
                );


            $query->where(
                'fecha_entrada',
                '>=',
                $fechaInicio
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FECHA FIN
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filtros['fecha_fin']
            )
        ) {

            $fechaFin =
                Carbon::parse(
                    $filtros['fecha_fin']
                );


            $query->where(
                'fecha_entrada',
                '<=',
                $fechaFin
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ESTADO
        |--------------------------------------------------------------------------
        */

        if (
            !empty(
                $filtros['estado']
            )
        ) {

            switch (
                $filtros['estado']
            ) {

                /*
                | Vehículo todavía dentro
                */

                case 'dentro':

                    $query->whereNull(
                        'fecha_salida'
                    );

                    break;


                /*
                | Entrada y salida completadas
                */

                case 'finalizado':

                    $query->whereNotNull(
                        'fecha_salida'
                    );

                    break;


                /*
                | Cualquier tipo de alerta
                */

                case 'alerta':

                    $query->where(
                        function ($q) {

                            $q->where(
                                'alerta_generada',
                                true
                            )
                            ->orWhere(
                                'alerta_permanencia_generada',
                                true
                            );
                        }
                    );

                    break;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | RESULTADOS
        |--------------------------------------------------------------------------
        */

        $eventos =
            $query->paginate(20);


        $vehiculos =
            $vehiculosQuery->get();


        $geocercas =
            $zonasQuery->get();


        return view(
            'geocercas.historial',
            compact(
                'eventos',
                'vehiculos',
                'geocercas'
            )
        );
    }
}