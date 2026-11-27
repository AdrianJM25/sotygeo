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

        @if(session('backup_success') || session('success'))

            <div
                role="status"
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

                    {{ session('backup_success') ?? session('success') }}

                </div>

            </div>

        @endif


        {{-- ====================================================== --}}
        {{-- ERRORES --}}
        {{-- ====================================================== --}}

        @if(session('backup_error'))
            <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                {{ session('backup_error') }}
            </div>
        @endif

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
                    data-backup-operation="generate"
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
                                                data-backup-operation="restore"
                                                data-backup-name="{{ $respaldo['nombre'] }}"
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


    <dialog id="backup-confirm-dialog" class="w-[calc(100%-2rem)] max-w-lg rounded-2xl border border-gray-200 bg-white p-6 shadow-xl backdrop:bg-gray-900/50">
        <div class="flex items-start gap-3">
            <span class="material-symbols-rounded text-3xl text-amber-600">warning</span>
            <div>
                <h2 class="text-lg font-semibold text-gray-900">¿Restaurar base de datos?</h2>
                <p class="mt-2 text-sm text-gray-600">Esta operación puede reemplazar los datos actuales de SotyGeo. Se intentará generar un respaldo de seguridad antes de continuar.</p>
                <p id="backup-selected-name" class="mt-3 break-all rounded-lg bg-gray-100 p-3 text-sm font-medium text-gray-800"></p>
                <p class="mt-3 text-sm text-red-700">Comprueba que estás en la base de datos de prueba y que el respaldo es válido antes de continuar.</p>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
            <button id="backup-cancel" type="button" autofocus class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700">Cancelar</button>
            <button id="backup-confirm" type="button" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Sí, restaurar</button>
        </div>
    </dialog>

    <dialog id="backup-loading-dialog" class="w-[calc(100%-2rem)] max-w-sm rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-xl backdrop:bg-gray-900/50" aria-labelledby="backup-loading-title">
        <div class="mx-auto h-10 w-10 animate-spin rounded-full border-4 border-blue-100 border-t-[#0056b3]" aria-hidden="true"></div>
        <h2 id="backup-loading-title" class="mt-4 font-semibold text-gray-900">Procesando respaldo…</h2>
        <p class="mt-2 text-sm text-gray-500">No cierres ni actualices esta página mientras se realiza la operación.</p>
    </dialog>

    <script>
        (() => {
            const confirmDialog = document.getElementById('backup-confirm-dialog');
            const loadingDialog = document.getElementById('backup-loading-dialog');
            const selectedName = document.getElementById('backup-selected-name');
            let pendingForm = null;
            let approvedForm = null;
            let busy = false;

            function showLoading(form) {
                busy = true;
                const restoring = form.dataset.backupOperation === 'restore';
                document.getElementById('backup-loading-title').textContent = restoring
                    ? 'Restaurando base de datos…' : 'Generando respaldo…';
                document.querySelectorAll('form[data-backup-operation] button[type="submit"]').forEach(button => {
                    button.disabled = true;
                    button.classList.add('opacity-60', 'cursor-not-allowed');
                });
                if (typeof loadingDialog.showModal === 'function') loadingDialog.showModal();
            }

            document.querySelectorAll('form[data-backup-operation]').forEach(form => {
                form.addEventListener('submit', event => {
                    if (busy) { event.preventDefault(); return; }
                    if (form.dataset.backupOperation === 'restore' && approvedForm !== form) {
                        event.preventDefault();
                        if (typeof confirmDialog.showModal !== 'function') {
                            if (window.confirm('¿Restaurar la base de datos desde ' + form.dataset.backupName + '? Puede reemplazar los datos actuales.')) {
                                approvedForm = form;
                                form.requestSubmit();
                            }
                            return;
                        }
                        pendingForm = form;
                        selectedName.textContent = form.dataset.backupName;
                        confirmDialog.showModal();
                        return;
                    }
                    approvedForm = null;
                    showLoading(form);
                });
            });

            document.getElementById('backup-cancel').addEventListener('click', () => confirmDialog.close());
            confirmDialog.addEventListener('close', () => { pendingForm = null; });
            document.getElementById('backup-confirm').addEventListener('click', () => {
                const form = pendingForm;
                confirmDialog.close();
                if (form) { approvedForm = form; form.requestSubmit(); }
            });
            loadingDialog.addEventListener('cancel', event => event.preventDefault());
            window.addEventListener('pageshow', event => {
                if (!event.persisted) return;
                busy = false;
                approvedForm = null;
                if (loadingDialog.open) loadingDialog.close();
                document.querySelectorAll('form[data-backup-operation] button[type="submit"]').forEach(button => {
                    button.disabled = false;
                    button.classList.remove('opacity-60', 'cursor-not-allowed');
                });
            });
        })();
    </script>

</x-app-layout>