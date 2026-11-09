<x-app-layout>

    <x-slot name="titulo">
        Reporte de Visitas y Tiempos en Zonas
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
                        distance
                    </span>

                </div>


                <div>

                    <h1
                        class="font-['Josefin_Sans']
                               text-2xl font-semibold
                               text-white"
                    >
                        Reporte de Visitas y Tiempos en Zonas
                    </h1>

                    <p
                        class="text-sm
                               text-blue-100"
                    >
                        Consulta las entradas, salidas y tiempos de permanencia
                        de los vehículos en las zonas registradas.
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

                <span>—</span>

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
                            Selecciona el periodo, zona y vehículo que deseas analizar.
                        </p>

                    </div>

                </div>

            </div>


            <form
                action="{{ route('reportes.zonas') }}"
                method="GET"

                class="grid grid-cols-1
                       gap-4 p-5
                       md:grid-cols-2
                       xl:grid-cols-6"
            >

                {{-- ================================================== --}}
                {{-- FECHA INICIAL --}}
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
                {{-- FECHA FINAL --}}
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
                {{-- ZONA --}}
                {{-- ================================================== --}}

                <div>

                    <label
                        for="zona_id"

                        class="mb-1.5 block
                               text-xs font-semibold
                               uppercase
                               text-gray-600"
                    >
                        Zona
                    </label>

                    <select
                        id="zona_id"
                        name="zona_id"

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


                        @foreach($zonas as $zona)

                            <option
                                value="{{ $zona->id }}"

                                @selected(
                                    request('zona_id')
                                    ==
                                    $zona->id
                                )
                            >
                                {{ $zona->nombre }}
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
                        xl:col-span-6"
                >

                    {{-- GENERAR REPORTE --}}

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


                    {{-- LIMPIAR FILTROS --}}

                    <a
                        href="{{ route('reportes.zonas') }}"

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


                    {{-- EXPORTAR PDF --}}

                    <a
                        href="{{ route(
                            'reportes.zonas.pdf',
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


                    {{-- EXPORTAR EXCEL --}}

                    <a
                        href="{{ route(
                            'reportes.zonas.excel',
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

            {{-- VISITAS --}}

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
                        Visitas registradas
                    </p>

                    <p
                        class="mt-1
                               font-['Josefin_Sans']
                               text-3xl font-bold
                               text-[#0056b3]"
                    >
                        {{ $totalVisitas }}
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
                        pin_drop
                    </span>

                </div>

            </div>


            {{-- TIEMPO TOTAL --}}

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
                        Tiempo total en zonas
                    </p>

                    <p
                        class="mt-1
                               font-['Josefin_Sans']
                               text-2xl font-bold
                               text-[#0056b3]"
                    >
                        {{ $tiempoTotalFormateado }}
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
                        timer
                    </span>

                </div>

            </div>


            {{-- PROMEDIO --}}

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
                        Promedio de permanencia
                    </p>

                    <p
                        class="mt-1
                               font-['Josefin_Sans']
                               text-2xl font-bold
                               text-emerald-600"
                    >
                        {{ $promedioFormateado }}
                    </p>

                </div>


                <div
                    class="flex h-12 w-12
                           items-center justify-center
                           rounded-xl
                           bg-emerald-50
                           text-emerald-600"
                >

                    <span class="material-symbols-rounded">
                        schedule
                    </span>

                </div>

            </div>


            {{-- ZONAS VISITADAS --}}

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
                        Zonas visitadas
                    </p>

                    <p
                        class="mt-1
                               font-['Josefin_Sans']
                               text-3xl font-bold
                               text-amber-600"
                    >
                        {{ $zonasVisitadas }}
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
                        location_on
                    </span>

                </div>

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- INFORMACIÓN ADICIONAL --}}
        {{-- ====================================================== --}}

        <div
            class="grid grid-cols-1
                   gap-4
                   md:grid-cols-3"
        >

            <div
                class="rounded-2xl
                       border border-gray-200
                       bg-white
                       px-5 py-4
                       shadow-sm"
            >

                <p
                    class="text-xs font-semibold
                           uppercase
                           text-gray-400"
                >
                    Visitas finalizadas
                </p>

                <p
                    class="mt-1
                           text-xl font-bold
                           text-gray-700"
                >
                    {{ $visitasFinalizadas }}
                </p>

            </div>


            <div
                class="rounded-2xl
                       border border-gray-200
                       bg-white
                       px-5 py-4
                       shadow-sm"
            >

                <p
                    class="text-xs font-semibold
                           uppercase
                           text-gray-400"
                >
                    Vehículos actualmente en zona
                </p>

                <p
                    class="mt-1
                           text-xl font-bold
                           text-emerald-600"
                >
                    {{ $visitasActivas }}
                </p>

            </div>


            <div
                class="rounded-2xl
                       border border-gray-200
                       bg-white
                       px-5 py-4
                       shadow-sm"
            >

                <p
                    class="text-xs font-semibold
                           uppercase
                           text-gray-400"
                >
                    Zona con más visitas
                </p>

                <p
                    class="mt-1
                           font-['Josefin_Sans']
                           text-lg font-semibold
                           text-[#0056b3]"
                >

                    {{
                        $zonaMasVisitada['zona']
                        ??
                        'Sin información'
                    }}

                </p>


                @if($zonaMasVisitada)

                    <p
                        class="mt-1
                               text-xs
                               text-gray-400"
                    >
                        {{ $zonaMasVisitada['visitas'] }}
                        visita(s)
                    </p>

                @endif

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- GRÁFICA POR ZONA --}}
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
                            Permanencia por zona
                        </h2>

                        <p
                            class="text-xs
                                   text-gray-500"
                        >
                            Tiempo acumulado de permanencia y número de visitas por zona.
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
                        Sin información disponible
                    </h3>

                    <p
                        class="mt-1
                               text-sm
                               text-gray-500"
                    >
                        No existen visitas para los filtros seleccionados.
                    </p>

                </div>

            @else

                @php

                    $maximoMinutos =
                        max(
                            1,
                            (int)
                            $grafica->max('minutos')
                        );

                @endphp


                <div class="space-y-6 p-5">

                    @foreach($grafica as $item)

                        @php

                            $porcentaje =
                                $item['minutos'] > 0

                                    ?

                                    max(
                                        3,
                                        min(
                                            100,
                                            (
                                                $item['minutos']
                                                /
                                                $maximoMinutos
                                            )
                                            *
                                            100
                                        )
                                    )

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

                                <div
                                    class="flex items-center
                                           gap-2"
                                >

                                    <span
                                        class="h-3 w-3
                                               rounded-full"

                                        style="
                                            background-color:
                                            {{ $item['color'] }};
                                        "
                                    ></span>


                                    <div>

                                        <p
                                            class="text-sm
                                                   font-medium
                                                   text-gray-700"
                                        >
                                            {{ $item['zona'] }}
                                        </p>

                                        <p
                                            class="text-xs
                                                   text-gray-400"
                                        >
                                            {{ $item['visitas'] }}
                                            visita(s)
                                        </p>

                                    </div>

                                </div>


                                <div
                                    class="text-right
                                           text-sm font-semibold
                                           text-[#0056b3]"
                                >
                                    {{ $item['tiempo'] }}
                                </div>

                            </div>


                            <div
                                class="h-3
                                       overflow-hidden
                                       rounded-full
                                       bg-gray-100"
                            >

                                @if($item['minutos'] > 0)

                                    <div
                                        class="h-full
                                               rounded-full
                                               bg-[#0056b3]"

                                        style="
                                            width:
                                            {{ $porcentaje }}%;
                                        "
                                    ></div>

                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>


        {{-- ====================================================== --}}
        {{-- GRÁFICA POR VEHÍCULO --}}
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
                            directions_car
                        </span>

                    </div>


                    <div>

                        <h2
                            class="font-['Josefin_Sans']
                                   font-semibold
                                   text-[#003d82]"
                        >
                            Permanencia por vehículo
                        </h2>

                        <p
                            class="text-xs
                                   text-gray-500"
                        >
                            Tiempo acumulado dentro de las zonas por cada unidad.
                        </p>

                    </div>

                </div>

            </div>


            @if($graficaVehiculos->isEmpty())

                <div
                    class="px-6 py-12
                           text-center
                           text-sm
                           text-gray-500"
                >
                    No existen datos disponibles.
                </div>

            @else

                @php

                    $maximoVehiculo =
                        max(
                            1,
                            (int)
                            $graficaVehiculos->max('minutos')
                        );

                @endphp


                <div class="space-y-5 p-5">

                    @foreach($graficaVehiculos as $item)

                        @php

                            $porcentaje =
                                $item['minutos'] > 0

                                    ?

                                    max(
                                        3,
                                        min(
                                            100,
                                            (
                                                $item['minutos']
                                                /
                                                $maximoVehiculo
                                            )
                                            *
                                            100
                                        )
                                    )

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

                                        ·

                                        {{ $item['visitas'] }}
                                        visita(s)
                                    </p>

                                </div>


                                <span
                                    class="text-sm
                                           font-semibold
                                           text-[#0056b3]"
                                >
                                    {{ $item['tiempo'] }}
                                </span>

                            </div>


                            <div
                                class="h-3
                                       overflow-hidden
                                       rounded-full
                                       bg-gray-100"
                            >

                                @if($item['minutos'] > 0)

                                    <div
                                        class="h-full
                                               rounded-full
                                               bg-[#56928C]"

                                        style="
                                            width:
                                            {{ $porcentaje }}%;
                                        "
                                    ></div>

                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>


        {{-- ====================================================== --}}
        {{-- TABLA DE VISITAS --}}
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
                            Visitas registradas
                        </h2>

                        <p
                            class="text-xs
                                   text-gray-500"
                        >
                            Entradas, salidas y tiempos de permanencia registrados.
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
                            location_off
                        </span>

                    </div>


                    <h3
                        class="mt-3
                               font-['Josefin_Sans']
                               font-semibold
                               text-gray-700"
                    >
                        Sin visitas registradas
                    </h3>

                    <p
                        class="mt-1
                               text-sm
                               text-gray-500"
                    >
                        No se encontraron entradas o salidas para los criterios seleccionados.
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
                                    Zona
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-xs font-semibold
                                           uppercase
                                           text-gray-500"
                                >
                                    Entrada
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-xs font-semibold
                                           uppercase
                                           text-gray-500"
                                >
                                    Salida
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-xs font-semibold
                                           uppercase
                                           text-gray-500"
                                >
                                    Permanencia
                                </th>

                                <th
                                    class="px-5 py-3
                                           text-left
                                           text-xs font-semibold
                                           uppercase
                                           text-gray-500"
                                >
                                    Estado
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

                                    {{-- VEHÍCULO --}}

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


                                    {{-- ZONA --}}

                                    <td class="px-5 py-4">

                                        <div
                                            class="inline-flex
                                                   items-center gap-2"
                                        >

                                            <span
                                                class="h-3 w-3
                                                       rounded-full"

                                                style="
                                                    background-color:
                                                    {{ $evento['zona_color'] }};
                                                "
                                            ></span>

                                            <span
                                                class="text-sm
                                                       font-medium
                                                       text-gray-700"
                                            >
                                                {{ $evento['zona'] }}
                                            </span>

                                        </div>

                                    </td>


                                    {{-- ENTRADA --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4
                                               text-sm
                                               text-gray-600"
                                    >

                                        {{
                                            $evento['fecha_entrada']
                                                ->format(
                                                    'd/m/Y H:i'
                                                )
                                        }}

                                    </td>


                                    {{-- SALIDA --}}

                                    <td
                                        class="whitespace-nowrap
                                               px-5 py-4
                                               text-sm
                                               text-gray-600"
                                    >

                                        @if($evento['fecha_salida'])

                                            {{
                                                $evento['fecha_salida']
                                                    ->format(
                                                        'd/m/Y H:i'
                                                    )
                                            }}

                                        @else

                                            <span
                                                class="font-medium
                                                       text-emerald-600"
                                            >
                                                Aún en zona
                                            </span>

                                        @endif

                                    </td>


                                    {{-- PERMANENCIA --}}

                                    <td class="px-5 py-4">

                                        <span
                                            class="inline-flex
                                                   items-center gap-1
                                                   rounded-full
                                                   bg-blue-50
                                                   px-2.5 py-1
                                                   text-xs font-semibold
                                                   text-[#0056b3]"
                                        >

                                            <span
                                                class="material-symbols-rounded
                                                       text-[15px]"
                                            >
                                                schedule
                                            </span>

                                            {{ $evento['duracion_formateada'] }}

                                        </span>

                                    </td>


                                    {{-- ESTADO --}}

                                    <td class="px-5 py-4">

                                        @if($evento['estado'] === 'Finalizada')

                                            <span
                                                class="inline-flex
                                                       items-center gap-1
                                                       rounded-full
                                                       bg-gray-100
                                                       px-2.5 py-1
                                                       text-xs font-semibold
                                                       text-gray-600"
                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[15px]"
                                                >
                                                    check_circle
                                                </span>

                                                Finalizada

                                            </span>

                                        @else

                                            <span
                                                class="inline-flex
                                                       items-center gap-1
                                                       rounded-full
                                                       bg-emerald-50
                                                       px-2.5 py-1
                                                       text-xs font-semibold
                                                       text-emerald-700"
                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[15px]"
                                                >
                                                    radio_button_checked
                                                </span>

                                                En zona

                                            </span>

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
            class="rounded-2xl
                   border border-gray-200
                   bg-white
                   px-5 py-4
                   text-xs
                   text-gray-500
                   shadow-sm"
        >

            <span class="font-semibold text-gray-600">
                Nota:
            </span>

            El tiempo de permanencia se calcula desde la entrada hasta la salida
            registrada. Cuando el vehículo continúa dentro de una zona,
            el tiempo se calcula hasta el momento de la consulta.

        </div>

    </div>

</x-app-layout>