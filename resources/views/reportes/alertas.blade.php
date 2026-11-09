<x-app-layout>

    <x-slot name="titulo">
        Reporte de Excesos de Velocidad y Alertas
    </x-slot>


    <div
        class="mx-auto max-w-[1500px]
               space-y-5 pb-8
               font-['Ubuntu']"
    >

        {{-- ====================================================== --}}
        {{-- HEADER --}}
        {{-- ====================================================== --}}

        <div
            class="flex flex-col gap-4
                   rounded-2xl
                   bg-[#0056b3]
                   p-5 shadow-sm
                   sm:flex-row
                   sm:items-center
                   sm:justify-between"
        >

            <div class="flex items-center gap-4">

                <div
                    class="flex h-12 w-12
                           items-center justify-center
                           rounded-xl
                           bg-white/15
                           text-white"
                >

                    <span
                        class="material-symbols-rounded
                               text-[28px]"
                    >
                        warning
                    </span>

                </div>


                <div>

                    <h1
                        class="font-['Josefin_Sans']
                               text-2xl font-semibold
                               text-white"
                    >
                        Reporte de Excesos de Velocidad y Alertas
                    </h1>

                    <p
                        class="text-sm
                               text-blue-100"
                    >
                        Consulta incidencias y excesos de velocidad registrados
                        durante la operación de los vehículos.
                    </p>

                </div>

            </div>


            <div
                class="inline-flex items-center
                       gap-2 rounded-xl
                       bg-white/10
                       px-4 py-2
                       text-sm
                       text-blue-100"
            >

                <span
                    class="material-symbols-rounded
                           text-[19px]"
                >
                    date_range
                </span>

                {{ $fechaInicio->format('d/m/Y') }}

                <span>
                    —
                </span>

                {{ $fechaFin->format('d/m/Y') }}

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- ERRORES --}}
        {{-- ====================================================== --}}

        @if($errors->any())

            <div
                class="rounded-2xl
                       border border-red-200
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

                    Revisa los filtros seleccionados.

                </div>


                <ul
                    class="mt-2 ml-7
                           list-disc
                           space-y-1
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
        {{-- FILTROS --}}
        {{-- ====================================================== --}}

        <div
            class="overflow-hidden
                   rounded-2xl
                   border border-gray-200
                   bg-white
                   shadow-sm"
        >

            <div
                class="border-b
                       border-gray-100
                       bg-gray-50/70
                       px-5 py-4"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-10 w-10
                               items-center justify-center
                               rounded-xl
                               bg-blue-50
                               text-[#0056b3]"
                    >

                        <span class="material-symbols-rounded">
                            filter_alt
                        </span>

                    </div>


                    <div>

                        <h2
                            class="font-['Josefin_Sans']
                                   font-semibold
                                   text-[#003d82]"
                        >
                            Filtros del reporte
                        </h2>

                        <p
                            class="text-xs
                                   text-gray-500"
                        >
                            Selecciona el periodo y los vehículos que deseas analizar.
                        </p>

                    </div>

                </div>

            </div>


            <form
                action="{{ route('reportes.alertas') }}"
                method="GET"

                class="grid grid-cols-1
                       gap-4 p-5
                       md:grid-cols-2
                       xl:grid-cols-5"
            >

                {{-- ================================================== --}}
                {{-- FECHA INICIO --}}
                {{-- ================================================== --}}

                <div>

                    <label
                        for="fecha_inicio"

                        class="mb-1.5 block
                               text-xs font-semibold
                               uppercase
                               text-gray-600"
                    >
                        Desde
                    </label>

                    <input
                        type="date"
                        id="fecha_inicio"
                        name="fecha_inicio"

                        value="{{ request(
                            'fecha_inicio',
                            $fechaInicio->format('Y-m-d')
                        ) }}"

                        class="w-full
                               rounded-xl
                               border-gray-300
                               text-sm
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                </div>


                {{-- ================================================== --}}
                {{-- FECHA FIN --}}
                {{-- ================================================== --}}

                <div>

                    <label
                        for="fecha_fin"

                        class="mb-1.5 block
                               text-xs font-semibold
                               uppercase
                               text-gray-600"
                    >
                        Hasta
                    </label>

                    <input
                        type="date"
                        id="fecha_fin"
                        name="fecha_fin"

                        value="{{ request(
                            'fecha_fin',
                            $fechaFin->format('Y-m-d')
                        ) }}"

                        class="w-full
                               rounded-xl
                               border-gray-300
                               text-sm
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                </div>


                {{-- ================================================== --}}
                {{-- EMPRESA --}}
                {{-- ================================================== --}}

                <div>

                    <label
                        for="empresa_id"

                        class="mb-1.5 block
                               text-xs font-semibold
                               uppercase
                               text-gray-600"
                    >
                        Empresa
                    </label>


                    @if(auth()->user()->hasRole('Super Administrador'))

                        <select
                            id="empresa_id"
                            name="empresa_id"

                            class="w-full
                                   rounded-xl
                                   border-gray-300
                                   text-sm
                                   focus:border-[#0056b3]
                                   focus:ring-[#0056b3]"
                        >

                            <option value="">
                                Todas
                            </option>


                            @foreach($empresas as $empresa)

                                <option
                                    value="{{ $empresa->id }}"

                                    @selected(
                                        request('empresa_id')
                                        ==
                                        $empresa->id
                                    )
                                >
                                    {{ $empresa->nombre }}
                                </option>

                            @endforeach

                        </select>

                    @else

                        <div
                            class="flex h-[42px]
                                   items-center
                                   rounded-xl
                                   border border-gray-200
                                   bg-gray-50
                                   px-3
                                   text-sm
                                   text-gray-600"
                        >

                            {{
                                $empresas->first()?->nombre
                                ??
                                'Empresa'
                            }}

                        </div>

                    @endif

                </div>


                {{-- ================================================== --}}
                {{-- FLOTILLA --}}
                {{-- ================================================== --}}

                <div>

                    <label
                        for="flotilla_id"

                        class="mb-1.5 block
                               text-xs font-semibold
                               uppercase
                               text-gray-600"
                    >
                        Flotilla
                    </label>

                    <select
                        id="flotilla_id"
                        name="flotilla_id"

                        class="w-full
                               rounded-xl
                               border-gray-300
                               text-sm
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                        <option value="">
                            Todas
                        </option>


                        @foreach($flotillas as $flotilla)

                            <option
                                value="{{ $flotilla->id }}"

                                @selected(
                                    request('flotilla_id')
                                    ==
                                    $flotilla->id
                                )
                            >
                                {{ $flotilla->nombre }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- ================================================== --}}
                {{-- VEHÍCULO --}}
                {{-- ================================================== --}}

                <div>

                    <label
                        for="vehiculo_id"

                        class="mb-1.5 block
                               text-xs font-semibold
                               uppercase
                               text-gray-600"
                    >
                        Vehículo
                    </label>

                    <select
                        id="vehiculo_id"
                        name="vehiculo_id"

                        class="w-full
                               rounded-xl
                               border-gray-300
                               text-sm
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                        <option value="">
                            Todos
                        </option>


                        @foreach($vehiculos as $vehiculo)

                            <option
                                value="{{ $vehiculo->id }}"

                                @selected(
                                    request('vehiculo_id')
                                    ==
                                    $vehiculo->id
                                )
                            >

                                {{ $vehiculo->nombre }}

                                @if($vehiculo->placas)
                                    — {{ $vehiculo->placas }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- ================================================== --}}
                {{-- ACCIONES --}}
                {{-- ================================================== --}}

                <div
                    class="flex flex-wrap
                           items-end gap-2
                           md:col-span-2
                           xl:col-span-5"
                >

                    <button
                        type="submit"

                        class="inline-flex
                               items-center gap-2
                               rounded-xl
                               bg-[#0056b3]
                               px-5 py-2.5
                               text-sm font-medium
                               text-white
                               transition
                               hover:bg-[#004494]"
                    >

                        <span
                            class="material-symbols-rounded
                                   text-[19px]"
                        >
                            search
                        </span>

                        Generar reporte

                    </button>


                    <a
                        href="{{ route('reportes.alertas') }}"

                        class="inline-flex
                               items-center gap-2
                               rounded-xl
                               border border-gray-300
                               bg-white
                               px-4 py-2.5
                               text-sm font-medium
                               text-gray-600
                               transition
                               hover:bg-gray-50"
                    >

                        <span
                            class="material-symbols-rounded
                                   text-[18px]"
                        >
                            restart_alt
                        </span>

                        Limpiar filtros

                    </a>
                        {{-- ================================================== --}}
                        {{-- EXPORTAR PDF --}}
                        {{-- ================================================== --}}

                        <a
                            href="{{ route(
                                'reportes.alertas.pdf',
                                request()->query()
                            ) }}"

                            class="inline-flex
                                items-center gap-2
                                rounded-xl
                                bg-red-600
                                px-4 py-2.5
                                text-sm font-medium
                                text-white
                                transition
                                hover:bg-red-700"
                        >

                            <span
                                class="material-symbols-rounded
                                    text-[18px]"
                            >
                                picture_as_pdf
                            </span>

                            Exportar PDF

                        </a>


                        {{-- ================================================== --}}
                        {{-- EXPORTAR EXCEL --}}
                        {{-- ================================================== --}}

                        <a
                            href="{{ route(
                                'reportes.alertas.excel',
                                request()->query()
                            ) }}"

                            class="inline-flex
                                items-center gap-2
                                rounded-xl
                                bg-emerald-600
                                px-4 py-2.5
                                text-sm font-medium
                                text-white
                                transition
                                hover:bg-emerald-700"
                        >

                            <span
                                class="material-symbols-rounded
                                    text-[18px]"
                            >
                                table_view
                            </span>

                            Exportar Excel

                        </a>
                </div>

            </form>

        </div>


        {{-- ====================================================== --}}
        {{-- KPIs --}}
        {{-- ====================================================== --}}

        <div
            class="grid grid-cols-1
                   gap-4
                   sm:grid-cols-2
                   xl:grid-cols-4"
        >

            {{-- TOTAL EVENTOS --}}

            <div
                class="flex items-center
                       justify-between
                       rounded-2xl
                       border border-gray-200
                       bg-white p-5
                       shadow-sm"
            >

                <div>

                    <p class="text-sm text-gray-500">
                        Eventos registrados
                    </p>

                    <p
                        class="mt-1
                               font-['Josefin_Sans']
                               text-3xl font-bold
                               text-[#0056b3]"
                    >
                        {{ $totalEventos }}
                    </p>

                </div>


                <div
                    class="flex h-12 w-12
                           items-center justify-center
                           rounded-xl
                           bg-blue-50
                           text-[#0056b3]"
                >

                    <span class="material-symbols-rounded">
                        notifications_active
                    </span>

                </div>

            </div>


            {{-- EXCESOS --}}

            <div
                class="flex items-center
                       justify-between
                       rounded-2xl
                       border border-gray-200
                       bg-white p-5
                       shadow-sm"
            >

                <div>

                    <p class="text-sm text-gray-500">
                        Excesos de velocidad
                    </p>

                    <p
                        class="mt-1
                               font-['Josefin_Sans']
                               text-3xl font-bold
                               text-red-600"
                    >
                        {{ $totalExcesos }}
                    </p>

                </div>


                <div
                    class="flex h-12 w-12
                           items-center justify-center
                           rounded-xl
                           bg-red-50
                           text-red-600"
                >

                    <span class="material-symbols-rounded">
                        speed
                    </span>

                </div>

            </div>


            {{-- OTRAS ALERTAS --}}

            <div
                class="flex items-center
                       justify-between
                       rounded-2xl
                       border border-gray-200
                       bg-white p-5
                       shadow-sm"
            >

                <div>

                    <p class="text-sm text-gray-500">
                        Otras alertas
                    </p>

                    <p
                        class="mt-1
                               font-['Josefin_Sans']
                               text-3xl font-bold
                               text-amber-600"
                    >
                        {{ $totalAlertas }}
                    </p>

                </div>


                <div
                    class="flex h-12 w-12
                           items-center justify-center
                           rounded-xl
                           bg-amber-50
                           text-amber-600"
                >

                    <span class="material-symbols-rounded">
                        warning
                    </span>

                </div>

            </div>


            {{-- VELOCIDAD MÁXIMA --}}

            <div
                class="flex items-center
                       justify-between
                       rounded-2xl
                       border border-gray-200
                       bg-white p-5
                       shadow-sm"
            >

                <div>

                    <p class="text-sm text-gray-500">
                        Velocidad máxima
                    </p>

                    <p
                        class="mt-1
                               font-['Josefin_Sans']
                               text-3xl font-bold
                               text-[#0056b3]"
                    >
                        {{ number_format($velocidadMaxima, 2) }}
                    </p>

                    <p class="text-xs text-gray-400">
                        km/h
                    </p>

                </div>


                <div
                    class="flex h-12 w-12
                           items-center justify-center
                           rounded-xl
                           bg-blue-50
                           text-[#0056b3]"
                >

                    <span class="material-symbols-rounded">
                        avg_pace
                    </span>

                </div>

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- GRÁFICA --}}
        {{-- ====================================================== --}}

        <div
            class="overflow-hidden
                   rounded-2xl
                   border border-gray-200
                   bg-white
                   shadow-sm"
        >

            <div
                class="border-b
                       border-gray-100
                       bg-gray-50/60
                       px-5 py-4"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-10 w-10
                               items-center justify-center
                               rounded-xl
                               bg-blue-50
                               text-[#0056b3]"
                    >

                        <span class="material-symbols-rounded">
                            bar_chart
                        </span>

                    </div>


                    <div>

                        <h2
                            class="font-['Josefin_Sans']
                                   font-semibold
                                   text-[#003d82]"
                        >
                            Incidencias por vehículo
                        </h2>

                        <p
                            class="text-xs
                                   text-gray-500"
                        >
                            Comparativa de excesos de velocidad y otras alertas.
                        </p>

                    </div>

                </div>

            </div>


            @if($grafica->isEmpty())

                <div class="px-6 py-14 text-center">

                    <span
                        class="material-symbols-rounded
                               text-5xl
                               text-gray-300"
                    >
                        query_stats
                    </span>

                    <h3
                        class="mt-3
                               font-['Josefin_Sans']
                               font-semibold
                               text-gray-700"
                    >
                        No hay información disponible
                    </h3>

                </div>

            @else

                @php

                    $maximoEventos =
                        max(
                            1,
                            (int)
                            $grafica->max(
                                'total'
                            )
                        );

                @endphp


                <div class="space-y-6 p-5">

                    @foreach($grafica as $item)

                        @php

                            $porcentajeTotal =
                                $item['total'] > 0

                                    ?

                                    (
                                        $item['total']
                                        /
                                        $maximoEventos
                                    )
                                    *
                                    100

                                    :

                                    0;


                            $porcentajeExcesos =
                                $item['total'] > 0

                                    ?

                                    (
                                        $item['excesos']
                                        /
                                        $item['total']
                                    )
                                    *
                                    100

                                    :

                                    0;

                        @endphp


                        <div>

                            <div
                                class="mb-2
                                       flex items-center
                                       justify-between
                                       gap-4"
                            >

                                <div>

                                    <p
                                        class="text-sm
                                               font-medium
                                               text-gray-700"
                                    >
                                        {{ $item['vehiculo'] }}
                                    </p>

                                    <p
                                        class="text-xs
                                               text-gray-400"
                                    >
                                        {{ $item['placas'] ?: 'Sin placas' }}
                                    </p>

                                </div>


                                <div
                                    class="text-right
                                           text-xs"
                                >

                                    <span
                                        class="font-semibold
                                               text-red-600"
                                    >
                                        {{ $item['excesos'] }}
                                        excesos
                                    </span>

                                    <span class="mx-1 text-gray-300">
                                        ·
                                    </span>

                                    <span
                                        class="font-semibold
                                               text-amber-600"
                                    >
                                        {{ $item['alertas'] }}
                                        alertas
                                    </span>

                                </div>

                            </div>


                            <div
                                class="h-4
                                       overflow-hidden
                                       rounded-full
                                       bg-gray-100"
                            >

                                @if($item['total'] > 0)

                                    <div
                                        class="h-full
                                               overflow-hidden
                                               rounded-full"

                                        style="
                                            width:
                                            {{ min(
                                                100,
                                                $porcentajeTotal
                                            ) }}%;
                                        "
                                    >

                                        <div
                                            class="h-full
                                                   bg-red-500"

                                            style="
                                                width:
                                                {{ min(
                                                    100,
                                                    $porcentajeExcesos
                                                ) }}%;
                                                float:left;
                                            "
                                        ></div>


                                        <div
                                            class="h-full
                                                   bg-amber-400"

                                            style="
                                                width:
                                                {{
                                                    max(
                                                        0,
                                                        100
                                                        -
                                                        min(
                                                            100,
                                                            $porcentajeExcesos
                                                        )
                                                    )
                                                }}%;
                                                float:left;
                                            "
                                        ></div>

                                    </div>

                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>


        {{-- ====================================================== --}}
        {{-- TABLA --}}
        {{-- ====================================================== --}}

        <div
            class="overflow-hidden
                   rounded-2xl
                   border border-gray-200
                   bg-white
                   shadow-sm"
        >

            <div
                class="border-b
                       border-gray-100
                       bg-gray-50/60
                       px-5 py-4"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-10 w-10
                               items-center justify-center
                               rounded-xl
                               bg-blue-50
                               text-[#0056b3]"
                    >

                        <span class="material-symbols-rounded">
                            table_rows
                        </span>

                    </div>


                    <div>

                        <h2
                            class="font-['Josefin_Sans']
                                   font-semibold
                                   text-[#003d82]"
                        >
                            Eventos registrados
                        </h2>

                        <p
                            class="text-xs
                                   text-gray-500"
                        >
                            Excesos de velocidad y alertas durante el periodo seleccionado.
                        </p>

                    </div>

                </div>

            </div>


            @if($eventos->isEmpty())

                <div class="px-6 py-14 text-center">

                    <div
                        class="mx-auto
                               flex h-14 w-14
                               items-center justify-center
                               rounded-2xl
                               bg-gray-100
                               text-gray-400"
                    >

                        <span class="material-symbols-rounded">
                            notifications_off
                        </span>

                    </div>


                    <h3
                        class="mt-3
                               font-['Josefin_Sans']
                               font-semibold
                               text-gray-700"
                    >
                        Sin eventos
                    </h3>

                    <p
                        class="mt-1
                               text-sm
                               text-gray-500"
                    >
                        No se encontraron excesos de velocidad ni alertas
                        para los criterios seleccionados.
                    </p>

                </div>

            @else

                <div class="overflow-x-auto">

                    <table
                        class="min-w-full
                               divide-y
                               divide-gray-200"
                    >

                        <thead class="bg-gray-50">

                            <tr>

                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-xs font-semibold
                                           uppercase
                                           text-gray-500"
                                >
                                    Fecha
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-xs font-semibold
                                           uppercase
                                           text-gray-500"
                                >
                                    Vehículo
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-xs font-semibold
                                           uppercase
                                           text-gray-500"
                                >
                                    Tipo
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-right
                                           text-xs font-semibold
                                           uppercase
                                           text-gray-500"
                                >
                                    Velocidad
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-right
                                           text-xs font-semibold
                                           uppercase
                                           text-gray-500"
                                >
                                    Límite
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-xs font-semibold
                                           uppercase
                                           text-gray-500"
                                >
                                    Descripción
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            class="divide-y
                                   divide-gray-100
                                   bg-white"
                        >

                            @foreach($eventos as $evento)

                                <tr
                                    class="transition
                                           hover:bg-blue-50/30"
                                >

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4
                                               text-sm
                                               text-gray-500"
                                    >

                                        {{
                                            $evento['fecha']
                                                ->format(
                                                    'd/m/Y H:i'
                                                )
                                        }}

                                    </td>


                                    <td class="px-5 py-4">

                                        <p
                                            class="font-['Josefin_Sans']
                                                   font-semibold
                                                   text-gray-800"
                                        >
                                            {{ $evento['vehiculo'] }}
                                        </p>

                                        <p
                                            class="mt-0.5
                                                   text-xs
                                                   text-gray-400"
                                        >
                                            {{ $evento['placas'] ?: 'Sin placas' }}

                                            @if($evento['flotilla'])

                                                · {{ $evento['flotilla'] }}

                                            @endif
                                        </p>

                                    </td>


                                    <td class="px-5 py-4">

                                        @if($evento['origen'] === 'gps')

                                            <span
                                                class="inline-flex
                                                       items-center gap-1
                                                       rounded-full
                                                       bg-red-50
                                                       px-2.5 py-1
                                                       text-xs font-semibold
                                                       text-red-700"
                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[15px]"
                                                >
                                                    speed
                                                </span>

                                                {{ $evento['tipo'] }}

                                            </span>

                                        @else

                                            <span
                                                class="inline-flex
                                                       items-center gap-1
                                                       rounded-full
                                                       bg-amber-50
                                                       px-2.5 py-1
                                                       text-xs font-semibold
                                                       text-amber-700"
                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[15px]"
                                                >
                                                    warning
                                                </span>

                                                {{ $evento['tipo'] }}

                                            </span>

                                        @endif

                                    </td>


                                    <td
                                        class="px-5 py-4
                                               text-right
                                               text-sm"
                                    >

                                        @if($evento['velocidad'] !== null)

                                            <span
                                                class="font-semibold
                                                       text-red-600"
                                            >
                                                {{
                                                    number_format(
                                                        $evento['velocidad'],
                                                        2
                                                    )
                                                }}
                                            </span>

                                            <span
                                                class="text-xs
                                                       text-gray-400"
                                            >
                                                km/h
                                            </span>

                                        @else

                                            <span class="text-gray-400">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    <td
                                        class="px-5 py-4
                                               text-right
                                               text-sm"
                                    >

                                        @if($evento['limite'] !== null)

                                            {{
                                                number_format(
                                                    $evento['limite'],
                                                    2
                                                )
                                            }}

                                            <span
                                                class="text-xs
                                                       text-gray-400"
                                            >
                                                km/h
                                            </span>

                                        @else

                                            <span class="text-gray-400">
                                                —
                                            </span>

                                        @endif

                                    </td>


                                    <td
                                        class="max-w-[420px]
                                               px-5 py-4
                                               text-sm
                                               text-gray-600"
                                    >

                                        {{ $evento['mensaje'] }}

                                        @if($evento['excedente'] !== null)

                                            <div
                                                class="mt-1
                                                       text-xs
                                                       font-semibold
                                                       text-red-600"
                                            >
                                                Excedente:
                                                {{
                                                    number_format(
                                                        $evento['excedente'],
                                                        2
                                                    )
                                                }}
                                                km/h
                                            </div>

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>


        {{-- ====================================================== --}}
        {{-- LEYENDA --}}
        {{-- ====================================================== --}}

        <div
            class="flex flex-wrap
                   items-center gap-4
                   rounded-2xl
                   border border-gray-200
                   bg-white
                   px-5 py-4
                   text-xs text-gray-500
                   shadow-sm"
        >

            <span class="font-semibold text-gray-600">
                Referencia:
            </span>


            <span class="inline-flex items-center gap-2">

                <span
                    class="h-3 w-3
                           rounded-full
                           bg-red-500"
                ></span>

                Exceso de velocidad

            </span>


            <span class="inline-flex items-center gap-2">

                <span
                    class="h-3 w-3
                           rounded-full
                           bg-amber-400"
                ></span>

                Otras alertas

            </span>

        </div>

    </div>

</x-app-layout>