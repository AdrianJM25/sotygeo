<?php

declare(strict_types=1);

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Connection to back up
    |--------------------------------------------------------------------------
    |
    | Conexión de base de datos que se respaldará.
    | En SotyGeo utilizamos PostgreSQL.
    |
    */

    'connection' => env(
        'DB_BACKUP_CONNECTION',
        env(
            'DB_CONNECTION',
            'pgsql'
        )
    ),


    /*
    |--------------------------------------------------------------------------
    | Destination disk
    |--------------------------------------------------------------------------
    |
    | Disco definido en config/filesystems.php
    | donde se almacenarán los respaldos.
    |
    */

    'disk' => env(
        'DB_BACKUP_DISK',
        'backups'
    ),


    /*
    |--------------------------------------------------------------------------
    | Root folder
    |--------------------------------------------------------------------------
    |
    | Carpeta donde se almacenarán los archivos de respaldo.
    |
    */

    'path' => env(
        'DB_BACKUP_PATH',
        Str::slug(
            (string) env(
                'APP_NAME',
                'sotygeo'
            )
        )
    ),


    /*
    |--------------------------------------------------------------------------
    | Compression
    |--------------------------------------------------------------------------
    |
    | Comprime el respaldo utilizando gzip.
    | El archivo generado tendrá extensión .sql.gz.
    |
    */

    'compress' => (bool) env(
        'DB_BACKUP_COMPRESS',
        true
    ),


    /*
    |--------------------------------------------------------------------------
    | Retention
    |--------------------------------------------------------------------------
    |
    | keep_last:
    | Número máximo de respaldos recientes.
    |
    | keep_days:
    | Eliminar respaldos con más de N días.
    |
    */

    'retention' => [

        'keep_last' => (int) env(
            'DB_BACKUP_KEEP_LAST',
            10
        ),

        'keep_days' => (int) env(
            'DB_BACKUP_KEEP_DAYS',
            30
        ),

    ],


    /*
    |--------------------------------------------------------------------------
    | Dump behaviour
    |--------------------------------------------------------------------------
    |
    | Tiempo máximo permitido para realizar
    | una copia de seguridad.
    |
    */

    'timeout' => (int) env(
        'DB_BACKUP_TIMEOUT',
        300
    ),


    /*
    |--------------------------------------------------------------------------
    | Temporary directory
    |--------------------------------------------------------------------------
    |
    | Utilizamos una carpeta dentro del proyecto
    | para evitar problemas de permisos de Windows.
    |
    */

    'temp_directory' => storage_path(
        'app/backup-temp'
    ),


    /*
    |--------------------------------------------------------------------------
    | Database binaries
    |--------------------------------------------------------------------------
    |
    | Ejecutables utilizados para respaldar
    | y restaurar las bases de datos.
    |
    */

    'binaries' => [

        'mysqldump' => env(
            'DB_BACKUP_BIN_MYSQLDUMP',
            'mysqldump'
        ),

        'mysql' => env(
            'DB_BACKUP_BIN_MYSQL',
            'mysql'
        ),

        'pg_dump' => env(
            'DB_BACKUP_BIN_PGDUMP',
            'pg_dump'
        ),

        'psql' => env(
            'DB_BACKUP_BIN_PSQL',
            'psql'
        ),

    ],


    /*
    |--------------------------------------------------------------------------
    | Extra dump options
    |--------------------------------------------------------------------------
    |
    | Parámetros adicionales que se envían
    | directamente a las herramientas de respaldo.
    |
    */

    'extra_options' => [

        /*
        |--------------------------------------------------------------------------
        | MYSQL
        |--------------------------------------------------------------------------
        */

        'mysql' => [

            '--single-transaction',

            '--quick',

            '--no-tablespaces',

            '--routines',

            '--triggers',

            '--events',

        ],


        /*
        |--------------------------------------------------------------------------
        | MARIADB
        |--------------------------------------------------------------------------
        */

        'mariadb' => [

            '--single-transaction',

            '--quick',

            '--no-tablespaces',

            '--routines',

            '--triggers',

            '--events',

        ],


        /*
        |--------------------------------------------------------------------------
        | POSTGRESQL
        |--------------------------------------------------------------------------
        |
        | restrict-key evita el error que PostgreSQL 18
        | estaba generando automáticamente en Windows.
        |
        */

        'pgsql' => [

            '--no-owner',

            '--no-privileges',

            '--clean',

            '--if-exists',

        ],

    ],

];