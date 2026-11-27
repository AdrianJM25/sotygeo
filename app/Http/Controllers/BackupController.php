<?php

namespace App\Http\Controllers;

use AmdadulHaq\DatabaseBackup\DatabaseBackupManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Throwable;

class BackupController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | FN.21 - RESPALDO DE BASE DE DATOS
    |--------------------------------------------------------------------------
    */

    public function __construct(
        private readonly DatabaseBackupManager $backupManager
    ) {
        //
    }


    /*
    |--------------------------------------------------------------------------
    | MOSTRAR RESPALDOS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $this->validarSuperAdministrador();

        $conexion =
            config(
                'database-backup.connection',
                'pgsql'
            );


        /*
        |--------------------------------------------------------------------------
        | CONSULTAR RESPALDOS
        |--------------------------------------------------------------------------
        */

        try {

            $archivos =
                $this->backupManager
                    ->backups(
                        $conexion
                    );

        } catch (Throwable $e) {

            report(
                $e
            );

            $archivos =
                [];

            session()->flash(
                'backup_error',
                'No fue posible consultar los respaldos.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | PREPARAR RESPALDOS PARA LA VISTA
        |--------------------------------------------------------------------------
        */

        $respaldos =
            collect(
                $archivos
            )
            ->map(
                function (
                    array $respaldo
                ) {

                    return [

                        'nombre' =>
                            basename(
                                $respaldo[
                                    'path'
                                ]
                            ),

                        'ruta' =>
                            $respaldo[
                                'path'
                            ],

                        'tamano' =>
                            $this->formatearTamano(
                                (int)
                                $respaldo[
                                    'size'
                                ]
                            ),

                        'fecha' =>
                            Carbon::createFromTimestamp(
                                (int)
                                $respaldo[
                                    'modified'
                                ]
                            ),

                    ];
                }
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
        | CONEXIÓN
        |--------------------------------------------------------------------------
        */

        $conexion =
            config(
                'database-backup.connection',
                'pgsql'
            );


        /*
        |--------------------------------------------------------------------------
        | EJECUTAR COMANDO CLI DE LA LIBRERÍA
        |--------------------------------------------------------------------------
        |
        | Ejecutamos:
        |
        | php artisan db:backup --connection=pgsql
        |
        | dentro de un proceso PHP CLI separado.
        |
        */

        $proceso =
            new Process(
                [

                    $this->phpCliBinary(),

                    base_path(
                        'artisan'
                    ),

                    'db:backup',

                    '--connection='
                    .
                    $conexion,

                ],

                base_path()
            );


        /*
        |--------------------------------------------------------------------------
        | TIEMPO MÁXIMO
        |--------------------------------------------------------------------------
        */

        $proceso->setTimeout(
            (float)
            config(
                'database-backup.timeout',
                900
            )
            +
            60
        );


        /*
        |--------------------------------------------------------------------------
        | EJECUTAR
        |--------------------------------------------------------------------------
        */

        try {

            $proceso->run();

        } catch (Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | REGISTRAR EXCEPCIÓN
            |--------------------------------------------------------------------------
            */

            Log::error(
                'FN21 - Excepción al iniciar el proceso de respaldo',
                [

                    'mensaje' =>
                        $e->getMessage(),

                    'php_binary' =>
                        $this->phpCliBinary(),

                    'artisan' =>
                        base_path(
                            'artisan'
                        ),

                    'connection' =>
                        $conexion,

                    'temp_directory' =>
                        sys_get_temp_dir(),

                    'working_directory' =>
                        base_path(),

                ]
            );


            report(
                $e
            );


            return back()
                ->withErrors([
                    'backup' =>
                        'No fue posible iniciar el proceso de respaldo: '
                        .
                        $e->getMessage(),
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | COMPROBAR RESULTADO
        |--------------------------------------------------------------------------
        */

        if (
            !$proceso->isSuccessful()
        ) {

            /*
            |--------------------------------------------------------------------------
            | OBTENER SALIDAS CRUDAS
            |--------------------------------------------------------------------------
            */

            $stderr =
                $proceso
                    ->getErrorOutput();


            $stdout =
                $proceso
                    ->getOutput();


            /*
            |--------------------------------------------------------------------------
            | GUARDAR DIAGNÓSTICO EN LARAVEL.LOG
            |--------------------------------------------------------------------------
            |
            | No se registra ninguna contraseña.
            |
            */

            Log::error(
                'FN21 - Error al generar respaldo',
                [

                    'exit_code' =>
                        $proceso
                            ->getExitCode(),

                    'successful' =>
                        $proceso
                            ->isSuccessful(),

                    /*
                    |--------------------------------------------------------------------------
                    | STDERR
                    |--------------------------------------------------------------------------
                    */

                    'stderr' =>
                        $stderr,

                    'stderr_base64' =>
                        base64_encode(
                            $stderr
                        ),

                    'stderr_hex' =>
                        bin2hex(
                            $stderr
                        ),


                    /*
                    |--------------------------------------------------------------------------
                    | STDOUT
                    |--------------------------------------------------------------------------
                    */

                    'stdout' =>
                        $stdout,

                    'stdout_base64' =>
                        base64_encode(
                            $stdout
                        ),


                    /*
                    |--------------------------------------------------------------------------
                    | PROCESO
                    |--------------------------------------------------------------------------
                    */

                    'php_binary' =>
                        $this->phpCliBinary(),

                    'artisan' =>
                        base_path(
                            'artisan'
                        ),

                    'command_line' =>
                        $proceso
                            ->getCommandLine(),

                    'connection' =>
                        $conexion,


                    /*
                    |--------------------------------------------------------------------------
                    | DIRECTORIOS
                    |--------------------------------------------------------------------------
                    */

                    'temp_directory' =>
                        sys_get_temp_dir(),

                    'temp_directory_exists' =>
                        is_dir(
                            sys_get_temp_dir()
                        ),

                    'temp_directory_writable' =>
                        is_writable(
                            sys_get_temp_dir()
                        ),

                    'working_directory' =>
                        base_path(),

                    'working_directory_exists' =>
                        is_dir(
                            base_path()
                        ),

                    'storage_path' =>
                        storage_path(),

                    'backup_temp_path' =>
                        storage_path(
                            'app/backup-temp'
                        ),

                    'backup_temp_exists' =>
                        is_dir(
                            storage_path(
                                'app/backup-temp'
                            )
                        ),

                    'backup_temp_writable' =>
                        is_writable(
                            storage_path(
                                'app/backup-temp'
                            )
                        ),


                    /*
                    |--------------------------------------------------------------------------
                    | CONFIGURACIÓN DE LA LIBRERÍA
                    |--------------------------------------------------------------------------
                    */

                    'backup_disk' =>
                        config(
                            'database-backup.disk'
                        ),

                    'backup_path' =>
                        config(
                            'database-backup.path'
                        ),

                    'backup_temp_config' =>
                        config(
                            'database-backup.temp_directory'
                        ),

                    'pg_dump_binary' =>
                        config(
                            'database-backup.binaries.pg_dump'
                        ),

                    'pgsql_extra_options' =>
                        config(
                            'database-backup.extra_options.pgsql'
                        ),

                ]
            );


            /*
            |--------------------------------------------------------------------------
            | PREPARAR MENSAJE PARA LA INTERFAZ
            |--------------------------------------------------------------------------
            */

            $detalle =
                trim(
                    $stderr
                );


            if (
                $detalle === ''
            ) {

                $detalle =
                    trim(
                        $stdout
                    );
            }


            if (
                $detalle === ''
            ) {

                $detalle =
                    'El proceso de respaldo terminó con código '
                    .
                    $proceso->getExitCode()
                    .
                    '.';
            }


            /*
            |--------------------------------------------------------------------------
            | REGRESAR ERROR
            |--------------------------------------------------------------------------
            */

            return back()
                ->withErrors([
                    'backup' =>
                        'No fue posible generar el respaldo. '
                        .
                        $detalle,
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
                'Respaldo de la base de datos generado correctamente.'
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
        | SEGURIDAD
        |--------------------------------------------------------------------------
        */

        $archivo =
            basename(
                $archivo
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR EXTENSIÓN
        |--------------------------------------------------------------------------
        */

        $nombreMinusculas =
            strtolower(
                $archivo
            );


        $esSql =
            str_ends_with(
                $nombreMinusculas,
                '.sql'
            );


        $esSqlComprimido =
            str_ends_with(
                $nombreMinusculas,
                '.sql.gz'
            );


        if (
            !$esSql
            &&
            !$esSqlComprimido
        ) {

            abort(
                404
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CONEXIÓN
        |--------------------------------------------------------------------------
        */

        $conexion =
            config(
                'database-backup.connection',
                'pgsql'
            );


        /*
        |--------------------------------------------------------------------------
        | CONSULTAR RESPALDOS
        |--------------------------------------------------------------------------
        */

        try {

            $respaldos =
                $this->backupManager
                    ->backups(
                        $conexion
                    );

        } catch (Throwable $e) {

            report(
                $e
            );


            abort(
                500,
                'No fue posible consultar los respaldos.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BUSCAR RESPALDO
        |--------------------------------------------------------------------------
        */

        $respaldo =
            collect(
                $respaldos
            )
            ->first(
                function (
                    array $respaldo
                ) use (
                    $archivo
                ) {

                    return
                        basename(
                            $respaldo[
                                'path'
                            ]
                        )
                        ===
                        $archivo;
                }
            );


        /*
        |--------------------------------------------------------------------------
        | RESPALDO NO ENCONTRADO
        |--------------------------------------------------------------------------
        */

        if (
            !$respaldo
        ) {

            abort(
                404,
                'El respaldo solicitado no existe.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | DISCO
        |--------------------------------------------------------------------------
        */

        $disco =
            config(
                'database-backup.disk',
                'backups'
            );


        /*
        |--------------------------------------------------------------------------
        | DESCARGAR
        |--------------------------------------------------------------------------
        */

        return Storage::disk(
            $disco
        )
            ->download(
                $respaldo[
                    'path'
                ],
                $archivo
            );
    }


    /*
    |--------------------------------------------------------------------------
    | FN.22 - RESTAURAR RESPALDO DE BASE DE DATOS
    |--------------------------------------------------------------------------
    */

    public function restore(
        string $archivo
    ) {
        $this->validarSuperAdministrador();


        /*
        |--------------------------------------------------------------------------
        | SEGURIDAD DEL NOMBRE DEL ARCHIVO
        |--------------------------------------------------------------------------
        */

        $archivo =
            basename(
                $archivo
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDAR EXTENSIÓN
        |--------------------------------------------------------------------------
        */

        $nombreMinusculas =
            strtolower(
                $archivo
            );


        $esSql =
            str_ends_with(
                $nombreMinusculas,
                '.sql'
            );


        $esSqlComprimido =
            str_ends_with(
                $nombreMinusculas,
                '.sql.gz'
            );


        if (
            !$esSql
            &&
            !$esSqlComprimido
        ) {

            abort(
                404
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CONEXIÓN
        |--------------------------------------------------------------------------
        */

        $conexion =
            config(
                'database-backup.connection',
                'pgsql'
            );


        /*
        |--------------------------------------------------------------------------
        | LOCALIZAR EL RESPALDO EN EL DISCO CONFIGURADO
        |--------------------------------------------------------------------------
        */

        try {

            $respaldos =
                $this->backupManager
                    ->backups(
                        $conexion
                    );

        } catch (Throwable $e) {

            report(
                $e
            );


            return back()
                ->withErrors([
                    'backup' =>
                        'No fue posible consultar los respaldos antes de restaurar.',
                ]);
        }


        $respaldo =
            collect(
                $respaldos
            )
            ->first(
                function (
                    array $respaldo
                ) use (
                    $archivo
                ) {

                    return
                        basename(
                            $respaldo[
                                'path'
                            ]
                        )
                        ===
                        $archivo;
                }
            );


        if (
            !$respaldo
        ) {

            abort(
                404,
                'El respaldo solicitado no existe.'
            );
        }


        $rutaRespaldo =
            $respaldo[
                'path'
            ];


        /*
        |--------------------------------------------------------------------------
        | RESPALDO DE SEGURIDAD ANTES DE RESTAURAR
        |--------------------------------------------------------------------------
        |
        | Antes de sobrescribir la base de datos se genera una copia del estado
        | actual. Si esta copia falla, la restauración NO continúa.
        |
        */

        $respaldoSeguridad =
            new Process(
                [

                    $this->phpCliBinary(),

                    base_path(
                        'artisan'
                    ),

                    'db:backup',

                    '--connection='
                    .
                    $conexion,

                ],

                base_path()
            );


        $respaldoSeguridad->setTimeout(
            (float)
            config(
                'database-backup.timeout',
                900
            )
            +
            60
        );


        try {

            $respaldoSeguridad->run();

        } catch (Throwable $e) {

            Log::error(
                'FN22 - Excepción al generar respaldo de seguridad antes de restaurar',
                [
                    'archivo_objetivo' => $archivo,
                    'mensaje' => $e->getMessage(),
                ]
            );


            report(
                $e
            );


            return back()
                ->withErrors([
                    'backup' =>
                        'No fue posible crear el respaldo de seguridad previo. '
                        .
                        'La restauración fue cancelada.',
                ]);
        }


        if (
            !$respaldoSeguridad->isSuccessful()
        ) {

            $stderrSeguridad =
                trim(
                    $respaldoSeguridad
                        ->getErrorOutput()
                );


            $stdoutSeguridad =
                trim(
                    $respaldoSeguridad
                        ->getOutput()
                );


            Log::error(
                'FN22 - No se pudo crear respaldo de seguridad previo',
                [
                    'archivo_objetivo' => $archivo,
                    'exit_code' => $respaldoSeguridad->getExitCode(),
                    'stderr' => $stderrSeguridad,
                    'stdout' => $stdoutSeguridad,
                ]
            );


            return back()
                ->withErrors([
                    'backup' =>
                        'No fue posible crear el respaldo de seguridad previo. '
                        .
                        'La restauración fue cancelada.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | EJECUTAR RESTAURACIÓN
        |--------------------------------------------------------------------------
        |
        | La librería descarga el respaldo, lo descomprime si es necesario y lo
        | restaura utilizando psql. --force evita la confirmación interactiva de
        | consola porque la confirmación se realizará desde la interfaz web.
        |
        */

        $proceso =
            new Process(
                [

                    $this->phpCliBinary(),

                    base_path(
                        'artisan'
                    ),

                    'db:restore',

                    $rutaRespaldo,

                    '--force',

                ],

                base_path()
            );


        $proceso->setTimeout(
            (float)
            config(
                'database-backup.timeout',
                900
            )
            +
            120
        );


        try {

            $proceso->run();

        } catch (Throwable $e) {

            Log::error(
                'FN22 - Excepción al iniciar restauración',
                [
                    'archivo' => $archivo,
                    'ruta' => $rutaRespaldo,
                    'mensaje' => $e->getMessage(),
                    'php_binary' => $this->phpCliBinary(),
                    'temp_directory' => sys_get_temp_dir(),
                    'working_directory' => base_path(),
                ]
            );


            report(
                $e
            );


            return back()
                ->withErrors([
                    'backup' =>
                        'No fue posible iniciar la restauración: '
                        .
                        $e->getMessage(),
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDAR RESULTADO DE LA RESTAURACIÓN
        |--------------------------------------------------------------------------
        */

        if (
            !$proceso->isSuccessful()
        ) {

            $stderr =
                $proceso
                    ->getErrorOutput();


            $stdout =
                $proceso
                    ->getOutput();


            Log::error(
                'FN22 - Error al restaurar respaldo',
                [
                    'archivo' => $archivo,
                    'ruta' => $rutaRespaldo,
                    'exit_code' => $proceso->getExitCode(),
                    'stderr' => $stderr,
                    'stderr_base64' => base64_encode($stderr),
                    'stdout' => $stdout,
                    'stdout_base64' => base64_encode($stdout),
                    'command_line' => $proceso->getCommandLine(),
                    'temp_directory' => sys_get_temp_dir(),
                    'psql_binary' => config(
                        'database-backup.binaries.psql'
                    ),
                ]
            );


            $detalle =
                trim(
                    $stderr
                );


            if (
                $detalle === ''
            ) {

                $detalle =
                    trim(
                        $stdout
                    );
            }


            if (
                $detalle === ''
            ) {

                $detalle =
                    'El proceso de restauración terminó con código '
                    .
                    $proceso->getExitCode()
                    .
                    '.';
            }


            return back()
                ->withErrors([
                    'backup' =>
                        'No fue posible restaurar la base de datos. '
                        .
                        $detalle,
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | RESTAURACIÓN CORRECTA
        |--------------------------------------------------------------------------
        */

        Log::info(
            'FN22 - Respaldo restaurado correctamente',
            [
                'archivo' => $archivo,
                'ruta' => $rutaRespaldo,
                'usuario_id' => auth()->id(),
            ]
        );


        return redirect()
            ->route(
                'backups.index'
            )
            ->with(
                'success',
                'Base de datos restaurada correctamente desde: '
                .
                $archivo
            );
    }


    /**
     * Ejecutar Artisan con PHP CLI, no con el binario PHP-FPM.
     */
    private function phpCliBinary(): string
    {
        return PHP_SAPI === 'cli' ? PHP_BINARY : 'php';
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR SUPER ADMINISTRADOR
    |--------------------------------------------------------------------------
    */

    private function validarSuperAdministrador(): void
    {
        $user =
            auth()->user();


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

        /*
        |--------------------------------------------------------------------------
        | GB
        |--------------------------------------------------------------------------
        */

        if (
            $bytes
            >=
            1024
            *
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
                    1024
                    /
                    1024,
                    2
                )
                .
                ' GB';
        }


        /*
        |--------------------------------------------------------------------------
        | MB
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | KB
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | BYTES
        |--------------------------------------------------------------------------
        */

        return
            $bytes
            .
            ' B';
    }
}