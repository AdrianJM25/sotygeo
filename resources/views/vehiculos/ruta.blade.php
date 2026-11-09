<x-app-layout>

    <x-slot name="titulo">
        Historial de Ruta
    </x-slot>

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        crossorigin=""
    >

    <div class="mx-auto max-w-[1600px] space-y-5 pb-8 font-['Ubuntu']">

        {{-- ====================================================== --}}
        {{-- HEADER --}}
        {{-- ====================================================== --}}

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
                        route
                    </span>
                </div>

                <div>

                    <h1
                        class="font-['Josefin_Sans'] text-2xl
                               font-semibold text-white"
                    >
                        Historial de Ruta
                    </h1>

                    <p class="text-sm text-blue-100">

                        {{ $vehiculo->nombre }}

                        @if($vehiculo->placas)
                            · {{ $vehiculo->placas }}
                        @endif

                    </p>

                </div>

            </div>


            <a
                href="{{ route('vehiculos.index') }}"
                class="inline-flex items-center gap-2
                       rounded-xl border border-white/30
                       bg-white/10 px-4 py-2.5
                       text-sm font-medium text-white
                       transition hover:bg-white/20"
            >

                <span class="material-symbols-rounded">
                    arrow_back
                </span>

                Volver a Vehículos

            </a>

        </div>


        {{-- ====================================================== --}}
        {{-- ERRORES --}}
        {{-- ====================================================== --}}

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

                    Revisa la información seleccionada.

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


        {{-- ====================================================== --}}
        {{-- FILTRO --}}
        {{-- ====================================================== --}}

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
                               text-[#0056b3]"
                    >
                        calendar_month
                    </span>

                    <h2
                        class="font-['Josefin_Sans']
                               font-semibold text-[#003d82]"
                    >
                        Consultar recorrido
                    </h2>

                </div>

            </div>


            <form
                method="GET"
                action="{{ route('vehiculos.ruta', $vehiculo->id) }}"
                class="flex flex-col gap-4 p-5
                       sm:flex-row sm:items-end"
            >

                <div class="w-full sm:max-w-xs">

                    <label
                        for="fecha"
                        class="mb-1.5 block text-xs
                               font-semibold uppercase
                               text-gray-600"
                    >
                        Fecha
                    </label>

                    <input
                        type="date"
                        name="fecha"
                        id="fecha"
                        value="{{ request('fecha', $fechaConsulta->format('Y-m-d')) }}"
                        class="w-full rounded-xl border-gray-300
                               text-sm focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                </div>


                <button
                    type="submit"
                    class="inline-flex items-center justify-center
                           gap-2 rounded-xl bg-[#0056b3]
                           px-5 py-2.5 text-sm font-medium
                           text-white transition
                           hover:bg-[#004494]"
                >

                    <span class="material-symbols-rounded">
                        search
                    </span>

                    Consultar ruta

                </button>

            </form>

        </div>


        {{-- ====================================================== --}}
        {{-- RESUMEN --}}
        {{-- ====================================================== --}}

        <div
            class="grid grid-cols-2 gap-4
                   md:grid-cols-3 xl:grid-cols-6"
        >

            {{-- PUNTOS GPS --}}

            <div
                class="rounded-2xl border border-gray-200
                       bg-white p-4 shadow-sm"
            >

                <div class="flex items-center gap-2 text-gray-500">

                    <span class="material-symbols-rounded text-[20px]">
                        location_on
                    </span>

                    <span class="text-xs font-medium uppercase">
                        Puntos GPS
                    </span>

                </div>

                <div class="mt-2 text-2xl font-semibold text-gray-800">
                    {{ $ubicaciones->count() }}
                </div>

            </div>


            {{-- FECHA --}}

            <div
                class="rounded-2xl border border-gray-200
                       bg-white p-4 shadow-sm"
            >

                <div class="flex items-center gap-2 text-gray-500">

                    <span class="material-symbols-rounded text-[20px]">
                        calendar_today
                    </span>

                    <span class="text-xs font-medium uppercase">
                        Fecha
                    </span>

                </div>

                <div class="mt-2 text-base font-semibold text-gray-800">
                    {{ $fechaConsulta->format('d/m/Y') }}
                </div>

            </div>


            {{-- INICIO --}}

            <div
                class="rounded-2xl border border-gray-200
                       bg-white p-4 shadow-sm"
            >

                <div class="flex items-center gap-2 text-gray-500">

                    <span class="material-symbols-rounded text-[20px]">
                        play_circle
                    </span>

                    <span class="text-xs font-medium uppercase">
                        Inicio
                    </span>

                </div>

                <div
                    id="hora-inicio"
                    class="mt-2 text-base font-semibold text-gray-800"
                >
                    —
                </div>

            </div>


            {{-- FIN --}}

            <div
                class="rounded-2xl border border-gray-200
                       bg-white p-4 shadow-sm"
            >

                <div class="flex items-center gap-2 text-gray-500">

                    <span class="material-symbols-rounded text-[20px]">
                        flag
                    </span>

                    <span class="text-xs font-medium uppercase">
                        Último punto
                    </span>

                </div>

                <div
                    id="hora-fin"
                    class="mt-2 text-base font-semibold text-gray-800"
                >
                    —
                </div>

            </div>


            {{-- DISTANCIA --}}

            <div
                class="rounded-2xl border border-gray-200
                       bg-white p-4 shadow-sm"
            >

                <div class="flex items-center gap-2 text-gray-500">

                    <span class="material-symbols-rounded text-[20px]">
                        straighten
                    </span>

                    <span class="text-xs font-medium uppercase">
                        Distancia
                    </span>

                </div>

                <div
                    id="distancia-total"
                    class="mt-2 text-base font-semibold text-gray-800"
                >
                    —
                </div>

            </div>


            {{-- TIPO DE RECORRIDO --}}

            <div
                class="rounded-2xl border border-gray-200
                       bg-white p-4 shadow-sm"
            >

                <div class="flex items-center gap-2 text-gray-500">

                    <span class="material-symbols-rounded text-[20px]">
                        alt_route
                    </span>

                    <span class="text-xs font-medium uppercase">
                        Tipo de recorrido
                    </span>

                </div>

                <div class="mt-2">

                    @if($modoRuta === 'match')

                        <span
                            class="inline-flex rounded-full
                                   border border-emerald-200
                                   bg-emerald-50 px-2.5 py-1
                                   text-xs font-medium text-emerald-700"
                        >
                            Ajustado a calles
                        </span>

                    @elseif($modoRuta === 'route')

                        <span
                            class="inline-flex rounded-full
                                   border border-amber-200
                                   bg-amber-50 px-2.5 py-1
                                   text-xs font-medium text-amber-700"
                        >
                            Ruta estimada
                        </span>

                    @else

                        <span
                            class="inline-flex rounded-full
                                   border border-gray-200
                                   bg-gray-50 px-2.5 py-1
                                   text-xs font-medium text-gray-600"
                        >
                            Puntos GPS
                        </span>

                    @endif

                </div>

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- MAPA --}}
        {{-- ====================================================== --}}

        <div
            class="overflow-hidden rounded-2xl
                   border border-[#0056b3]/15
                   bg-white shadow-sm"
        >

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
                        Recorrido del vehículo
                    </h2>

                    <p class="mt-1 text-xs text-gray-500">
                        Visualiza la trayectoria registrada y su ajuste a la red vial.
                    </p>

                </div>


                <span
                    id="estado-reproductor"
                    class="rounded-full border border-gray-200
                           bg-white px-3 py-1
                           text-xs font-medium text-gray-600"
                >
                    Detenido
                </span>

            </div>


            <div class="p-3">

                <div
                    id="mapa-historial"
                    class="h-[540px] w-full
                           rounded-xl border border-gray-200"
                ></div>

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- REPRODUCTOR --}}
        {{-- ====================================================== --}}

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
                               text-[#0056b3]"
                    >
                        slow_motion_video
                    </span>

                    <h2
                        class="font-['Josefin_Sans']
                               font-semibold text-[#003d82]"
                    >
                        Reproductor de ruta
                    </h2>

                </div>

            </div>


            <div class="space-y-5 p-5">

                {{-- BARRA DE PROGRESO --}}

                <div>

                    <div
                        class="mb-2 flex items-center
                               justify-between text-xs
                               text-gray-500"
                    >

                        <span id="progreso-inicio">
                            —
                        </span>

                        <span
                            id="contador-puntos"
                            class="font-medium text-gray-700"
                        >
                            Punto 0
                        </span>

                        <span id="progreso-fin">
                            —
                        </span>

                    </div>


                    <input
                        id="barra-progreso"
                        type="range"
                        min="0"
                        max="0"
                        value="0"
                        step="1"
                        disabled
                        class="w-full cursor-pointer
                               accent-[#0056b3]
                               disabled:cursor-not-allowed
                               disabled:opacity-50"
                    >

                </div>


                {{-- CONTROLES --}}

                <div
                    class="flex flex-col gap-4
                           lg:flex-row lg:items-center
                           lg:justify-between"
                >

                    <div class="flex flex-wrap items-center gap-2">

                        <button
                            id="btn-reiniciar"
                            type="button"
                            disabled
                            title="Volver al inicio"
                            class="inline-flex h-11 w-11
                                   items-center justify-center
                                   rounded-xl border border-gray-300
                                   bg-white text-gray-700
                                   transition hover:bg-gray-50
                                   disabled:cursor-not-allowed
                                   disabled:opacity-40"
                        >

                            <span class="material-symbols-rounded">
                                replay
                            </span>

                        </button>


                        <button
                            id="btn-anterior"
                            type="button"
                            disabled
                            title="Punto anterior"
                            class="inline-flex h-11 w-11
                                   items-center justify-center
                                   rounded-xl border border-gray-300
                                   bg-white text-gray-700
                                   transition hover:bg-gray-50
                                   disabled:cursor-not-allowed
                                   disabled:opacity-40"
                        >

                            <span class="material-symbols-rounded">
                                skip_previous
                            </span>

                        </button>


                        <button
                            id="btn-reproducir"
                            type="button"
                            disabled
                            class="inline-flex items-center
                                   justify-center gap-2
                                   rounded-xl bg-[#0056b3]
                                   px-5 py-2.5 text-sm
                                   font-medium text-white
                                   transition hover:bg-[#004494]
                                   disabled:cursor-not-allowed
                                   disabled:opacity-40"
                        >

                            <span
                                id="icono-reproducir"
                                class="material-symbols-rounded"
                            >
                                play_arrow
                            </span>

                            <span id="texto-reproducir">
                                Reproducir
                            </span>

                        </button>


                        <button
                            id="btn-siguiente"
                            type="button"
                            disabled
                            title="Siguiente punto"
                            class="inline-flex h-11 w-11
                                   items-center justify-center
                                   rounded-xl border border-gray-300
                                   bg-white text-gray-700
                                   transition hover:bg-gray-50
                                   disabled:cursor-not-allowed
                                   disabled:opacity-40"
                        >

                            <span class="material-symbols-rounded">
                                skip_next
                            </span>

                        </button>


                        <button
                            id="btn-final"
                            type="button"
                            disabled
                            title="Ir al final"
                            class="inline-flex h-11 w-11
                                   items-center justify-center
                                   rounded-xl border border-gray-300
                                   bg-white text-gray-700
                                   transition hover:bg-gray-50
                                   disabled:cursor-not-allowed
                                   disabled:opacity-40"
                        >

                            <span class="material-symbols-rounded">
                                last_page
                            </span>

                        </button>

                    </div>


                    <div class="flex items-center gap-3">

                        <label
                            for="velocidad-reproduccion"
                            class="text-sm font-medium text-gray-600"
                        >
                            Velocidad:
                        </label>

                        <select
                            id="velocidad-reproduccion"
                            disabled
                            class="rounded-xl border-gray-300
                                   text-sm focus:border-[#0056b3]
                                   focus:ring-[#0056b3]
                                   disabled:opacity-50"
                        >

                            <option value="0.5">
                                0.5x
                            </option>

                            <option value="1" selected>
                                1x
                            </option>

                            <option value="2">
                                2x
                            </option>

                            <option value="4">
                                4x
                            </option>

                        </select>

                    </div>

                </div>


                {{-- INFORMACIÓN DEL PUNTO ACTUAL --}}

                <div
                    class="grid grid-cols-2 gap-4
                           border-t border-gray-100 pt-5
                           md:grid-cols-4"
                >

                    <div>

                        <span
                            class="block text-xs
                                   font-medium uppercase
                                   text-gray-500"
                        >
                            Hora actual
                        </span>

                        <span
                            id="dato-hora"
                            class="mt-1 block text-sm
                                   font-semibold text-gray-800"
                        >
                            —
                        </span>

                    </div>


                    <div>

                        <span
                            class="block text-xs
                                   font-medium uppercase
                                   text-gray-500"
                        >
                            Velocidad GPS
                        </span>

                        <span
                            id="dato-velocidad"
                            class="mt-1 block text-sm
                                   font-semibold text-gray-800"
                        >
                            —
                        </span>

                    </div>


                    <div>

                        <span
                            class="block text-xs
                                   font-medium uppercase
                                   text-gray-500"
                        >
                            Latitud
                        </span>

                        <span
                            id="dato-latitud"
                            class="mt-1 block text-sm
                                   font-semibold text-gray-800"
                        >
                            —
                        </span>

                    </div>


                    <div>

                        <span
                            class="block text-xs
                                   font-medium uppercase
                                   text-gray-500"
                        >
                            Longitud
                        </span>

                        <span
                            id="dato-longitud"
                            class="mt-1 block text-sm
                                   font-semibold text-gray-800"
                        >
                            —
                        </span>

                    </div>

                </div>

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- SIN DATOS --}}
        {{-- ====================================================== --}}

        @if($ubicaciones->isEmpty())

            <div
                class="rounded-2xl border border-amber-200
                       bg-amber-50 px-5 py-5
                       text-sm text-amber-800"
            >

                <div class="flex gap-3">

                    <span class="material-symbols-rounded">
                        info
                    </span>

                    <div>

                        <div class="font-semibold">
                            No hay posiciones GPS registradas.
                        </div>

                        <p class="mt-1 text-amber-700">
                            Selecciona otra fecha o espera a que el vehículo registre nuevas ubicaciones.
                        </p>

                    </div>

                </div>

            </div>

        @endif

    </div>


    {{-- ====================================================== --}}
    {{-- LEAFLET --}}
    {{-- ====================================================== --}}

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        crossorigin=""
    ></script>


    {{-- ====================================================== --}}
    {{-- JAVASCRIPT --}}
    {{-- ====================================================== --}}

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                /*
                |--------------------------------------------------------------------------
                | DATOS
                |--------------------------------------------------------------------------
                */

                const ubicaciones =
                    @json($ubicaciones->values());

                const rutaServidor =
                    @json($rutaAjustada);


                /*
                |--------------------------------------------------------------------------
                | RUTA VISUAL
                |--------------------------------------------------------------------------
                */

                let ruta = [];


                if (
                    Array.isArray(rutaServidor)
                    &&
                    rutaServidor.length > 0
                ) {

                    ruta =
                        rutaServidor.map(
                            punto => [

                                parseFloat(
                                    punto[0]
                                ),

                                parseFloat(
                                    punto[1]
                                )
                            ]
                        );

                } else {

                    ruta =
                        ubicaciones.map(
                            punto => [

                                parseFloat(
                                    punto.latitud
                                ),

                                parseFloat(
                                    punto.longitud
                                )
                            ]
                        );
                }


                /*
                |--------------------------------------------------------------------------
                | MAPA
                |--------------------------------------------------------------------------
                */

                let latInicial =
                    19.4326;

                let lonInicial =
                    -99.1332;


                if (ruta.length > 0) {

                    latInicial =
                        ruta[0][0];

                    lonInicial =
                        ruta[0][1];
                }


                const map =
                    L.map(
                        'mapa-historial'
                    )
                    .setView(
                        [
                            latInicial,
                            lonInicial
                        ],
                        13
                    );


                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        maxZoom:
                            19,

                        attribution:
                            '&copy; OpenStreetMap contributors'
                    }
                )
                .addTo(map);


                /*
                |--------------------------------------------------------------------------
                | ELEMENTOS
                |--------------------------------------------------------------------------
                */

                const barra =
                    document.getElementById(
                        'barra-progreso'
                    );

                const btnPlay =
                    document.getElementById(
                        'btn-reproducir'
                    );

                const btnInicio =
                    document.getElementById(
                        'btn-reiniciar'
                    );

                const btnAnterior =
                    document.getElementById(
                        'btn-anterior'
                    );

                const btnSiguiente =
                    document.getElementById(
                        'btn-siguiente'
                    );

                const btnFinal =
                    document.getElementById(
                        'btn-final'
                    );

                const selectorVelocidad =
                    document.getElementById(
                        'velocidad-reproduccion'
                    );

                const estadoReproductor =
                    document.getElementById(
                        'estado-reproductor'
                    );


                /*
                |--------------------------------------------------------------------------
                | SIN DATOS
                |--------------------------------------------------------------------------
                */

                if (
                    ruta.length === 0
                    ||
                    ubicaciones.length === 0
                ) {

                    estadoReproductor.textContent =
                        'Sin recorrido';


                    setTimeout(
                        () => map.invalidateSize(),
                        150
                    );


                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | FUNCIONES DE FECHA
                |--------------------------------------------------------------------------
                */

                function obtenerFecha(
                    fechaGps
                ) {

                    if (!fechaGps) {
                        return null;
                    }


                    const valor =
                        new Date(
                            fechaGps
                        );


                    if (
                        Number.isNaN(
                            valor.getTime()
                        )
                    ) {

                        return null;
                    }


                    return valor;
                }


                function formatearHora(
                    fechaGps
                ) {

                    const d =
                        obtenerFecha(
                            fechaGps
                        );


                    if (!d) {
                        return '—';
                    }


                    return d.toLocaleTimeString(
                        'es-MX',
                        {
                            hour:
                                '2-digit',

                            minute:
                                '2-digit',

                            second:
                                '2-digit',
                        }
                    );
                }


                function formatearFechaCompleta(
                    valor
                ) {

                    let d;


                    if (
                        valor instanceof Date
                    ) {

                        d = valor;

                    } else {

                        d =
                            obtenerFecha(
                                valor
                            );
                    }


                    if (!d) {
                        return '—';
                    }


                    return d.toLocaleString(
                        'es-MX',
                        {
                            day:
                                '2-digit',

                            month:
                                '2-digit',

                            year:
                                'numeric',

                            hour:
                                '2-digit',

                            minute:
                                '2-digit',

                            second:
                                '2-digit',
                        }
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | FECHA INICIAL / FINAL
                |--------------------------------------------------------------------------
                */

                const primeraFecha =
                    obtenerFecha(
                        ubicaciones[0]
                            .fecha_gps
                    );


                const ultimaFecha =
                    obtenerFecha(
                        ubicaciones[
                            ubicaciones.length - 1
                        ].fecha_gps
                    );


                document.getElementById(
                    'hora-inicio'
                ).textContent =
                    formatearHora(
                        ubicaciones[0]
                            .fecha_gps
                    );


                document.getElementById(
                    'hora-fin'
                ).textContent =
                    formatearHora(
                        ubicaciones[
                            ubicaciones.length - 1
                        ].fecha_gps
                    );


                document.getElementById(
                    'progreso-inicio'
                ).textContent =
                    formatearHora(
                        ubicaciones[0]
                            .fecha_gps
                    );


                document.getElementById(
                    'progreso-fin'
                ).textContent =
                    formatearHora(
                        ubicaciones[
                            ubicaciones.length - 1
                        ].fecha_gps
                    );


                /*
                |--------------------------------------------------------------------------
                | DIBUJAR RUTA COMPLETA
                |--------------------------------------------------------------------------
                */

                let lineaCompleta = null;


                if (ruta.length > 1) {

                    lineaCompleta =
                        L.polyline(
                            ruta,
                            {
                                color:
                                    '#64748b',

                                weight:
                                    5,

                                opacity:
                                    0.75
                            }
                        )
                        .addTo(map);


                    map.fitBounds(
                        lineaCompleta
                            .getBounds(),
                        {
                            padding:
                                [
                                    30,
                                    30
                                ]
                        }
                    );

                } else {

                    map.setView(
                        ruta[0],
                        16
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | MARCADOR DE INICIO
                |--------------------------------------------------------------------------
                */

                L.circleMarker(
                    ruta[0],
                    {
                        radius:
                            8,

                        color:
                            '#047857',

                        fillColor:
                            '#10b981',

                        fillOpacity:
                            1,

                        weight:
                            3
                    }
                )
                .addTo(map)
                .bindPopup(
                    '<b>Inicio del recorrido</b><br>'
                    +
                    formatearFechaCompleta(
                        primeraFecha
                    )
                );


                /*
                |--------------------------------------------------------------------------
                | MARCADOR FINAL
                |--------------------------------------------------------------------------
                */

                if (ruta.length > 1) {

                    L.circleMarker(
                        ruta[
                            ruta.length - 1
                        ],
                        {
                            radius:
                                8,

                            color:
                                '#b91c1c',

                            fillColor:
                                '#ef4444',

                            fillOpacity:
                                1,

                            weight:
                                3
                        }
                    )
                    .addTo(map)
                    .bindPopup(
                        '<b>Fin del recorrido</b><br>'
                        +
                        formatearFechaCompleta(
                            ultimaFecha
                        )
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | ICONO DEL VEHÍCULO
                |--------------------------------------------------------------------------
                */

                const iconoVehiculo =
                    L.divIcon({

                        className:
                            'sotygeo-marker',

                        html:
                            `
                            <div style="
                                width:42px;
                                height:42px;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                border-radius:50%;
                                background:#0056b3;
                                color:white;
                                border:3px solid white;
                                box-shadow:0 3px 12px rgba(0,0,0,.30);
                            ">
                                <span
                                    class="material-symbols-rounded"
                                    style="font-size:23px;"
                                >
                                    local_shipping
                                </span>
                            </div>
                            `,

                        iconSize:
                            [
                                42,
                                42
                            ],

                        iconAnchor:
                            [
                                21,
                                21
                            ]
                    });


                const marcador =
                    L.marker(
                        ruta[0],
                        {
                            icon:
                                iconoVehiculo,

                            zIndexOffset:
                                1000
                        }
                    )
                    .addTo(map);


                /*
                |--------------------------------------------------------------------------
                | RECORRIDO COMPLETADO
                |--------------------------------------------------------------------------
                */

                const lineaRecorrida =
                    L.polyline(
                        [
                            ruta[0]
                        ],
                        {
                            color:
                                '#0056b3',

                            weight:
                                7,

                            opacity:
                                1
                        }
                    )
                    .addTo(map);


                /*
                |--------------------------------------------------------------------------
                | DISTANCIA TOTAL
                |--------------------------------------------------------------------------
                */

                let distanciaTotal =
                    0;


                for (
                    let i = 1;
                    i < ruta.length;
                    i++
                ) {

                    distanciaTotal +=
                        map.distance(
                            ruta[
                                i - 1
                            ],
                            ruta[i]
                        );
                }


                document.getElementById(
                    'distancia-total'
                ).textContent =
                    distanciaTotal < 1000
                        ?
                        `${Math.round(
                            distanciaTotal
                        )} m`
                        :
                        `${(
                            distanciaTotal
                            /
                            1000
                        ).toFixed(2)} km`;


                /*
                |--------------------------------------------------------------------------
                | VARIABLES DEL REPRODUCTOR
                |--------------------------------------------------------------------------
                */

                let indice =
                    0;

                let reproduciendo =
                    false;

                let temporizador =
                    null;


                /*
                |--------------------------------------------------------------------------
                | HABILITAR CONTROLES
                |--------------------------------------------------------------------------
                */

                barra.max =
                    Math.max(
                        ruta.length - 1,
                        0
                    );

                barra.disabled =
                    false;

                btnInicio.disabled =
                    false;

                btnAnterior.disabled =
                    false;

                btnSiguiente.disabled =
                    false;

                btnFinal.disabled =
                    false;

                selectorVelocidad.disabled =
                    false;

                btnPlay.disabled =
                    ruta.length < 2;


                /*
                |--------------------------------------------------------------------------
                | OBTENER GPS MÁS CERCANO AL PROGRESO
                |--------------------------------------------------------------------------
                */

                function obtenerGpsPorProgreso(
                    progreso
                ) {

                    if (
                        ubicaciones.length === 1
                    ) {

                        return ubicaciones[0];
                    }


                    const indiceGps =
                        Math.min(
                            ubicaciones.length - 1,
                            Math.max(
                                0,
                                Math.round(
                                    progreso
                                    *
                                    (
                                        ubicaciones.length - 1
                                    )
                                )
                            )
                        );


                    return ubicaciones[
                        indiceGps
                    ];
                }


                /*
                |--------------------------------------------------------------------------
                | MOSTRAR PUNTO
                |--------------------------------------------------------------------------
                */

                function mostrarPunto(
                    nuevoIndice,
                    centrar = true
                ) {

                    indice =
                        Math.max(
                            0,
                            Math.min(
                                nuevoIndice,
                                ruta.length - 1
                            )
                        );


                    const punto =
                        ruta[indice];


                    const progreso =
                        ruta.length <= 1
                            ?
                            0
                            :
                            indice
                            /
                            (
                                ruta.length - 1
                            );


                    const gps =
                        obtenerGpsPorProgreso(
                            progreso
                        );


                    /*
                    | Interpolar tiempo entre
                    | primer y último reporte.
                    */

                    let tiempoActual =
                        primeraFecha;


                    if (
                        primeraFecha
                        &&
                        ultimaFecha
                    ) {

                        const milisegundos =
                            primeraFecha.getTime()
                            +
                            (
                                ultimaFecha.getTime()
                                -
                                primeraFecha.getTime()
                            )
                            *
                            progreso;


                        tiempoActual =
                            new Date(
                                milisegundos
                            );
                    }


                    marcador.setLatLng(
                        punto
                    );


                    lineaRecorrida.setLatLngs(
                        ruta.slice(
                            0,
                            indice + 1
                        )
                    );


                    barra.value =
                        indice;


                    document.getElementById(
                        'contador-puntos'
                    ).textContent =
                        `Punto ${indice + 1} de ${ruta.length}`;


                    document.getElementById(
                        'dato-hora'
                    ).textContent =
                        formatearFechaCompleta(
                            tiempoActual
                        );


                    const velocidad =
                        parseFloat(
                            gps.velocidad
                            ??
                            0
                        );


                    document.getElementById(
                        'dato-velocidad'
                    ).textContent =
                        `${
                            Number.isFinite(
                                velocidad
                            )
                                ?
                                velocidad.toFixed(1)
                                :
                                '0.0'
                        } km/h`;


                    document.getElementById(
                        'dato-latitud'
                    ).textContent =
                        punto[0]
                            .toFixed(7);


                    document.getElementById(
                        'dato-longitud'
                    ).textContent =
                        punto[1]
                            .toFixed(7);


                    if (centrar) {

                        map.panTo(
                            punto,
                            {
                                animate:
                                    true,

                                duration:
                                    0.25
                            }
                        );
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | INTERVALO DE REPRODUCCIÓN
                |--------------------------------------------------------------------------
                */

                function obtenerIntervalo()
                {
                    const multiplicador =
                        parseFloat(
                            selectorVelocidad
                                .value
                        )
                        ||
                        1;


                    return 500
                        /
                        multiplicador;
                }


                /*
                |--------------------------------------------------------------------------
                | BOTÓN PLAY
                |--------------------------------------------------------------------------
                */

                function actualizarBoton()
                {
                    document.getElementById(
                        'icono-reproducir'
                    ).textContent =
                        reproduciendo
                            ?
                            'pause'
                            :
                            'play_arrow';


                    document.getElementById(
                        'texto-reproducir'
                    ).textContent =
                        reproduciendo
                            ?
                            'Pausar'
                            :
                            'Reproducir';


                    if (reproduciendo) {

                        estadoReproductor.textContent =
                            'Reproduciendo';

                        estadoReproductor.className =
                            'rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700';

                    } else {

                        estadoReproductor.textContent =
                            indice
                            >=
                            ruta.length - 1
                                ?
                                'Finalizado'
                                :
                                'Pausado';

                        estadoReproductor.className =
                            'rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-600';
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | DETENER
                |--------------------------------------------------------------------------
                */

                function detener()
                {
                    reproduciendo =
                        false;


                    if (temporizador) {

                        clearInterval(
                            temporizador
                        );

                        temporizador =
                            null;
                    }


                    actualizarBoton();
                }


                /*
                |--------------------------------------------------------------------------
                | REPRODUCIR
                |--------------------------------------------------------------------------
                */

                function reproducir()
                {
                    if (
                        ruta.length < 2
                    ) {

                        return;
                    }


                    if (
                        indice
                        >=
                        ruta.length - 1
                    ) {

                        mostrarPunto(
                            0
                        );
                    }


                    reproduciendo =
                        true;


                    actualizarBoton();


                    temporizador =
                        setInterval(
                            function () {

                                if (
                                    indice
                                    >=
                                    ruta.length - 1
                                ) {

                                    detener();

                                    return;
                                }


                                mostrarPunto(
                                    indice + 1
                                );

                            },
                            obtenerIntervalo()
                        );
                }


                /*
                |--------------------------------------------------------------------------
                | PLAY / PAUSA
                |--------------------------------------------------------------------------
                */

                btnPlay.addEventListener(
                    'click',
                    function () {

                        if (reproduciendo) {

                            detener();

                        } else {

                            reproducir();
                        }
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | INICIO
                |--------------------------------------------------------------------------
                */

                btnInicio.addEventListener(
                    'click',
                    function () {

                        detener();

                        mostrarPunto(
                            0
                        );
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | ANTERIOR
                |--------------------------------------------------------------------------
                */

                btnAnterior.addEventListener(
                    'click',
                    function () {

                        detener();

                        mostrarPunto(
                            indice - 1
                        );
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | SIGUIENTE
                |--------------------------------------------------------------------------
                */

                btnSiguiente.addEventListener(
                    'click',
                    function () {

                        detener();

                        mostrarPunto(
                            indice + 1
                        );
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | FINAL
                |--------------------------------------------------------------------------
                */

                btnFinal.addEventListener(
                    'click',
                    function () {

                        detener();

                        mostrarPunto(
                            ruta.length - 1
                        );
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | BARRA DE PROGRESO
                |--------------------------------------------------------------------------
                */

                barra.addEventListener(
                    'input',
                    function () {

                        detener();


                        mostrarPunto(
                            parseInt(
                                this.value,
                                10
                            )
                            ||
                            0
                        );
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | VELOCIDAD
                |--------------------------------------------------------------------------
                */

                selectorVelocidad.addEventListener(
                    'change',
                    function () {

                        if (reproduciendo) {

                            if (temporizador) {

                                clearInterval(
                                    temporizador
                                );
                            }


                            temporizador =
                                setInterval(
                                    function () {

                                        if (
                                            indice
                                            >=
                                            ruta.length - 1
                                        ) {

                                            detener();

                                            return;
                                        }


                                        mostrarPunto(
                                            indice + 1
                                        );

                                    },
                                    obtenerIntervalo()
                                );
                        }
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | INICIALIZAR
                |--------------------------------------------------------------------------
                */

                mostrarPunto(
                    0,
                    false
                );


                estadoReproductor.textContent =
                    'Listo';


                setTimeout(
                    function () {

                        map.invalidateSize();

                    },
                    150
                );
            }
        );

    </script>


    <style>

        .sotygeo-marker {
            background: transparent !important;
            border: none !important;
        }

    </style>

</x-app-layout>