<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Seguimiento de vehículo | SotyGeo
    </title>


    {{-- FUENTES --}}

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;500;600;700&family=Ubuntu:wght@300;400;500;700&display=swap"
        rel="stylesheet"
    >


    {{-- LEAFLET --}}

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

        * {
            box-sizing: border-box;
        }


        html,
        body {
            margin: 0;
            min-height: 100%;
        }


        body {
            font-family: 'Ubuntu', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }


        .font-josefin {
            font-family: 'Josefin Sans', sans-serif;
        }


        .vehiculo-marker {
            position: relative;
            width: 44px;
            height: 44px;
        }


        .vehiculo-marker-pulse {
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            background: rgba(0, 86, 179, 0.25);
            animation: pulse 2s ease-out infinite;
        }


        .vehiculo-marker-circle {
            position: absolute;
            inset: 5px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 9999px;

            background: #0056b3;

            border: 3px solid white;

            box-shadow:
                0 5px 18px rgba(0, 0, 0, 0.25);
        }


        .vehiculo-marker.offline
        .vehiculo-marker-pulse {
            display: none;
        }


        .vehiculo-marker.offline
        .vehiculo-marker-circle {
            background: #64748b;
        }


        @keyframes pulse {

            0% {
                transform: scale(0.65);
                opacity: 0.9;
            }

            100% {
                transform: scale(1.8);
                opacity: 0;
            }
        }


        .leaflet-popup-content-wrapper {
            border-radius: 14px;
            font-family: 'Ubuntu', sans-serif;
        }


        .leaflet-popup-content {
            margin: 14px;
        }


        @media (max-width: 700px) {

            .info-grid {
                grid-template-columns: 1fr 1fr !important;
            }

            .header-content {
                align-items: flex-start !important;
                flex-direction: column;
            }
        }

    </style>

</head>


<body>

    {{-- ====================================================== --}}
    {{-- HEADER --}}
    {{-- ====================================================== --}}

    <header
        style="
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        "
    >

        <div
            class="header-content"
            style="
                max-width: 1500px;
                margin: 0 auto;
                min-height: 76px;
                padding: 14px 22px;

                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 20px;
            "
        >

            <div
                style="
                    display: flex;
                    align-items: center;
                    gap: 12px;
                "
            >

                <div
                    style="
                        width: 44px;
                        height: 44px;

                        display: flex;
                        align-items: center;
                        justify-content: center;

                        border-radius: 12px;

                        background: #0056b3;
                        color: white;
                    "
                >

                    <svg
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M10 13a5 5 0 007.07 0l2.12-2.12a5 5 0 00-7.07-7.07L11 4.93"
                        ></path>

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M14 11a5 5 0 00-7.07 0l-2.12 2.12a5 5 0 007.07 7.07L13 19.07"
                        ></path>

                    </svg>

                </div>


                <div>

                    <div
                        class="font-josefin"
                        style="
                            font-size: 23px;
                            font-weight: 700;
                            color: #0056b3;
                        "
                    >
                        SotyGeo
                    </div>

                    <div
                        style="
                            font-size: 12px;
                            color: #64748b;
                        "
                    >
                        Seguimiento temporal
                    </div>

                </div>

            </div>


            <div
                style="
                    display: flex;
                    align-items: center;
                    gap: 8px;

                    padding: 8px 12px;

                    border: 1px solid #dbeafe;
                    border-radius: 9999px;

                    background: #eff6ff;
                    color: #0056b3;

                    font-size: 12px;
                    font-weight: 500;
                "
            >

                <span
                    id="punto-estado"
                    style="
                        width: 8px;
                        height: 8px;
                        border-radius: 9999px;
                        background: #94a3b8;
                    "
                ></span>

                <span id="estado-general">
                    Conectando...
                </span>

            </div>

        </div>

    </header>


    {{-- ====================================================== --}}
    {{-- CONTENIDO --}}
    {{-- ====================================================== --}}

    <main
        style="
            max-width: 1500px;
            margin: 0 auto;
            padding: 22px;
        "
    >

        {{-- VEHÍCULO --}}

        <section
            style="
                margin-bottom: 16px;

                padding: 20px;

                border: 1px solid #dbeafe;
                border-radius: 18px;

                background: #0056b3;
                color: white;

                box-shadow:
                    0 4px 16px rgba(15, 23, 42, 0.06);
            "
        >

            <div
                style="
                    display: flex;
                    align-items: center;
                    gap: 15px;
                "
            >

                <div
                    style="
                        width: 50px;
                        height: 50px;

                        display: flex;
                        align-items: center;
                        justify-content: center;

                        flex-shrink: 0;

                        border-radius: 14px;

                        background: rgba(255,255,255,0.15);
                    "
                >

                    <svg
                        width="27"
                        height="27"
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


                <div>

                    <div
                        style="
                            font-size: 12px;
                            color: #bfdbfe;
                            margin-bottom: 4px;
                        "
                    >
                        Ubicación compartida de
                    </div>

                    <h1
                        class="font-josefin"
                        style="
                            margin: 0;
                            font-size: 22px;
                            font-weight: 600;
                        "
                    >
                        {{ $enlace->vehiculo->nombre }}
                    </h1>


                    @if(
                        $enlace->vehiculo->marca
                        ||
                        $enlace->vehiculo->modelo
                    )

                        <div
                            style="
                                margin-top: 4px;
                                font-size: 13px;
                                color: #dbeafe;
                            "
                        >
                            {{ $enlace->vehiculo->marca }}
                            {{ $enlace->vehiculo->modelo }}
                        </div>

                    @endif

                </div>

            </div>

        </section>


        {{-- ====================================================== --}}
        {{-- INFORMACIÓN --}}
        {{-- ====================================================== --}}

        <section
            class="info-grid"
            style="
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 14px;

                margin-bottom: 16px;
            "
        >

            {{-- ESTADO --}}

            <div
                style="
                    padding: 16px;

                    border-radius: 16px;
                    border: 1px solid #e2e8f0;

                    background: white;
                "
            >

                <div
                    style="
                        margin-bottom: 7px;
                        font-size: 11px;
                        font-weight: 500;
                        text-transform: uppercase;
                        color: #64748b;
                    "
                >
                    Estado
                </div>


                <div
                    id="dato-estado"
                    style="
                        font-size: 15px;
                        font-weight: 600;
                        color: #334155;
                    "
                >
                    Consultando...
                </div>

            </div>


            {{-- VELOCIDAD --}}

            <div
                style="
                    padding: 16px;

                    border-radius: 16px;
                    border: 1px solid #e2e8f0;

                    background: white;
                "
            >

                <div
                    style="
                        margin-bottom: 7px;
                        font-size: 11px;
                        font-weight: 500;
                        text-transform: uppercase;
                        color: #64748b;
                    "
                >
                    Velocidad
                </div>


                <div
                    id="dato-velocidad"
                    style="
                        font-size: 15px;
                        font-weight: 600;
                        color: #334155;
                    "
                >
                    —
                </div>

            </div>


            {{-- ÚLTIMO REPORTE --}}

            <div
                style="
                    padding: 16px;

                    border-radius: 16px;
                    border: 1px solid #e2e8f0;

                    background: white;
                "
            >

                <div
                    style="
                        margin-bottom: 7px;
                        font-size: 11px;
                        font-weight: 500;
                        text-transform: uppercase;
                        color: #64748b;
                    "
                >
                    Último reporte
                </div>


                <div
                    id="dato-reporte"
                    style="
                        font-size: 15px;
                        font-weight: 600;
                        color: #334155;
                    "
                >
                    —
                </div>

            </div>


            {{-- EXPIRACIÓN --}}

            <div
                style="
                    padding: 16px;

                    border-radius: 16px;
                    border: 1px solid #e2e8f0;

                    background: white;
                "
            >

                <div
                    style="
                        margin-bottom: 7px;
                        font-size: 11px;
                        font-weight: 500;
                        text-transform: uppercase;
                        color: #64748b;
                    "
                >
                    Enlace disponible
                </div>


                <div
                    id="dato-expiracion"
                    style="
                        font-size: 15px;
                        font-weight: 600;
                        color: #334155;
                    "
                >
                    —
                </div>

            </div>

        </section>


        {{-- ====================================================== --}}
        {{-- MAPA --}}
        {{-- ====================================================== --}}

        <section
            style="
                overflow: hidden;

                border: 1px solid #e2e8f0;
                border-radius: 18px;

                background: white;

                box-shadow:
                    0 4px 16px rgba(15, 23, 42, 0.05);
            "
        >

            <div
                style="
                    padding: 15px 18px;

                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 10px;

                    border-bottom: 1px solid #e2e8f0;
                "
            >

                <div>

                    <div
                        class="font-josefin"
                        style="
                            font-weight: 600;
                            color: #003d82;
                        "
                    >
                        Ubicación del vehículo
                    </div>


                    <div
                        style="
                            margin-top: 3px;
                            font-size: 11px;
                            color: #64748b;
                        "
                    >
                        La posición se actualiza automáticamente cada 5 segundos.
                    </div>

                </div>


                <div
                    id="ultima-actualizacion"
                    style="
                        font-size: 11px;
                        color: #64748b;
                        white-space: nowrap;
                    "
                >
                    Esperando datos...
                </div>

            </div>


            <div
                id="mapa"
                style="
                    width: 100%;
                    height: min(68vh, 650px);
                    min-height: 420px;
                "
            ></div>

        </section>


        {{-- ====================================================== --}}
        {{-- AVISO --}}
        {{-- ====================================================== --}}

        <div
            style="
                margin-top: 16px;

                padding: 14px 16px;

                display: flex;
                align-items: flex-start;
                gap: 10px;

                border-radius: 14px;
                border: 1px solid #e2e8f0;

                background: white;

                color: #64748b;
                font-size: 12px;
                line-height: 1.5;
            "
        >

            <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#0056b3"
                stroke-width="2"
                style="flex-shrink:0;"
            >

                <circle
                    cx="12"
                    cy="12"
                    r="10"
                ></circle>

                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 16v-4M12 8h.01"
                ></path>

            </svg>


            <div>
                Este enlace únicamente permite consultar temporalmente la ubicación compartida del vehículo.
                El acceso se desactivará automáticamente al llegar a su fecha de expiración.
            </div>

        </div>

    </main>


    {{-- ====================================================== --}}
    {{-- JAVASCRIPT --}}
    {{-- ====================================================== --}}

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                /*
                |--------------------------------------------------------------------------
                | CONFIGURACIÓN
                |--------------------------------------------------------------------------
                */

                const urlUbicacion =
                    @json(
                        route(
                            'seguimiento.ubicacion',
                            $enlace->token
                        )
                    );


                const fechaExpiracion =
                    new Date(
                        @json(
                            $enlace->fecha_expiracion
                        )
                    );


                /*
                |--------------------------------------------------------------------------
                | MAPA
                |--------------------------------------------------------------------------
                */

                const mapa =
                    L.map(
                        'mapa'
                    )
                    .setView(
                        [
                            18.8814,
                            -99.1764
                        ],
                        14
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
                .addTo(mapa);


                let marcador =
                    null;


                let primeraUbicacion =
                    true;


                let intervaloUbicacion =
                    null;


                let ultimaConsulta =
                    null;


                /*
                |--------------------------------------------------------------------------
                | ICONO
                |--------------------------------------------------------------------------
                */

                function crearIcono(
                    enLinea
                ) {

                    const clase =
                        enLinea
                            ?
                            ''
                            :
                            'offline';


                    return L.divIcon({

                        className:
                            '',

                        html:
                            `
                                <div class="vehiculo-marker ${clase}">

                                    <div class="vehiculo-marker-pulse"></div>

                                    <div class="vehiculo-marker-circle">

                                        <svg
                                            width="19"
                                            height="19"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="white"
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
                                44,
                                44
                            ],

                        iconAnchor:
                            [
                                22,
                                22
                            ],

                        popupAnchor:
                            [
                                0,
                                -23
                            ]
                    });
                }


                /*
                |--------------------------------------------------------------------------
                | FORMATEAR FECHA
                |--------------------------------------------------------------------------
                */

                function formatearFecha(
                    valor
                ) {

                    if (!valor) {
                        return '—';
                    }


                    const fecha =
                        new Date(
                            valor
                        );


                    if (
                        Number.isNaN(
                            fecha.getTime()
                        )
                    ) {

                        return '—';
                    }


                    return fecha.toLocaleString(
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
                                '2-digit'
                        }
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | ESTADO
                |--------------------------------------------------------------------------
                */

                function cambiarEstado(
                    enLinea
                ) {

                    const texto =
                        document.getElementById(
                            'estado-general'
                        );


                    const punto =
                        document.getElementById(
                            'punto-estado'
                        );


                    const datoEstado =
                        document.getElementById(
                            'dato-estado'
                        );


                    if (enLinea) {

                        texto.textContent =
                            'En línea';

                        punto.style.background =
                            '#10b981';

                        datoEstado.textContent =
                            'En línea';

                        datoEstado.style.color =
                            '#047857';

                    } else {

                        texto.textContent =
                            'Sin reporte reciente';

                        punto.style.background =
                            '#94a3b8';

                        datoEstado.textContent =
                            'Sin reporte reciente';

                        datoEstado.style.color =
                            '#64748b';
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | CONSULTAR UBICACIÓN
                |--------------------------------------------------------------------------
                */

                async function consultarUbicacion()
                {
                    try {

                        const response =
                            await fetch(
                                urlUbicacion,
                                {
                                    cache:
                                        'no-store'
                                }
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | ENLACE EXPIRADO
                        |--------------------------------------------------------------------------
                        */

                        if (
                            response.status
                            ===
                            410
                        ) {

                            detenerSeguimiento();


                            document.getElementById(
                                'estado-general'
                            ).textContent =
                                'Enlace expirado';


                            document.getElementById(
                                'dato-estado'
                            ).textContent =
                                'Enlace expirado';


                            document.getElementById(
                                'punto-estado'
                            ).style.background =
                                '#ef4444';


                            setTimeout(
                                function () {

                                    window.location.reload();

                                },
                                1500
                            );


                            return;
                        }


                        if (!response.ok) {

                            throw new Error(
                                'No fue posible consultar la ubicación.'
                            );
                        }


                        const data =
                            await response.json();


                        /*
                        |--------------------------------------------------------------------------
                        | SIN UBICACIÓN
                        |--------------------------------------------------------------------------
                        */

                        if (
                            data.estado
                            ===
                            'sin_ubicacion'
                        ) {

                            document.getElementById(
                                'estado-general'
                            ).textContent =
                                'Esperando ubicación';


                            document.getElementById(
                                'dato-estado'
                            ).textContent =
                                'Sin ubicación disponible';


                            document.getElementById(
                                'dato-velocidad'
                            ).textContent =
                                '—';


                            document.getElementById(
                                'dato-reporte'
                            ).textContent =
                                '—';


                            ultimaConsulta =
                                Date.now();


                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | DATOS
                        |--------------------------------------------------------------------------
                        */

                        const latitud =
                            parseFloat(
                                data
                                    .ubicacion
                                    .latitud
                            );


                        const longitud =
                            parseFloat(
                                data
                                    .ubicacion
                                    .longitud
                            );


                        if (
                            !Number.isFinite(
                                latitud
                            )
                            ||
                            !Number.isFinite(
                                longitud
                            )
                        ) {

                            return;
                        }


                        cambiarEstado(
                            data.en_linea
                            ===
                            true
                        );


                        document.getElementById(
                            'dato-velocidad'
                        ).textContent =
                            `${
                                Number(
                                    data
                                        .ubicacion
                                        .velocidad
                                    ||
                                    0
                                ).toFixed(1)
                            } km/h`;


                        /*
                        |--------------------------------------------------------------------------
                        | ÚLTIMO REPORTE
                        |--------------------------------------------------------------------------
                        */

                        if (
                            data.minutos_sin_reporte
                            ===
                            0
                        ) {

                            document.getElementById(
                                'dato-reporte'
                            ).textContent =
                                'Hace menos de 1 min';

                        } else {

                            document.getElementById(
                                'dato-reporte'
                            ).textContent =
                                `Hace ${data.minutos_sin_reporte} min`;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | POPUP
                        |--------------------------------------------------------------------------
                        */

                        const nombre =
                            data.vehiculo.nombre
                            ||
                            'Vehículo';


                        const modelo =
                            [
                                data.vehiculo.marca,
                                data.vehiculo.modelo
                            ]
                            .filter(Boolean)
                            .join(' ');


                        const popup =
                            `
                                <div style="min-width:190px;">

                                    <div
                                        style="
                                            font-family:'Josefin Sans',sans-serif;
                                            font-size:15px;
                                            font-weight:600;
                                            color:#0056b3;
                                            margin-bottom:8px;
                                        "
                                    >
                                        ${nombre}
                                    </div>


                                    ${
                                        modelo
                                            ?
                                            `
                                                <div
                                                    style="
                                                        font-size:12px;
                                                        color:#64748b;
                                                        margin-bottom:5px;
                                                    "
                                                >
                                                    ${modelo}
                                                </div>
                                            `
                                            :
                                            ''
                                    }


                                    <div
                                        style="
                                            font-size:12px;
                                            color:#475569;
                                            margin-top:4px;
                                        "
                                    >
                                        Velocidad:
                                        <b>
                                            ${Number(
                                                data.ubicacion.velocidad || 0
                                            ).toFixed(1)} km/h
                                        </b>
                                    </div>


                                    <div
                                        style="
                                            margin-top:7px;
                                            padding-top:7px;
                                            border-top:1px solid #e2e8f0;
                                            font-size:10px;
                                            color:#94a3b8;
                                        "
                                    >
                                        ${formatearFecha(
                                            data.ubicacion.fecha_gps
                                        )}
                                    </div>

                                </div>
                            `;


                        /*
                        |--------------------------------------------------------------------------
                        | MARCADOR
                        |--------------------------------------------------------------------------
                        */

                        if (!marcador) {

                            marcador =
                                L.marker(
                                    [
                                        latitud,
                                        longitud
                                    ],
                                    {
                                        icon:
                                            crearIcono(
                                                data.en_linea
                                            )
                                    }
                                )
                                .addTo(
                                    mapa
                                )
                                .bindPopup(
                                    popup
                                );


                        } else {

                            marcador.setLatLng(
                                [
                                    latitud,
                                    longitud
                                ]
                            );


                            marcador.setIcon(
                                crearIcono(
                                    data.en_linea
                                )
                            );


                            marcador
                                .getPopup()
                                .setContent(
                                    popup
                                );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | CENTRAR PRIMERA VEZ
                        |--------------------------------------------------------------------------
                        */

                        if (
                            primeraUbicacion
                        ) {

                            mapa.setView(
                                [
                                    latitud,
                                    longitud
                                ],
                                17
                            );


                            primeraUbicacion =
                                false;
                        }


                        ultimaConsulta =
                            Date.now();

                    } catch (error) {

                        console.error(
                            error
                        );


                        document.getElementById(
                            'estado-general'
                        ).textContent =
                            'Sin conexión';


                        document.getElementById(
                            'punto-estado'
                        ).style.background =
                            '#ef4444';
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | CUENTA REGRESIVA
                |--------------------------------------------------------------------------
                */

                function actualizarExpiracion()
                {
                    const ahora =
                        new Date();


                    const diferencia =
                        fechaExpiracion.getTime()
                        -
                        ahora.getTime();


                    const elemento =
                        document.getElementById(
                            'dato-expiracion'
                        );


                    if (
                        diferencia <= 0
                    ) {

                        elemento.textContent =
                            'Expirado';

                        elemento.style.color =
                            '#dc2626';


                        return;
                    }


                    const minutosTotales =
                        Math.floor(
                            diferencia
                            /
                            60000
                        );


                    const dias =
                        Math.floor(
                            minutosTotales
                            /
                            1440
                        );


                    const horas =
                        Math.floor(
                            (
                                minutosTotales
                                %
                                1440
                            )
                            /
                            60
                        );


                    const minutos =
                        minutosTotales
                        %
                        60;


                    let texto =
                        '';


                    if (
                        dias > 0
                    ) {

                        texto +=
                            `${dias} d `;

                    }


                    if (
                        horas > 0
                        ||
                        dias > 0
                    ) {

                        texto +=
                            `${horas} h `;
                    }


                    texto +=
                        `${minutos} min`;


                    elemento.textContent =
                        texto;
                }


                /*
                |--------------------------------------------------------------------------
                | ÚLTIMA CONSULTA DEL SISTEMA
                |--------------------------------------------------------------------------
                */

                function actualizarTextoConsulta()
                {
                    if (
                        !ultimaConsulta
                    ) {

                        return;
                    }


                    const segundos =
                        Math.floor(
                            (
                                Date.now()
                                -
                                ultimaConsulta
                            )
                            /
                            1000
                        );


                    const elemento =
                        document.getElementById(
                            'ultima-actualizacion'
                        );


                    elemento.textContent =
                        segundos < 2
                            ?
                            'Actualizado ahora'
                            :
                            `Actualizado hace ${segundos}s`;
                }


                /*
                |--------------------------------------------------------------------------
                | DETENER SEGUIMIENTO
                |--------------------------------------------------------------------------
                */

                function detenerSeguimiento()
                {
                    if (
                        intervaloUbicacion
                    ) {

                        clearInterval(
                            intervaloUbicacion
                        );


                        intervaloUbicacion =
                            null;
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | INICIAR
                |--------------------------------------------------------------------------
                */

                consultarUbicacion();

                actualizarExpiracion();


                intervaloUbicacion =
                    setInterval(
                        consultarUbicacion,
                        5000
                    );


                setInterval(
                    actualizarExpiracion,
                    30000
                );


                setInterval(
                    actualizarTextoConsulta,
                    1000
                );


                /*
                |--------------------------------------------------------------------------
                | CORREGIR TAMAÑO DEL MAPA
                |--------------------------------------------------------------------------
                */

                setTimeout(
                    function () {

                        mapa.invalidateSize();

                    },
                    250
                );

            }
        );

    </script>

</body>

</html>