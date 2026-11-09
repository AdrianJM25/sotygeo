<?php

namespace App\Http\Controllers;

use App\Models\Conductor;
use App\Models\Empresa;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ConductorController extends Controller
{
    /**
     * Mostrar listado de conductores.
     */
    public function index()
    {
        $user = auth()->user();

        $query = Conductor::with([
            'empresa',
            'vehiculo',
        ])
        ->latest();

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMINISTRADOR
        |--------------------------------------------------------------------------
        |
        | Puede consultar conductores
        | de todas las empresas.
        |
        */

        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {

            $empresas = Empresa::orderBy(
                'nombre'
            )
            ->get();


            $vehiculos = Vehiculo::orderBy(
                'nombre'
            )
            ->get();

        } else {

            /*
            |--------------------------------------------------------------------------
            | ADMINISTRADOR DE EMPRESA / GESTOR
            |--------------------------------------------------------------------------
            |
            | Solamente pueden consultar
            | conductores de su empresa.
            |
            */

            $query->where(
                'empresa_id',
                $user->empresa_id
            );


            $empresas = collect([
                $user->empresa,
            ]);


            $vehiculos = Vehiculo::where(
                'empresa_id',
                $user->empresa_id
            )
            ->orderBy(
                'nombre'
            )
            ->get();
        }


        $conductores = $query->paginate(
            10
        );


        return view(
            'conductores.index',
            compact(
                'conductores',
                'empresas',
                'vehiculos'
            )
        );
    }



    /**
     * Registrar un nuevo conductor.
     */
    public function store(
        Request $request
    ) {

        $user = auth()->user();


        /*
        |--------------------------------------------------------------------------
        | VALIDACIÓN
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'empresa_id' =>
                $user->hasRole(
                    'Super Administrador'
                )
                    ? 'required|exists:empresas,id'
                    : 'nullable',


            'vehiculo_id' =>
                'nullable|exists:vehiculos,id',


            'nombre' =>
                'required|string|max:100',


            'apellido_paterno' =>
                'nullable|string|max:100',


            'apellido_materno' =>
                'nullable|string|max:100',


            'telefono' =>
                'nullable|string|max:20',


            'email' => [

                'nullable',

                'email',

                'max:255',

                Rule::unique(
                    'conductores',
                    'email'
                ),

            ],


            'numero_licencia' => [

                'nullable',

                'string',

                'max:50',

                Rule::unique(
                    'conductores',
                    'numero_licencia'
                ),

            ],


            'vigencia_licencia' =>
                'nullable|date',


            /*
            |--------------------------------------------------------------------------
            | FOTO LICENCIA - FRENTE
            |--------------------------------------------------------------------------
            |
            | Máximo 5 MB.
            |
            */

            'licencia_frente' => [

                'nullable',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:5120',

            ],


            /*
            |--------------------------------------------------------------------------
            | FOTO LICENCIA - REVERSO
            |--------------------------------------------------------------------------
            */

            'licencia_reverso' => [

                'nullable',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:5120',

            ],


            'activo' =>
                'nullable|boolean',

        ]);



        /*
        |--------------------------------------------------------------------------
        | DETERMINAR EMPRESA
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {

            $empresaId =
                $validated[
                    'empresa_id'
                ];

        } else {

            $empresaId =
                $user->empresa_id;

        }



        /*
        |--------------------------------------------------------------------------
        | VALIDAR VEHÍCULO
        |--------------------------------------------------------------------------
        |
        | El vehículo debe pertenecer
        | a la misma empresa.
        |
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


            if (
                $vehiculo->empresa_id
                !=
                $empresaId
            ) {

                return back()

                    ->withErrors([
                        'vehiculo_id' =>
                            'El vehículo seleccionado pertenece a otra empresa.',
                    ])

                    ->withInput();

            }
        }



        /*
        |--------------------------------------------------------------------------
        | VALIDAR VEHÍCULO YA ASIGNADO
        |--------------------------------------------------------------------------
        |
        | Un vehículo solamente puede estar
        | asignado a un conductor activo.
        |
        */

        $activoNuevo =
            $request->boolean(
                'activo',
                true
            );

        if (
            $request->filled(
                'vehiculo_id'
            )
            &&
            $activoNuevo
        ) {

            $vehiculoOcupado =
                Conductor::query()
                    ->where(
                        'vehiculo_id',
                        $request->vehiculo_id
                    )
                    ->where(
                        'activo',
                        true
                    )
                    ->exists();


            if (
                $vehiculoOcupado
            ) {

                return back()

                    ->withErrors([
                        'vehiculo_id' =>
                            'Este vehículo ya está asignado a otro conductor activo.',
                    ])

                    ->withInput();
            }
        }



        /*
        |--------------------------------------------------------------------------
        | CREAR CONDUCTOR
        |--------------------------------------------------------------------------
        */

        $conductor = Conductor::create([

            'empresa_id' =>
                $empresaId,


            'vehiculo_id' =>
                $validated[
                    'vehiculo_id'
                ] ?? null,


            'nombre' =>
                $validated[
                    'nombre'
                ],


            'apellido_paterno' =>
                $validated[
                    'apellido_paterno'
                ] ?? null,


            'apellido_materno' =>
                $validated[
                    'apellido_materno'
                ] ?? null,


            'telefono' =>
                $validated[
                    'telefono'
                ] ?? null,


            'email' =>
                $validated[
                    'email'
                ] ?? null,


            'numero_licencia' =>
                $validated[
                    'numero_licencia'
                ] ?? null,


            'vigencia_licencia' =>
                $validated[
                    'vigencia_licencia'
                ] ?? null,


            'activo' =>
                $activoNuevo,

        ]);



        /*
        |--------------------------------------------------------------------------
        | GUARDAR IMÁGENES DE LICENCIA
        |--------------------------------------------------------------------------
        |
        | Los archivos se almacenan en
        | el disco privado "local".
        |
        | Ejemplo:
        |
        | conductores/licencias/12/xxxx.jpg
        |
        */

        $rutasLicencia = [];


        /*
        | Frente
        */

        if (
            $request->hasFile(
                'licencia_frente'
            )
        ) {

            $rutasLicencia[
                'licencia_frente_path'
            ] = $request
                ->file(
                    'licencia_frente'
                )
                ->store(
                    'conductores/licencias/'
                    .
                    $conductor->id,
                    'local'
                );

        }


        /*
        | Reverso
        */

        if (
            $request->hasFile(
                'licencia_reverso'
            )
        ) {

            $rutasLicencia[
                'licencia_reverso_path'
            ] = $request
                ->file(
                    'licencia_reverso'
                )
                ->store(
                    'conductores/licencias/'
                    .
                    $conductor->id,
                    'local'
                );

        }



        /*
        | Guardar las rutas
        | en PostgreSQL.
        */

        if (
            !empty(
                $rutasLicencia
            )
        ) {

            $conductor->update(
                $rutasLicencia
            );

        }



        return redirect()

            ->route(
                'conductores.index'
            )

            ->with(
                'success',
                'Conductor registrado correctamente.'
            );
    }



    /**
     * Actualizar conductor.
     */
    public function update(
        Request $request,
        Conductor $conductor
    ) {

        /*
        |--------------------------------------------------------------------------
        | VERIFICAR PERMISOS
        |--------------------------------------------------------------------------
        */

        $this
            ->verificarPropiedadConductor(
                $conductor
            );


        $user = auth()->user();



        /*
        |--------------------------------------------------------------------------
        | VALIDACIÓN
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'empresa_id' =>
                $user->hasRole(
                    'Super Administrador'
                )
                    ? 'required|exists:empresas,id'
                    : 'nullable',


            'vehiculo_id' =>
                'nullable|exists:vehiculos,id',


            'nombre' =>
                'required|string|max:100',


            'apellido_paterno' =>
                'nullable|string|max:100',


            'apellido_materno' =>
                'nullable|string|max:100',


            'telefono' =>
                'nullable|string|max:20',


            'email' => [

                'nullable',

                'email',

                'max:255',

                Rule::unique(
                    'conductores',
                    'email'
                )
                ->ignore(
                    $conductor->id
                ),

            ],


            'numero_licencia' => [

                'nullable',

                'string',

                'max:50',

                Rule::unique(
                    'conductores',
                    'numero_licencia'
                )
                ->ignore(
                    $conductor->id
                ),

            ],


            'vigencia_licencia' =>
                'nullable|date',


            /*
            |--------------------------------------------------------------------------
            | FOTO LICENCIA - FRENTE
            |--------------------------------------------------------------------------
            */

            'licencia_frente' => [

                'nullable',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:5120',

            ],


            /*
            |--------------------------------------------------------------------------
            | FOTO LICENCIA - REVERSO
            |--------------------------------------------------------------------------
            */

            'licencia_reverso' => [

                'nullable',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:5120',

            ],


            'activo' =>
                'nullable|boolean',

        ]);



        /*
        |--------------------------------------------------------------------------
        | EMPRESA
        |--------------------------------------------------------------------------
        */

        $empresaId =
            $user->hasRole(
                'Super Administrador'
            )

                ? $validated[
                    'empresa_id'
                ]

                : $conductor
                    ->empresa_id;



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


            if (
                $vehiculo->empresa_id
                !=
                $empresaId
            ) {

                return back()

                    ->withErrors([
                        'vehiculo_id' =>
                            'El vehículo seleccionado pertenece a otra empresa.',
                    ])

                    ->withInput();

            }
        }



        /*
        |--------------------------------------------------------------------------
        | VALIDAR VEHÍCULO YA ASIGNADO
        |--------------------------------------------------------------------------
        |
        | Ignora al conductor que se está editando.
        |
        */

        $activoNuevo =
            $request->boolean(
                'activo'
            );

        if (
            $request->filled(
                'vehiculo_id'
            )
            &&
            $activoNuevo
        ) {

            $vehiculoOcupado =
                Conductor::query()
                    ->where(
                        'vehiculo_id',
                        $request->vehiculo_id
                    )
                    ->where(
                        'activo',
                        true
                    )
                    ->where(
                        'id',
                        '!=',
                        $conductor->id
                    )
                    ->exists();


            if (
                $vehiculoOcupado
            ) {

                return back()

                    ->withErrors([
                        'vehiculo_id' =>
                            'Este vehículo ya está asignado a otro conductor activo.',
                    ])

                    ->withInput();
            }
        }



        /*
        |--------------------------------------------------------------------------
        | DATOS A ACTUALIZAR
        |--------------------------------------------------------------------------
        */

        $datos = [

            'empresa_id' =>
                $empresaId,


            'vehiculo_id' =>
                $validated[
                    'vehiculo_id'
                ] ?? null,


            'nombre' =>
                $validated[
                    'nombre'
                ],


            'apellido_paterno' =>
                $validated[
                    'apellido_paterno'
                ] ?? null,


            'apellido_materno' =>
                $validated[
                    'apellido_materno'
                ] ?? null,


            'telefono' =>
                $validated[
                    'telefono'
                ] ?? null,


            'email' =>
                $validated[
                    'email'
                ] ?? null,


            'numero_licencia' =>
                $validated[
                    'numero_licencia'
                ] ?? null,


            'vigencia_licencia' =>
                $validated[
                    'vigencia_licencia'
                ] ?? null,


            'activo' =>
                $activoNuevo,

        ];



        /*
        |--------------------------------------------------------------------------
        | GUARDAR NUEVA FOTO DEL FRENTE
        |--------------------------------------------------------------------------
        */

        $licenciaFrenteAnterior =
            $conductor
                ->licencia_frente_path;


        $licenciaReversoAnterior =
            $conductor
                ->licencia_reverso_path;


        if (
            $request->hasFile(
                'licencia_frente'
            )
        ) {

            $datos[
                'licencia_frente_path'
            ] = $request
                ->file(
                    'licencia_frente'
                )
                ->store(
                    'conductores/licencias/'
                    .
                    $conductor->id,
                    'local'
                );

        }



        /*
        |--------------------------------------------------------------------------
        | GUARDAR NUEVA FOTO DEL REVERSO
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile(
                'licencia_reverso'
            )
        ) {

            $datos[
                'licencia_reverso_path'
            ] = $request
                ->file(
                    'licencia_reverso'
                )
                ->store(
                    'conductores/licencias/'
                    .
                    $conductor->id,
                    'local'
                );

        }



        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR CONDUCTOR
        |--------------------------------------------------------------------------
        */

        $conductor->update(
            $datos
        );



        /*
        |--------------------------------------------------------------------------
        | ELIMINAR ARCHIVOS ANTERIORES
        |--------------------------------------------------------------------------
        |
        | Solamente se eliminan cuando
        | fueron reemplazados.
        |
        */

        if (
            $request->hasFile(
                'licencia_frente'
            )
            &&
            $licenciaFrenteAnterior
        ) {

            Storage::disk(
                'local'
            )
            ->delete(
                $licenciaFrenteAnterior
            );

        }


        if (
            $request->hasFile(
                'licencia_reverso'
            )
            &&
            $licenciaReversoAnterior
        ) {

            Storage::disk(
                'local'
            )
            ->delete(
                $licenciaReversoAnterior
            );

        }



        return redirect()

            ->route(
                'conductores.index'
            )

            ->with(
                'success',
                'Conductor actualizado correctamente.'
            );
    }



    /**
     * Activar o desactivar conductor.
     */
    public function cambiarEstado(
        Conductor $conductor
    ) {

        /*
        |--------------------------------------------------------------------------
        | VERIFICAR PERMISOS
        |--------------------------------------------------------------------------
        */

        $this
            ->verificarPropiedadConductor(
                $conductor
            );



        /*
        |--------------------------------------------------------------------------
        | VALIDAR VEHÍCULO AL ACTIVAR
        |--------------------------------------------------------------------------
        |
        | Si actualmente está inactivo y se intenta
        | activar, comprobamos que su vehículo no
        | esté ocupado por otro conductor activo.
        |
        */

        if (
            !$conductor->activo
            &&
            $conductor->vehiculo_id
        ) {

            $vehiculoOcupado =
                Conductor::query()
                    ->where(
                        'vehiculo_id',
                        $conductor->vehiculo_id
                    )
                    ->where(
                        'activo',
                        true
                    )
                    ->where(
                        'id',
                        '!=',
                        $conductor->id
                    )
                    ->exists();


            if (
                $vehiculoOcupado
            ) {

                return redirect()

                    ->route(
                        'conductores.index'
                    )

                    ->withErrors([
                        'vehiculo_id' =>
                            'No se puede activar este conductor porque su vehículo ya está asignado a otro conductor activo.',
                    ]);
            }
        }



        /*
        |--------------------------------------------------------------------------
        | CAMBIAR ESTADO
        |--------------------------------------------------------------------------
        */

        $conductor->update([

            'activo' =>
                !$conductor->activo,

        ]);



        /*
        |--------------------------------------------------------------------------
        | MENSAJE
        |--------------------------------------------------------------------------
        */

        $mensaje =
            $conductor->activo

                ? 'Conductor activado correctamente.'

                : 'Conductor desactivado correctamente.';



        return redirect()

            ->route(
                'conductores.index'
            )

            ->with(
                'success',
                $mensaje
            );
    }



    /**
     * Mostrar una fotografía privada
     * de la licencia del conductor.
     */
    public function verLicencia(
        Conductor $conductor,
        string $lado
    ) {

        /*
        |--------------------------------------------------------------------------
        | VERIFICAR PERMISOS
        |--------------------------------------------------------------------------
        */

        $this
            ->verificarPropiedadConductor(
                $conductor
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR LADO
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $lado,
                [
                    'frente',
                    'reverso',
                ],
                true
            )
        ) {

            abort(404);

        }


        /*
        |--------------------------------------------------------------------------
        | OBTENER RUTA
        |--------------------------------------------------------------------------
        */

        $path =
            $lado === 'frente'

                ? $conductor
                    ->licencia_frente_path

                : $conductor
                    ->licencia_reverso_path;


        /*
        |--------------------------------------------------------------------------
        | COMPROBAR ARCHIVO
        |--------------------------------------------------------------------------
        */

        if (
            !$path
            ||
            !Storage::disk('local')
                ->exists($path)
        ) {

            abort(
                404,
                'La fotografía de la licencia no está disponible.'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | MOSTRAR IMAGEN
        |--------------------------------------------------------------------------
        |
        | El archivo permanece privado.
        | Laravel lo entrega solamente después
        | de comprobar los permisos.
        |
        */

        return Storage::disk('local')
            ->response(
                $path,
                basename($path),
                [
                    'Cache-Control' =>
                        'private, no-store',
                ],
                'inline'
            );
    }



    /**
     * Verificar que el usuario tenga permiso
     * sobre el conductor.
     */
    private function verificarPropiedadConductor(
        Conductor $conductor
    ) {

        $user = auth()->user();



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
        | OTROS ROLES
        |--------------------------------------------------------------------------
        |
        | Solamente pueden trabajar con
        | conductores de su misma empresa.
        |
        */

        if (
            $conductor->empresa_id
            !==
            $user->empresa_id
        ) {

            abort(
                403,
                'El conductor pertenece a otra empresa.'
            );

        }
    }
}