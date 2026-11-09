<x-app-layout>
    <x-slot name="titulo">
        Reporte de Kilometraje y Recorridos
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
                        monitoring
                    </span>
                </div>

                <div>
                    <h1
                        class="font-['Josefin_Sans']
                               text-2xl font-semibold
                               text-white"
                    >
                        Reporte de Kilometraje y Recorridos
                    </h1>

                    <p
                        class="text-sm
                               text-blue-100"
                    >
                        Consulta la distancia recorrida por los vehículos
                        durante un periodo determinado.
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
                action="{{ route('reportes.kilometraje') }}"
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

                        value="{{ request('fecha_inicio', $fechaInicio->format('Y-m-d')) }}"

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

                        value="{{ request('fecha_fin', $fechaFin->format('Y-m-d')) }}"

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
                            {{ $empresas->first()?->nombre ?? 'Empresa' }}
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
                        href="{{ route('reportes.kilometraje') }}"

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
                            'reportes.kilometraje.pdf',
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
                            'reportes.kilometraje.excel',
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

            {{-- VEHÍCULOS --}}

            <div
                class="flex items-center
                       justify-between
                       rounded-2xl
                       border border-gray-200
                       bg-white p-5
                       shadow-sm"
            >

                <div>

                    <p
                        class="text-sm
                               text-gray-500"
                    >
                        Vehículos analizados
                    </p>

                    <p
                        class="mt-1
                               font-['Josefin_Sans']
                               text-3xl font-bold
                               text-[#0056b3]"
                    >
                        {{ $resultados->count() }}
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
                        directions_car
                    </span>

                </div>

            </div>


            {{-- KILÓMETROS --}}

            <div
                class="flex items-center
                       justify-between
                       rounded-2xl
                       border border-gray-200
                       bg-white p-5
                       shadow-sm"
            >

                <div>

                    <p
                        class="text-sm
                               text-gray-500"
                    >
                        Kilómetros totales
                    </p>

                    <p
                        class="mt-1
                               font-['Josefin_Sans']
                               text-3xl font-bold
                               text-[#0056b3]"
                    >
                        {{ number_format($totalKilometros, 2) }}
                    </p>

                    <p
                        class="text-xs
                               text-gray-400"
                    >
                        km
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
                        route
                    </span>

                </div>

            </div>


            {{-- RECORRIDOS --}}

            <div
                class="flex items-center
                       justify-between
                       rounded-2xl
                       border border-gray-200
                       bg-white p-5
                       shadow-sm"
            >

                <div>

                    <p
                        class="text-sm
                               text-gray-500"
                    >
                        Recorridos registrados
                    </p>

                    <p
                        class="mt-1
                               font-['Josefin_Sans']
                               text-3xl font-bold
                               text-[#0056b3]"
                    >
                        {{ $totalRecorridos }}
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
                        conversion_path
                    </span>

                </div>

            </div>


            {{-- GPS --}}

            <div
                class="flex items-center
                       justify-between
                       rounded-2xl
                       border border-gray-200
                       bg-white p-5
                       shadow-sm"
            >

                <div>

                    <p
                        class="text-sm
                               text-gray-500"
                    >
                        Registros GPS
                    </p>

                    <p
                        class="mt-1
                               font-['Josefin_Sans']
                               text-3xl font-bold
                               text-[#0056b3]"
                    >
                        {{ $totalRegistros }}
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
                        satellite_alt
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
                            Comparativa de kilometraje
                        </h2>

                        <p
                            class="text-xs
                                   text-gray-500"
                        >
                            Distancia acumulada por vehículo durante el periodo seleccionado.
                        </p>

                    </div>

                </div>

            </div>


            @if($resultados->isEmpty())

                <div
                    class="px-6 py-14
                           text-center"
                >

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

                    <p
                        class="mt-1
                               text-sm
                               text-gray-500"
                    >
                        No se encontraron vehículos para los filtros seleccionados.
                    </p>

                </div>

            @else

                @php

                    $maximoKilometros =
                        max(
                            0.01,
                            (float)
                            $resultados->max(
                                'kilometros'
                            )
                        );

                @endphp


                <div
                    class="space-y-5
                           p-5"
                >

                    @foreach($resultados as $resultado)

                        @php

                            $porcentaje =
                                min(
                                    100,
                                    (
                                        (float)
                                        $resultado['kilometros']

                                        /

                                        $maximoKilometros
                                    )

                                    *

                                    100
                                );

                        @endphp


                        <div>

                            <div
                                class="mb-2
                                       flex items-center
                                       justify-between
                                       gap-4"
                            >

                                <div class="min-w-0">

                                    <p
                                        class="truncate
                                               text-sm font-medium
                                               text-gray-700"
                                    >
                                        {{ $resultado['vehiculo'] }}
                                    </p>

                                    <p
                                        class="text-xs
                                               text-gray-400"
                                    >
                                        {{ $resultado['placas'] ?: 'Sin placas' }}
                                    </p>

                                </div>


                                <span
                                    class="shrink-0
                                           font-['Josefin_Sans']
                                           text-sm font-bold
                                           text-[#0056b3]"
                                >
                                    {{ number_format($resultado['kilometros'], 2) }}
                                    km
                                </span>

                            </div>


                            <div
                                class="h-3
                                       overflow-hidden
                                       rounded-full
                                       bg-gray-100"
                            >

                                <div
                                    class="h-full
                                           rounded-full
                                           bg-[#0056b3]
                                           transition-all
                                           duration-500"

                                    style="width: {{ $porcentaje }}%"
                                ></div>

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
                            Resultados del reporte
                        </h2>

                        <p
                            class="text-xs
                                   text-gray-500"
                        >
                            Información obtenida del historial GPS.
                        </p>

                    </div>

                </div>

            </div>


            @if($resultados->isEmpty())

                <div
                    class="px-6 py-14
                           text-center"
                >

                    <div
                        class="mx-auto
                               flex h-14 w-14
                               items-center justify-center
                               rounded-2xl
                               bg-gray-100
                               text-gray-400"
                    >

                        <span class="material-symbols-rounded">
                            search_off
                        </span>

                    </div>

                    <h3
                        class="mt-3
                               font-['Josefin_Sans']
                               font-semibold
                               text-gray-700"
                    >
                        Sin resultados
                    </h3>

                    <p
                        class="mt-1
                               text-sm
                               text-gray-500"
                    >
                        No existe información para los criterios indicados.
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
                                    Vehículo
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-xs font-semibold
                                           uppercase
                                           text-gray-500"
                                >
                                    Flotilla
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-right
                                           text-xs font-semibold
                                           uppercase
                                           text-gray-500"
                                >
                                    Kilómetros
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-right
                                           text-xs font-semibold
                                           uppercase
                                           text-gray-500"
                                >
                                    Recorridos
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-right
                                           text-xs font-semibold
                                           uppercase
                                           text-gray-500"
                                >
                                    GPS
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-xs font-semibold
                                           uppercase
                                           text-gray-500"
                                >
                                    Primer reporte
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-xs font-semibold
                                           uppercase
                                           text-gray-500"
                                >
                                    Último reporte
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            class="divide-y
                                   divide-gray-100
                                   bg-white"
                        >

                            @foreach($resultados as $resultado)

                                <tr
                                    class="transition
                                           hover:bg-blue-50/30"
                                >

                                    <td class="px-5 py-4">

                                        <div>

                                            <p
                                                class="font-['Josefin_Sans']
                                                       font-semibold
                                                       text-gray-800"
                                            >
                                                {{ $resultado['vehiculo'] }}
                                            </p>

                                            <p
                                                class="mt-0.5
                                                       text-xs
                                                       text-gray-400"
                                            >
                                                {{ trim(
                                                    ($resultado['marca'] ?? '')
                                                    .
                                                    ' '
                                                    .
                                                    ($resultado['modelo'] ?? '')
                                                ) }}

                                                @if($resultado['placas'])
                                                    · {{ $resultado['placas'] }}
                                                @endif
                                            </p>

                                        </div>

                                    </td>


                                    <td
                                        class="px-5 py-4
                                               text-sm
                                               text-gray-600"
                                    >
                                        {{ $resultado['flotilla'] ?: 'Sin flotilla' }}
                                    </td>


                                    <td
                                        class="px-5 py-4
                                               text-right"
                                    >

                                        <span
                                            class="font-['Josefin_Sans']
                                                   font-bold
                                                   text-[#0056b3]"
                                        >
                                            {{ number_format(
                                                $resultado['kilometros'],
                                                2
                                            ) }}
                                        </span>

                                        <span
                                            class="text-xs
                                                   text-gray-400"
                                        >
                                            km
                                        </span>

                                    </td>


                                    <td
                                        class="px-5 py-4
                                               text-right
                                               text-sm
                                               font-medium
                                               text-gray-700"
                                    >
                                        {{ $resultado['recorridos'] }}
                                    </td>


                                    <td
                                        class="px-5 py-4
                                               text-right
                                               text-sm
                                               font-medium
                                               text-gray-700"
                                    >
                                        {{ $resultado['registros_gps'] }}
                                    </td>


                                    <td
                                        class="px-5 py-4
                                               text-sm
                                               text-gray-500"
                                    >

                                        @if($resultado['primer_reporte'])

                                            {{ $resultado['primer_reporte']
                                                ->format('d/m/Y H:i')
                                            }}

                                        @else

                                            —

                                        @endif

                                    </td>


                                    <td
                                        class="px-5 py-4
                                               text-sm
                                               text-gray-500"
                                    >

                                        @if($resultado['ultimo_reporte'])

                                            {{ $resultado['ultimo_reporte']
                                                ->format('d/m/Y H:i')
                                            }}

                                        @else

                                            —

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>


                        <tfoot
                            class="border-t-2
                                   border-gray-200
                                   bg-gray-50"
                        >

                            <tr>

                                <td
                                    colspan="2"

                                    class="px-5 py-4
                                           font-['Josefin_Sans']
                                           font-semibold
                                           text-gray-700"
                                >
                                    Totales
                                </td>


                                <td
                                    class="px-5 py-4
                                           text-right
                                           font-['Josefin_Sans']
                                           font-bold
                                           text-[#0056b3]"
                                >
                                    {{ number_format(
                                        $totalKilometros,
                                        2
                                    ) }}
                                    km
                                </td>


                                <td
                                    class="px-5 py-4
                                           text-right
                                           font-semibold
                                           text-gray-700"
                                >
                                    {{ $totalRecorridos }}
                                </td>


                                <td
                                    class="px-5 py-4
                                           text-right
                                           font-semibold
                                           text-gray-700"
                                >
                                    {{ $totalRegistros }}
                                </td>


                                <td colspan="2"></td>

                            </tr>

                        </tfoot>

                    </table>

                </div>

            @endif

        </div>

    </div>

</x-app-layout>