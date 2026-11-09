<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ZonaController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $query = Zona::select(
            'id',
            'empresa_id',
            'user_id',
            'nombre',
            'color_hex',
            DB::raw('ST_AsGeoJSON(poligono) as geojson'),
            'created_at'
        )
        ->with([
            'vehiculos',
            'empresa',
            'usuario',
        ]);

        /*
        |--------------------------------------------------------------------------
        | FILTRO MULTI-TENANT
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Cliente Individual')) {

            $query->where(
                'user_id',
                $user->id
            );

            $vehiculos = Vehiculo::where(
                'user_id',
                $user->id
            )
            ->orderBy('nombre')
            ->get();

            $empresas = collect();
            $clientes = collect();

        } elseif (!$user->hasRole('Super Administrador')) {

            $query->where(
                'empresa_id',
                $user->empresa_id
            );

            $vehiculos = Vehiculo::where(
                'empresa_id',
                $user->empresa_id
            )
            ->orderBy('nombre')
            ->get();

            $empresas = collect([
                $user->empresa
            ]);

            $clientes = collect();

        } else {

            $vehiculos = Vehiculo::orderBy('nombre')
                ->get();

            $empresas = Empresa::orderBy('nombre')
                ->get();

            $clientes = User::role('Cliente Individual')
                ->orderBy('nombre')
                ->get();
        }

        $zonas = $query
            ->latest()
            ->get();

        return view(
            'zonas.index',
            compact(
                'zonas',
                'vehiculos',
                'empresas',
                'clientes'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | REGISTRAR ZONA
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $user = auth()->user();

        $reglas = [
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],

            'color_hex' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            'coordenadas' => [
                'required',
                'string',
            ],

            'vehiculos' => [
                'nullable',
                'array',
            ],

            'vehiculos.*.id' => [
                'required',
                'exists:vehiculos,id',
            ],

            'vehiculos.*.tipo_regla' => [
                'required',
                'in:permitida,restringida,informativa',
            ],

            'vehiculos.*.notificar_entrada' => [
                'nullable',
                'boolean',
            ],

            'vehiculos.*.notificar_salida' => [
                'nullable',
                'boolean',
            ],

            'vehiculos.*.permanencia_minima_minutos' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'vehiculos.*.permanencia_maxima_minutos' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];

        if ($user->hasRole('Super Administrador')) {
            $reglas['propietario'] = [
                'required',
                'string',
            ];
        }

        $datos = $request->validate($reglas);


        /*
        |--------------------------------------------------------------------------
        | DETERMINAR PROPIETARIO
        |--------------------------------------------------------------------------
        */

        [$empresaId, $userId] =
            $this->resolverPropietario(
                $request
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR POLÍGONO
        |--------------------------------------------------------------------------
        */

        $stringPoligono =
            $this->parsearCoordenadasAPoligono(
                $request->coordenadas
            );

        if (!$stringPoligono) {

            throw ValidationException::withMessages([
                'coordenadas' =>
                    'Debes dibujar un polígono válido con al menos 3 puntos.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | PREPARAR VEHÍCULOS
        |--------------------------------------------------------------------------
        */

        $syncData =
            $this->prepararVehiculos(
                $request->input(
                    'vehiculos',
                    []
                ),
                $empresaId,
                $userId
            );


        /*
        |--------------------------------------------------------------------------
        | CREAR EN TRANSACCIÓN
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $datos,
            $empresaId,
            $userId,
            $stringPoligono,
            $syncData
        ) {

            $zona = Zona::create([
                'empresa_id' =>
                    $empresaId,

                'user_id' =>
                    $userId,

                'nombre' =>
                    $datos['nombre'],

                'color_hex' =>
                    $datos['color_hex'],

                'poligono' =>
                    DB::raw(
                        "ST_GeomFromText('$stringPoligono', 4326)"
                    ),
            ]);

            $zona->vehiculos()->sync(
                $syncData
            );
        });


        return redirect()
            ->route('zonas.index')
            ->with(
                'success',
                'Geocerca creada correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Zona $zona
    ) {

        $this->verificarPropiedadZona(
            $zona
        );

        $user = auth()->user();

        $reglas = [
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],

            'color_hex' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],

            'coordenadas' => [
                'nullable',
                'string',
            ],

            'vehiculos' => [
                'nullable',
                'array',
            ],

            'vehiculos.*.id' => [
                'required',
                'exists:vehiculos,id',
            ],

            'vehiculos.*.tipo_regla' => [
                'required',
                'in:permitida,restringida,informativa',
            ],

            'vehiculos.*.notificar_entrada' => [
                'nullable',
                'boolean',
            ],

            'vehiculos.*.notificar_salida' => [
                'nullable',
                'boolean',
            ],

            'vehiculos.*.permanencia_minima_minutos' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'vehiculos.*.permanencia_maxima_minutos' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ];

        if ($user->hasRole('Super Administrador')) {
            $reglas['propietario'] = [
                'required',
                'string',
            ];
        }

        $datos = $request->validate(
            $reglas
        );


        /*
        |--------------------------------------------------------------------------
        | PROPIETARIO
        |--------------------------------------------------------------------------
        */

        [$empresaId, $userId] =
            $this->resolverPropietario(
                $request,
                $zona
            );


        /*
        |--------------------------------------------------------------------------
        | VEHÍCULOS
        |--------------------------------------------------------------------------
        */

        $syncData =
            $this->prepararVehiculos(
                $request->input(
                    'vehiculos',
                    []
                ),
                $empresaId,
                $userId
            );


        /*
        |--------------------------------------------------------------------------
        | DATOS GENERALES
        |--------------------------------------------------------------------------
        */

        $datosActualizar = [
            'empresa_id' =>
                $empresaId,

            'user_id' =>
                $userId,

            'nombre' =>
                $datos['nombre'],

            'color_hex' =>
                $datos['color_hex'],
        ];


        /*
        |--------------------------------------------------------------------------
        | POLÍGONO
        |--------------------------------------------------------------------------
        */

        if (
            $request->filled(
                'coordenadas'
            )
        ) {

            $stringPoligono =
                $this->parsearCoordenadasAPoligono(
                    $request->coordenadas
                );

            if (!$stringPoligono) {

                throw ValidationException::withMessages([
                    'coordenadas' =>
                        'El polígono de la geocerca no es válido.',
                ]);
            }

            $datosActualizar['poligono'] =
                DB::raw(
                    "ST_GeomFromText('$stringPoligono', 4326)"
                );
        }


        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR EN TRANSACCIÓN
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $zona,
            $datosActualizar,
            $syncData
        ) {

            $zona->update(
                $datosActualizar
            );

            $zona->vehiculos()->sync(
                $syncData
            );
        });


        return redirect()
            ->route('zonas.index')
            ->with(
                'success',
                'Geocerca actualizada correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Zona $zona
    ) {

        $this->verificarPropiedadZona(
            $zona
        );

        $zona->delete();

        return redirect()
            ->route('zonas.index')
            ->with(
                'success',
                'Geocerca eliminada correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | RESOLVER PROPIETARIO
    |--------------------------------------------------------------------------
    */

    private function resolverPropietario(
        Request $request,
        ?Zona $zona = null
    ): array {

        $user = auth()->user();


        /*
        | Cliente Individual
        */

        if (
            $user->hasRole(
                'Cliente Individual'
            )
        ) {

            return [
                null,
                $user->id,
            ];
        }


        /*
        | Administrador / Gestor
        */

        if (
            !$user->hasRole(
                'Super Administrador'
            )
        ) {

            return [
                $user->empresa_id,
                null,
            ];
        }


        /*
        | Super Administrador
        */

        $propietario =
            $request->input(
                'propietario'
            );


        if (
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

            return [
                $empresaId,
                null,
            ];
        }


        if (
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
                        'El usuario seleccionado debe ser un Cliente Individual.',
                ]);
            }

            return [
                null,
                $userId,
            ];
        }


        throw ValidationException::withMessages([
            'propietario' =>
                'Selecciona una empresa o un cliente individual.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PREPARAR VEHÍCULOS
    |--------------------------------------------------------------------------
    */

    private function prepararVehiculos(
        array $vehiculosInput,
        $empresaId,
        $userId
    ): array {

        if (empty($vehiculosInput)) {
            return [];
        }

        $ids = collect(
            $vehiculosInput
        )
        ->pluck('id')
        ->filter()
        ->unique()
        ->values()
        ->all();


        $vehiculos =
            Vehiculo::whereIn(
                'id',
                $ids
            )
            ->get()
            ->keyBy('id');


        $syncData = [];
        $rechazados = [];


        foreach (
            $vehiculosInput
            as $vehiculoInput
        ) {

            $vehiculo =
                $vehiculos->get(
                    $vehiculoInput['id']
                );


            if (!$vehiculo) {
                continue;
            }


            /*
            | Validar propietario
            */

            $propietarioValido = false;


            if ($empresaId !== null) {

                $propietarioValido =
                    (int) $vehiculo->empresa_id
                    ===
                    (int) $empresaId;
            }


            if ($userId !== null) {

                $propietarioValido =
                    (int) $vehiculo->user_id
                    ===
                    (int) $userId;
            }


            if (!$propietarioValido) {

                $rechazados[] =
                    $vehiculo->nombre;

                continue;
            }


            /*
            | Permanencias
            */

            $minimo =
                $vehiculoInput[
                    'permanencia_minima_minutos'
                ] ?? null;


            $maximo =
                $vehiculoInput[
                    'permanencia_maxima_minutos'
                ] ?? null;


            if (
                $minimo !== null
                &&
                $maximo !== null
                &&
                $minimo !== ''
                &&
                $maximo !== ''
                &&
                (int) $minimo
                    >
                (int) $maximo
            ) {

                throw ValidationException::withMessages([
                    'vehiculos' =>
                        "En {$vehiculo->nombre}, la permanencia mínima no puede ser mayor que la máxima.",
                ]);
            }


            $syncData[
                $vehiculo->id
            ] = [

                'tipo_regla' =>
                    $vehiculoInput[
                        'tipo_regla'
                    ] ?? 'informativa',

                'notificar_entrada' =>
                    filter_var(
                        $vehiculoInput[
                            'notificar_entrada'
                        ] ?? false,
                        FILTER_VALIDATE_BOOLEAN
                    ),

                'notificar_salida' =>
                    filter_var(
                        $vehiculoInput[
                            'notificar_salida'
                        ] ?? false,
                        FILTER_VALIDATE_BOOLEAN
                    ),

                'permanencia_minima_minutos' =>
                    $minimo ?: null,

                'permanencia_maxima_minutos' =>
                    $maximo ?: null,
            ];
        }


        if (!empty($rechazados)) {

            throw ValidationException::withMessages([
                'vehiculos' =>
                    'Los siguientes vehículos no pertenecen al propietario de la zona: '
                    . implode(', ', $rechazados)
                    . '.',
            ]);
        }


        return $syncData;
    }


    /*
    |--------------------------------------------------------------------------
    | CONVERTIR COORDENADAS A POLYGON
    |--------------------------------------------------------------------------
    */

    private function parsearCoordenadasAPoligono(
        string $jsonCoordenadas
    ): ?string {

        $coordenadas =
            json_decode(
                $jsonCoordenadas,
                true
            );


        if (
            !is_array($coordenadas)
            ||
            count($coordenadas) < 3
        ) {
            return null;
        }


        $puntos = [];


        foreach (
            $coordenadas
            as $coord
        ) {

            if (
                !isset(
                    $coord['lat'],
                    $coord['lng']
                )
                ||
                !is_numeric(
                    $coord['lat']
                )
                ||
                !is_numeric(
                    $coord['lng']
                )
            ) {
                return null;
            }


            $lat =
                (float) $coord['lat'];

            $lng =
                (float) $coord['lng'];


            if (
                $lat < -90
                ||
                $lat > 90
                ||
                $lng < -180
                ||
                $lng > 180
            ) {
                return null;
            }


            $lngTexto =
                number_format(
                    $lng,
                    8,
                    '.',
                    ''
                );

            $latTexto =
                number_format(
                    $lat,
                    8,
                    '.',
                    ''
                );


            $puntos[] =
                $lngTexto
                . ' '
                . $latTexto;
        }


        /*
        | Cerrar el polígono
        */

        if (
            $puntos[0]
            !==
            end($puntos)
        ) {

            $puntos[] =
                $puntos[0];
        }


        return
            'POLYGON(('
            . implode(
                ',',
                $puntos
            )
            . '))';
    }


    /*
    |--------------------------------------------------------------------------
    | SEGURIDAD MULTI-TENANT
    |--------------------------------------------------------------------------
    */

    private function verificarPropiedadZona(
        Zona $zona
    ): void {

        $user =
            auth()->user();


        if (
            $user->hasRole(
                'Super Administrador'
            )
        ) {
            return;
        }


        if (
            $user->hasRole(
                'Cliente Individual'
            )
        ) {

            if (
                (int) $zona->user_id
                !==
                (int) $user->id
            ) {

                abort(
                    403,
                    'Acceso denegado a esta geocerca.'
                );
            }

            return;
        }


        if (
            (int) $zona->empresa_id
            !==
            (int) $user->empresa_id
        ) {

            abort(
                403,
                'La geocerca pertenece a otra empresa.'
            );
        }
    }
}