<x-app-layout>

    <x-slot name="titulo">
        Respaldo de Base de Datos
    </x-slot>


    <div
        class="mx-auto max-w-[1400px]
               space-y-5 pb-8"
    >

        {{-- ====================================================== --}}
        {{-- ENCABEZADO --}}
        {{-- ====================================================== --}}

        <div
            class="flex flex-col gap-4 rounded-2xl
                   bg-[#0056b3] p-5 shadow-sm
                   sm:flex-row sm:items-center
                   sm:justify-between"
        >

            <div class="flex items-center gap-4">

                <div
                    class="flex h-12 w-12 items-center
                           justify-center rounded-xl
                           bg-white/15 text-white"
                >

                    <span
                        class="material-symbols-rounded
                               text-[28px]"
                    >
                        database
                    </span>

                </div>


                <div>

                    <h1
                        class="font-['Josefin_Sans']
                               text-2xl font-semibold
                               text-white"
                    >
                        Respaldo de Base de Datos
                    </h1>

                    <p
                        class="text-sm
                               text-blue-100"
                    >
                        Genera, descarga y restaura copias de seguridad
                        de la información de SotyGeo.
                    </p>

                </div>

            </div>


            <div
                class="rounded-xl bg-white/10
                       px-4 py-2 text-sm
                       text-blue-100"
            >

                <span
                    class="material-symbols-rounded
                           mr-1 align-middle
                           text-[18px]"
                >
                    admin_panel_settings
                </span>

                Solo Super Administrador

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- MENSAJE DE ÉXITO --}}
        {{-- ====================================================== --}}

        @if(session('success'))

            <div
                class="rounded-2xl border
                       border-emerald-200
                       bg-emerald-50
                       px-5 py-4
                       text-sm font-medium
                       text-emerald-700"
            >

                <div class="flex items-center gap-2">

                    <span
                        class="material-symbols-rounded
                               text-[20px]"
                    >
                        check_circle
                    </span>

                    {{ session('success') }}

                </div>

            </div>

        @endif


        {{-- ====================================================== --}}
        {{-- ERRORES --}}
        {{-- ====================================================== --}}

        @if($errors->any())

            <div
                class="rounded-2xl border
                       border-red-200
                       bg-red-50
                       px-5 py-4"
            >

                <div
                    class="flex items-center gap-2
                           text-sm font-semibold
                           text-red-700"
                >

                    <span
                        class="material-symbols-rounded
                               text-[20px]"
                    >
                        error
                    </span>

                    No fue posible completar la operación de respaldo.

                </div>


                <ul
                    class="mt-2 ml-7
                           list-disc space-y-1
                           text-sm text-red-600"
                >

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- ====================================================== --}}
        {{-- GENERAR RESPALDO --}}
        {{-- ====================================================== --}}

        <div
            class="rounded-2xl border
                   border-gray-200
                   bg-white p-6
                   shadow-sm"
        >

            <div
                class="flex flex-col gap-5
                       md:flex-row
                       md:items-center
                       md:justify-between"
            >

                <div class="flex items-start gap-4">

                    <div
                        class="flex h-12 w-12
                               shrink-0 items-center
                               justify-center
                               rounded-xl bg-blue-50
                               text-[#0056b3]"
                    >

                        <span
                            class="material-symbols-rounded
                                   text-[27px]"
                        >
                            backup
                        </span>

                    </div>


                    <div>

                        <h2
                            class="font-['Josefin_Sans']
                                   text-lg font-semibold
                                   text-[#003d82]"
                        >
                            Generar nuevo respaldo
                        </h2>

                        <p
                            class="mt-1 max-w-2xl
                                   text-sm text-gray-500"
                        >
                            Se generará una copia de seguridad
                            de PostgreSQL con la información
                            almacenada actualmente en la base
                            de datos de SotyGeo.
                        </p>


                        <div
                            class="mt-3 flex flex-wrap
                                   items-center gap-2"
                        >

                            <span
                                class="inline-flex items-center gap-1
                                       rounded-lg bg-blue-50
                                       px-2.5 py-1
                                       text-xs font-medium
                                       text-blue-700"
                            >

                                <span
                                    class="material-symbols-rounded
                                           text-[15px]"
                                >
                                    database
                                </span>

                                PostgreSQL

                            </span>


                            <span
                                class="inline-flex items-center gap-1
                                       rounded-lg bg-emerald-50
                                       px-2.5 py-1
                                       text-xs font-medium
                                       text-emerald-700"
                            >

                                <span
                                    class="material-symbols-rounded
                                           text-[15px]"
                                >
                                    verified
                                </span>

                                Copia de seguridad

                            </span>

                        </div>

                    </div>

                </div>


                <form
                    action="{{ route('backups.store') }}"
                    method="POST"
                >

                    @csrf

                    <button
                        type="submit"
                        class="inline-flex items-center
                               gap-2 rounded-xl
                               bg-[#0056b3]
                               px-5 py-3
                               text-sm font-medium
                               text-white transition
                               hover:bg-[#004494]
                               focus:outline-none
                               focus:ring-2
                               focus:ring-[#0056b3]/30"
                    >

                        <span
                            class="material-symbols-rounded
                                   text-[20px]"
                        >
                            backup
                        </span>

                        Generar respaldo

                    </button>

                </form>

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- HISTORIAL DE RESPALDOS --}}
        {{-- ====================================================== --}}

        <div
            class="overflow-hidden rounded-2xl
                   border border-gray-200
                   bg-white shadow-sm"
        >

            <div
                class="border-b border-gray-100
                       bg-gray-50/60
                       px-5 py-4"
            >

                <div
                    class="flex flex-col gap-3
                           sm:flex-row sm:items-center
                           sm:justify-between"
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-10 w-10
                                   items-center
                                   justify-center
                                   rounded-xl
                                   bg-blue-50
                                   text-[#0056b3]"
                        >

                            <span
                                class="material-symbols-rounded"
                            >
                                history
                            </span>

                        </div>


                        <div>

                            <h2
                                class="font-['Josefin_Sans']
                                       font-semibold
                                       text-[#003d82]"
                            >
                                Respaldos generados
                            </h2>

                            <p
                                class="text-xs
                                       text-gray-500"
                            >
                                Consulta, descarga y restaura las copias
                                de seguridad disponibles.
                            </p>

                        </div>

                    </div>


                    @if(!$respaldos->isEmpty())

                        <div
                            class="rounded-lg
                                   bg-blue-50
                                   px-3 py-1.5
                                   text-xs font-medium
                                   text-blue-700"
                        >
                            {{ $respaldos->count() }}

                            {{
                                $respaldos->count() === 1
                                    ? 'respaldo disponible'
                                    : 'respaldos disponibles'
                            }}
                        </div>

                    @endif

                </div>

            </div>


            {{-- ====================================================== --}}
            {{-- SIN RESPALDOS --}}
            {{-- ====================================================== --}}

            @if($respaldos->isEmpty())

                <div
                    class="px-6 py-16
                           text-center"
                >

                    <div
                        class="mx-auto flex h-16 w-16
                               items-center justify-center
                               rounded-2xl bg-gray-50"
                    >

                        <span
                            class="material-symbols-rounded
                                   text-4xl
                                   text-gray-300"
                        >
                            database
                        </span>

                    </div>


                    <h3
                        class="mt-4
                               font-['Josefin_Sans']
                               font-semibold
                               text-gray-700"
                    >
                        No hay respaldos generados
                    </h3>


                    <p
                        class="mt-1
                               text-sm
                               text-gray-500"
                    >
                        Genera el primer respaldo
                        de la base de datos.
                    </p>

                </div>


            {{-- ====================================================== --}}
            {{-- TABLA --}}
            {{-- ====================================================== --}}

            @else

                <div class="overflow-x-auto">

                    <table
                        class="min-w-full
                               divide-y
                               divide-gray-200"
                    >

                        <thead
                            class="bg-[#0056b3]
                                   text-white"
                        >

                            <tr>

                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-xs
                                           font-semibold
                                           uppercase"
                                >
                                    Archivo
                                </th>


                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-xs
                                           font-semibold
                                           uppercase"
                                >
                                    Fecha
                                </th>


                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-xs
                                           font-semibold
                                           uppercase"
                                >
                                    Tamaño
                                </th>


                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-xs
                                           font-semibold
                                           uppercase"
                                >
                                    Tipo
                                </th>


                                <th
                                    class="px-5 py-3
                                           text-right
                                           text-xs
                                           font-semibold
                                           uppercase"
                                >
                                    Acciones
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            class="divide-y
                                   divide-gray-100
                                   bg-white"
                        >

                            @foreach($respaldos as $respaldo)

                                @php

                                    $nombreRespaldo =
                                        strtolower(
                                            $respaldo['nombre']
                                        );

                                    $comprimido =
                                        str_ends_with(
                                            $nombreRespaldo,
                                            '.sql.gz'
                                        );

                                @endphp


                                <tr
                                    class="transition
                                           hover:bg-gray-50"
                                >

                                    {{-- ====================================== --}}
                                    {{-- ARCHIVO --}}
                                    {{-- ====================================== --}}

                                    <td
                                        class="px-5 py-4"
                                    >

                                        <div
                                            class="flex
                                                   items-center
                                                   gap-3"
                                        >

                                            <div
                                                class="flex h-10 w-10
                                                       shrink-0
                                                       items-center
                                                       justify-center
                                                       rounded-xl
                                                       bg-blue-50
                                                       text-[#0056b3]"
                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[21px]"
                                                >
                                                    backup
                                                </span>

                                            </div>


                                            <div
                                                class="min-w-0"
                                            >

                                                <p
                                                    class="max-w-[450px]
                                                           truncate
                                                           text-sm
                                                           font-medium
                                                           text-gray-800"
                                                    title="{{ $respaldo['nombre'] }}"
                                                >
                                                    {{ $respaldo['nombre'] }}
                                                </p>

                                                <p
                                                    class="mt-0.5
                                                           text-xs
                                                           text-gray-400"
                                                >
                                                    Respaldo PostgreSQL
                                                </p>

                                            </div>

                                        </div>

                                    </td>


                                    {{-- ====================================== --}}
                                    {{-- FECHA --}}
                                    {{-- ====================================== --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4
                                               text-sm
                                               text-gray-600"
                                    >

                                        <div
                                            class="flex items-center
                                                   gap-1.5"
                                        >

                                            <span
                                                class="material-symbols-rounded
                                                       text-[17px]
                                                       text-gray-400"
                                            >
                                                schedule
                                            </span>

                                            {{
                                                $respaldo[
                                                    'fecha'
                                                ]->format(
                                                    'd/m/Y H:i:s'
                                                )
                                            }}

                                        </div>

                                    </td>


                                    {{-- ====================================== --}}
                                    {{-- TAMAÑO --}}
                                    {{-- ====================================== --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4
                                               text-sm
                                               text-gray-600"
                                    >

                                        {{
                                            $respaldo[
                                                'tamano'
                                            ]
                                        }}

                                    </td>


                                    {{-- ====================================== --}}
                                    {{-- TIPO --}}
                                    {{-- ====================================== --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4"
                                    >

                                        @if($comprimido)

                                            <span
                                                class="inline-flex
                                                       items-center gap-1
                                                       rounded-lg
                                                       bg-emerald-50
                                                       px-2.5 py-1
                                                       text-xs
                                                       font-medium
                                                       text-emerald-700"
                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[15px]"
                                                >
                                                    folder_zip
                                                </span>

                                                SQL comprimido

                                            </span>

                                        @else

                                            <span
                                                class="inline-flex
                                                       items-center gap-1
                                                       rounded-lg
                                                       bg-blue-50
                                                       px-2.5 py-1
                                                       text-xs
                                                       font-medium
                                                       text-blue-700"
                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[15px]"
                                                >
                                                    description
                                                </span>

                                                SQL

                                            </span>

                                        @endif

                                    </td>


                                    {{-- ====================================== --}}
                                    {{-- ACCIONES: DESCARGAR / RESTAURAR --}}
                                    {{-- ====================================== --}}

                                    <td
                                        class="px-5 py-4
                                               text-right"
                                    >

                                        <div
                                            class="flex flex-wrap
                                                   items-center
                                                   justify-end
                                                   gap-2"
                                        >

                                            {{-- ================================== --}}
                                            {{-- DESCARGAR --}}
                                            {{-- ================================== --}}

                                            <a
                                                href="{{
                                                    route(
                                                        'backups.download',
                                                        $respaldo[
                                                            'nombre'
                                                        ]
                                                    )
                                                }}"
                                                class="inline-flex
                                                       items-center
                                                       gap-2
                                                       rounded-lg
                                                       bg-emerald-600
                                                       px-4 py-2
                                                       text-sm
                                                       font-medium
                                                       text-white
                                                       transition
                                                       hover:bg-emerald-700
                                                       focus:outline-none
                                                       focus:ring-2
                                                       focus:ring-emerald-500/30"
                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[18px]"
                                                >
                                                    download
                                                </span>

                                                Descargar

                                            </a>


                                            {{-- ================================== --}}
                                            {{-- FN.22 - RESTAURAR --}}
                                            {{-- ================================== --}}

                                            <form
                                                action="{{
                                                    route(
                                                        'backups.restore',
                                                        $respaldo[
                                                            'nombre'
                                                        ]
                                                    )
                                                }}"
                                                method="POST"
                                                onsubmit="return confirm(
                                                    'ADVERTENCIA: Vas a restaurar la base de datos usando el respaldo:\n\n{{ addslashes($respaldo['nombre']) }}\n\nAntes de continuar, SotyGeo generará automáticamente un respaldo de seguridad del estado actual.\n\n¿Deseas continuar con la restauración?'
                                                );"
                                            >

                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="inline-flex
                                                           items-center
                                                           gap-2
                                                           rounded-lg
                                                           bg-amber-500
                                                           px-4 py-2
                                                           text-sm
                                                           font-medium
                                                           text-white
                                                           transition
                                                           hover:bg-amber-600
                                                           focus:outline-none
                                                           focus:ring-2
                                                           focus:ring-amber-500/30"
                                                >

                                                    <span
                                                        class="material-symbols-rounded
                                                               text-[18px]"
                                                    >
                                                        restore
                                                    </span>

                                                    Restaurar

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>


        {{-- ====================================================== --}}
        {{-- INFORMACIÓN --}}
        {{-- ====================================================== --}}

        <div
            class="rounded-2xl border
                   border-blue-100
                   bg-blue-50/50
                   p-5"
        >

            <div
                class="flex items-start
                       gap-3"
            >

                <span
                    class="material-symbols-rounded
                           mt-0.5 text-[21px]
                           text-[#0056b3]"
                >
                    info
                </span>


                <div>

                    <h3
                        class="text-sm
                               font-semibold
                               text-[#003d82]"
                    >
                        Información del respaldo
                    </h3>

                    <p
                        class="mt-1
                               text-sm
                               leading-relaxed
                               text-gray-600"
                    >
                        Las copias de seguridad contienen la
                        estructura y los datos almacenados en
                        PostgreSQL. Los archivos se mantienen
                        dentro del almacenamiento privado de
                        SotyGeo y únicamente el Super
                        Administrador puede generarlos,
                        descargarlos o restaurarlos.
                    </p>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>