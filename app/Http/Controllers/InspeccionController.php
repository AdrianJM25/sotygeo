<?php

namespace App\Http\Controllers;

use App\Models\Conductor;
use App\Models\DetalleInspeccion;
use App\Models\ElementoInspeccion;
use App\Models\Inspeccion;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InspeccionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO / HISTORIAL DE INSPECCIONES
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = auth()->user();


        /*
        |--------------------------------------------------------------------------
        | VEHÍCULOS DISPONIBLES
        |--------------------------------------------------------------------------
        */

        $vehiculos =
            $this->vehiculosPermitidos()
                ->orderBy('nombre')
                ->get();


        /*
        |--------------------------------------------------------------------------
        | ELEMENTOS ACTIVOS
        |--------------------------------------------------------------------------
        */

        $elementos =
            ElementoInspeccion::query()
                ->where(
                    'activo',
                    true
                )
                ->orderBy('categoria')
                ->orderBy('nombre')
                ->get();


        /*
        |--------------------------------------------------------------------------
        | INSPECCIONES VISIBLES
        |--------------------------------------------------------------------------
        */

        $query =
            Inspeccion::with([
                'vehiculo',
                'usuario',
                'conductor',
                'detalles.elemento',
            ])
            ->latest(
                'fecha_inspeccion'
            );


        /*
        |--------------------------------------------------------------------------
        | SUPER ADMINISTRADOR
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {

            // Puede consultar todas.

        }


        /*
        |--------------------------------------------------------------------------
        | CONDUCTOR
        |--------------------------------------------------------------------------
        */

        elseif (
            $user->hasRole(
                'Conductor'
            )
        ) {

            $conductor =
                $this->conductorActual();


            if (
                $conductor
                &&
                $conductor->vehiculo_id
            ) {

                $query->where(
                    'vehiculo_id',
                    $conductor->vehiculo_id
                );

            } else {

                $query->whereRaw(
                    '1 = 0'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | ADMINISTRADOR / GESTOR
        |--------------------------------------------------------------------------
        */

        elseif (
            $user->hasAnyRole([
                'Administrador de Empresa',
                'Gestor de flotilla',
            ])
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
        }


        /*
        |--------------------------------------------------------------------------
        | OTROS ROLES
        |--------------------------------------------------------------------------
        */

        else {

            abort(
                403,
                'No tienes permiso para consultar inspecciones.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | OBTENER REGISTROS
        |--------------------------------------------------------------------------
        */

        $inspecciones =
            $query
                ->limit(50)
                ->get();


        return view(
            'inspecciones.index',
            compact(
                'vehiculos',
                'elementos',
                'inspecciones'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | REGISTRAR INSPECCIÓN
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request
    ) {

        $user =
            auth()->user();


        /*
        |--------------------------------------------------------------------------
        | VALIDACIÓN GENERAL
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate([
                'vehiculo_id' => [
                    'required',
                    'integer',
                    'exists:vehiculos,id',
                ],

                'observaciones' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],

                'detalles' => [
                    'required',
                    'array',
                ],

                'detalles.*.resultado' => [
                    'nullable',
                    'string',
                    'in:correcto,incidencia',
                ],

                'detalles.*.observaciones' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],
            ]);


        /*
        |--------------------------------------------------------------------------
        | VEHÍCULO
        |--------------------------------------------------------------------------
        */

        $vehiculo =
            Vehiculo::findOrFail(
                $validated[
                    'vehiculo_id'
                ]
            );


        /*
        |--------------------------------------------------------------------------
        | VERIFICAR ACCESO AL VEHÍCULO
        |--------------------------------------------------------------------------
        */

        $this->verificarAccesoVehiculo(
            $vehiculo
        );


        /*
        |--------------------------------------------------------------------------
        | ELEMENTOS OBLIGATORIOS
        |--------------------------------------------------------------------------
        |
        | FF.16:
        | Una inspección incompleta no debe guardarse.
        |
        */

        $elementos =
            ElementoInspeccion::query()
                ->where(
                    'activo',
                    true
                )
                ->orderBy('id')
                ->get();


        if (
            $elementos->isEmpty()
        ) {

            throw ValidationException::withMessages([
                'detalles' =>
                    'No existen elementos de inspección configurados.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | COMPROBAR CADA ELEMENTO
        |--------------------------------------------------------------------------
        */

        foreach (
            $elementos
            as $elemento
        ) {

            $detalle =
                $request->input(
                    'detalles.'
                    .
                    $elemento->id
                );


            if (
                !is_array(
                    $detalle
                )
                ||
                empty(
                    $detalle[
                        'resultado'
                    ]
                    ??
                    null
                )
            ) {

                throw ValidationException::withMessages([
                    'detalles.'
                    .
                    $elemento->id
                    .
                    '.resultado'
                    =>
                    'Debes indicar el estado de '
                    .
                    $elemento->nombre
                    .
                    '.',
                ]);
            }


            if (
                !in_array(
                    $detalle[
                        'resultado'
                    ],
                    [
                        'correcto',
                        'incidencia',
                    ],
                    true
                )
            ) {

                throw ValidationException::withMessages([
                    'detalles.'
                    .
                    $elemento->id
                    .
                    '.resultado'
                    =>
                    'El estado seleccionado para '
                    .
                    $elemento->nombre
                    .
                    ' no es válido.',
                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CONDUCTOR RELACIONADO
        |--------------------------------------------------------------------------
        */

        $conductorId =
            null;


        if (
            $user->hasRole(
                'Conductor'
            )
        ) {

            $conductor =
                $this->conductorActual();


            if (
                !$conductor
            ) {

                abort(
                    403,
                    'No existe un conductor activo asociado a esta cuenta.'
                );
            }


            $conductorId =
                $conductor->id;
        }


        /*
        |--------------------------------------------------------------------------
        | RESULTADO GENERAL
        |--------------------------------------------------------------------------
        */

        $tieneIncidencias =
            false;


        foreach (
            $elementos
            as $elemento
        ) {

            $resultado =
                $request->input(
                    'detalles.'
                    .
                    $elemento->id
                    .
                    '.resultado'
                );


            if (
                $resultado
                ===
                'incidencia'
            ) {

                $tieneIncidencias =
                    true;

                break;
            }
        }


        $resultadoGeneral =
            $tieneIncidencias
                ?
                'con_incidencias'
                :
                'correcto';


        /*
        |--------------------------------------------------------------------------
        | GUARDAR EN TRANSACCIÓN
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $request,
                $vehiculo,
                $user,
                $conductorId,
                $resultadoGeneral,
                $elementos
            ) {

                /*
                |--------------------------------------------------------------------------
                | INSPECCIÓN
                |--------------------------------------------------------------------------
                */

                $inspeccion =
                    Inspeccion::create([
                        'vehiculo_id' =>
                            $vehiculo->id,

                        'user_id' =>
                            $user->id,

                        'conductor_id' =>
                            $conductorId,

                        'fecha_inspeccion' =>
                            now(),

                        'resultado_general' =>
                            $resultadoGeneral,

                        'observaciones' =>
                            $request->input(
                                'observaciones'
                            ),
                    ]);


                /*
                |--------------------------------------------------------------------------
                | DETALLES
                |--------------------------------------------------------------------------
                */

                foreach (
                    $elementos
                    as $elemento
                ) {

                    DetalleInspeccion::create([
                        'inspeccion_id' =>
                            $inspeccion->id,

                        'elemento_id' =>
                            $elemento->id,

                        'resultado' =>
                            $request->input(
                                'detalles.'
                                .
                                $elemento->id
                                .
                                '.resultado'
                            ),

                        'observaciones' =>
                            $request->input(
                                'detalles.'
                                .
                                $elemento->id
                                .
                                '.observaciones'
                            ),
                    ]);
                }
            }
        );


        return redirect()
            ->route(
                'inspecciones.index'
            )
            ->with(
                'success',
                $resultadoGeneral
                ===
                'con_incidencias'

                    ?

                    'Inspección registrada correctamente. Se detectaron incidencias.'

                    :

                    'Inspección registrada correctamente sin incidencias.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | VEHÍCULOS PERMITIDOS
    |--------------------------------------------------------------------------
    */

    private function vehiculosPermitidos()
    {
        $user =
            auth()->user();


        $query =
            Vehiculo::query();


        /*
        |--------------------------------------------------------------------------
        | SUPER ADMINISTRADOR
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {

            return $query;
        }


        /*
        |--------------------------------------------------------------------------
        | CONDUCTOR
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Conductor'
            )
        ) {

            $conductor =
                $this->conductorActual();


            if (
                $conductor
                &&
                $conductor->vehiculo_id
            ) {

                return $query
                    ->where(
                        'id',
                        $conductor->vehiculo_id
                    );
            }


            return $query
                ->whereRaw(
                    '1 = 0'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | ADMINISTRADOR DE EMPRESA / GESTOR
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasAnyRole([
                'Administrador de Empresa',
                'Gestor de flotilla',
            ])
        ) {

            return $query
                ->where(
                    'empresa_id',
                    $user->empresa_id
                );
        }


        /*
        |--------------------------------------------------------------------------
        | OTROS ROLES
        |--------------------------------------------------------------------------
        */

        return $query
            ->whereRaw(
                '1 = 0'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFICAR ACCESO A VEHÍCULO
    |--------------------------------------------------------------------------
    */

    private function verificarAccesoVehiculo(
        Vehiculo $vehiculo
    ): void {

        $user =
            auth()->user();


        /*
        |--------------------------------------------------------------------------
        | SUPER ADMINISTRADOR
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CONDUCTOR
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Conductor'
            )
        ) {

            $conductor =
                $this->conductorActual();


            if (
                !$conductor
                ||
                !$conductor->vehiculo_id
                ||
                (int)
                $conductor->vehiculo_id
                !==
                (int)
                $vehiculo->id
            ) {

                abort(
                    403,
                    'Solo puedes inspeccionar el vehículo que tienes asignado.'
                );
            }


            return;
        }


        /*
        |--------------------------------------------------------------------------
        | ADMINISTRADOR / GESTOR
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasAnyRole([
                'Administrador de Empresa',
                'Gestor de flotilla',
            ])
        ) {

            if (
                (int)
                $vehiculo->empresa_id
                !==
                (int)
                $user->empresa_id
            ) {

                abort(
                    403,
                    'El vehículo pertenece a otra empresa.'
                );
            }


            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CUALQUIER OTRO ROL
        |--------------------------------------------------------------------------
        */

        abort(
            403,
            'No tienes permiso para registrar inspecciones.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CONDUCTOR ACTUAL
    |--------------------------------------------------------------------------
    |
    | Al igual que en Historial y Dashboard,
    | relacionamos temporalmente la cuenta de usuario
    | con el conductor mediante el correo electrónico.
    |
    */

    private function conductorActual(): ?Conductor
    {
        $user =
            auth()->user();


        return Conductor::query()
            ->where(
                'email',
                $user->email
            )
            ->where(
                'activo',
                true
            )
            ->first();
    }
}