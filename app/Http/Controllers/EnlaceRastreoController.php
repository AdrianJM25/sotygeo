<?php

namespace App\Http\Controllers;

use App\Models\EnlaceRastreo;
use App\Models\Ubicacion;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnlaceRastreoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO DE ENLACES
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $user = auth()->user();

        $vehiculos = Vehiculo::query();

        if ($user->hasRole('Cliente Individual')) {
            $vehiculos->where('user_id', $user->id);
        } elseif (!$user->hasRole('Super Administrador')) {
            $vehiculos->where('empresa_id', $user->empresa_id);
        }

        $vehiculos = $vehiculos
            ->orderBy('nombre')
            ->get();


        $enlaces = EnlaceRastreo::with('vehiculo')
            ->whereHas('vehiculo', function ($query) use ($user) {

                if ($user->hasRole('Cliente Individual')) {

                    $query->where(
                        'user_id',
                        $user->id
                    );

                } elseif (!$user->hasRole('Super Administrador')) {

                    $query->where(
                        'empresa_id',
                        $user->empresa_id
                    );
                }
            })
            ->latest()
            ->get();


        return view(
            'enlaces.index',
            compact(
                'vehiculos',
                'enlaces'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREAR ENLACE TEMPORAL
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'vehiculo_id' => [
                'required',
                'integer',
                'exists:vehiculos,id',
            ],

            'duracion_horas' => [
                'required',
                'integer',
                'min:1',
                'max:168',
            ],
        ]);


        $vehiculo = Vehiculo::findOrFail(
            $request->vehiculo_id
        );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR ACCESO AL VEHÍCULO
        |--------------------------------------------------------------------------
        */

        $this->verificarAccesoVehiculo(
            $vehiculo
        );


        /*
        |--------------------------------------------------------------------------
        | GENERAR TOKEN ÚNICO
        |--------------------------------------------------------------------------
        */

        do {

            $token = Str::random(64);

        } while (
            EnlaceRastreo::where(
                'token',
                $token
            )->exists()
        );


        /*
        |--------------------------------------------------------------------------
        | CREAR ENLACE
        |--------------------------------------------------------------------------
        */

        $enlace = EnlaceRastreo::create([
            'vehiculo_id' =>
                $vehiculo->id,

            'token' =>
                $token,

            'fecha_expiracion' =>
                now()->addHours(
                    (int) $request->duracion_horas
                ),
        ]);


        return redirect()
            ->route('enlaces.index')
            ->with(
                'success',
                'Enlace de seguimiento generado correctamente.'
            )
            ->with(
                'enlace_generado',
                route(
                    'seguimiento.publico',
                    $enlace->token
                )
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR / CANCELAR ENLACE
    |--------------------------------------------------------------------------
    */

    public function destroy(EnlaceRastreo $enlace)
    {
        $enlace->load('vehiculo');


        if (!$enlace->vehiculo) {

            abort(
                404,
                'El vehículo asociado al enlace no existe.'
            );
        }


        $this->verificarAccesoVehiculo(
            $enlace->vehiculo
        );


        $enlace->delete();


        return redirect()
            ->route('enlaces.index')
            ->with(
                'success',
                'Enlace de seguimiento cancelado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | VISTA PÚBLICA DEL SEGUIMIENTO
    |--------------------------------------------------------------------------
    */

    public function publico(string $token)
    {
        $enlace = EnlaceRastreo::with('vehiculo')
            ->where(
                'token',
                $token
            )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | VALIDAR EXPIRACIÓN
        |--------------------------------------------------------------------------
        */

        if ($enlace->estaExpirado()) {

            return response()
                ->view(
                    'enlaces.expirado',
                    compact('enlace'),
                    410
                );
        }


        if (!$enlace->vehiculo) {

            abort(
                404,
                'El vehículo asociado no existe.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ÚLTIMA UBICACIÓN
        |--------------------------------------------------------------------------
        */

        $ultimaUbicacion = Ubicacion::where(
            'vehiculo_id',
            $enlace->vehiculo_id
        )
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->latest('fecha_gps')
            ->first();


        return view(
            'enlaces.publico',
            compact(
                'enlace',
                'ultimaUbicacion'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UBICACIÓN PÚBLICA EN TIEMPO REAL
    |--------------------------------------------------------------------------
    |
    | Esta ruta será consultada cada pocos segundos desde el mapa público.
    |
    */

    public function ubicacionPublica(string $token)
    {
        $enlace = EnlaceRastreo::where(
            'token',
            $token
        )
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | BLOQUEAR ENLACE EXPIRADO
        |--------------------------------------------------------------------------
        */

        if ($enlace->estaExpirado()) {

            return response()->json([
                'estado' =>
                    'expirado',

                'mensaje' =>
                    'Este enlace de seguimiento ha expirado.',
            ], 410);
        }


        /*
        |--------------------------------------------------------------------------
        | ÚLTIMA POSICIÓN DEL VEHÍCULO
        |--------------------------------------------------------------------------
        */

        $ubicacion = Ubicacion::where(
            'vehiculo_id',
            $enlace->vehiculo_id
        )
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->latest('fecha_gps')
            ->first();


        if (!$ubicacion) {

            return response()->json([
                'estado' =>
                    'sin_ubicacion',

                'mensaje' =>
                    'El vehículo todavía no tiene una ubicación disponible.',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | ESTADO DE CONEXIÓN
        |--------------------------------------------------------------------------
        */

        $segundosSinReporte =
            now()->timestamp
            -
            $ubicacion->fecha_gps->timestamp;


        $segundosSinReporte =
            max(
                0,
                $segundosSinReporte
            );


        $enLinea =
            $segundosSinReporte <= 300;


        /*
        |--------------------------------------------------------------------------
        | RESPUESTA PÚBLICA
        |--------------------------------------------------------------------------
        |
        | Solamente enviamos información necesaria para el seguimiento.
        |
        */

        return response()->json([
            'estado' =>
                'ok',

            'vehiculo' => [
                'nombre' =>
                    $enlace->vehiculo?->nombre
                    ??
                    'Vehículo',

                'marca' =>
                    $enlace->vehiculo?->marca,

                'modelo' =>
                    $enlace->vehiculo?->modelo,
            ],

            'ubicacion' => [
                'latitud' =>
                    (float) $ubicacion->latitud,

                'longitud' =>
                    (float) $ubicacion->longitud,

                'velocidad' =>
                    (float) (
                        $ubicacion->velocidad
                        ??
                        0
                    ),

                'porcentaje_bateria' =>
                    $ubicacion->porcentaje_bateria,

                'fecha_gps' =>
                    $ubicacion->fecha_gps,
            ],

            'en_linea' =>
                $enLinea,

            'minutos_sin_reporte' =>
                (int) floor(
                    $segundosSinReporte / 60
                ),

            'fecha_expiracion' =>
                $enlace->fecha_expiracion,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFICAR ACCESO AL VEHÍCULO
    |--------------------------------------------------------------------------
    */

    private function verificarAccesoVehiculo(
        Vehiculo $vehiculo
    ): void {

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
        | CLIENTE INDIVIDUAL
        |--------------------------------------------------------------------------
        */

        if (
            $user->hasRole(
                'Cliente Individual'
            )
        ) {

            if (
                (int) $vehiculo->user_id
                !==
                (int) $user->id
            ) {

                abort(
                    403,
                    'No tienes permiso para compartir este vehículo.'
                );
            }


            return;
        }


        /*
        |--------------------------------------------------------------------------
        | USUARIOS DE EMPRESA
        |--------------------------------------------------------------------------
        */

        if (
            (int) $vehiculo->empresa_id
            !==
            (int) $user->empresa_id
        ) {

            abort(
                403,
                'No tienes permiso para compartir este vehículo.'
            );
        }
    }
}