<x-app-layout>

    <x-slot name="titulo">
        Reporte Histórico de Inspecciones Vehiculares
    </x-slot>

    <div class="mx-auto max-w-[1500px] space-y-5 pb-8 font-['Ubuntu']">

        {{-- ====================================================== --}}
        {{-- HEADER --}}
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
                        fact_check
                    </span>
                </div>

                <div>

                    <h1
                        class="font-['Josefin_Sans'] text-2xl
                               font-semibold text-white"
                    >
                        Reporte Histórico de Inspecciones Vehiculares
                    </h1>

                    <p class="text-sm text-blue-100">
                        Consulta las inspecciones realizadas y las incidencias
                        detectadas en los vehículos.
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
                    class="mt-2 ml-7 list-disc
                           space-y-1 text-sm text-red-600"
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
                        class="flex h-10 w-10 items-center
                               justify-center rounded-xl
                               bg-blue-50 text-[#0056b3]"
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
                            Filtra las inspecciones por periodo, empresa,
                            vehículo, conductor o resultado.
                        </p>

                    </div>

                </div>

            </div>


            <form
                action="{{ route('reportes.inspecciones') }}"
                method="GET"
                class="grid grid-cols-1 gap-4 p-5
                       md:grid-cols-2 xl:grid-cols-6"
            >

                {{-- FECHA INICIAL --}}

                <div>

                    <label
                        for="fecha_inicio"
                        class="mb-1.5 block text-xs font-semibold
                               uppercase text-gray-600"
                    >
                        Desde
                    </label>

                    <input
                        type="date"
                        id="fecha_inicio"
                        name="fecha_inicio"
                        value="{{ request('fecha_inicio', $fechaInicio->format('Y-m-d')) }}"
                        class="w-full rounded-xl border-gray-300 text-sm
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                </div>


                {{-- FECHA FINAL --}}

                <div>

                    <label
                        for="fecha_fin"
                        class="mb-1.5 block text-xs font-semibold
                               uppercase text-gray-600"
                    >
                        Hasta
                    </label>

                    <input
                        type="date"
                        id="fecha_fin"
                        name="fecha_fin"
                        value="{{ request('fecha_fin', $fechaFin->format('Y-m-d')) }}"
                        class="w-full rounded-xl border-gray-300 text-sm
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                </div>


                {{-- EMPRESA --}}

                <div>

                    <label
                        for="empresa_id"
                        class="mb-1.5 block text-xs font-semibold
                               uppercase text-gray-600"
                    >
                        Empresa
                    </label>

                    @if(auth()->user()->hasRole('Super Administrador'))

                        <select
                            id="empresa_id"
                            name="empresa_id"
                            class="w-full rounded-xl border-gray-300 text-sm
                                   focus:border-[#0056b3]
                                   focus:ring-[#0056b3]"
                        >

                            <option value="">
                                Todas
                            </option>

                            @foreach($empresas as $empresa)

                                <option
                                    value="{{ $empresa->id }}"
                                    @selected(request('empresa_id') == $empresa->id)
                                >
                                    {{ $empresa->nombre }}
                                </option>

                            @endforeach

                        </select>

                    @else

                        <div
                            class="flex h-[42px] items-center
                                   rounded-xl border border-gray-200
                                   bg-gray-50 px-3 text-sm text-gray-600"
                        >
                            {{ $empresas->first()?->nombre ?? 'Empresa' }}
                        </div>

                    @endif

                </div>


                {{-- VEHÍCULO --}}

                <div>

                    <label
                        for="vehiculo_id"
                        class="mb-1.5 block text-xs font-semibold
                               uppercase text-gray-600"
                    >
                        Vehículo
                    </label>

                    <select
                        id="vehiculo_id"
                        name="vehiculo_id"
                        class="w-full rounded-xl border-gray-300 text-sm
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                        <option value="">
                            Todos
                        </option>

                        @foreach($vehiculos as $vehiculo)

                            <option
                                value="{{ $vehiculo->id }}"
                                @selected(request('vehiculo_id') == $vehiculo->id)
                            >

                                {{ $vehiculo->nombre }}

                                @if($vehiculo->placas)
                                    — {{ $vehiculo->placas }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- CONDUCTOR --}}

                <div>

                    <label
                        for="conductor_id"
                        class="mb-1.5 block text-xs font-semibold
                               uppercase text-gray-600"
                    >
                        Conductor
                    </label>

                    <select
                        id="conductor_id"
                        name="conductor_id"
                        class="w-full rounded-xl border-gray-300 text-sm
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                        <option value="">
                            Todos
                        </option>

                        @foreach($conductores as $conductor)

                            <option
                                value="{{ $conductor->id }}"
                                @selected(request('conductor_id') == $conductor->id)
                            >
                                {{
                                    trim(
                                        $conductor->nombre
                                        . ' '
                                        . ($conductor->apellido_paterno ?? '')
                                        . ' '
                                        . ($conductor->apellido_materno ?? '')
                                    )
                                }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- ESTADO --}}

                <div>

                    <label
                        for="estado"
                        class="mb-1.5 block text-xs font-semibold
                               uppercase text-gray-600"
                    >
                        Estado
                    </label>

                    <select
                        id="estado"
                        name="estado"
                        class="w-full rounded-xl border-gray-300 text-sm
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                        <option value="">
                            Todos
                        </option>

                        <option
                            value="correcto"
                            @selected(request('estado') === 'correcto')
                        >
                            Correcta
                        </option>

                        <option
                            value="con_incidencias"
                            @selected(request('estado') === 'con_incidencias')
                        >
                            Con incidencias
                        </option>

                    </select>

                </div>


                {{-- ACCIONES --}}

                <div
                    class="flex flex-wrap items-end gap-2
                        md:col-span-2 xl:col-span-6"
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
                        href="{{ route('reportes.inspecciones') }}"
                        class="inline-flex items-center gap-2
                            rounded-xl border border-gray-300
                            bg-white px-4 py-2.5
                            text-sm font-medium text-gray-600
                            transition hover:bg-gray-50"
                    >

                        <span class="material-symbols-rounded text-[18px]">
                            restart_alt
                        </span>

                        Limpiar filtros

                    </a>


                    <a
                        href="{{ route('reportes.inspecciones.pdf', request()->query()) }}"
                        class="inline-flex items-center gap-2
                            rounded-xl bg-red-600
                            px-4 py-2.5
                            text-sm font-medium text-white
                            transition hover:bg-red-700"
                    >

                        <span class="material-symbols-rounded text-[18px]">
                            picture_as_pdf
                        </span>

                        Exportar PDF

                    </a>


                    <a
                        href="{{ route('reportes.inspecciones.excel', request()->query()) }}"
                        class="inline-flex items-center gap-2
                            rounded-xl bg-emerald-600
                            px-4 py-2.5
                            text-sm font-medium text-white
                            transition hover:bg-emerald-700"
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
        {{-- KPIs --}}
        {{-- ====================================================== --}}

        <div
            class="grid grid-cols-1 gap-4
                   sm:grid-cols-2 xl:grid-cols-4"
        >

            {{-- TOTAL --}}

            <div
                class="flex items-center justify-between
                       rounded-2xl border border-gray-200
                       bg-white p-5 shadow-sm"
            >

                <div>

                    <p class="text-sm text-gray-500">
                        Inspecciones registradas
                    </p>

                    <p
                        class="mt-1 font-['Josefin_Sans']
                               text-3xl font-bold text-[#0056b3]"
                    >
                        {{ $totalInspecciones }}
                    </p>

                </div>

                <div
                    class="flex h-12 w-12 items-center
                           justify-center rounded-xl
                           bg-blue-50 text-[#0056b3]"
                >
                    <span class="material-symbols-rounded">
                        checklist
                    </span>
                </div>

            </div>


            {{-- CORRECTAS --}}

            <div
                class="flex items-center justify-between
                       rounded-2xl border border-gray-200
                       bg-white p-5 shadow-sm"
            >

                <div>

                    <p class="text-sm text-gray-500">
                        Correctas
                    </p>

                    <p
                        class="mt-1 font-['Josefin_Sans']
                               text-3xl font-bold text-emerald-600"
                    >
                        {{ $totalCorrectas }}
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


            {{-- CON INCIDENCIAS --}}

            <div
                class="flex items-center justify-between
                       rounded-2xl border border-gray-200
                       bg-white p-5 shadow-sm"
            >

                <div>

                    <p class="text-sm text-gray-500">
                        Con incidencias
                    </p>

                    <p
                        class="mt-1 font-['Josefin_Sans']
                               text-3xl font-bold text-red-600"
                    >
                        {{ $totalConIncidencias }}
                    </p>

                </div>

                <div
                    class="flex h-12 w-12 items-center
                           justify-center rounded-xl
                           bg-red-50 text-red-600"
                >
                    <span class="material-symbols-rounded">
                        warning
                    </span>
                </div>

            </div>


            {{-- VEHÍCULOS --}}

            <div
                class="flex items-center justify-between
                       rounded-2xl border border-gray-200
                       bg-white p-5 shadow-sm"
            >

                <div>

                    <p class="text-sm text-gray-500">
                        Vehículos inspeccionados
                    </p>

                    <p
                        class="mt-1 font-['Josefin_Sans']
                               text-3xl font-bold text-amber-600"
                    >
                        {{ $vehiculosInspeccionados }}
                    </p>

                </div>

                <div
                    class="flex h-12 w-12 items-center
                           justify-center rounded-xl
                           bg-amber-50 text-amber-600"
                >
                    <span class="material-symbols-rounded">
                        directions_car
                    </span>
                </div>

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- INFORMACIÓN ADICIONAL --}}
        {{-- ====================================================== --}}

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

            <div
                class="rounded-2xl border border-gray-200
                       bg-white px-5 py-4 shadow-sm"
            >

                <p
                    class="text-xs font-semibold
                           uppercase text-gray-400"
                >
                    Incidencias detectadas
                </p>

                <p
                    class="mt-1 text-xl font-bold
                           text-red-600"
                >
                    {{ $totalIncidencias }}
                </p>

            </div>


            <div
                class="rounded-2xl border border-gray-200
                       bg-white px-5 py-4 shadow-sm"
            >

                <p
                    class="text-xs font-semibold
                           uppercase text-gray-400"
                >
                    Vehículo con más incidencias
                </p>

                <p
                    class="mt-1 font-['Josefin_Sans']
                           text-lg font-semibold text-[#0056b3]"
                >
                    {{ $vehiculoMasIncidencias['vehiculo'] ?? 'Sin información' }}
                </p>

                @if($vehiculoMasIncidencias && $vehiculoMasIncidencias['incidencias'] > 0)

                    <p class="mt-1 text-xs text-gray-400">
                        {{ $vehiculoMasIncidencias['incidencias'] }}
                        incidencia(s)
                    </p>

                @endif

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- GRÁFICA DE RESULTADOS --}}
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
                            bar_chart
                        </span>
                    </div>

                    <div>

                        <h2
                            class="font-['Josefin_Sans']
                                   font-semibold text-[#003d82]"
                        >
                            Resultado de inspecciones
                        </h2>

                        <p class="text-xs text-gray-500">
                            Comparativa entre inspecciones correctas
                            y aquellas que presentan incidencias.
                        </p>

                    </div>

                </div>

            </div>


            <div class="space-y-6 p-5">

                @foreach($graficaResultados as $item)

                    <div>

                        <div
                            class="mb-2 flex items-center
                                   justify-between gap-4"
                        >

                            <div>

                                <p class="text-sm font-medium text-gray-700">
                                    {{ $item['nombre'] }}
                                </p>

                                <p class="text-xs text-gray-400">
                                    {{ $item['cantidad'] }}
                                    inspección(es)
                                </p>

                            </div>

                            <div
                                class="text-sm font-semibold
                                       text-[#0056b3]"
                            >
                                {{ $item['porcentaje'] }}%
                            </div>

                        </div>


                        <div
                            class="h-3 overflow-hidden
                                   rounded-full bg-gray-100"
                        >

                            @if($item['porcentaje'] > 0)

                                <div
                                    class="h-full rounded-full {{ $item['nombre'] === 'Correctas' ? 'bg-emerald-500' : 'bg-red-500' }}"
                                    style="width: {{ $item['porcentaje'] }}%;"
                                ></div>

                            @endif

                        </div>

                    </div>

                @endforeach

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- INCIDENCIAS POR VEHÍCULO --}}
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
                               bg-red-50 text-red-600"
                    >
                        <span class="material-symbols-rounded">
                            car_crash
                        </span>
                    </div>

                    <div>

                        <h2
                            class="font-['Josefin_Sans']
                                   font-semibold text-[#003d82]"
                        >
                            Incidencias por vehículo
                        </h2>

                        <p class="text-xs text-gray-500">
                            Cantidad de incidencias encontradas
                            en las inspecciones de cada unidad.
                        </p>

                    </div>

                </div>

            </div>


            @if($graficaVehiculos->isEmpty())

                <div
                    class="px-6 py-12 text-center
                           text-sm text-gray-500"
                >
                    No existen inspecciones para los filtros seleccionados.
                </div>

            @else

                @php
                    $maximoIncidencias = max(
                        1,
                        (int) $graficaVehiculos->max('incidencias')
                    );
                @endphp

                <div class="space-y-5 p-5">

                    @foreach($graficaVehiculos as $item)

                        @php
                            $porcentaje = $item['incidencias'] > 0
                                ? max(
                                    3,
                                    min(
                                        100,
                                        ($item['incidencias'] / $maximoIncidencias) * 100
                                    )
                                )
                                : 0;
                        @endphp

                        <div>

                            <div
                                class="mb-2 flex items-center
                                       justify-between gap-4"
                            >

                                <div>

                                    <p class="text-sm font-medium text-gray-700">
                                        {{ $item['vehiculo'] }}
                                    </p>

                                    <p class="text-xs text-gray-400">

                                        {{ $item['placas'] ?: 'Sin placas' }}

                                        ·

                                        {{ $item['inspecciones'] }}
                                        inspección(es)

                                    </p>

                                </div>

                                <span
                                    class="text-sm font-semibold
                                           text-red-600"
                                >
                                    {{ $item['incidencias'] }}
                                    incidencia(s)
                                </span>

                            </div>


                            <div
                                class="h-3 overflow-hidden
                                       rounded-full bg-gray-100"
                            >

                                @if($item['incidencias'] > 0)

                                    <div
                                        class="h-full rounded-full bg-red-500"
                                        style="width: {{ $porcentaje }}%;"
                                    ></div>

                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>


        {{-- ====================================================== --}}
        {{-- HISTORIAL DE INSPECCIONES --}}
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
                            history
                        </span>
                    </div>

                    <div>

                        <h2
                            class="font-['Josefin_Sans']
                                   font-semibold text-[#003d82]"
                        >
                            Historial de inspecciones
                        </h2>

                        <p class="text-xs text-gray-500">
                            Inspecciones realizadas y detalles
                            de las incidencias detectadas.
                        </p>

                    </div>

                </div>

            </div>


            @if($inspecciones->isEmpty())

                <div class="px-6 py-14 text-center">

                    <div
                        class="mx-auto flex h-14 w-14
                               items-center justify-center
                               rounded-2xl bg-gray-100 text-gray-400"
                    >
                        <span class="material-symbols-rounded">
                            fact_check
                        </span>
                    </div>

                    <h3
                        class="mt-3 font-['Josefin_Sans']
                               font-semibold text-gray-700"
                    >
                        Sin inspecciones registradas
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        No se encontraron inspecciones
                        para los criterios seleccionados.
                    </p>

                </div>

            @else

                <div class="overflow-x-auto">

                    <table
                        class="min-w-full
                               divide-y divide-gray-200"
                    >

                        <thead class="bg-gray-50">

                            <tr>

                                <th
                                    class="px-5 py-3 text-left
                                           text-xs font-semibold
                                           uppercase text-gray-500"
                                >
                                    Fecha
                                </th>

                                <th
                                    class="px-5 py-3 text-left
                                           text-xs font-semibold
                                           uppercase text-gray-500"
                                >
                                    Vehículo
                                </th>

                                <th
                                    class="px-5 py-3 text-left
                                           text-xs font-semibold
                                           uppercase text-gray-500"
                                >
                                    Conductor
                                </th>

                                <th
                                    class="px-5 py-3 text-left
                                           text-xs font-semibold
                                           uppercase text-gray-500"
                                >
                                    Resultado
                                </th>

                                <th
                                    class="px-5 py-3 text-left
                                           text-xs font-semibold
                                           uppercase text-gray-500"
                                >
                                    Incidencias
                                </th>

                                <th
                                    class="px-5 py-3 text-left
                                           text-xs font-semibold
                                           uppercase text-gray-500"
                                >
                                    Observaciones
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            class="divide-y divide-gray-100 bg-white"
                        >

                            @foreach($inspecciones as $inspeccion)

                                <tr
                                    class="align-top transition
                                           hover:bg-blue-50/30"
                                >

                                    {{-- FECHA --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4
                                               text-sm text-gray-600"
                                    >
                                        {{ $inspeccion['fecha']->format('d/m/Y H:i') }}
                                    </td>


                                    {{-- VEHÍCULO --}}

                                    <td class="px-5 py-4">

                                        <p
                                            class="font-['Josefin_Sans']
                                                   font-semibold text-gray-800"
                                        >
                                            {{ $inspeccion['vehiculo'] }}
                                        </p>

                                        <p
                                            class="mt-0.5 text-xs
                                                   text-gray-400"
                                        >
                                            {{ $inspeccion['placas'] ?: 'Sin placas' }}
                                        </p>

                                    </td>


                                    {{-- CONDUCTOR --}}

                                    <td
                                        class="px-5 py-4
                                               text-sm text-gray-600"
                                    >
                                        {{ $inspeccion['conductor'] }}
                                    </td>


                                    {{-- RESULTADO --}}

                                    <td class="px-5 py-4">

                                        @if($inspeccion['resultado'] === 'correcto')

                                            <span
                                                class="inline-flex items-center gap-1
                                                       rounded-full bg-emerald-50
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

                                                Correcta

                                            </span>

                                        @else

                                            <span
                                                class="inline-flex items-center gap-1
                                                       rounded-full bg-red-50
                                                       px-2.5 py-1
                                                       text-xs font-semibold
                                                       text-red-700"
                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[15px]"
                                                >
                                                    warning
                                                </span>

                                                Con incidencias

                                            </span>

                                        @endif

                                    </td>


                                    {{-- INCIDENCIAS --}}

                                    <td
                                        class="min-w-[250px]
                                               px-5 py-4"
                                    >

                                        @if($inspeccion['incidencias']->isEmpty())

                                            <span
                                                class="text-sm
                                                       text-emerald-600"
                                            >
                                                Sin incidencias
                                            </span>

                                        @else

                                            <div class="space-y-2">

                                                @foreach($inspeccion['incidencias'] as $incidencia)

                                                    <div
                                                        class="rounded-xl
                                                               border border-red-100
                                                               bg-red-50
                                                               px-3 py-2"
                                                    >

                                                        <p
                                                            class="text-xs
                                                                   font-semibold
                                                                   text-red-700"
                                                        >
                                                            {{ $incidencia['elemento'] }}
                                                        </p>

                                                        @if($incidencia['observaciones'])

                                                            <p
                                                                class="mt-1
                                                                       text-xs
                                                                       text-red-600"
                                                            >
                                                                {{ $incidencia['observaciones'] }}
                                                            </p>

                                                        @endif

                                                    </div>

                                                @endforeach

                                            </div>

                                        @endif

                                    </td>


                                    {{-- OBSERVACIONES --}}

                                    <td
                                        class="max-w-[280px]
                                               px-5 py-4
                                               text-sm text-gray-600"
                                    >
                                        {{ $inspeccion['observaciones'] ?: 'Sin observaciones' }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>


        {{-- ====================================================== --}}
        {{-- NOTA --}}
        {{-- ====================================================== --}}

        <div
            class="rounded-2xl
                   border border-gray-200
                   bg-white px-5 py-4
                   text-xs text-gray-500
                   shadow-sm"
        >

            <span class="font-semibold text-gray-600">
                Nota:
            </span>

            Una inspección se considera con incidencias cuando al menos uno
            de los elementos revisados presenta un resultado que requiere
            atención.

        </div>

    </div>

</x-app-layout>