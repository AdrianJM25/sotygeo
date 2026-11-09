<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class BusquedaModuloController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();
        $consulta = trim((string) $request->query('q', ''));

        $modulos = collect(Route::getRoutes())

            /*
            |--------------------------------------------------------------------------
            | SOLO MÓDULOS NAVEGABLES
            |--------------------------------------------------------------------------
            */
            ->filter(function ($route) {

                if (! in_array('GET', $route->methods(), true)) {
                    return false;
                }

                $nombreRuta = $route->getName();

                if (! $nombreRuta) {
                    return false;
                }

                /*
                |--------------------------------------------------------------------------
                | EVITAR RUTAS QUE REQUIEREN PARÁMETROS
                |--------------------------------------------------------------------------
                |
                | Por ejemplo:
                |
                | vehiculos/{vehiculo}
                | vehiculos/{vehiculo}/ruta
                |
                | No deben aparecer directamente en el buscador.
                |
                */

                if (count($route->parameterNames()) > 0) {
                    return false;
                }

                /*
                |--------------------------------------------------------------------------
                | RUTAS QUE REPRESENTAN MÓDULOS
                |--------------------------------------------------------------------------
                */

                return Str::endsWith(
                    $nombreRuta,
                    '.index'
                )
                    || in_array(
                        $nombreRuta,
                        [
                            'dashboard',
                            'geocercas.historial',
                        ],
                        true
                    );
            })


            /*
            |--------------------------------------------------------------------------
            | VERIFICAR ROLES Y PERMISOS
            |--------------------------------------------------------------------------
            */
            ->filter(function ($route) use ($usuario) {

                foreach ($route->gatherMiddleware() as $middleware) {

                    if (! is_string($middleware)) {
                        continue;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | MIDDLEWARE DE ROLES
                    |--------------------------------------------------------------------------
                    |
                    | Se conserva compatibilidad por si todavía existe alguna
                    | ruta antigua usando:
                    |
                    | role:Super Administrador|Administrador de Empresa
                    |
                    */

                    if (Str::startsWith($middleware, 'role:')) {

                        $rolesTexto = Str::after(
                            $middleware,
                            'role:'
                        );

                        $rolesTexto = Str::before(
                            $rolesTexto,
                            ','
                        );

                        $roles = array_values(
                            array_filter(
                                explode('|', $rolesTexto)
                            )
                        );

                        if (! $usuario->hasAnyRole($roles)) {
                            return false;
                        }
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | MIDDLEWARE DE PERMISOS
                    |--------------------------------------------------------------------------
                    |
                    | Ejemplo:
                    |
                    | permission:gestionar vehiculos
                    |
                    */

                    if (Str::startsWith($middleware, 'permission:')) {

                        $permisosTexto = Str::after(
                            $middleware,
                            'permission:'
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | ELIMINAR GUARD SI EXISTE
                        |--------------------------------------------------------------------------
                        |
                        | permission:gestionar vehiculos,web
                        |
                        */

                        $permisosTexto = Str::before(
                            $permisosTexto,
                            ','
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | SOPORTAR VARIOS PERMISOS
                        |--------------------------------------------------------------------------
                        */

                        $permisos = array_values(
                            array_filter(
                                explode('|', $permisosTexto)
                            )
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | EL USUARIO DEBE TENER AL MENOS UNO
                        |--------------------------------------------------------------------------
                        */

                        $tienePermiso = false;

                        foreach ($permisos as $permiso) {

                            if ($usuario->can($permiso)) {
                                $tienePermiso = true;

                                break;
                            }
                        }

                        if (! $tienePermiso) {
                            return false;
                        }
                    }
                }

                return true;
            })


            /*
            |--------------------------------------------------------------------------
            | PREPARAR MÓDULOS
            |--------------------------------------------------------------------------
            */
            ->map(function ($route) {

                $nombreRuta = $route->getName();

                return [
                    'nombre' => $this->obtenerNombreModulo(
                        $nombreRuta
                    ),

                    'ruta' => route(
                        $nombreRuta
                    ),

                    'nombre_ruta' => $nombreRuta,
                ];
            })


            /*
            |--------------------------------------------------------------------------
            | ELIMINAR DUPLICADOS
            |--------------------------------------------------------------------------
            */
            ->unique('ruta')
            ->sortBy('nombre')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | FILTRAR POR BÚSQUEDA
        |--------------------------------------------------------------------------
        */

        if ($consulta !== '') {

            $consultaNormalizada = Str::lower(
                Str::ascii(
                    $consulta
                )
            );

            $modulos = $modulos
                ->filter(function ($modulo) use ($consultaNormalizada) {

                    $nombreNormalizado = Str::lower(
                        Str::ascii(
                            $modulo['nombre']
                        )
                    );

                    $rutaNormalizada = Str::lower(
                        Str::ascii(
                            $modulo['nombre_ruta']
                        )
                    );

                    return Str::contains(
                        $nombreNormalizado,
                        $consultaNormalizada
                    )
                        || Str::contains(
                            $rutaNormalizada,
                            $consultaNormalizada
                        );
                })
                ->values();
        }


        /*
        |--------------------------------------------------------------------------
        | RESPUESTA
        |--------------------------------------------------------------------------
        */

        if ($request->expectsJson()) {

            return response()->json(
                $modulos
            );
        }

        return view(
            'modulos.buscar',
            [
                'modulos' => $modulos,
                'consulta' => $consulta,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | NOMBRES DE LOS MÓDULOS
    |--------------------------------------------------------------------------
    */

    private function obtenerNombreModulo(
        string $nombreRuta
    ): string {

        $nombresEspeciales = [

            'dashboard' =>
                'Mapa en Vivo',

            'empresas.index' =>
                'Empresas',

            'usuarios.index' =>
                'Usuarios',

            'flotillas.index' =>
                'Flotillas',

            'conductores.index' =>
                'Conductores',

            'vehiculos.index' =>
                'Vehículos',

            'dispositivos.index' =>
                'Dispositivos GPS',

            'zonas.index' =>
                'Geocercas',

            'geocercas.historial' =>
                'Historial de Geocercas',
        ];


        /*
        |--------------------------------------------------------------------------
        | NOMBRE PERSONALIZADO
        |--------------------------------------------------------------------------
        */

        if (
            array_key_exists(
                $nombreRuta,
                $nombresEspeciales
            )
        ) {

            return $nombresEspeciales[
                $nombreRuta
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | GENERAR NOMBRE AUTOMÁTICO
        |--------------------------------------------------------------------------
        |
        | inspecciones.index
        | -> Inspecciones
        |
        | enlaces_temporales.index
        | -> Enlaces Temporales
        |
        */

        $nombreBase = Str::before(
            $nombreRuta,
            '.'
        );

        $nombreBase = str_replace(
            [
                '-',
                '_',
            ],
            ' ',
            $nombreBase
        );

        return Str::title(
            $nombreBase
        );
    }
}