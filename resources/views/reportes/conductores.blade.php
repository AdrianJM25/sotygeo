<x-app-layout>

    <x-slot name="titulo">
        Reporte de Actividad por Conductor
    </x-slot>

    <div class="mx-auto max-w-[1500px] space-y-5 pb-8 font-['Ubuntu']">

        {{-- ====================================================== --}}
        {{-- ENCABEZADO --}}
        {{-- ====================================================== --}}

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
                        badge
                    </span>
                </div>

                <div>

                    <h1
                        class="font-['Josefin_Sans'] text-2xl
                               font-semibold text-white"
                    >
                        Reporte de Actividad por Conductor
                    </h1>

                    <p class="text-sm text-blue-100">
                        Consulta los vehículos utilizados y recorridos asociados
                        a cada conductor.
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


        {{-- ====================================================== --}}
        {{-- ERRORES --}}
        {{-- ====================================================== --}}

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


        {{-- ====================================================== --}}
        {{-- FILTROS --}}
        {{-- ====================================================== --}}

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
                            Selecciona un conductor y el periodo de actividad.
                        </p>

                    </div>

                </div>

            </div>


            <form
                action="{{ route('reportes.conductores') }}"
                method="GET"
                class="grid grid-cols-1 gap-4 p-5
                       md:grid-cols-2 xl:grid-cols-4"
            >

                {{-- FECHA INICIAL --}}
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


                {{-- FECHA FINAL --}}
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


                {{-- CONDUCTOR --}}
                <div>

                    <label
                        for="conductor_id"
                        class="mb-1.5 block text-xs
                               font-semibold uppercase text-gray-600"
                    >
                        Conductor
                    </label>

                    <select
                        id="conductor_id"
                        name="conductor_id"
                        class="w-full rounded-xl border-gray-300
                               text-sm focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                        <option value="">
                            Todos
                        </option>

                        @foreach($conductores as $conductor)

                            @php

                                $nombreConductor =
                                    trim(
                                        $conductor->nombre
                                        . ' '
                                        . ($conductor->apellido_paterno ?? '')
                                        . ' '
                                        . ($conductor->apellido_materno ?? '')
                                    );

                            @endphp

                            <option
                                value="{{ $conductor->id }}"
                                @selected(
                                    request('conductor_id')
                                    ==
                                    $conductor->id
                                )
                            >

                                {{ $nombreConductor }}

                                @if($conductor->vehiculo)

                                    — {{ $conductor->vehiculo->nombre }}

                                @endif

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
                        href="{{ route('reportes.conductores') }}"
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
                            'reportes.conductores.pdf',
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
                            'reportes.conductores.excel',
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


        {{-- ====================================================== --}}
        {{-- INDICADORES --}}
        {{-- ====================================================== --}}

        <div
            class="grid grid-cols-1 gap-4
                   sm:grid-cols-2 xl:grid-cols-5"
        >

            {{-- CONDUCTORES --}}
            <div
                class="flex items-center justify-between
                       rounded-2xl border border-gray-200
                       bg-white p-5 shadow-sm"
            >

                <div>

                    <p class="text-sm text-gray-500">
                        Conductores
                    </p>

                    <p
                        class="mt-1 font-['Josefin_Sans']
                               text-3xl font-bold text-[#0056b3]"
                    >
                        {{ $totalConductores }}
                    </p>

                </div>

                <div
                    class="flex h-12 w-12 items-center
                           justify-center rounded-xl
                           bg-blue-50 text-[#0056b3]"
                >
                    <span class="material-symbols-rounded">
                        badge
                    </span>
                </div>

            </div>


            {{-- CON ACTIVIDAD --}}
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
                        {{ $conductoresConActividad }}
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


            {{-- SIN ACTIVIDAD --}}
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
                        {{ $conductoresSinActividad }}
                    </p>

                </div>

                <div
                    class="flex h-12 w-12 items-center
                           justify-center rounded-xl
                           bg-red-50 text-red-600"
                >
                    <span class="material-symbols-rounded">
                        person_off
                    </span>
                </div>

            </div>


            {{-- RECORRIDOS --}}
            <div
                class="flex items-center justify-between
                       rounded-2xl border border-gray-200
                       bg-white p-5 shadow-sm"
            >

                <div>

                    <p class="text-sm text-gray-500">
                        Recorridos
                    </p>

                    <p
                        class="mt-1 font-['Josefin_Sans']
                               text-3xl font-bold text-[#0056b3]"
                    >
                        {{ $totalRecorridos }}
                    </p>

                </div>

                <div
                    class="flex h-12 w-12 items-center
                           justify-center rounded-xl
                           bg-blue-50 text-[#0056b3]"
                >
                    <span class="material-symbols-rounded">
                        route
                    </span>
                </div>

            </div>


            {{-- DISTANCIA --}}
            <div
                class="flex items-center justify-between
                       rounded-2xl border border-gray-200
                       bg-white p-5 shadow-sm"
            >

                <div>

                    <p class="text-sm text-gray-500">
                        Distancia total
                    </p>

                    <p
                        class="mt-1 font-['Josefin_Sans']
                               text-2xl font-bold text-[#0056b3]"
                    >
                        {{ number_format($distanciaTotal, 2) }}
                        <span class="text-sm">
                            km
                        </span>
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


        {{-- ====================================================== --}}
        {{-- RESUMEN POR CONDUCTOR --}}
        {{-- ====================================================== --}}

        <div
            class="overflow-hidden rounded-2xl
                   border border-gray-200 bg-white shadow-sm"
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
                            monitoring
                        </span>
                    </div>

                    <div>

                        <h2
                            class="font-['Josefin_Sans']
                                   font-semibold text-[#003d82]"
                        >
                            Resumen de actividad
                        </h2>

                        <p class="text-xs text-gray-500">
                            Actividad registrada por cada conductor.
                        </p>

                    </div>

                </div>

            </div>


            @if($resumenConductores->isEmpty())

                <div class="px-6 py-14 text-center">

                    <span
                        class="material-symbols-rounded
                               text-5xl text-gray-300"
                    >
                        person_search
                    </span>

                    <h3
                        class="mt-3 font-['Josefin_Sans']
                               font-semibold text-gray-700"
                    >
                        No hay conductores para mostrar
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Modifica los filtros y vuelve a generar el reporte.
                    </p>

                </div>

            @else

                <div class="grid grid-cols-1 gap-3 p-5 lg:grid-cols-2">

                    @foreach($resumenConductores as $resumen)

                        <div
                            class="flex items-center justify-between
                                   rounded-xl border border-gray-200
                                   bg-gray-50/50 p-4"
                        >

                            <div>

                                <p
                                    class="font-medium text-gray-800"
                                >
                                    {{ $resumen['conductor'] }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $resumen['vehiculo'] }}
                                </p>

                            </div>


                            <div class="text-right">

                                @if($resumen['tiene_actividad'])

                                    <span
                                        class="inline-flex rounded-full
                                               bg-emerald-50 px-2.5 py-1
                                               text-xs font-semibold
                                               text-emerald-700"
                                    >
                                        Con actividad
                                    </span>

                                @else

                                    <span
                                        class="inline-flex rounded-full
                                               bg-red-50 px-2.5 py-1
                                               text-xs font-semibold
                                               text-red-700"
                                    >
                                        Sin actividad
                                    </span>

                                @endif

                                <p class="mt-2 text-xs text-gray-500">

                                    {{ $resumen['recorridos'] }}
                                    recorrido(s)

                                    ·

                                    {{
                                        number_format(
                                            $resumen['distancia_km'],
                                            2
                                        )
                                    }}
                                    km

                                </p>

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>


        {{-- ====================================================== --}}
        {{-- DETALLE DE RECORRIDOS --}}
        {{-- ====================================================== --}}

        <div
            class="overflow-hidden rounded-2xl
                   border border-gray-200 bg-white shadow-sm"
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
                            Detalle de actividad
                        </h2>

                        <p class="text-xs text-gray-500">

                            @if($conductorSeleccionado)

                                {{
                                    trim(
                                        $conductorSeleccionado->nombre
                                        . ' '
                                        . (
                                            $conductorSeleccionado
                                                ->apellido_paterno
                                            ??
                                            ''
                                        )
                                        . ' '
                                        . (
                                            $conductorSeleccionado
                                                ->apellido_materno
                                            ??
                                            ''
                                        )
                                    )
                                }}

                            @else

                                Todos los conductores disponibles

                            @endif

                        </p>

                    </div>


                    <span class="text-xs text-gray-400">

                        {{ $vehiculosUtilizados }}

                        vehículo(s) utilizado(s)

                    </span>

                </div>

            </div>


            @if($resultados->isEmpty())

                <div class="px-6 py-14 text-center">

                    <span
                        class="material-symbols-rounded
                               text-5xl text-gray-300"
                    >
                        route
                    </span>

                    <h3
                        class="mt-3 font-['Josefin_Sans']
                               font-semibold text-gray-700"
                    >
                        Sin actividad registrada
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        No se encontraron datos para el periodo seleccionado.
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
                                    Conductor
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
                                    Inicio
                                </th>

                                <th
                                    class="px-4 py-3 text-left
                                           text-xs font-semibold uppercase"
                                >
                                    Fin
                                </th>

                                <th
                                    class="px-4 py-3 text-left
                                           text-xs font-semibold uppercase"
                                >
                                    Estado
                                </th>

                                <th
                                    class="px-4 py-3 text-right
                                           text-xs font-semibold uppercase"
                                >
                                    Distancia
                                </th>

                                <th
                                    class="px-4 py-3 text-left
                                           text-xs font-semibold uppercase"
                                >
                                    Duración
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            class="divide-y divide-gray-100 bg-white"
                        >

                            @foreach($resultados as $resultado)

                                <tr class="hover:bg-gray-50/80">

                                    {{-- CONDUCTOR --}}
                                    <td class="px-4 py-3">

                                        <p
                                            class="text-sm font-medium
                                                   text-gray-800"
                                        >
                                            {{ $resultado['conductor'] }}
                                        </p>

                                        <p class="text-xs text-gray-400">
                                            {{ $resultado['empresa'] }}
                                        </p>

                                    </td>


                                    {{-- VEHÍCULO --}}
                                    <td
                                        class="px-4 py-3
                                               text-sm text-gray-700"
                                    >
                                        {{ $resultado['vehiculo'] }}
                                    </td>


                                    {{-- PLACAS --}}
                                    <td
                                        class="whitespace-nowrap
                                               px-4 py-3 text-sm
                                               text-gray-600"
                                    >
                                        {{ $resultado['placas'] }}
                                    </td>


                                    {{-- INICIO --}}
                                    <td
                                        class="whitespace-nowrap
                                               px-4 py-3 text-sm
                                               text-gray-600"
                                    >

                                        {{
                                            $resultado['fecha_inicio']
                                                ? $resultado[
                                                    'fecha_inicio'
                                                ]->format(
                                                    'd/m/Y H:i'
                                                )
                                                : '—'
                                        }}

                                    </td>


                                    {{-- FIN --}}
                                    <td
                                        class="whitespace-nowrap
                                               px-4 py-3 text-sm
                                               text-gray-600"
                                    >

                                        @if($resultado['fecha_fin'])

                                            {{
                                                $resultado[
                                                    'fecha_fin'
                                                ]->format(
                                                    'd/m/Y H:i'
                                                )
                                            }}

                                        @elseif(
                                            $resultado['estado']
                                            ===
                                            'En curso'
                                        )

                                            En curso

                                        @else

                                            —

                                        @endif

                                    </td>


                                    {{-- ESTADO --}}
                                    <td class="px-4 py-3">

                                        @if(
                                            $resultado['estado']
                                            ===
                                            'Finalizado'
                                        )

                                            <span
                                                class="inline-flex rounded-full
                                                       bg-emerald-50 px-2.5 py-1
                                                       text-xs font-semibold
                                                       text-emerald-700"
                                            >
                                                Finalizado
                                            </span>

                                        @elseif(
                                            $resultado['estado']
                                            ===
                                            'En curso'
                                        )

                                            <span
                                                class="inline-flex rounded-full
                                                       bg-blue-50 px-2.5 py-1
                                                       text-xs font-semibold
                                                       text-blue-700"
                                            >
                                                En curso
                                            </span>

                                        @else

                                            <span
                                                class="inline-flex rounded-full
                                                       bg-red-50 px-2.5 py-1
                                                       text-xs font-semibold
                                                       text-red-700"
                                            >
                                                {{ $resultado['estado'] }}
                                            </span>

                                        @endif

                                    </td>


                                    {{-- DISTANCIA --}}
                                    <td
                                        class="whitespace-nowrap
                                               px-4 py-3 text-right
                                               text-sm font-medium
                                               text-gray-700"
                                    >

                                        {{
                                            number_format(
                                                $resultado[
                                                    'distancia_km'
                                                ],
                                                2
                                            )
                                        }}
                                        km

                                    </td>


                                    {{-- DURACIÓN --}}
                                    <td
                                        class="whitespace-nowrap
                                               px-4 py-3 text-sm
                                               text-gray-600"
                                    >
                                        {{ $resultado['duracion'] }}
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