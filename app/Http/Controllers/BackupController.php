<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class BackupController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FN.21 - RESPALDO DE BASE DE DATOS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $this->validarSuperAdministrador();

        $directorio = storage_path(
            'app/backups'
        );

        File::ensureDirectoryExists(
            $directorio
        );

        /*
        |--------------------------------------------------------------------------
        | LISTAR RESPALDOS
        |--------------------------------------------------------------------------
        */

        $respaldos = collect(
            File::files(
                $directorio
            )
        )
            ->filter(
                function ($archivo) {

                    return strtolower(
                        $archivo->getExtension()
                    ) === 'backup';
                }
            )
            ->map(
                function ($archivo) {

                    return [

                        'nombre' =>
                            $archivo->getFilename(),

                        'tamano' =>
                            $this->formatearTamano(
                                $archivo->getSize()
                            ),

                        'fecha' =>
                            Carbon::createFromTimestamp(
                                $archivo->getMTime()
                            ),

                    ];
                }
            )
            ->sortByDesc(
                'fecha'
            )
            ->values();

        return view(
            'backups.index',
            compact(
                'respaldos'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GENERAR RESPALDO
    |--------------------------------------------------------------------------
    */

    public function store()
    {
        $this->validarSuperAdministrador();


        /*
        |--------------------------------------------------------------------------
        | CONFIGURACIÓN DE POSTGRESQL
        |--------------------------------------------------------------------------
        */

        $conexion = config(
            'database.connections.pgsql'
        );


        if (!$conexion) {

            return back()
                ->withErrors([
                    'backup' =>
                        'No se encontró la configuración de PostgreSQL.',
                ]);
        }


        $host =
            $conexion['host']
            ??
            '127.0.0.1';


        $puerto =
            $conexion['port']
            ??
            '5432';


        $baseDatos =
            $conexion['database']
            ??
            null;


        $usuario =
            $conexion['username']
            ??
            null;


        $password =
            (string) (
                $conexion['password']
                ??
                ''
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR CONFIGURACIÓN
        |--------------------------------------------------------------------------
        */

        if (
            !$baseDatos
            ||
            !$usuario
        ) {

            return back()
                ->withErrors([
                    'backup' =>
                        'La configuración de PostgreSQL está incompleta.',
                ]);
        }


        if (
            $password === ''
        ) {

            return back()
                ->withErrors([
                    'backup' =>
                        'No se encontró la contraseña de PostgreSQL.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | DIRECTORIO DE RESPALDOS
        |--------------------------------------------------------------------------
        */

        $directorio = storage_path(
            'app/backups'
        );


        File::ensureDirectoryExists(
            $directorio
        );


        /*
        |--------------------------------------------------------------------------
        | NOMBRE DEL ARCHIVO
        |--------------------------------------------------------------------------
        */

        $fecha = now()->format(
            'Ymd_His'
        );


        $nombreArchivo =
            'sotygeo_backup_'
            .
            $fecha
            .
            '.backup';


        $rutaArchivo =
            $directorio
            .
            DIRECTORY_SEPARATOR
            .
            $nombreArchivo;


        /*
        |--------------------------------------------------------------------------
        | PG_DUMP
        |--------------------------------------------------------------------------
        */

        $pgDump = env(
            'PG_DUMP_PATH',
            'C:/Program Files/PostgreSQL/18/bin/pg_dump.exe'
        );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR PG_DUMP
        |--------------------------------------------------------------------------
        */

        if (
            !File::exists(
                $pgDump
            )
        ) {

            return back()
                ->withErrors([
                    'backup' =>
                        'No se encontró pg_dump en la ruta configurada: '
                        .
                        $pgDump,
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | PROCESO PG_DUMP
        |--------------------------------------------------------------------------
        |
        | IMPORTANTE:
        |
        | Este bloque utiliza exactamente el mismo método
        | comprobado correctamente desde Laravel Tinker.
        |
        */

        $proceso = new Process(
            [

                $pgDump,

                '--host='
                .
                $host,

                '--port='
                .
                $puerto,

                '--username='
                .
                $usuario,

                '--dbname='
                .
                $baseDatos,

                '--format=custom',

                '--no-owner',

                '--no-privileges',

                '--file='
                .
                $rutaArchivo,

            ],

            null,

            [
                'PGPASSWORD' =>
                    $password,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | TIEMPO MÁXIMO
        |--------------------------------------------------------------------------
        */

        $proceso->setTimeout(
            300
        );


        /*
        |--------------------------------------------------------------------------
        | EJECUTAR RESPALDO
        |--------------------------------------------------------------------------
        */

        try {

            $proceso->run();

        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | ELIMINAR ARCHIVO INCOMPLETO
            |--------------------------------------------------------------------------
            */

            if (
                File::exists(
                    $rutaArchivo
                )
            ) {

                File::delete(
                    $rutaArchivo
                );
            }


            return back()
                ->withErrors([
                    'backup' =>
                        'No fue posible ejecutar pg_dump. '
                        .
                        $e->getMessage(),
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | VERIFICAR PG_DUMP
        |--------------------------------------------------------------------------
        */

        if (
            !$proceso->isSuccessful()
        ) {

            if (
                File::exists(
                    $rutaArchivo
                )
            ) {

                File::delete(
                    $rutaArchivo
                );
            }


            $error =
                trim(
                    $proceso->getErrorOutput()
                );


            /*
            |--------------------------------------------------------------------------
            | ERROR SIN DETALLE
            |--------------------------------------------------------------------------
            */

            if ($error === '') {

                $error =
                    'pg_dump terminó con código '
                    .
                    $proceso->getExitCode()
                    .
                    '.';
            }


            return back()
                ->withErrors([
                    'backup' =>
                        'No fue posible generar el respaldo. '
                        .
                        $error,
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR ARCHIVO GENERADO
        |--------------------------------------------------------------------------
        */

        if (
            !File::exists(
                $rutaArchivo
            )
        ) {

            return back()
                ->withErrors([
                    'backup' =>
                        'pg_dump terminó correctamente, pero el archivo no fue encontrado.',
                ]);
        }


        if (
            File::size(
                $rutaArchivo
            )
            <=
            0
        ) {

            File::delete(
                $rutaArchivo
            );


            return back()
                ->withErrors([
                    'backup' =>
                        'El respaldo generado está vacío.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | RESPALDO CORRECTO
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'backups.index'
            )
            ->with(
                'success',
                'Respaldo generado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DESCARGAR RESPALDO
    |--------------------------------------------------------------------------
    */

    public function download(
        string $archivo
    ) {
        $this->validarSuperAdministrador();


        /*
        |--------------------------------------------------------------------------
        | SEGURIDAD DEL NOMBRE
        |--------------------------------------------------------------------------
        */

        $archivo = basename(
            $archivo
        );


        if (
            !preg_match(
                '/^sotygeo_backup_\d{8}_\d{6}\.backup$/',
                $archivo
            )
        ) {

            abort(
                404
            );
        }


        /*
        |--------------------------------------------------------------------------
        | RUTA
        |--------------------------------------------------------------------------
        */

        $ruta = storage_path(
            'app/backups/'
            .
            $archivo
        );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR EXISTENCIA
        |--------------------------------------------------------------------------
        */

        if (
            !File::exists(
                $ruta
            )
        ) {

            abort(
                404,
                'El respaldo solicitado no existe.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DESCARGAR
        |--------------------------------------------------------------------------
        */

        return response()
            ->download(
                $ruta,
                $archivo,
                [
                    'Content-Type' =>
                        'application/octet-stream',
                ]
            );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR SUPER ADMINISTRADOR
    |--------------------------------------------------------------------------
    */

    private function validarSuperAdministrador(): void
    {
        $user = auth()->user();


        if (
            !$user
            ||
            !$user->hasRole(
                'Super Administrador'
            )
        ) {

            abort(
                403,
                'Solo el Super Administrador puede administrar respaldos.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | FORMATEAR TAMAÑO
    |--------------------------------------------------------------------------
    */

    private function formatearTamano(
        int $bytes
    ): string {

        if (
            $bytes
            >=
            1024
            *
            1024
        ) {

            return
                number_format(
                    $bytes
                    /
                    1024
                    /
                    1024,
                    2
                )
                .
                ' MB';
        }


        if (
            $bytes
            >=
            1024
        ) {

            return
                number_format(
                    $bytes
                    /
                    1024,
                    2
                )
                .
                ' KB';
        }


        return
            $bytes
            .
            ' B';
    }
}