<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Flotilla;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class FlotillaController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $userActual = auth()->user();

        $query = Flotilla::query()
            ->with([
                'usuario',
                'empresa',
                'vehiculos',
            ])
            ->latest();

        if (!$userActual->hasRole('Super Administrador')) {
            $query->where(
                'empresa_id',
                $userActual->empresa_id
            );
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;

            $query->where(function ($q) use ($buscar) {

                $q->where(
                    'nombre',
                    'like',
                    "%{$buscar}%"
                )
                    ->orWhere(
                        'descripcion',
                        'like',
                        "%{$buscar}%"
                    )
                    ->orWhereHas(
                        'empresa',
                        function ($empresa) use ($buscar) {
                            $empresa->where(
                                'nombre',
                                'like',
                                "%{$buscar}%"
                            );
                        }
                    )
                    ->orWhereHas(
                        'usuario',
                        function ($usuario) use ($buscar) {

                            $usuario
                                ->where(
                                    'nombre',
                                    'like',
                                    "%{$buscar}%"
                                )
                                ->orWhere(
                                    'apellido_paterno',
                                    'like',
                                    "%{$buscar}%"
                                );
                        }
                    );
            });
        }

        $flotillas = $query
            ->paginate(10)
            ->withQueryString();

        return view(
            'flotillas.index',
            compact(
                'flotillas'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO CREAR
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $userActual = auth()->user();

        if ($userActual->hasRole('Super Administrador')) {

            $empresas = Empresa::query()
                ->orderBy('nombre')
                ->get();

            $users = User::query()
                ->with('empresa')
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            $vehiculos = Vehiculo::query()
                ->with('empresa')
                ->whereNull('flotilla_id')
                ->orderBy('nombre')
                ->get();

        } else {

            $empresas = Empresa::query()
                ->where(
                    'id',
                    $userActual->empresa_id
                )
                ->get();

            $users = User::query()
                ->where(
                    'empresa_id',
                    $userActual->empresa_id
                )
                ->where(
                    'activo',
                    true
                )
                ->orderBy('nombre')
                ->get();

            $vehiculos = Vehiculo::query()
                ->where(
                    'empresa_id',
                    $userActual->empresa_id
                )
                ->whereNull('flotilla_id')
                ->orderBy('nombre')
                ->get();
        }

        return view(
            'flotillas.create',
            compact(
                'empresas',
                'users',
                'vehiculos'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GUARDAR
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $userActual = auth()->user();

        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],

            'descripcion' => [
                'nullable',
                'string',
            ],

            'user_id' => [
                'nullable',
                'exists:users,id',
            ],

            'empresa_id' => [
                $userActual->hasRole('Super Administrador')
                    ? 'required'
                    : 'nullable',
                'exists:empresas,id',
            ],

            'vehiculos' => [
                'nullable',
                'array',
            ],

            'vehiculos.*' => [
                'exists:vehiculos,id',
            ],
        ]);

        $empresaId =
            $userActual->hasRole('Super Administrador')
                ? (int) $request->empresa_id
                : (int) $userActual->empresa_id;

        /*
        |--------------------------------------------------------------------------
        | VALIDAR RESPONSABLE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('user_id')) {

            $gestor = User::findOrFail(
                $request->user_id
            );

            if (
                (int) $gestor->empresa_id
                !==
                $empresaId
            ) {

                return back()
                    ->withErrors([
                        'user_id' =>
                            'El responsable seleccionado pertenece a otra empresa.',
                    ])
                    ->withInput();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDAR VEHÍCULOS
        |--------------------------------------------------------------------------
        */

        $vehiculosIds = collect(
            $request->input(
                'vehiculos',
                []
            )
        )
            ->map(
                fn ($id) => (int) $id
            )
            ->unique()
            ->values();

        if ($vehiculosIds->isNotEmpty()) {

            $vehiculosValidos =
                Vehiculo::query()
                    ->whereIn(
                        'id',
                        $vehiculosIds
                    )
                    ->where(
                        'empresa_id',
                        $empresaId
                    )
                    ->whereNull(
                        'flotilla_id'
                    )
                    ->pluck(
                        'id'
                    );

            if (
                $vehiculosValidos->count()
                !==
                $vehiculosIds->count()
            ) {

                return back()
                    ->withErrors([
                        'vehiculos' =>
                            'Uno o más vehículos no pertenecen a la empresa seleccionada o ya están asignados a otra flotilla.',
                    ])
                    ->withInput();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CREAR FLOTILLA
        |--------------------------------------------------------------------------
        */

        $flotilla = Flotilla::create([
            'empresa_id' =>
                $empresaId,

            'user_id' =>
                $request->user_id,

            'nombre' =>
                $request->nombre,

            'descripcion' =>
                $request->descripcion,
        ]);

        /*
        |--------------------------------------------------------------------------
        | ASIGNAR VEHÍCULOS
        |--------------------------------------------------------------------------
        */

        if ($vehiculosIds->isNotEmpty()) {

            Vehiculo::query()
                ->whereIn(
                    'id',
                    $vehiculosIds
                )
                ->where(
                    'empresa_id',
                    $empresaId
                )
                ->whereNull(
                    'flotilla_id'
                )
                ->update([
                    'flotilla_id' =>
                        $flotilla->id,
                ]);
        }

        return redirect()
            ->route(
                'flotillas.index'
            )
            ->with(
                'success',
                'Flotilla registrada correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | VER DETALLE
    |--------------------------------------------------------------------------
    */

    public function show(
        Flotilla $flotilla
    ) {
        $this->validarAcceso(
            $flotilla
        );

        $flotilla->load([
            'empresa',
            'usuario',
            'vehiculos',
        ]);

        return view(
            'flotillas.show',
            compact(
                'flotilla'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | FORMULARIO EDITAR
    |--------------------------------------------------------------------------
    */

    public function edit(
        Flotilla $flotilla
    ) {
        $this->validarAcceso(
            $flotilla
        );

        $userActual =
            auth()->user();

        $flotilla->load(
            'vehiculos'
        );

        if (
            $userActual
                ->hasRole(
                    'Super Administrador'
                )
        ) {

            $empresas =
                Empresa::query()
                    ->orderBy(
                        'nombre'
                    )
                    ->get();

            $users =
                User::query()
                    ->with(
                        'empresa'
                    )
                    ->where(
                        'activo',
                        true
                    )
                    ->orderBy(
                        'nombre'
                    )
                    ->get();

            $vehiculos =
                Vehiculo::query()
                    ->with(
                        'empresa'
                    )
                    ->where(
                        function ($query) use ($flotilla) {

                            $query
                                ->whereNull(
                                    'flotilla_id'
                                )
                                ->orWhere(
                                    'flotilla_id',
                                    $flotilla->id
                                );
                        }
                    )
                    ->orderBy(
                        'nombre'
                    )
                    ->get();

        } else {

            $empresas =
                Empresa::query()
                    ->where(
                        'id',
                        $userActual
                            ->empresa_id
                    )
                    ->get();

            $users =
                User::query()
                    ->where(
                        'empresa_id',
                        $userActual
                            ->empresa_id
                    )
                    ->where(
                        'activo',
                        true
                    )
                    ->orderBy(
                        'nombre'
                    )
                    ->get();

            $vehiculos =
                Vehiculo::query()
                    ->where(
                        'empresa_id',
                        $userActual
                            ->empresa_id
                    )
                    ->where(
                        function ($query) use ($flotilla) {

                            $query
                                ->whereNull(
                                    'flotilla_id'
                                )
                                ->orWhere(
                                    'flotilla_id',
                                    $flotilla->id
                                );
                        }
                    )
                    ->orderBy(
                        'nombre'
                    )
                    ->get();
        }

        return view(
            'flotillas.edit',
            compact(
                'flotilla',
                'empresas',
                'users',
                'vehiculos'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Flotilla $flotilla
    ) {
        $this->validarAcceso(
            $flotilla
        );

        $userActual =
            auth()->user();

        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],

            'descripcion' => [
                'nullable',
                'string',
            ],

            'user_id' => [
                'nullable',
                'exists:users,id',
            ],

            'empresa_id' => [
                $userActual->hasRole('Super Administrador')
                    ? 'required'
                    : 'nullable',
                'exists:empresas,id',
            ],

            'vehiculos' => [
                'nullable',
                'array',
            ],

            'vehiculos.*' => [
                'exists:vehiculos,id',
            ],
        ]);

        $empresaId =
            $userActual->hasRole(
                'Super Administrador'
            )
                ? (int) $request->empresa_id
                : (int) $flotilla->empresa_id;

        /*
        |--------------------------------------------------------------------------
        | VALIDAR RESPONSABLE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('user_id')) {

            $gestor =
                User::findOrFail(
                    $request->user_id
                );

            if (
                (int) $gestor->empresa_id
                !==
                $empresaId
            ) {

                return back()
                    ->withErrors([
                        'user_id' =>
                            'El responsable seleccionado pertenece a otra empresa.',
                    ])
                    ->withInput();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDAR VEHÍCULOS
        |--------------------------------------------------------------------------
        */

        $vehiculosIds =
            collect(
                $request->input(
                    'vehiculos',
                    []
                )
            )
                ->map(
                    fn ($id) =>
                        (int) $id
                )
                ->unique()
                ->values();

        if (
            $vehiculosIds
                ->isNotEmpty()
        ) {

            $vehiculosValidos =
                Vehiculo::query()
                    ->whereIn(
                        'id',
                        $vehiculosIds
                    )
                    ->where(
                        'empresa_id',
                        $empresaId
                    )
                    ->where(
                        function ($query) use ($flotilla) {

                            $query
                                ->whereNull(
                                    'flotilla_id'
                                )
                                ->orWhere(
                                    'flotilla_id',
                                    $flotilla->id
                                );
                        }
                    )
                    ->pluck(
                        'id'
                    );

            if (
                $vehiculosValidos->count()
                !==
                $vehiculosIds->count()
            ) {

                return back()
                    ->withErrors([
                        'vehiculos' =>
                            'Uno o más vehículos no pertenecen a la empresa seleccionada o están asignados a otra flotilla.',
                    ])
                    ->withInput();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | LIBERAR VEHÍCULOS ACTUALES
        |--------------------------------------------------------------------------
        */

        Vehiculo::query()
            ->where(
                'flotilla_id',
                $flotilla->id
            )
            ->update([
                'flotilla_id' =>
                    null,
            ]);

        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR FLOTILLA
        |--------------------------------------------------------------------------
        */

        $flotilla->update([
            'empresa_id' =>
                $empresaId,

            'user_id' =>
                $request->user_id,

            'nombre' =>
                $request->nombre,

            'descripcion' =>
                $request->descripcion,
        ]);

        /*
        |--------------------------------------------------------------------------
        | REASIGNAR VEHÍCULOS
        |--------------------------------------------------------------------------
        */

        if (
            $vehiculosIds
                ->isNotEmpty()
        ) {

            Vehiculo::query()
                ->whereIn(
                    'id',
                    $vehiculosIds
                )
                ->where(
                    'empresa_id',
                    $empresaId
                )
                ->update([
                    'flotilla_id' =>
                        $flotilla->id,
                ]);
        }

        return redirect()
            ->route(
                'flotillas.index'
            )
            ->with(
                'success',
                'Flotilla actualizada correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Flotilla $flotilla
    ) {
        $this->validarAcceso(
            $flotilla
        );

        Vehiculo::query()
            ->where(
                'flotilla_id',
                $flotilla->id
            )
            ->update([
                'flotilla_id' =>
                    null,
            ]);

        $flotilla->delete();

        return redirect()
            ->route(
                'flotillas.index'
            )
            ->with(
                'success',
                'Flotilla eliminada correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAR ACCESO MULTI-TENANT
    |--------------------------------------------------------------------------
    */

    private function validarAcceso(
        Flotilla $flotilla
    ): void {
        $userActual =
            auth()->user();

        if (
            !$userActual
                ->hasRole(
                    'Super Administrador'
                )
            &&
            (int) $flotilla->empresa_id
            !==
            (int) $userActual->empresa_id
        ) {

            abort(
                403,
                'Acceso denegado a esta flotilla.'
            );
        }
    }
}