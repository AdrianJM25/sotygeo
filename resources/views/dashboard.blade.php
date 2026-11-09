<x-app-layout>

    {{-- ====================================================== --}}
    {{-- FUENTES --}}
    {{-- ====================================================== --}}

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;600;700&family=Ubuntu:wght@300;400;500;700&display=swap"
        rel="stylesheet"
    >


    {{-- ====================================================== --}}
    {{-- LEAFLET --}}
    {{-- ====================================================== --}}

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""
    >

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""
    ></script>


    <style>

        :root {
            --sg-blanco: #ffffff;
            --sg-fondo: #f8fafc;
            --sg-borde: #e2e8f0;

            --sg-azul:
                #0056b3;

            --sg-azul-oscuro:
                #003d82;

            --sg-amarillo:
                #f59e0b;

            --sg-gris:
                #94a3b8;
        }


        .font-josefin {
            font-family:
                'Josefin Sans',
                sans-serif;
        }


        .font-ubuntu {
            font-family:
                'Ubuntu',
                sans-serif;
        }


        body {
            font-family:
                'Ubuntu',
                sans-serif;

            background:
                var(--sg-fondo);
        }


        @keyframes cardIn {

            from {
                opacity: 0;
                transform:
                    translateY(10px);
            }

            to {
                opacity: 1;
                transform:
                    translateY(0);
            }
        }


        .card-in {
            animation:
                cardIn
                0.45s
                ease-out
                both;
        }


        .card-in:nth-child(1) {
            animation-delay:
                0.04s;
        }


        .card-in:nth-child(2) {
            animation-delay:
                0.08s;
        }


        .card-in:nth-child(3) {
            animation-delay:
                0.12s;
        }


        .card-in:nth-child(4) {
            animation-delay:
                0.16s;
        }


        @keyframes kpiFlash {

            0% {
                background-color:
                    #e6f0fa;
            }

            100% {
                background-color:
                    #ffffff;
            }
        }


        .kpi-flash {
            animation:
                kpiFlash
                0.7s
                ease;
        }


        /*
        |--------------------------------------------------------------------------
        | MARCADOR
        |--------------------------------------------------------------------------
        */

        .vehiculo-marker {
            position:
                relative;

            width:
                34px;

            height:
                34px;
        }


        .vehiculo-marker-pulse {
            position:
                absolute;

            inset:
                0;

            border-radius:
                9999px;

            animation:
                markerPulse
                2.2s
                ease-out
                infinite;
        }


        .vehiculo-marker.movimiento
        .vehiculo-marker-pulse {

            background:
                rgba(
                    0,
                    86,
                    179,
                    0.25
                );
        }


        .vehiculo-marker.detenido
        .vehiculo-marker-pulse {

            display:
                none;
        }


        .vehiculo-marker.sin-conexion
        .vehiculo-marker-pulse {

            display:
                none;
        }


        .vehiculo-marker-dot {
            position:
                absolute;

            inset:
                7px;

            border-radius:
                9999px;

            background:
                white;

            border:
                2px solid
                var(--sg-azul);

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            box-shadow:
                0
                2px
                6px
                rgba(
                    0,
                    0,
                    0,
                    0.15
                );
        }


        .vehiculo-marker.movimiento
        .vehiculo-marker-dot {

            border-color:
                var(--sg-azul);
        }


        .vehiculo-marker.movimiento
        .vehiculo-marker-dot svg {

            stroke:
                var(--sg-azul);
        }


        .vehiculo-marker.detenido
        .vehiculo-marker-dot {

            border-color:
                var(--sg-amarillo);
        }


        .vehiculo-marker.detenido
        .vehiculo-marker-dot svg {

            stroke:
                var(--sg-amarillo);
        }


        .vehiculo-marker.sin-conexion
        .vehiculo-marker-dot {

            border-color:
                var(--sg-gris);

            background:
                #f8fafc;
        }


        .vehiculo-marker.sin-conexion
        .vehiculo-marker-dot svg {

            stroke:
                #64748b;
        }


        @keyframes markerPulse {

            0% {
                transform:
                    scale(0.6);

                opacity:
                    0.9;
            }

            100% {
                transform:
                    scale(1.9);

                opacity:
                    0;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | POPUP
        |--------------------------------------------------------------------------
        */

        .sotygeo-popup
        .leaflet-popup-content-wrapper {

            border-radius:
                1rem;

            padding:
                0;

            background:
                white;

            border:
                1px solid
                var(--sg-borde);

            box-shadow:
                0
                10px
                25px
                -5px
                rgba(
                    0,
                    0,
                    0,
                    0.1
                );

            font-family:
                'Ubuntu',
                sans-serif;
        }


        .sotygeo-popup
        .leaflet-popup-content {

            margin:
                0;

            width:
                235px
                !important;
        }


        .zona-tooltip {
            background:
                white
                !important;

            border:
                1px solid
                var(--sg-azul)
                !important;

            border-radius:
                0.5rem
                !important;

            color:
                var(--sg-azul-oscuro)
                !important;

            font-weight:
                600;

            font-size:
                0.75rem;

            padding:
                4px 10px
                !important;

            box-shadow:
                0
                4px
                10px
                rgba(
                    0,
                    0,
                    0,
                    0.1
                );
        }


        .zona-tooltip::before {
            display:
                none;
        }


        /*
        |--------------------------------------------------------------------------
        | GRÁFICA
        |--------------------------------------------------------------------------
        */

        .grafica-operativa {
            width:
                92px;

            height:
                92px;

            border-radius:
                9999px;

            background:
                conic-gradient(
                    #e2e8f0
                    0%
                    100%
                );

            position:
                relative;

            flex-shrink:
                0;
        }


        .grafica-operativa::after {
            content:
                '';

            position:
                absolute;

            inset:
                17px;

            background:
                white;

            border-radius:
                9999px;
        }


        .grafica-operativa-centro {
            position:
                absolute;

            inset:
                0;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            z-index:
                2;

            font-family:
                'Josefin Sans',
                sans-serif;

            font-weight:
                700;

            color:
                #003d82;
        }


        .scrollbar-hide::-webkit-scrollbar {
            display:
                none;
        }


        .scrollbar-hide {
            scrollbar-width:
                none;
        }


        @media (
            prefers-reduced-motion:
            reduce
        ) {

            .card-in,
            .vehiculo-marker-pulse,
            .kpi-flash {

                animation:
                    none;
            }
        }

    </style>


    <div
        class="flex flex-col gap-4
               min-h-[500px]
               h-[calc(100vh-6rem)]
               font-ubuntu"
    >

        {{-- ====================================================== --}}
        {{-- KPIs --}}
        {{-- ====================================================== --}}

        <div
            class="grid grid-cols-2
                   lg:grid-cols-4
                   gap-4 shrink-0"
        >

            {{-- TOTAL --}}

            <div
                class="kpi-card card-in
                       bg-white
                       border border-slate-200
                       p-4 rounded-2xl
                       shadow-sm
                       flex items-center
                       justify-between"
            >

                <div>

                    <p
                        class="text-sm
                               text-slate-500
                               font-josefin
                               font-medium"
                    >
                        Unidades Totales
                    </p>

                    <h3
                        id="kpi-total"
                        data-valor="0"

                        class="text-3xl
                               font-bold
                               text-[#0056b3]
                               mt-1
                               font-josefin"
                    >
                        0
                    </h3>

                </div>


                <div
                    class="p-3 rounded-xl
                           border border-blue-100
                           bg-blue-50
                           text-[#0056b3]"
                >

                    <span
                        class="material-symbols-rounded
                               text-[25px]"
                    >
                        directions_car
                    </span>

                </div>

            </div>


            {{-- MOVIMIENTO --}}

            <div
                class="kpi-card card-in
                       bg-white
                       border border-slate-200
                       p-4 rounded-2xl
                       shadow-sm
                       flex items-center
                       justify-between"
            >

                <div>

                    <p
                        class="text-sm
                               text-slate-500
                               font-josefin
                               font-medium"
                    >
                        En Movimiento
                    </p>

                    <h3
                        id="kpi-movimiento"
                        data-valor="0"

                        class="text-3xl
                               font-bold
                               text-[#0056b3]
                               mt-1
                               font-josefin"
                    >
                        0
                    </h3>

                </div>


                <div
                    class="p-3 rounded-xl
                           border border-blue-100
                           bg-blue-50
                           text-[#0056b3]"
                >

                    <span
                        class="material-symbols-rounded
                               text-[25px]"
                    >
                        speed
                    </span>

                </div>

            </div>


            {{-- DETENIDAS --}}

            <div
                class="kpi-card card-in
                       bg-white
                       border border-slate-200
                       p-4 rounded-2xl
                       shadow-sm
                       flex items-center
                       justify-between"
            >

                <div>

                    <p
                        class="text-sm
                               text-slate-500
                               font-josefin
                               font-medium"
                    >
                        Detenidas
                    </p>

                    <h3
                        id="kpi-detenidas"
                        data-valor="0"

                        class="text-3xl
                               font-bold
                               text-amber-500
                               mt-1
                               font-josefin"
                    >
                        0
                    </h3>

                </div>


                <div
                    class="p-3 rounded-xl
                           border border-amber-100
                           bg-amber-50
                           text-amber-500"
                >

                    <span
                        class="material-symbols-rounded
                               text-[25px]"
                    >
                        pause_circle
                    </span>

                </div>

            </div>


            {{-- SIN CONEXIÓN --}}

            <div
                class="kpi-card card-in
                       bg-white
                       border border-slate-200
                       p-4 rounded-2xl
                       shadow-sm
                       flex items-center
                       justify-between"
            >

                <div>

                    <p
                        class="text-sm
                               text-slate-500
                               font-josefin
                               font-medium"
                    >
                        Sin Conexión
                    </p>

                    <h3
                        id="kpi-sin-conexion"
                        data-valor="0"

                        class="text-3xl
                               font-bold
                               text-slate-500
                               mt-1
                               font-josefin"
                    >
                        0
                    </h3>

                </div>


                <div
                    class="p-3 rounded-xl
                           border border-slate-200
                           bg-slate-50
                           text-slate-500"
                >

                    <span
                        class="material-symbols-rounded
                               text-[25px]"
                    >
                        signal_disconnected
                    </span>

                </div>

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- MAPA + COLUMNA DERECHA --}}
        {{-- ====================================================== --}}

        <div
            class="flex flex-col
                   lg:flex-row
                   gap-4
                   flex-1
                   overflow-hidden"
        >

            {{-- ================================================== --}}
            {{-- MAPA --}}
            {{-- ================================================== --}}

            <div
                class="card-in bg-white
                       border border-slate-200
                       rounded-2xl shadow-sm
                       flex-1 overflow-hidden
                       relative flex flex-col
                       min-h-[400px]
                       lg:min-h-0"
            >

                <div
                    class="p-4
                           border-b border-slate-200
                           flex flex-wrap
                           justify-between
                           items-center
                           gap-3
                           bg-white
                           z-[400]
                           shrink-0"
                >

                    <h2
                        class="text-base
                               font-bold
                               text-[#0056b3]
                               font-josefin
                               flex items-center
                               gap-2"
                    >

                        <span
                            class="relative
                                   flex
                                   w-2.5 h-2.5"
                        >

                            <span
                                class="animate-ping
                                       absolute
                                       inline-flex
                                       h-full w-full
                                       rounded-full
                                       bg-[#0056b3]
                                       opacity-50"
                            ></span>

                            <span
                                class="relative
                                       inline-flex
                                       rounded-full
                                       h-2.5 w-2.5
                                       bg-[#0056b3]"
                            ></span>

                        </span>

                        Monitoreo Geográfico en Vivo

                    </h2>


                    <div
                        class="flex items-center
                               gap-3
                               text-xs"
                    >

                        <span
                            id="contador-zonas"
                            class="text-slate-500"
                        >
                            0 zonas
                        </span>


                        <span
                            id="estado-conexion"

                            class="inline-flex
                                   items-center gap-1.5
                                   px-2.5 py-1
                                   rounded-full
                                   bg-slate-50
                                   text-slate-500
                                   border border-slate-200"
                        >
                            <span
                                class="w-1.5 h-1.5
                                       rounded-full
                                       bg-slate-400"
                            ></span>

                            Conectando...
                        </span>


                        <span
                            class="hidden xl:inline
                                   text-slate-400"
                        >
                            Actualizado
                            <span
                                id="ultima-actualizacion"
                            >
                                esperando…
                            </span>
                        </span>

                    </div>

                </div>


                <div
                    id="mapa-rastreo"

                    class="w-full
                           flex-1
                           z-0
                           min-h-[300px]"
                ></div>

            </div>


            {{-- ================================================== --}}
            {{-- COLUMNA DERECHA --}}
            {{-- ================================================== --}}

            <div
                class="w-full
                       lg:w-96
                       flex flex-col
                       gap-4
                       shrink-0
                       min-h-0"
            >

                {{-- ============================================== --}}
                {{-- GRÁFICA --}}
                {{-- ============================================== --}}

                <div
                    class="card-in
                           bg-white
                           border border-slate-200
                           rounded-2xl
                           shadow-sm
                           p-4
                           shrink-0"
                >

                    <div
                        class="flex items-center
                               justify-between
                               mb-4"
                    >

                        <div>

                            <h2
                                class="text-base
                                       font-bold
                                       text-slate-800
                                       font-josefin"
                            >
                                Estado Operativo
                            </h2>

                            <p
                                class="text-xs
                                       text-slate-400
                                       mt-0.5"
                            >
                                Distribución actual de unidades
                            </p>

                        </div>

                        <span
                            class="material-symbols-rounded
                                   text-[#0056b3]"
                        >
                            donut_large
                        </span>

                    </div>


                    <div
                        class="flex items-center
                               gap-5"
                    >

                        <div
                            id="grafica-operativa"
                            class="grafica-operativa"
                        >

                            <div
                                id="grafica-total"
                                class="grafica-operativa-centro"
                            >
                                0
                            </div>

                        </div>


                        <div
                            class="flex-1
                                   space-y-2
                                   text-xs"
                        >

                            <div
                                class="flex
                                       justify-between
                                       items-center"
                            >

                                <span
                                    class="flex
                                           items-center
                                           gap-2
                                           text-slate-600"
                                >

                                    <span
                                        class="w-2.5 h-2.5
                                               rounded-full
                                               bg-[#0056b3]"
                                    ></span>

                                    Movimiento

                                </span>

                                <strong
                                    id="grafica-movimiento"
                                    class="text-slate-700"
                                >
                                    0
                                </strong>

                            </div>


                            <div
                                class="flex
                                       justify-between
                                       items-center"
                            >

                                <span
                                    class="flex
                                           items-center
                                           gap-2
                                           text-slate-600"
                                >

                                    <span
                                        class="w-2.5 h-2.5
                                               rounded-full
                                               bg-amber-500"
                                    ></span>

                                    Detenidas

                                </span>

                                <strong
                                    id="grafica-detenidas"
                                    class="text-slate-700"
                                >
                                    0
                                </strong>

                            </div>


                            <div
                                class="flex
                                       justify-between
                                       items-center"
                            >

                                <span
                                    class="flex
                                           items-center
                                           gap-2
                                           text-slate-600"
                                >

                                    <span
                                        class="w-2.5 h-2.5
                                               rounded-full
                                               bg-slate-400"
                                    ></span>

                                    Sin conexión

                                </span>

                                <strong
                                    id="grafica-sin-conexion"
                                    class="text-slate-700"
                                >
                                    0
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ============================================== --}}
                {{-- ALERTAS --}}
                {{-- ============================================== --}}

                <div
                    class="card-in
                           bg-white
                           border border-slate-200
                           rounded-2xl
                           shadow-sm
                           flex flex-col
                           flex-1
                           min-h-[250px]
                           overflow-hidden"
                >

                    <div
                        class="p-4
                               border-b
                               border-slate-200
                               flex
                               justify-between
                               items-center
                               bg-white
                               shrink-0"
                    >

                        <h2
                            class="text-base
                                   font-bold
                                   text-slate-800
                                   font-josefin
                                   flex items-center
                                   gap-2"
                        >

                            <span
                                class="material-symbols-rounded
                                       text-slate-400
                                       text-[20px]"
                            >
                                notifications
                            </span>

                            Alertas Recientes

                        </h2>


                        <span
                            id="contador-alertas"

                            class="bg-slate-100
                                   text-slate-600
                                   border
                                   border-slate-200
                                   text-xs
                                   font-bold
                                   px-2 py-0.5
                                   rounded-full"
                        >
                            0
                        </span>

                    </div>


                    <div
                        id="lista-alertas"

                        class="flex-1
                               overflow-y-auto
                               scrollbar-hide
                               p-4
                               space-y-3
                               bg-[#f8fafc]"
                    >

                        <div
                            class="text-center
                                   text-sm
                                   text-slate-500
                                   py-8"
                        >
                            Cargando alertas...
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                /*
                |--------------------------------------------------------------------------
                | MAPA
                |--------------------------------------------------------------------------
                */

                const map =
                    L.map(
                        'mapa-rastreo',
                        {
                            zoomControl:
                                false
                        }
                    )
                    .setView(
                        [
                            18.8814,
                            -99.1764
                        ],
                        14
                    );


                L.control
                    .zoom(
                        {
                            position:
                                'topright'
                        }
                    )
                    .addTo(
                        map
                    );


                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        maxZoom:
                            19,

                        attribution:
                            '© OpenStreetMap'
                    }
                )
                .addTo(
                    map
                );


                setTimeout(
                    function () {

                        map.invalidateSize();

                    },
                    300
                );


                /*
                |--------------------------------------------------------------------------
                | VARIABLES
                |--------------------------------------------------------------------------
                */

                const marcadores =
                    {};


                let ultimaActualizacionTs =
                    null;


                let capaZonas =
                    null;


                /*
                |--------------------------------------------------------------------------
                | ESCAPAR HTML
                |--------------------------------------------------------------------------
                */

                function escaparHtml(
                    valor
                ) {

                    if (
                        valor === null
                        ||
                        valor === undefined
                    ) {

                        return '';
                    }


                    return String(
                        valor
                    )
                        .replaceAll(
                            '&',
                            '&amp;'
                        )
                        .replaceAll(
                            '<',
                            '&lt;'
                        )
                        .replaceAll(
                            '>',
                            '&gt;'
                        )
                        .replaceAll(
                            '"',
                            '&quot;'
                        )
                        .replaceAll(
                            "'",
                            '&#039;'
                        );
                }


                /*
                |--------------------------------------------------------------------------
                | ICONO DEL VEHÍCULO
                |--------------------------------------------------------------------------
                */

                function crearIconoVehiculo(
                    estadoOperativo
                ) {

                    let claseEstado =
                        'sin-conexion';


                    if (
                        estadoOperativo
                        ===
                        'movimiento'
                    ) {

                        claseEstado =
                            'movimiento';

                    } else if (
                        estadoOperativo
                        ===
                        'detenido'
                    ) {

                        claseEstado =
                            'detenido';
                    }


                    return L.divIcon({

                        className:
                            '',

                        html:
                            `
                                <div class="vehiculo-marker ${claseEstado}">

                                    <div class="vehiculo-marker-pulse"></div>

                                    <div class="vehiculo-marker-dot">

                                        <svg
                                            width="14"
                                            height="14"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"
                                            ></path>

                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1"
                                            ></path>

                                        </svg>

                                    </div>

                                </div>
                            `,

                        iconSize:
                            [
                                34,
                                34
                            ],

                        iconAnchor:
                            [
                                17,
                                17
                            ],

                        popupAnchor:
                            [
                                0,
                                -22
                            ],
                    });
                }


                /*
                |--------------------------------------------------------------------------
                | ANIMACIÓN DE KPI
                |--------------------------------------------------------------------------
                */

                function animarConteo(
                    elemento,
                    hasta,
                    duracion
                ) {

                    if (
                        !elemento
                    ) {

                        return;
                    }


                    const desde =
                        parseInt(
                            elemento
                                .dataset
                                .valor
                            ||
                            '0',
                            10
                        );


                    elemento
                        .dataset
                        .valor =
                            hasta;


                    if (
                        desde
                        ===
                        hasta
                    ) {

                        elemento.innerText =
                            hasta;

                        return;
                    }


                    const tarjeta =
                        elemento.closest(
                            '.kpi-card'
                        );


                    if (
                        tarjeta
                    ) {

                        tarjeta
                            .classList
                            .remove(
                                'kpi-flash'
                            );


                        void tarjeta.offsetWidth;


                        tarjeta
                            .classList
                            .add(
                                'kpi-flash'
                            );
                    }


                    const inicio =
                        performance.now();


                    function paso(
                        ahora
                    ) {

                        const progreso =
                            Math.min(
                                (
                                    ahora
                                    -
                                    inicio
                                )
                                /
                                duracion,
                                1
                            );


                        elemento.innerText =
                            Math.round(
                                desde
                                +
                                (
                                    hasta
                                    -
                                    desde
                                )
                                *
                                progreso
                            );


                        if (
                            progreso < 1
                        ) {

                            requestAnimationFrame(
                                paso
                            );
                        }
                    }


                    requestAnimationFrame(
                        paso
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | ACTUALIZAR GRÁFICA
                |--------------------------------------------------------------------------
                */

                function actualizarGrafica(
                    movimiento,
                    detenidas,
                    sinConexion
                ) {

                    const total =
                        movimiento
                        +
                        detenidas
                        +
                        sinConexion;


                    document.getElementById(
                        'grafica-total'
                    ).innerText =
                        total;


                    document.getElementById(
                        'grafica-movimiento'
                    ).innerText =
                        movimiento;


                    document.getElementById(
                        'grafica-detenidas'
                    ).innerText =
                        detenidas;


                    document.getElementById(
                        'grafica-sin-conexion'
                    ).innerText =
                        sinConexion;


                    if (
                        total === 0
                    ) {

                        document.getElementById(
                            'grafica-operativa'
                        ).style.background =
                            '#e2e8f0';

                        return;
                    }


                    const porcentajeMovimiento =
                        (
                            movimiento
                            /
                            total
                        )
                        *
                        100;


                    const porcentajeDetenidas =
                        (
                            detenidas
                            /
                            total
                        )
                        *
                        100;


                    const corteMovimiento =
                        porcentajeMovimiento;


                    const corteDetenidas =
                        porcentajeMovimiento
                        +
                        porcentajeDetenidas;


                    document.getElementById(
                        'grafica-operativa'
                    ).style.background =
                        `
                            conic-gradient(
                                #0056b3
                                0%
                                ${corteMovimiento}%,

                                #f59e0b
                                ${corteMovimiento}%
                                ${corteDetenidas}%,

                                #94a3b8
                                ${corteDetenidas}%
                                100%
                            )
                        `;
                }


                /*
                |--------------------------------------------------------------------------
                | GEOCERCAS
                |--------------------------------------------------------------------------
                */

                function cargarZonas()
                {
                    fetch(
                        '{{ route("api.zonas.en-vivo") }}'
                    )
                    .then(
                        function (
                            response
                        ) {

                            if (
                                !response.ok
                            ) {

                                throw new Error(
                                    'No fue posible cargar las geocercas.'
                                );
                            }


                            return response.json();
                        }
                    )
                    .then(
                        function (
                            geojson
                        ) {

                            if (
                                capaZonas
                            ) {

                                map.removeLayer(
                                    capaZonas
                                );
                            }


                            capaZonas =
                                L.geoJSON(
                                    geojson,
                                    {

                                        style:
                                            function (
                                                feature
                                            ) {

                                                const color =
                                                    feature
                                                        .properties
                                                        .color
                                                    ||
                                                    '#0056b3';


                                                return {

                                                    color:
                                                        color,

                                                    weight:
                                                        2,

                                                    fillColor:
                                                        color,

                                                    fillOpacity:
                                                        0.10,

                                                    dashArray:
                                                        '6 4'
                                                };
                                            },


                                        onEachFeature:
                                            function (
                                                feature,
                                                layer
                                            ) {

                                                layer.bindTooltip(
                                                    feature
                                                        .properties
                                                        .nombre,
                                                    {
                                                        direction:
                                                            'center',

                                                        className:
                                                            'zona-tooltip'
                                                    }
                                                );
                                            }
                                    }
                                )
                                .addTo(
                                    map
                                );


                            const total =
                                Array.isArray(
                                    geojson.features
                                )
                                    ?
                                    geojson
                                        .features
                                        .length
                                    :
                                    0;


                            document.getElementById(
                                'contador-zonas'
                            ).innerText =
                                total
                                +
                                (
                                    total === 1
                                        ?
                                        ' zona'
                                        :
                                        ' zonas'
                                );
                        }
                    )
                    .catch(
                        function (
                            error
                        ) {

                            console.error(
                                'Error cargando zonas:',
                                error
                            );
                        }
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | VEHÍCULOS EN VIVO
                |--------------------------------------------------------------------------
                */

                function actualizarUbicaciones()
                {
                    fetch(
                        '{{ route("api.vehiculos.en-vivo") }}'
                    )
                    .then(
                        function (
                            response
                        ) {

                            if (
                                !response.ok
                            ) {

                                throw new Error(
                                    'No fue posible cargar los vehículos.'
                                );
                            }


                            return response.json();
                        }
                    )
                    .then(
                        function (
                            data
                        ) {

                            /*
                            |--------------------------------------------------------------------------
                            | SERVIDOR EN LÍNEA
                            |--------------------------------------------------------------------------
                            */

                            const estadoServidor =
                                document.getElementById(
                                    'estado-conexion'
                                );


                            estadoServidor.className =
                                'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 text-[#0056b3] border border-blue-100';


                            estadoServidor.innerHTML =
                                '<span class="w-1.5 h-1.5 rounded-full bg-[#0056b3]"></span> En Línea';


                            /*
                            |--------------------------------------------------------------------------
                            | CONTADORES
                            |--------------------------------------------------------------------------
                            */

                            let movimiento =
                                0;

                            let detenidas =
                                0;

                            let sinConexion =
                                0;


                            const idsActuales =
                                new Set();


                            data.forEach(
                                function (
                                    vehiculo
                                ) {

                                    idsActuales.add(
                                        String(
                                            vehiculo.id
                                        )
                                    );


                                    const estadoOperativo =
                                        vehiculo
                                            .estado_operativo
                                        ||
                                        'sin_conexion';


                                    if (
                                        estadoOperativo
                                        ===
                                        'movimiento'
                                    ) {

                                        movimiento++;

                                    } else if (
                                        estadoOperativo
                                        ===
                                        'detenido'
                                    ) {

                                        detenidas++;

                                    } else {

                                        sinConexion++;
                                    }


                                    /*
                                    |--------------------------------------------------------------------------
                                    | SIN UBICACIÓN
                                    |--------------------------------------------------------------------------
                                    */

                                    if (
                                        !vehiculo.dispositivo
                                        ||
                                        !Array.isArray(
                                            vehiculo
                                                .dispositivo
                                                .ubicaciones
                                        )
                                        ||
                                        vehiculo
                                            .dispositivo
                                            .ubicaciones
                                            .length
                                        ===
                                        0
                                    ) {

                                        if (
                                            marcadores[
                                                vehiculo.id
                                            ]
                                        ) {

                                            map.removeLayer(
                                                marcadores[
                                                    vehiculo.id
                                                ]
                                            );


                                            delete marcadores[
                                                vehiculo.id
                                            ];
                                        }


                                        return;
                                    }


                                    /*
                                    |--------------------------------------------------------------------------
                                    | ÚLTIMA UBICACIÓN
                                    |--------------------------------------------------------------------------
                                    */

                                    const ultimaUbicacion =
                                        vehiculo
                                            .dispositivo
                                            .ubicaciones[0];


                                    const lat =
                                        parseFloat(
                                            ultimaUbicacion
                                                .latitud
                                        );


                                    const lng =
                                        parseFloat(
                                            ultimaUbicacion
                                                .longitud
                                        );


                                    if (
                                        !Number.isFinite(
                                            lat
                                        )
                                        ||
                                        !Number.isFinite(
                                            lng
                                        )
                                    ) {

                                        return;
                                    }


                                    /*
                                    |--------------------------------------------------------------------------
                                    | TEXTO DEL ESTADO
                                    |--------------------------------------------------------------------------
                                    */

                                    let textoEstado =
                                        'Sin conexión';


                                    let claseEstado =
                                        'text-slate-600 bg-slate-50 border-slate-200';


                                    if (
                                        estadoOperativo
                                        ===
                                        'movimiento'
                                    ) {

                                        textoEstado =
                                            'En movimiento';


                                        claseEstado =
                                            'text-blue-700 bg-blue-50 border-blue-200';

                                    } else if (
                                        estadoOperativo
                                        ===
                                        'detenido'
                                    ) {

                                        textoEstado =
                                            'Detenido';


                                        claseEstado =
                                            'text-amber-700 bg-amber-50 border-amber-200';
                                    }


                                    /*
                                    |--------------------------------------------------------------------------
                                    | ÚLTIMO REPORTE
                                    |--------------------------------------------------------------------------
                                    */

                                    const minutos =
                                        vehiculo
                                            .minutos_sin_reporte;


                                    let textoUltimoReporte =
                                        'N/D';


                                    if (
                                        minutos
                                        !==
                                        null
                                        &&
                                        minutos
                                        !==
                                        undefined
                                    ) {

                                        textoUltimoReporte =
                                            minutos === 0
                                                ?
                                                'Hace menos de 1 min'
                                                :
                                                `Hace ${minutos} min`;
                                    }


                                    /*
                                    |--------------------------------------------------------------------------
                                    | DATOS
                                    |--------------------------------------------------------------------------
                                    */

                                    const nombre =
                                        escaparHtml(
                                            vehiculo.nombre
                                            ||
                                            'Vehículo'
                                        );


                                    const marca =
                                        escaparHtml(
                                            vehiculo.marca
                                            ||
                                            ''
                                        );


                                    const modelo =
                                        escaparHtml(
                                            vehiculo.modelo
                                            ||
                                            'N/D'
                                        );


                                    const bateria =
                                        ultimaUbicacion
                                            .porcentaje_bateria
                                        ??
                                        'N/D';


                                    const velocidad =
                                        ultimaUbicacion
                                            .velocidad
                                        ??
                                        0;


                                    const fechaGps =
                                        ultimaUbicacion
                                            .fecha_gps

                                            ?

                                            new Date(
                                                ultimaUbicacion
                                                    .fecha_gps
                                            )
                                            .toLocaleString(
                                                'es-MX'
                                            )

                                            :

                                            'N/D';


                                    /*
                                    |--------------------------------------------------------------------------
                                    | POPUP
                                    |--------------------------------------------------------------------------
                                    */

                                    const contenidoPopup =
                                        `
                                            <div class="font-ubuntu">

                                                <div
                                                    class="px-4 py-3
                                                           border-b border-gray-100
                                                           bg-[#f8fafc]
                                                           rounded-t-2xl"
                                                >

                                                    <b
                                                        class="text-[#0056b3]
                                                               text-sm
                                                               font-josefin"
                                                    >
                                                        ${nombre}
                                                    </b>

                                                </div>


                                                <div
                                                    class="px-4 py-3
                                                           text-xs
                                                           text-slate-600
                                                           space-y-2"
                                                >

                                                    <div
                                                        class="flex
                                                               justify-between
                                                               items-center"
                                                    >

                                                        <span
                                                            class="text-slate-400"
                                                        >
                                                            Estado
                                                        </span>

                                                        <span
                                                            class="inline-flex
                                                                   px-2 py-0.5
                                                                   rounded-full
                                                                   border
                                                                   font-medium
                                                                   ${claseEstado}"
                                                        >
                                                            ${textoEstado}
                                                        </span>

                                                    </div>


                                                    <div
                                                        class="flex
                                                               justify-between
                                                               gap-3"
                                                    >

                                                        <span
                                                            class="text-slate-400"
                                                        >
                                                            Modelo
                                                        </span>

                                                        <span
                                                            class="font-medium
                                                                   text-slate-700
                                                                   text-right"
                                                        >
                                                            ${marca} ${modelo}
                                                        </span>

                                                    </div>


                                                    <div
                                                        class="flex
                                                               justify-between"
                                                    >

                                                        <span
                                                            class="text-slate-400"
                                                        >
                                                            Batería
                                                        </span>

                                                        <span
                                                            class="font-medium
                                                                   text-slate-700"
                                                        >
                                                            ${bateria}%
                                                        </span>

                                                    </div>


                                                    <div
                                                        class="flex
                                                               justify-between"
                                                    >

                                                        <span
                                                            class="text-slate-400"
                                                        >
                                                            Velocidad
                                                        </span>

                                                        <span
                                                            class="font-medium
                                                                   text-slate-700"
                                                        >
                                                            ${velocidad} km/h
                                                        </span>

                                                    </div>


                                                    <div
                                                        class="flex
                                                               justify-between"
                                                    >

                                                        <span
                                                            class="text-slate-400"
                                                        >
                                                            Último reporte
                                                        </span>

                                                        <span
                                                            class="font-medium
                                                                   text-slate-700"
                                                        >
                                                            ${textoUltimoReporte}
                                                        </span>

                                                    </div>


                                                    <div
                                                        class="text-slate-400
                                                               text-[10px]
                                                               pt-2
                                                               border-t
                                                               border-gray-100"
                                                    >
                                                        GPS: ${fechaGps}
                                                    </div>

                                                </div>

                                            </div>
                                        `;


                                    /*
                                    |--------------------------------------------------------------------------
                                    | ACTUALIZAR / CREAR MARCADOR
                                    |--------------------------------------------------------------------------
                                    */

                                    if (
                                        marcadores[
                                            vehiculo.id
                                        ]
                                    ) {

                                        marcadores[
                                            vehiculo.id
                                        ]
                                        .setLatLng(
                                            [
                                                lat,
                                                lng
                                            ]
                                        );


                                        marcadores[
                                            vehiculo.id
                                        ]
                                        .setIcon(
                                            crearIconoVehiculo(
                                                estadoOperativo
                                            )
                                        );


                                        marcadores[
                                            vehiculo.id
                                        ]
                                        .getPopup()
                                        .setContent(
                                            contenidoPopup
                                        );

                                    } else {

                                        marcadores[
                                            vehiculo.id
                                        ] =
                                            L.marker(
                                                [
                                                    lat,
                                                    lng
                                                ],
                                                {
                                                    icon:
                                                        crearIconoVehiculo(
                                                            estadoOperativo
                                                        )
                                                }
                                            )
                                            .addTo(
                                                map
                                            )
                                            .bindPopup(
                                                contenidoPopup,
                                                {
                                                    className:
                                                        'sotygeo-popup'
                                                }
                                            );
                                    }
                                }
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | ELIMINAR MARCADORES QUE YA NO CORRESPONDEN
                            |--------------------------------------------------------------------------
                            */

                            Object.keys(
                                marcadores
                            )
                            .forEach(
                                function (
                                    id
                                ) {

                                    if (
                                        !idsActuales.has(
                                            String(
                                                id
                                            )
                                        )
                                    ) {

                                        map.removeLayer(
                                            marcadores[
                                                id
                                            ]
                                        );


                                        delete marcadores[
                                            id
                                        ];
                                    }
                                }
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | ACTUALIZAR KPIs
                            |--------------------------------------------------------------------------
                            */

                            animarConteo(
                                document.getElementById(
                                    'kpi-total'
                                ),
                                data.length,
                                500
                            );


                            animarConteo(
                                document.getElementById(
                                    'kpi-movimiento'
                                ),
                                movimiento,
                                500
                            );


                            animarConteo(
                                document.getElementById(
                                    'kpi-detenidas'
                                ),
                                detenidas,
                                500
                            );


                            animarConteo(
                                document.getElementById(
                                    'kpi-sin-conexion'
                                ),
                                sinConexion,
                                500
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | ACTUALIZAR GRÁFICA
                            |--------------------------------------------------------------------------
                            */

                            actualizarGrafica(
                                movimiento,
                                detenidas,
                                sinConexion
                            );


                            ultimaActualizacionTs =
                                Date.now();
                        }
                    )
                    .catch(
                        function (
                            error
                        ) {

                            console.error(
                                'Error cargando vehículos:',
                                error
                            );


                            const estadoServidor =
                                document.getElementById(
                                    'estado-conexion'
                                );


                            estadoServidor.className =
                                'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 border border-slate-200';


                            estadoServidor.innerHTML =
                                '<span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Desconectado';
                        }
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | ALERTAS
                |--------------------------------------------------------------------------
                */

                function cargarAlertas()
                {
                    fetch(
                        '{{ route("api.alertas.recientes") }}'
                    )
                    .then(
                        function (
                            response
                        ) {

                            if (
                                !response.ok
                            ) {

                                throw new Error(
                                    'No fue posible cargar las alertas.'
                                );
                            }


                            return response.json();
                        }
                    )
                    .then(
                        function (
                            alertas
                        ) {

                            const contenedor =
                                document.getElementById(
                                    'lista-alertas'
                                );


                            document.getElementById(
                                'contador-alertas'
                            ).innerText =
                                alertas.length;


                            if (
                                alertas.length
                                ===
                                0
                            ) {

                                contenedor.innerHTML =
                                    `
                                        <div
                                            class="text-center
                                                   text-sm
                                                   text-slate-400
                                                   py-8"
                                        >
                                            Sin alertas recientes
                                        </div>
                                    `;


                                return;
                            }


                            let html =
                                '';


                            alertas.forEach(
                                function (
                                    alerta
                                ) {

                                    const tipo =
                                        String(
                                            alerta.tipo
                                            ||
                                            ''
                                        );


                                    const esSalida =
                                        tipo.includes(
                                            'salida'
                                        );


                                    const color =
                                        esSalida
                                            ?
                                            'text-slate-600 bg-white border-slate-200'
                                            :
                                            'text-blue-600 bg-white border-blue-100';


                                    const icono =
                                        esSalida
                                            ?
                                            'logout'
                                            :
                                            'warning';


                                    const vehiculo =
                                        alerta.vehiculo
                                            ?
                                            escaparHtml(
                                                alerta
                                                    .vehiculo
                                                    .nombre
                                            )
                                            :
                                            'Vehículo desconocido';


                                    const mensaje =
                                        escaparHtml(
                                            alerta.mensaje
                                            ||
                                            'Alerta registrada.'
                                        );


                                    const fecha =
                                        alerta.created_at
                                            ?
                                            new Date(
                                                alerta.created_at
                                            )
                                            .toLocaleString(
                                                'es-MX'
                                            )
                                            :
                                            '';


                                    html +=
                                        `
                                            <div
                                                class="p-3
                                                       bg-white
                                                       border
                                                       border-slate-200
                                                       rounded-xl
                                                       shadow-sm
                                                       flex gap-3
                                                       hover:shadow-md
                                                       transition-shadow"
                                            >

                                                <div
                                                    class="w-8 h-8
                                                           rounded-full
                                                           ${color}
                                                           flex items-center
                                                           justify-center
                                                           border
                                                           shrink-0"
                                                >

                                                    <span
                                                        class="material-symbols-rounded
                                                               text-[17px]"
                                                    >
                                                        ${icono}
                                                    </span>

                                                </div>


                                                <div
                                                    class="flex-1
                                                           min-w-0"
                                                >

                                                    <p
                                                        class="text-sm
                                                               font-semibold
                                                               text-slate-800
                                                               font-josefin"
                                                    >
                                                        ${vehiculo}
                                                    </p>


                                                    <p
                                                        class="text-xs
                                                               text-slate-500
                                                               mt-0.5
                                                               leading-snug"
                                                    >
                                                        ${mensaje}
                                                    </p>


                                                    <span
                                                        class="text-[10px]
                                                               text-slate-400
                                                               font-medium
                                                               mt-1
                                                               block"
                                                    >
                                                        ${fecha}
                                                    </span>

                                                </div>

                                            </div>
                                        `;
                                }
                            );


                            contenedor.innerHTML =
                                html;
                        }
                    )
                    .catch(
                        function (
                            error
                        ) {

                            console.error(
                                'Error cargando alertas:',
                                error
                            );


                            document.getElementById(
                                'lista-alertas'
                            ).innerHTML =
                                `
                                    <div
                                        class="text-center
                                               text-sm
                                               text-red-500
                                               py-8"
                                    >
                                        No fue posible cargar las alertas.
                                    </div>
                                `;
                        }
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | TEXTO ÚLTIMA ACTUALIZACIÓN
                |--------------------------------------------------------------------------
                */

                setInterval(
                    function () {

                        const span =
                            document.getElementById(
                                'ultima-actualizacion'
                            );


                        if (
                            !span
                            ||
                            !ultimaActualizacionTs
                        ) {

                            return;
                        }


                        const segundos =
                            Math.floor(
                                (
                                    Date.now()
                                    -
                                    ultimaActualizacionTs
                                )
                                /
                                1000
                            );


                        span.innerText =
                            segundos < 2
                                ?
                                'justo ahora'
                                :
                                `hace ${segundos}s`;

                    },
                    1000
                );


                /*
                |--------------------------------------------------------------------------
                | CARGA INICIAL
                |--------------------------------------------------------------------------
                */

                cargarZonas();

                actualizarUbicaciones();

                cargarAlertas();


                /*
                |--------------------------------------------------------------------------
                | ACTUALIZACIÓN AUTOMÁTICA
                |--------------------------------------------------------------------------
                */

                setInterval(
                    actualizarUbicaciones,
                    5000
                );


                setInterval(
                    cargarAlertas,
                    10000
                );

            }
        );

    </script>

</x-app-layout>