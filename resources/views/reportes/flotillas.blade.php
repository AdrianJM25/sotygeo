<x-app-layout>

    <x-slot name="titulo">
        Reporte de Utilización de la Flotilla
    </x-slot>

    <div class="mx-auto max-w-[1500px] space-y-5 pb-8 font-['Ubuntu']">

        {{-- HEADER --}}
        <div
            class="flex flex-col gap-4 rounded-2xl bg-[#0056b3] p-5 shadow-sm
                   sm:flex-row sm:items-center sm:justify-between"
        >

            <div class="flex items-center gap-4">

                <div
                    class="flex h-12 w-12 items-center justify-center
                           rounded-xl bg-white/15 text-white"
                >
                    <span class="material-symbols-rounded text-[28px]">
                        local_shipping
                    </span>
                </div>

                <div>

                    <h1
                        class="font-['Josefin_Sans'] text-2xl
                               font-semibold text-white"
                    >
                        Reporte de Utilización de la Flotilla
                    </h1>

                    <p class="text-sm text-blue-100">
                        Identifica vehículos con actividad y sin actividad
                        durante el periodo seleccionado.
                    </p>

                </div>

            </div>

            <div
                class="inline-flex items-center gap-2 rounded-xl
                       bg-white/10 px-4 py-2 text-sm text-blue-100"
            >

                <span class="material-symbols-rounded text-[19px]">
                    date_range
                </span>

                {{ $fechaInicio->format('d/m/Y') }}

                <span>—</span>

                {{ $fechaFin->format('d/m/Y') }}

            </div>

        </div>


        {{-- ERRORES --}}
        @if($errors->any())

            <div
                class="rounded-2xl border border-red-200
                       bg-red-50 px-5 py-4"
            >

                <div
                    class="flex items-center gap-2
                           text-sm font-semibold text-red-700"
                >

                    <span class="material-symbols-rounded text-[20px]">
                        error
                    </span>

                    Revisa los filtros seleccionados.

                </div>

                <ul
                    class="mt-2 ml-7 list-disc space-y-1
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


        {{-- FILTROS --}}
        <div
            class="overflow-hidden rounded-2xl
                   border border-gray-200 bg-white shadow-sm"
        >

            <div
                class="border-b border-gray-100
                       bg-gray-50/70 px-5 py-4"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-10 w-10 items-center justify-center
                               rounded-xl bg-blue-50 text-[#0056b3]"
                    >

                        <span class="material-symbols-rounded">
                            filter_alt
                        </span>

                    </div>

                    <div>

                        <h2
                            class="font-['Josefin_Sans']
                                   font-semibold text-[#003d82]"
                        >
                            Filtros del reporte
                        </h2>

                        <p class="text-xs text-gray-500">
                            Selecciona una flotilla y el periodo que deseas analizar.
                        </p>

                    </div>

                </div>

            </div>


            <form
                action="{{ route('reportes.flotillas') }}"
                method="GET"
                class="grid grid-cols-1 gap-4 p-5
                       md:grid-cols-2 xl:grid-cols-4"
            >

                {{-- FECHA INICIO --}}
                <div>

                    <label
                        for="fecha_inicio"
                        class="mb-1.5 block text-xs
                               font-semibold uppercase text-gray-600"
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
                        class="w-full rounded-xl border-gray-300
                               text-sm focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                </div>


                {{-- FECHA FIN --}}
                <div>

                    <label
                        for="fecha_fin"
                        class="mb-1.5 block text-xs
                               font-semibold uppercase text-gray-600"
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
                        class="w-full rounded-xl border-gray-300
                               text-sm focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                </div>


                {{-- EMPRESA --}}
                <div>

                    <label
                        for="empresa_id"
                        class="mb-1.5 block text-xs
                               font-semibold uppercase text-gray-600"
                    >
                        Empresa
                    </label>

                    @if(auth()->user()->hasRole('Super Administrador'))

                        <select
                            id="empresa_id"
                            name="empresa_id"
                            class="w-full rounded-xl border-gray-300
                                   text-sm focus:border-[#0056b3]
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

                        <input
                            type="text"
                            value="{{ $empresas->first()?->nombre ?? 'Empresa asignada' }}"
                            disabled
                            class="w-full rounded-xl border-gray-200
                                   bg-gray-50 text-sm text-gray-500"
                        >

                    @endif

                </div>


                {{-- FLOTILLA --}}
                <div>

                    <label
                        for="flotilla_id"
                        class="mb-1.5 block text-xs
                               font-semibold uppercase text-gray-600"
                    >
                        Flotilla
                    </label>

                    <select
                        id="flotilla_id"
                        name="flotilla_id"
                        class="w-full rounded-xl border-gray-300
                               text-sm focus:border-[#0056b3]
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


                {{-- BOTONES --}}
                <div
                    class="flex flex-wrap items-end gap-2
                           md:col-span-2 xl:col-span-4"
                >

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2
                               rounded-xl bg-[#0056b3]
                               px-5 py-2.5 text-sm font-medium
                               text-white transition
                               hover:bg-[#004494]"
                    >

                        <span class="material-symbols-rounded text-[19px]">
                            search
                        </span>

                        Generar reporte

                    </button>


                    <a
                        href="{{ route('reportes.flotillas') }}"
                        class="inline-flex items-center gap-2
                               rounded-xl border border-gray-300
                               bg-white px-4 py-2.5 text-sm
                               font-medium text-gray-600 transition
                               hover:bg-gray-50"
                    >

                        <span class="material-symbols-rounded text-[18px]">
                            restart_alt
                        </span>

                        Limpiar filtros

                    </a>


                    <a
                        href="{{ route(
                            'reportes.flotillas.pdf',
                            request()->query()
                        ) }}"
                        class="inline-flex items-center gap-2
                               rounded-xl bg-red-600
                               px-4 py-2.5 text-sm font-medium
                               text-white transition hover:bg-red-700"
                    >

                        <span class="material-symbols-rounded text-[18px]">
                            picture_as_pdf
                        </span>

                        Exportar PDF

                    </a>


                    <a
                        href="{{ route(
                            'reportes.flotillas.excel',
                            request()->query()
                        ) }}"
                        class="inline-flex items-center gap-2
                               rounded-xl bg-emerald-600
                               px-4 py-2.5 text-sm font-medium
                               text-white transition hover:bg-emerald-700"
                    >

                        <span class="material-symbols-rounded text-[18px]">
                            table_view
                        </span>

                        Exportar Excel

                    </a>

                </div>

            </form>

        </div>


        {{-- KPIs --}}
        <div
            class="grid grid-cols-1 gap-4
                   sm:grid-cols-2 xl:grid-cols-4"
        >

            {{-- TOTAL VEHÍCULOS --}}
            <div
                class="flex items-center justify-between
                       rounded-2xl border border-gray-200
                       bg-white p-5 shadow-sm"
            >

                <div>

                    <p class="text-sm text-gray-500">
                        Vehículos analizados
                    </p>

                    <p
                        class="mt-1 font-['Josefin_Sans']
                               text-3xl font-bold text-[#0056b3]"
                    >
                        {{ $totalVehiculos }}
                    </p>

                </div>

                <div
                    class="flex h-12 w-12 items-center
                           justify-center rounded-xl
                           bg-blue-50 text-[#0056b3]"
                >

                    <span class="material-symbols-rounded">
                        directions_car
                    </span>

                </div>

            </div>


            {{-- ACTIVOS --}}
            <div
                class="flex items-center justify-between
                       rounded-2xl border border-gray-200
                       bg-white p-5 shadow-sm"
            >

                <div>

                    <p class="text-sm text-gray-500">
                        Con actividad
                    </p>

                    <p
                        class="mt-1 font-['Josefin_Sans']
                               text-3xl font-bold text-emerald-600"
                    >
                        {{ $totalActivos }}
                    </p>

                </div>

                <div
                    class="flex h-12 w-12 items-center
                           justify-center rounded-xl
                           bg-emerald-50 text-emerald-600"
                >

                    <span class="material-symbols-rounded">
                        check_circle
                    </span>

                </div>

            </div>


            {{-- INACTIVOS --}}
            <div
                class="flex items-center justify-between
                       rounded-2xl border border-gray-200
                       bg-white p-5 shadow-sm"
            >

                <div>

                    <p class="text-sm text-gray-500">
                        Sin actividad
                    </p>

                    <p
                        class="mt-1 font-['Josefin_Sans']
                               text-3xl font-bold text-red-600"
                    >
                        {{ $totalInactivos }}
                    </p>

                </div>

                <div
                    class="flex h-12 w-12 items-center
                           justify-center rounded-xl
                           bg-red-50 text-red-600"
                >

                    <span class="material-symbols-rounded">
                        do_not_disturb_on
                    </span>

                </div>

            </div>


            {{-- PORCENTAJE --}}
            <div
                class="flex items-center justify-between
                       rounded-2xl border border-gray-200
                       bg-white p-5 shadow-sm"
            >

                <div>

                    <p class="text-sm text-gray-500">
                        Utilización de flotilla
                    </p>

                    <p
                        class="mt-1 font-['Josefin_Sans']
                               text-3xl font-bold text-[#0056b3]"
                    >
                        {{ number_format(
                            $porcentajeUtilizacion,
                            2
                        ) }}%
                    </p>

                </div>

                <div
                    class="flex h-12 w-12 items-center
                           justify-center rounded-xl
                           bg-blue-50 text-[#0056b3]"
                >

                    <span class="material-symbols-rounded">
                        speed
                    </span>

                </div>

            </div>

        </div>


        {{-- RESUMEN VISUAL --}}
        <div
            class="overflow-hidden rounded-2xl
                   border border-gray-200
                   bg-white shadow-sm"
        >

            <div
                class="border-b border-gray-100
                       bg-gray-50/60 px-5 py-4"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-10 w-10 items-center
                               justify-center rounded-xl
                               bg-blue-50 text-[#0056b3]"
                    >

                        <span class="material-symbols-rounded">
                            bar_chart
                        </span>

                    </div>

                    <div>

                        <h2
                            class="font-['Josefin_Sans']
                                   font-semibold text-[#003d82]"
                        >
                            Utilización general
                        </h2>

                        <p class="text-xs text-gray-500">
                            Comparativa entre unidades con movimiento
                            y unidades sin actividad.
                        </p>

                    </div>

                </div>

            </div>


            @if($totalVehiculos === 0)

                <div class="px-6 py-14 text-center">

                    <span
                        class="material-symbols-rounded
                               text-5xl text-gray-300"
                    >
                        query_stats
                    </span>

                    <h3
                        class="mt-3 font-['Josefin_Sans']
                               font-semibold text-gray-700"
                    >
                        No hay información disponible
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        No se encontraron vehículos asignados
                        a flotillas para los filtros seleccionados.
                    </p>

                </div>

            @else

                @php

                    $porcentajeActivos =
                        $totalVehiculos > 0
                            ? (
                                $totalActivos
                                /
                                $totalVehiculos
                            ) * 100
                            : 0;

                    $porcentajeInactivos =
                        $totalVehiculos > 0
                            ? (
                                $totalInactivos
                                /
                                $totalVehiculos
                            ) * 100
                            : 0;

                @endphp


                <div class="space-y-5 p-5">

                    {{-- ACTIVOS --}}
                    <div>

                        <div
                            class="mb-2 flex items-center
                                   justify-between text-sm"
                        >

                            <span class="font-medium text-gray-700">
                                Vehículos con actividad
                            </span>

                            <span class="font-semibold text-emerald-600">

                                {{ $totalActivos }}

                                ({{ number_format(
                                    $porcentajeActivos,
                                    2
                                ) }}%)

                            </span>

                        </div>


                        <div
                            class="h-3 overflow-hidden
                                   rounded-full bg-gray-100"
                        >

                            <div
                                class="h-full rounded-full
                                       bg-emerald-500"
                                style="width:
                                {{ min(
                                    100,
                                    $porcentajeActivos
                                ) }}%"
                            >
                            </div>

                        </div>

                    </div>


                    {{-- INACTIVOS --}}
                    <div>

                        <div
                            class="mb-2 flex items-center
                                   justify-between text-sm"
                        >

                            <span class="font-medium text-gray-700">
                                Vehículos sin actividad
                            </span>

                            <span class="font-semibold text-red-600">

                                {{ $totalInactivos }}

                                ({{ number_format(
                                    $porcentajeInactivos,
                                    2
                                ) }}%)

                            </span>

                        </div>


                        <div
                            class="h-3 overflow-hidden
                                   rounded-full bg-gray-100"
                        >

                            <div
                                class="h-full rounded-full
                                       bg-red-500"
                                style="width:
                                {{ min(
                                    100,
                                    $porcentajeInactivos
                                ) }}%"
                            >
                            </div>

                        </div>

                    </div>

                </div>

            @endif

        </div>


        {{-- TABLA --}}
        <div
            class="overflow-hidden rounded-2xl
                   border border-gray-200
                   bg-white shadow-sm"
        >

            <div
                class="border-b border-gray-100
                       bg-gray-50/60 px-5 py-4"
            >

                <div
                    class="flex flex-col gap-1
                           sm:flex-row sm:items-center
                           sm:justify-between"
                >

                    <div>

                        <h2
                            class="font-['Josefin_Sans']
                                   font-semibold text-[#003d82]"
                        >
                            Detalle de utilización
                        </h2>

                        <p class="text-xs text-gray-500">

                            {{
                                $flotillaSeleccionada?->nombre
                                ??
                                'Todas las flotillas disponibles'
                            }}

                            ·

                            {{ $totalRegistrosGps }}

                            registros GPS analizados.

                        </p>

                    </div>

                    <span class="text-xs text-gray-400">

                        {{ $totalRegistrosMovimiento }}

                        registros detectados en movimiento

                    </span>

                </div>

            </div>


            @if($resultados->isEmpty())

                <div class="px-6 py-14 text-center">

                    <span
                        class="material-symbols-rounded
                               text-5xl text-gray-300"
                    >
                        directions_car_off
                    </span>

                    <h3
                        class="mt-3 font-['Josefin_Sans']
                               font-semibold text-gray-700"
                    >
                        Sin vehículos para mostrar
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Modifica los filtros y vuelve a generar el reporte.
                    </p>

                </div>

            @else

                <div class="overflow-x-auto">

                    <table
                        class="min-w-full divide-y
                               divide-gray-200"
                    >

                        <thead class="bg-[#0056b3] text-white">

                            <tr>

                                <th
                                    class="px-4 py-3 text-left
                                           text-xs font-semibold uppercase"
                                >
                                    Flotilla
                                </th>

                                <th
                                    class="px-4 py-3 text-left
                                           text-xs font-semibold uppercase"
                                >
                                    Vehículo
                                </th>

                                <th
                                    class="px-4 py-3 text-left
                                           text-xs font-semibold uppercase"
                                >
                                    Placas
                                </th>

                                <th
                                    class="px-4 py-3 text-left
                                           text-xs font-semibold uppercase"
                                >
                                    Estado
                                </th>

                                <th
                                    class="px-4 py-3 text-center
                                           text-xs font-semibold uppercase"
                                >
                                    GPS
                                </th>

                                <th
                                    class="px-4 py-3 text-center
                                           text-xs font-semibold uppercase"
                                >
                                    Movimiento
                                </th>

                                <th
                                    class="px-4 py-3 text-left
                                           text-xs font-semibold uppercase"
                                >
                                    Actividad GPS
                                </th>

                                <th
                                    class="px-4 py-3 text-left
                                           text-xs font-semibold uppercase"
                                >
                                    Primer movimiento
                                </th>

                                <th
                                    class="px-4 py-3 text-left
                                           text-xs font-semibold uppercase"
                                >
                                    Último movimiento
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            class="divide-y divide-gray-100 bg-white"
                        >

                            @foreach($resultados as $resultado)

                                <tr class="hover:bg-gray-50/80">

                                    <td
                                        class="px-4 py-3
                                               text-sm text-gray-600"
                                    >
                                        {{
                                            $resultado['flotilla']
                                            ?:
                                            'Sin flotilla'
                                        }}
                                    </td>


                                    <td class="px-4 py-3">

                                        <p
                                            class="text-sm font-medium
                                                   text-gray-800"
                                        >
                                            {{ $resultado['vehiculo'] }}
                                        </p>

                                        <p class="text-xs text-gray-400">

                                            {{
                                                trim(
                                                    (
                                                        $resultado['marca']
                                                        ??
                                                        ''
                                                    )
                                                    .
                                                    ' '
                                                    .
                                                    (
                                                        $resultado['modelo']
                                                        ??
                                                        ''
                                                    )
                                                )
                                                ?:
                                                'Sin marca/modelo'
                                            }}

                                        </p>

                                    </td>


                                    <td
                                        class="px-4 py-3
                                               text-sm text-gray-600"
                                    >
                                        {{
                                            $resultado['placas']
                                            ?:
                                            'Sin placas'
                                        }}
                                    </td>


                                    <td class="px-4 py-3">

                                        @if($resultado['con_actividad'])

                                            <span
                                                class="inline-flex items-center
                                                       gap-1 rounded-full
                                                       bg-emerald-50
                                                       px-2.5 py-1
                                                       text-xs font-semibold
                                                       text-emerald-700"
                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[15px]"
                                                >
                                                    check_circle
                                                </span>

                                                Con actividad

                                            </span>

                                        @else

                                            <span
                                                class="inline-flex items-center
                                                       gap-1 rounded-full
                                                       bg-red-50
                                                       px-2.5 py-1
                                                       text-xs font-semibold
                                                       text-red-700"
                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[15px]"
                                                >
                                                    do_not_disturb_on
                                                </span>

                                                Sin actividad

                                            </span>

                                        @endif

                                    </td>


                                    <td
                                        class="px-4 py-3 text-center
                                               text-sm font-medium
                                               text-gray-700"
                                    >
                                        {{ $resultado['registros_gps'] }}
                                    </td>


                                    <td
                                        class="px-4 py-3 text-center
                                               text-sm font-medium
                                               text-gray-700"
                                    >
                                        {{
                                            $resultado[
                                                'registros_movimiento'
                                            ]
                                        }}
                                    </td>


                                    <td
                                        class="min-w-[180px]
                                               px-4 py-3"
                                    >

                                        <div
                                            class="mb-1 flex items-center
                                                   justify-between text-xs"
                                        >

                                            <span class="text-gray-500">
                                                Proporción
                                            </span>

                                            <span
                                                class="font-semibold
                                                       text-[#0056b3]"
                                            >

                                                {{
                                                    number_format(
                                                        $resultado[
                                                            'porcentaje_actividad'
                                                        ],
                                                        2
                                                    )
                                                }}%

                                            </span>

                                        </div>


                                        <div
                                            class="h-2 overflow-hidden
                                                   rounded-full bg-gray-100"
                                        >

                                            <div
                                                class="h-full rounded-full
                                                       bg-[#0056b3]"
                                                style="width:
                                                {{
                                                    min(
                                                        100,
                                                        $resultado[
                                                            'porcentaje_actividad'
                                                        ]
                                                    )
                                                }}%"
                                            >
                                            </div>

                                        </div>

                                    </td>


                                    <td
                                        class="whitespace-nowrap px-4 py-3
                                               text-sm text-gray-600"
                                    >

                                        {{
                                            $resultado['primer_movimiento']
                                                ? $resultado[
                                                    'primer_movimiento'
                                                ]->format(
                                                    'd/m/Y H:i'
                                                )
                                                : 'Sin movimiento'
                                        }}

                                    </td>


                                    <td
                                        class="whitespace-nowrap px-4 py-3
                                               text-sm text-gray-600"
                                    >

                                        {{
                                            $resultado['ultimo_movimiento']
                                                ? $resultado[
                                                    'ultimo_movimiento'
                                                ]->format(
                                                    'd/m/Y H:i'
                                                )
                                                : 'Sin movimiento'
                                        }}

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>

</x-app-layout>