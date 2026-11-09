<?php

namespace App\Http\Controllers;

use App\Models\Dispositivo;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DispositivoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $query = Dispositivo::with([
            'vehiculo',
            'empresa',
            'usuario',
        ])->latest();

        $vehiculosQuery =
            Vehiculo::with('dispositivo');


        /*
        |--------------------------------------------------------------------------
        | FILTROS MULTI-TENANT
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Cliente Individual'
            )
        ) {

            $query->where(
                'user_id',
                $user->id
            );

            $vehiculosQuery->where(
                'user_id',
                $user->id
            );

            $empresas = collect();
            $clientes = collect();

        } elseif (
            !$user->hasRole(
                'Super Administrador'
            )
        ) {

            $query->where(
                'empresa_id',
                $user->empresa_id
            );

            $vehiculosQuery->where(
                'empresa_id',
                $user->empresa_id
            );

            $empresas = collect([
                $user->empresa
            ]);

            $clientes = collect();

        } else {

            $empresas =
                Empresa::orderBy('nombre')
                    ->get();

            $clientes =
                User::role(
                    'Cliente Individual'
                )
                    ->orderBy('nombre')
                    ->get();
        }


        $dispositivos =
            $query->paginate(10);


        /*
        |--------------------------------------------------------------------------
        | VEHÍCULOS
        |--------------------------------------------------------------------------
        |
        | vehiculos:
        | Todos los vehículos permitidos.
        | Se utilizan al editar.
        |
        | vehiculosDisponibles:
        | Solo vehículos sin GPS.
        | Se utilizan al registrar.
        |
        */

        $vehiculos =
            (clone $vehiculosQuery)
                ->orderBy('nombre')
                ->get();


        $vehiculosDisponibles =
            (clone $vehiculosQuery)
                ->whereDoesntHave(
                    'dispositivo'
                )
                ->orderBy('nombre')
                ->get();


        return view(
            'dispositivos.index',
            compact(
                'dispositivos',
                'vehiculos',
                'vehiculosDisponibles',
                'empresas',
                'clientes'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | REGISTRAR
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request
    ) {

        $user = auth()->user();


        $datos =
            $request->validate([
                'imei' => [
                    'required',
                    'string',
                    'max:20',
                    'unique:dispositivos,imei',
                ],

                'numero_sim' => [
                    'nullable',
                    'string',
                    'max:20',
                ],

                'modelo' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'capacidad_bateria_mah' => [
                    'nullable',
                    'integer',
                    'min:0',
                ],

                'vehiculo_id' => [
                    'nullable',
                    'exists:vehiculos,id',
                    'unique:dispositivos,vehiculo_id',
                ],

                'modo_reposo' => [
                    'nullable',
                    'boolean',
                ],

                'activo' => [
                    'nullable',
                    'boolean',
                ],

                'empresa_id' => [
                    'nullable',
                    'exists:empresas,id',
                ],

                'user_id' => [
                    'nullable',
                    'exists:users,id',
                ],
            ]);


        /*
        |--------------------------------------------------------------------------
        | EVITAR DOS PROPIETARIOS
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Super Administrador'
            )
            &&
            $request->filled('empresa_id')
            &&
            $request->filled('user_id')
        ) {

            throw ValidationException::withMessages([
                'empresa_id' =>
                    'Selecciona una empresa o un cliente individual, no ambos.',

                'user_id' =>
                    'Selecciona una empresa o un cliente individual, no ambos.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | DETERMINAR PROPIETARIO AL REGISTRAR
        |--------------------------------------------------------------------------
        */

        $empresaId = null;
        $userId = null;


        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {

            $empresaId =
                $request->filled(
                    'empresa_id'
                )
                    ? $request->empresa_id
                    : null;


            $userId =
                $request->filled(
                    'user_id'
                )
                    ? $request->user_id
                    : null;


            if ($userId) {

                $cliente =
                    User::findOrFail(
                        $userId
                    );


                if (
                    !$cliente->hasRole(
                        'Cliente Individual'
                    )
                ) {

                    throw ValidationException::withMessages([
                        'user_id' =>
                            'El usuario seleccionado debe ser un Cliente Individual.',
                    ]);
                }
            }

        } elseif (
            $user->hasRole(
                'Cliente Individual'
            )
        ) {

            $userId =
                $user->id;

        } else {

            $empresaId =
                $user->empresa_id;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR VEHÍCULO
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'vehiculo_id'
            )
        ) {

            $vehiculo =
                Vehiculo::findOrFail(
                    $request->vehiculo_id
                );


            /*
            | Si el Super Admin no eligió propietario
            | pero seleccionó un vehículo, el GPS
            | hereda el propietario del vehículo.
            */

            if (
                $user->hasRole(
                    'Super Administrador'
                )
                &&
                !$empresaId
                &&
                !$userId
            ) {

                $empresaId =
                    $vehiculo->empresa_id;

                $userId =
                    $vehiculo->user_id;
            }


            $this->validarPropietarioVehiculo(
                $vehiculo,
                $empresaId,
                $userId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CREAR
        |--------------------------------------------------------------------------
        */

        Dispositivo::create([
            'empresa_id' =>
                $empresaId,

            'user_id' =>
                $userId,

            'vehiculo_id' =>
                $datos['vehiculo_id']
                    ?? null,

            'imei' =>
                $datos['imei'],

            'numero_sim' =>
                $datos['numero_sim']
                    ?? null,

            'modelo' =>
                $datos['modelo']
                    ?? null,

            'capacidad_bateria_mah' =>
                $datos['capacidad_bateria_mah']
                    ?? null,

            'modo_reposo' =>
                $request->boolean(
                    'modo_reposo'
                ),

            'activo' =>
                $request->boolean(
                    'activo'
                ),
        ]);


        return redirect()
            ->route(
                'dispositivos.index'
            )
            ->with(
                'success',
                'Dispositivo registrado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Dispositivo $dispositivo
    ) {

        $this->verificarAccesoDispositivo(
            $dispositivo
        );


        $user =
            auth()->user();


        /*
        |--------------------------------------------------------------------------
        | REGLAS
        |--------------------------------------------------------------------------
        */

        $reglas = [
            'imei' => [
                'required',
                'string',
                'max:20',
                'unique:dispositivos,imei,'
                    . $dispositivo->id,
            ],

            'numero_sim' => [
                'nullable',
                'string',
                'max:20',
            ],

            'modelo' => [
                'nullable',
                'string',
                'max:255',
            ],

            'capacidad_bateria_mah' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'vehiculo_id' => [
                'nullable',
                'exists:vehiculos,id',
                'unique:dispositivos,vehiculo_id,'
                    . $dispositivo->id,
            ],

            'modo_reposo' => [
                'nullable',
                'boolean',
            ],

            'activo' => [
                'nullable',
                'boolean',
            ],
        ];


        /*
        | El select propietario solo existe
        | para Super Administrador.
        */

        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {

            $reglas['propietario'] = [
                'required',
                'string',
            ];
        }


        $datos =
            $request->validate(
                $reglas
            );


        /*
        |--------------------------------------------------------------------------
        | DETERMINAR PROPIETARIO AL ACTUALIZAR
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {

            $propietario =
                $request->input(
                    'propietario'
                );


            $empresaId = null;
            $userId = null;


            /*
            | STOCK SOTYTECH
            */

            if (
                $propietario ===
                'stock'
            ) {

                $empresaId = null;
                $userId = null;

            }


            /*
            | EMPRESA
            */

            elseif (
                str_starts_with(
                    $propietario,
                    'empresa:'
                )
            ) {

                $empresaId =
                    (int) str_replace(
                        'empresa:',
                        '',
                        $propietario
                    );


                if (
                    !Empresa::where(
                        'id',
                        $empresaId
                    )->exists()
                ) {

                    throw ValidationException::withMessages([
                        'propietario' =>
                            'La empresa seleccionada no existe.',
                    ]);
                }


                $userId = null;

            }


            /*
            | CLIENTE INDIVIDUAL
            */

            elseif (
                str_starts_with(
                    $propietario,
                    'cliente:'
                )
            ) {

                $userId =
                    (int) str_replace(
                        'cliente:',
                        '',
                        $propietario
                    );


                $cliente =
                    User::findOrFail(
                        $userId
                    );


                if (
                    !$cliente->hasRole(
                        'Cliente Individual'
                    )
                ) {

                    throw ValidationException::withMessages([
                        'propietario' =>
                            'El usuario seleccionado no es un Cliente Individual.',
                    ]);
                }


                $empresaId = null;

            }


            /*
            | VALOR INVÁLIDO
            */

            else {

                throw ValidationException::withMessages([
                    'propietario' =>
                        'Selecciona un propietario válido.',
                ]);
            }

        } else {

            /*
            | Los demás usuarios no pueden
            | cambiar el propietario del GPS.
            */

            $empresaId =
                $dispositivo->empresa_id;

            $userId =
                $dispositivo->user_id;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR VEHÍCULO
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'vehiculo_id'
            )
        ) {

            $vehiculo =
                Vehiculo::findOrFail(
                    $request->vehiculo_id
                );


            /*
            | Si se seleccionó Stock SOTyTECH,
            | pero el GPS sigue vinculado a un
            | vehículo, heredamos el propietario
            | de ese vehículo.
            |
            | Esto evita tener un GPS marcado como
            | "Stock" mientras está instalado.
            */

            if (
                $user->hasRole(
                    'Super Administrador'
                )
                &&
                !$empresaId
                &&
                !$userId
            ) {

                $empresaId =
                    $vehiculo->empresa_id;

                $userId =
                    $vehiculo->user_id;
            }


            $this->validarPropietarioVehiculo(
                $vehiculo,
                $empresaId,
                $userId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR REGISTRO
        |--------------------------------------------------------------------------
        */

        $dispositivo->update([
            'empresa_id' =>
                $empresaId,

            'user_id' =>
                $userId,

            'vehiculo_id' =>
                $datos['vehiculo_id']
                    ?? null,

            'imei' =>
                $datos['imei'],

            'numero_sim' =>
                $datos['numero_sim']
                    ?? null,

            'modelo' =>
                $datos['modelo']
                    ?? null,

            'capacidad_bateria_mah' =>
                $datos['capacidad_bateria_mah']
                    ?? null,

            'modo_reposo' =>
                $request->boolean(
                    'modo_reposo'
                ),

            'activo' =>
                $request->boolean(
                    'activo'
                ),
        ]);


        return redirect()
            ->route(
                'dispositivos.index'
            )
            ->with(
                'success',
                'Dispositivo actualizado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ACTIVAR / DESACTIVAR
    |--------------------------------------------------------------------------
    */

    public function cambiarEstado(
        Dispositivo $dispositivo
    ) {

        $this->verificarAccesoDispositivo(
            $dispositivo
        );


        $dispositivo->update([
            'activo' =>
                !$dispositivo->activo,
        ]);


        return redirect()
            ->route(
                'dispositivos.index'
            )
            ->with(
                'success',
                $dispositivo->activo
                    ? 'Dispositivo activado correctamente.'
                    : 'Dispositivo desactivado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Dispositivo $dispositivo
    ) {

        $this->verificarAccesoDispositivo(
            $dispositivo
        );


        /*
        | Si el GPS ya cuenta con historial,
        | no se elimina físicamente.
        */

        if (
            $dispositivo
                ->ubicaciones()
                ->exists()
        ) {

            return redirect()
                ->route(
                    'dispositivos.index'
                )
                ->with(
                    'error',
                    'Este GPS ya tiene ubicaciones registradas. Desactívalo en lugar de eliminarlo.'
                );
        }


        $dispositivo->delete();


        return redirect()
            ->route(
                'dispositivos.index'
            )
            ->with(
                'success',
                'Dispositivo eliminado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFICAR ACCESO
    |--------------------------------------------------------------------------
    */

    private function verificarAccesoDispositivo(
        Dispositivo $dispositivo
    ): void {

        $user =
            auth()->user();


        /*
        | Super Admin puede administrar todos.
        */

        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {
            return;
        }


        /*
        | Cliente Individual:
        | únicamente sus propios GPS.
        */

        if (
            $user->hasRole(
                'Cliente Individual'
            )
        ) {

            if (
                (int) $dispositivo->user_id
                !==
                (int) $user->id
            ) {

                abort(
                    403,
                    'Acceso denegado a este GPS.'
                );
            }


            return;
        }


        /*
        | Usuarios de empresa:
        | únicamente GPS de su empresa.
        */

        if (
            (int) $dispositivo->empresa_id
            !==
            (int) $user->empresa_id
        ) {

            abort(
                403,
                'El GPS pertenece a otra empresa.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR PROPIETARIO DEL VEHÍCULO
    |--------------------------------------------------------------------------
    */

    private function validarPropietarioVehiculo(
        Vehiculo $vehiculo,
        $empresaId,
        $userId
    ): void {

        /*
        | Empresa
        */

        if (
            $empresaId
            &&
            (int) $vehiculo->empresa_id
            !==
            (int) $empresaId
        ) {

            throw ValidationException::withMessages([
                'vehiculo_id' =>
                    'El vehículo seleccionado pertenece a otra empresa.',
            ]);
        }


        /*
        | Cliente Individual
        */

        if (
            $userId
            &&
            (int) $vehiculo->user_id
            !==
            (int) $userId
        ) {

            throw ValidationException::withMessages([
                'vehiculo_id' =>
                    'El vehículo seleccionado no pertenece al cliente seleccionado.',
            ]);
        }
    }
}