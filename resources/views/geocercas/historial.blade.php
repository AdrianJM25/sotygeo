<x-app-layout>

    <x-slot name="titulo">
        Historial de Zonas
    </x-slot>

    <div class="mx-auto max-w-[1600px] space-y-5 pb-8 font-['Ubuntu']">

        {{-- ===================================================== --}}
        {{-- HEADER --}}
        {{-- ===================================================== --}}

        <div
            class="flex flex-col gap-4 rounded-2xl bg-[#0056b3]
                   p-5 shadow-sm sm:flex-row sm:items-center
                   sm:justify-between"
        >

            <div class="flex items-center gap-4">

                <div
                    class="flex h-12 w-12 items-center justify-center
                           rounded-xl bg-white/15 text-white"
                >
                    <span class="material-symbols-rounded text-[27px]">
                        history
                    </span>
                </div>

                <div>

                    <h1
                        class="font-['Josefin_Sans'] text-xl
                               font-semibold text-white sm:text-2xl"
                    >
                        Historial de Zonas
                    </h1>

                    <p class="mt-0.5 text-sm text-blue-100">
                        Consulta entradas, salidas, permanencias y alertas generadas por las geocercas.
                    </p>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- ERRORES --}}
        {{-- ===================================================== --}}

        @if($errors->any())

            <div
                class="rounded-xl border border-red-200
                       bg-red-50 px-5 py-3.5
                       text-sm text-red-700"
            >

                <div class="mb-2 flex items-center gap-2 font-semibold">

                    <span class="material-symbols-rounded text-[19px]">
                        error
                    </span>

                    Revisa los filtros seleccionados.

                </div>

                <ul class="ml-6 list-disc space-y-1">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- ===================================================== --}}
        {{-- FILTROS --}}
        {{-- ===================================================== --}}

        <div
            class="rounded-2xl border border-[#0056b3]/15
                   bg-white shadow-sm"
        >

            <div
                class="border-b border-gray-100
                       bg-blue-50/50 px-5 py-4"
            >

                <div class="flex items-center gap-2">

                    <span
                        class="material-symbols-rounded
                               text-[20px] text-[#0056b3]"
                    >
                        filter_alt
                    </span>

                    <h2
                        class="font-['Josefin_Sans']
                               font-semibold text-[#003d82]"
                    >
                        Filtros del historial
                    </h2>

                </div>

            </div>


            <form
                action="{{ route('geocercas.historial') }}"
                method="GET"
                class="grid grid-cols-1 gap-5 p-5
                       md:grid-cols-2 xl:grid-cols-5"
            >

                {{-- FECHA INICIO --}}

                <div>

                    <label
                        class="mb-1.5 block text-xs
                               font-semibold uppercase
                               text-gray-600"
                    >
                        Desde
                    </label>

                    <input
                        type="datetime-local"
                        name="fecha_inicio"
                        value="{{ request('fecha_inicio') }}"
                        class="w-full rounded-xl border-gray-300
                               text-sm focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                </div>


                {{-- FECHA FIN --}}

                <div>

                    <label
                        class="mb-1.5 block text-xs
                               font-semibold uppercase
                               text-gray-600"
                    >
                        Hasta
                    </label>

                    <input
                        type="datetime-local"
                        name="fecha_fin"
                        value="{{ request('fecha_fin') }}"
                        class="w-full rounded-xl border-gray-300
                               text-sm focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                </div>


                {{-- VEHÍCULO --}}

                <div>

                    <label
                        class="mb-1.5 block text-xs
                               font-semibold uppercase
                               text-gray-600"
                    >
                        Vehículo
                    </label>

                    <select
                        name="vehiculo_id"
                        class="w-full rounded-xl border-gray-300
                               text-sm focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                        <option value="">
                            Todos
                        </option>

                        @foreach($vehiculos as $vehiculo)

                            <option
                                value="{{ $vehiculo->id }}"
                                @selected(
                                    (string) request('vehiculo_id')
                                    ===
                                    (string) $vehiculo->id
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


                {{-- ZONA --}}

                <div>

                    <label
                        class="mb-1.5 block text-xs
                               font-semibold uppercase
                               text-gray-600"
                    >
                        Zona
                    </label>

                    <select
                        name="geocerca_id"
                        class="w-full rounded-xl border-gray-300
                               text-sm focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                        <option value="">
                            Todas
                        </option>

                        @foreach($geocercas as $geocerca)

                            <option
                                value="{{ $geocerca->id }}"
                                @selected(
                                    (string) request('geocerca_id')
                                    ===
                                    (string) $geocerca->id
                                )
                            >
                                {{ $geocerca->nombre }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- ESTADO --}}

                <div>

                    <label
                        class="mb-1.5 block text-xs
                               font-semibold uppercase
                               text-gray-600"
                    >
                        Estado
                    </label>

                    <select
                        name="estado"
                        class="w-full rounded-xl border-gray-300
                               text-sm focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                        <option value="">
                            Todos
                        </option>

                        <option
                            value="dentro"
                            @selected(request('estado') === 'dentro')
                        >
                            Actualmente dentro
                        </option>

                        <option
                            value="finalizado"
                            @selected(request('estado') === 'finalizado')
                        >
                            Finalizados
                        </option>

                        <option
                            value="alerta"
                            @selected(request('estado') === 'alerta')
                        >
                            Con alerta
                        </option>

                    </select>

                </div>


                {{-- BOTONES --}}

                <div
                    class="flex flex-col gap-3 border-t
                           border-gray-100 pt-4
                           md:col-span-2 md:flex-row
                           md:justify-end xl:col-span-5"
                >

                    <a
                        href="{{ route('geocercas.historial') }}"
                        class="inline-flex items-center
                               justify-center gap-2 rounded-xl
                               border border-gray-300 bg-white
                               px-5 py-2.5 text-sm
                               font-medium text-gray-700
                               transition-colors hover:bg-gray-50"
                    >

                        <span class="material-symbols-rounded text-[18px]">
                            restart_alt
                        </span>

                        Limpiar

                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center
                               justify-center gap-2 rounded-xl
                               bg-[#0056b3] px-5 py-2.5
                               text-sm font-medium text-white
                               transition-colors hover:bg-[#004494]"
                    >

                        <span class="material-symbols-rounded text-[18px]">
                            search
                        </span>

                        Aplicar filtros

                    </button>

                </div>

            </form>

        </div>


        {{-- ===================================================== --}}
        {{-- TABLA --}}
        {{-- ===================================================== --}}

        <div
            class="overflow-hidden rounded-2xl
                   border border-[#0056b3]/15
                   bg-white shadow-sm"
        >

            {{-- CABECERA --}}

            <div
                class="flex items-center justify-between
                       border-b border-gray-100
                       bg-blue-50/50 px-5 py-4"
            >

                <div>

                    <h2
                        class="font-['Josefin_Sans']
                               font-semibold text-[#003d82]"
                    >
                        Eventos registrados
                    </h2>

                    <p class="mt-0.5 text-xs text-gray-500">
                        Registro histórico de permanencia en zonas.
                    </p>

                </div>

                <span
                    class="rounded-lg bg-white
                           px-3 py-1 text-xs
                           font-semibold text-[#0056b3]
                           shadow-sm"
                >
                    {{ $eventos->total() }}
                </span>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm">

                    <thead
                        class="border-b border-[#0056b3]/15
                               bg-blue-50/40 text-xs
                               text-[#0056b3]"
                    >

                        <tr>

                            <th class="px-5 py-4 font-semibold">
                                Entrada
                            </th>

                            <th class="px-5 py-4 font-semibold">
                                Salida
                            </th>

                            <th class="px-5 py-4 font-semibold">
                                Vehículo
                            </th>

                            <th class="px-5 py-4 font-semibold">
                                Zona
                            </th>

                            <th class="px-5 py-4 font-semibold">
                                Permanencia
                            </th>

                            <th class="px-5 py-4 font-semibold">
                                Estado
                            </th>

                            <th class="px-5 py-4 text-right font-semibold">
                                Ubicación
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse($eventos as $evento)

                            @php

                                /*
                                |--------------------------------------------------------------------------
                                | CALCULAR DURACIÓN
                                |--------------------------------------------------------------------------
                                */

                                if ($evento->duracion_minutos !== null) {

                                    $duracion = max(
                                        0,
                                        (int) $evento->duracion_minutos
                                    );

                                } elseif ($evento->fecha_salida) {

                                    $segundos =
                                        $evento->fecha_salida->timestamp
                                        -
                                        $evento->fecha_entrada->timestamp;

                                    $duracion = max(
                                        0,
                                        (int) floor($segundos / 60)
                                    );

                                } else {

                                    $segundos =
                                        now()->timestamp
                                        -
                                        $evento->fecha_entrada->timestamp;

                                    $duracion = max(
                                        0,
                                        (int) floor($segundos / 60)
                                    );

                                }


                                /*
                                |--------------------------------------------------------------------------
                                | ALERTA
                                |--------------------------------------------------------------------------
                                */

                                $tieneAlerta =
                                    $evento->alerta_generada
                                    ||
                                    $evento->alerta_permanencia_generada;

                            @endphp


                            <tr
                                class="transition-colors
                                       hover:bg-blue-50/30"
                            >

                                {{-- ===================================== --}}
                                {{-- ENTRADA --}}
                                {{-- ===================================== --}}

                                <td class="whitespace-nowrap px-5 py-4">

                                    <div class="font-medium text-gray-800">

                                        {{
                                            $evento
                                                ->fecha_entrada
                                                ->format('d/m/Y')
                                        }}

                                    </div>

                                    <div
                                        class="mt-0.5
                                               text-xs text-gray-500"
                                    >

                                        {{
                                            $evento
                                                ->fecha_entrada
                                                ->format('H:i')
                                        }}

                                    </div>

                                </td>


                                {{-- ===================================== --}}
                                {{-- SALIDA --}}
                                {{-- ===================================== --}}

                                <td class="whitespace-nowrap px-5 py-4">

                                    @if($evento->fecha_salida)

                                        <div class="font-medium text-gray-800">

                                            {{
                                                $evento
                                                    ->fecha_salida
                                                    ->format('d/m/Y')
                                            }}

                                        </div>

                                        <div
                                            class="mt-0.5
                                                   text-xs text-gray-500"
                                        >

                                            {{
                                                $evento
                                                    ->fecha_salida
                                                    ->format('H:i')
                                            }}

                                        </div>

                                    @else

                                        <span
                                            class="inline-flex
                                                   items-center gap-1
                                                   text-xs font-medium
                                                   text-emerald-600"
                                        >

                                            <span
                                                class="h-1.5 w-1.5
                                                       animate-pulse
                                                       rounded-full
                                                       bg-emerald-500"
                                            ></span>

                                            Aún dentro

                                        </span>

                                    @endif

                                </td>


                                {{-- ===================================== --}}
                                {{-- VEHÍCULO --}}
                                {{-- ===================================== --}}

                                <td class="px-5 py-4">

                                    <div
                                        class="font-semibold
                                               text-gray-800"
                                    >

                                        {{
                                            $evento->vehiculo?->nombre
                                            ??
                                            'Vehículo no disponible'
                                        }}

                                    </div>

                                    @if($evento->vehiculo?->placas)

                                        <div
                                            class="mt-0.5
                                                   text-xs
                                                   text-gray-500"
                                        >

                                            {{
                                                $evento
                                                    ->vehiculo
                                                    ->placas
                                            }}

                                        </div>

                                    @endif

                                </td>


                                {{-- ===================================== --}}
                                {{-- ZONA --}}
                                {{-- ===================================== --}}

                                <td class="px-5 py-4">

                                    <div
                                        class="flex items-center
                                               gap-2"
                                    >

                                        @if($evento->zona)

                                            <span
                                                class="h-2.5 w-2.5
                                                       shrink-0
                                                       rounded-full
                                                       border
                                                       border-black/10"
                                                style="
                                                    background-color:
                                                    {{
                                                        $evento
                                                            ->zona
                                                            ->color_hex
                                                        ??
                                                        '#0056B3'
                                                    }};
                                                "
                                            ></span>

                                        @endif

                                        <span
                                            class="font-medium
                                                   text-gray-700"
                                        >

                                            {{
                                                $evento->zona?->nombre
                                                ??
                                                'Zona no disponible'
                                            }}

                                        </span>

                                    </div>

                                </td>


                                {{-- ===================================== --}}
                                {{-- PERMANENCIA --}}
                                {{-- ===================================== --}}

                                <td class="px-5 py-4">

                                    <div
                                        class="inline-flex
                                               items-center gap-1.5
                                               text-gray-700"
                                    >

                                        <span
                                            class="material-symbols-rounded
                                                   text-[17px]
                                                   text-gray-400"
                                        >
                                            schedule
                                        </span>

                                        @if($duracion < 60)

                                            {{ $duracion }} min

                                        @else

                                            @php

                                                $horas =
                                                    intdiv(
                                                        $duracion,
                                                        60
                                                    );

                                                $minutosRestantes =
                                                    $duracion % 60;

                                            @endphp

                                            {{ $horas }} h

                                            @if($minutosRestantes > 0)
                                                {{ $minutosRestantes }} min
                                            @endif

                                        @endif

                                    </div>

                                </td>


                                {{-- ===================================== --}}
                                {{-- ESTADO --}}
                                {{-- ===================================== --}}

                                <td class="px-5 py-4">

                                    <div
                                        class="flex flex-col
                                               items-start gap-1.5"
                                    >

                                        {{-- DENTRO --}}

                                        @if(!$evento->fecha_salida)

                                            <span
                                                class="inline-flex
                                                       items-center gap-1
                                                       rounded-full
                                                       border
                                                       border-emerald-200
                                                       bg-emerald-50
                                                       px-2.5 py-1
                                                       text-xs
                                                       font-medium
                                                       text-emerald-700"
                                            >

                                                <span
                                                    class="h-1.5 w-1.5
                                                           animate-pulse
                                                           rounded-full
                                                           bg-emerald-500"
                                                ></span>

                                                Dentro

                                            </span>

                                        {{-- FINALIZADO --}}

                                        @else

                                            <span
                                                class="inline-flex
                                                       items-center gap-1
                                                       rounded-full
                                                       border border-blue-200
                                                       bg-blue-50
                                                       px-2.5 py-1
                                                       text-xs font-medium
                                                       text-[#0056b3]"
                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[14px]"
                                                >
                                                    check_circle
                                                </span>

                                                Finalizado

                                            </span>

                                        @endif


                                        {{-- ALERTA --}}

                                        @if($tieneAlerta)

                                            <span
                                                class="inline-flex
                                                       items-center gap-1
                                                       rounded-full
                                                       border border-red-200
                                                       bg-red-50
                                                       px-2.5 py-1
                                                       text-xs font-medium
                                                       text-red-700"
                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[14px]"
                                                >
                                                    warning
                                                </span>

                                                Alerta

                                            </span>

                                        @endif


                                        {{-- TIPO DE EVENTO --}}

                                        @if(
                                            $evento->tipo_evento
                                            &&
                                            $evento->tipo_evento !== 'normal'
                                        )

                                            <span
                                                class="text-[10px]
                                                       font-medium uppercase
                                                       text-gray-400"
                                            >

                                                {{
                                                    ucfirst(
                                                        str_replace(
                                                            '_',
                                                            ' ',
                                                            $evento->tipo_evento
                                                        )
                                                    )
                                                }}

                                            </span>

                                        @endif

                                    </div>

                                </td>


                                {{-- ===================================== --}}
                                {{-- UBICACIÓN --}}
                                {{-- ===================================== --}}

                                <td
                                    class="whitespace-nowrap
                                           px-5 py-4 text-right"
                                >

                                    @if(
                                        $evento->latitud_entrada !== null
                                        &&
                                        $evento->longitud_entrada !== null
                                    )

                                        <a
                                            href="https://maps.google.com/?q={{ $evento->latitud_entrada }},{{ $evento->longitud_entrada }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="inline-flex
                                                   items-center gap-1
                                                   rounded-lg
                                                   px-2.5 py-1.5
                                                   text-xs font-medium
                                                   text-[#0056b3]
                                                   transition-colors
                                                   hover:bg-blue-50"
                                        >

                                            <span
                                                class="material-symbols-rounded
                                                       text-[17px]"
                                            >
                                                location_on
                                            </span>

                                            Entrada

                                        </a>

                                    @else

                                        <span
                                            class="text-xs
                                                   text-gray-400"
                                        >
                                            Sin coordenadas
                                        </span>

                                    @endif

                                </td>

                            </tr>


                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="px-6 py-14
                                           text-center"
                                >

                                    <span
                                        class="material-symbols-rounded
                                               mb-2 block text-[42px]
                                               text-[#0056b3]/25"
                                    >
                                        history
                                    </span>

                                    <h3
                                        class="text-sm
                                               font-semibold
                                               text-gray-700"
                                    >
                                        Sin eventos registrados
                                    </h3>

                                    <p
                                        class="mt-1 text-sm
                                               text-gray-500"
                                    >
                                        No se encontraron eventos de geocercas con los filtros seleccionados.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ================================================= --}}
            {{-- PAGINACIÓN --}}
            {{-- ================================================= --}}

            @if($eventos->hasPages())

                <div
                    class="border-t border-gray-100
                           bg-gray-50/50
                           px-5 py-4"
                >

                    {{
                        $eventos
                            ->withQueryString()
                            ->links()
                    }}

                </div>

            @endif

        </div>

    </div>

</x-app-layout>