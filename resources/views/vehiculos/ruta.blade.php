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
                | DATOS DEL CONTROLADOR
                |--------------------------------------------------------------------------
                |
                | La geometría de OSRM y los registros GPS se reciben por separado.
                | Así no confundimos los vértices de las calles con los reportes GPS.
                |
                */

                const ubicaciones =
                    @json($ubicaciones->values());

                const rutaServidor =
                    @json($rutaAjustada ?? []);

                const segmentosRutaServidor =
                    @json($segmentosRuta ?? []);

                const segmentosReproduccionServidor =
                    @json($segmentosReproduccion ?? []);

                const puntosReproduccionServidor =
                    @json($puntosReproduccion ?? []);

                const modoRuta =
                    @json($modoRuta ?? 'gps');


                /*
                |--------------------------------------------------------------------------
                | FUNCIONES DE VALIDACIÓN Y CONVERSIÓN
                |--------------------------------------------------------------------------
                */

                function numeroValido(valor)
                {
                    const numero = Number(valor);

                    return Number.isFinite(numero)
                        ? numero
                        : null;
                }


                function coordenadaValida(punto)
                {
                    if (
                        !Array.isArray(punto)
                        || punto.length < 2
                    ) {
                        return false;
                    }

                    const latitud = numeroValido(punto[0]);
                    const longitud = numeroValido(punto[1]);

                    return latitud !== null
                        && longitud !== null
                        && latitud >= -90
                        && latitud <= 90
                        && longitud >= -180
                        && longitud <= 180;
                }


                function normalizarGeometria(geometria)
                {
                    if (!Array.isArray(geometria)) {
                        return [];
                    }

                    return geometria
                        .filter(coordenadaValida)
                        .map(punto => [
                            Number(punto[0]),
                            Number(punto[1])
                        ]);
                }


                function normalizarRegistro(registro)
                {
                    if (!registro) {
                        return null;
                    }

                    const latitud = numeroValido(
                        registro.latitud
                    );

                    const longitud = numeroValido(
                        registro.longitud
                    );

                    if (
                        latitud === null
                        || longitud === null
                        || latitud < -90
                        || latitud > 90
                        || longitud < -180
                        || longitud > 180
                    ) {
                        return null;
                    }

                    const velocidad = numeroValido(
                        registro.velocidad
                    );

                    return {
                        id: registro.id ?? null,
                        latitud: latitud,
                        longitud: longitud,
                        fecha_gps: registro.fecha_gps ?? null,
                        velocidad: velocidad,
                        timestamp: registro.fecha_gps
                            ? new Date(registro.fecha_gps).getTime()
                            : null,
                        segmento: registro.segmento ?? null,
                        posicion: [latitud, longitud],
                        indiceGeometria: 0
                    };
                }


                /*
                |--------------------------------------------------------------------------
                | PREPARAR SEGMENTOS
                |--------------------------------------------------------------------------
                |
                | Cada segmento conserva su propia geometría y sus propios registros.
                | Nunca se conectan entre sí dos recorridos separados.
                |
                */

                let segmentosPreparados = [];

                if (
                    Array.isArray(segmentosRutaServidor)
                    && segmentosRutaServidor.length > 0
                ) {

                    segmentosPreparados = segmentosRutaServidor.map(
                        function (geometria, indice) {

                            const path = normalizarGeometria(
                                geometria
                            );

                            const registrosCrudos =
                                Array.isArray(
                                    segmentosReproduccionServidor[indice]
                                )
                                    ? segmentosReproduccionServidor[indice]
                                    : [];

                            const registros = registrosCrudos
                                .map(normalizarRegistro)
                                .filter(Boolean);

                            return {
                                path: path,
                                registros: registros
                            };
                        }
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | RESPALDO PARA VERSIONES ANTERIORES DEL CONTROLADOR
                |--------------------------------------------------------------------------
                */

                if (segmentosPreparados.length === 0) {

                    let geometria = normalizarGeometria(
                        rutaServidor
                    );

                    let registrosCrudos =
                        Array.isArray(puntosReproduccionServidor)
                        && puntosReproduccionServidor.length > 0
                            ? puntosReproduccionServidor
                            : ubicaciones;

                    let registros = registrosCrudos
                        .map(normalizarRegistro)
                        .filter(Boolean);

                    if (geometria.length === 0) {
                        geometria = registros.map(registro => [
                            registro.latitud,
                            registro.longitud
                        ]);
                    }

                    segmentosPreparados = [{
                        path: geometria,
                        registros: registros
                    }];
                }


                /*
                |--------------------------------------------------------------------------
                | ASOCIAR CADA REGISTRO CON LA GEOMETRÍA DE CALLES
                |--------------------------------------------------------------------------
                |
                | Se busca el vértice más cercano de la geometría, manteniendo
                | el orden de avance del vehículo.
                |
                */

                function distanciaEntrePuntos(a, b)
                {
                    const radioTierra = 6371000;

                    const lat1 = a[0] * Math.PI / 180;
                    const lat2 = b[0] * Math.PI / 180;
                    const diferenciaLatitud =
                        (b[0] - a[0]) * Math.PI / 180;
                    const diferenciaLongitud =
                        (b[1] - a[1]) * Math.PI / 180;

                    const valor =
                        Math.sin(diferenciaLatitud / 2) ** 2
                        + Math.cos(lat1)
                        * Math.cos(lat2)
                        * Math.sin(diferenciaLongitud / 2) ** 2;

                    return 2 * radioTierra * Math.asin(
                        Math.min(1, Math.sqrt(valor))
                    );
                }


                segmentosPreparados = segmentosPreparados
                    .map(function (segmento) {

                        let path = segmento.path;
                        const registros = segmento.registros;

                        if (path.length === 0 && registros.length > 0) {
                            path = registros.map(registro => [
                                registro.latitud,
                                registro.longitud
                            ]);
                        }

                        let cursorGeometria = 0;

                        registros.forEach(function (registro) {

                            if (path.length === 0) {
                                registro.posicion = [
                                    registro.latitud,
                                    registro.longitud
                                ];
                                registro.indiceGeometria = 0;
                                return;
                            }

                            let mejorIndice = cursorGeometria;
                            let menorDistancia = Infinity;

                            for (
                                let i = cursorGeometria;
                                i < path.length;
                                i++
                            ) {

                                const distancia = distanciaEntrePuntos(
                                    [registro.latitud, registro.longitud],
                                    path[i]
                                );

                                if (distancia < menorDistancia) {
                                    menorDistancia = distancia;
                                    mejorIndice = i;
                                }
                            }

                            cursorGeometria = mejorIndice;
                            registro.indiceGeometria = mejorIndice;
                            registro.posicion = path[mejorIndice];
                        });

                        return {
                            path: path,
                            registros: registros
                        };
                    })
                    .filter(segmento =>
                        segmento.path.length > 0
                        || segmento.registros.length > 0
                    );


                /*
                |--------------------------------------------------------------------------
                | RESPALDO FINAL: REGISTROS GPS ORIGINALES
                |--------------------------------------------------------------------------
                */

                const hayRegistros = segmentosPreparados.some(
                    segmento => segmento.registros.length > 0
                );

                if (!hayRegistros && ubicaciones.length > 0) {

                    const registros = ubicaciones
                        .map(normalizarRegistro)
                        .filter(Boolean);

                    segmentosPreparados = [{
                        path: registros.map(registro => [
                            registro.latitud,
                            registro.longitud
                        ]),
                        registros: registros
                    }];

                    segmentosPreparados[0].registros.forEach(
                        function (registro, indice) {
                            registro.indiceGeometria = indice;
                            registro.posicion =
                                segmentosPreparados[0].path[indice];
                        }
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | LÍNEA DE TIEMPO DEL REPRODUCTOR
                |--------------------------------------------------------------------------
                |
                | El índice del reproductor corresponde a un registro GPS,
                | no a cada vértice generado por OSRM.
                |
                */

                const lineaTiempo = [];

                segmentosPreparados.forEach(
                    function (segmento, indiceSegmento) {

                        segmento.registros.forEach(
                            function (registro, indiceRegistro) {

                                lineaTiempo.push({
                                    segmento: indiceSegmento,
                                    registro: indiceRegistro,
                                    datos: registro,
                                    posicion: registro.posicion,
                                    indiceGeometria:
                                        registro.indiceGeometria
                                });
                            }
                        );
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | MAPA LEAFLET
                |--------------------------------------------------------------------------
                */

                let coordenadaInicial = [
                    19.4326,
                    -99.1332
                ];

                if (lineaTiempo.length > 0) {
                    coordenadaInicial = lineaTiempo[0].posicion;
                } else if (
                    segmentosPreparados.length > 0
                    && segmentosPreparados[0].path.length > 0
                ) {
                    coordenadaInicial =
                        segmentosPreparados[0].path[0];
                }

                const map = L.map(
                    'mapa-historial'
                ).setView(
                    coordenadaInicial,
                    13
                );

                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        maxZoom: 19,
                        attribution:
                            '&copy; OpenStreetMap contributors'
                    }
                ).addTo(map);


                /*
                |--------------------------------------------------------------------------
                | ELEMENTOS DEL REPRODUCTOR
                |--------------------------------------------------------------------------
                */

                const barra = document.getElementById(
                    'barra-progreso'
                );

                const btnPlay = document.getElementById(
                    'btn-reproducir'
                );

                const btnInicio = document.getElementById(
                    'btn-reiniciar'
                );

                const btnAnterior = document.getElementById(
                    'btn-anterior'
                );

                const btnSiguiente = document.getElementById(
                    'btn-siguiente'
                );

                const btnFinal = document.getElementById(
                    'btn-final'
                );

                const selectorVelocidad = document.getElementById(
                    'velocidad-reproduccion'
                );

                const estadoReproductor = document.getElementById(
                    'estado-reproductor'
                );


                /*
                |--------------------------------------------------------------------------
                | FUNCIONES DE FECHA Y HORA
                |--------------------------------------------------------------------------
                */

                function obtenerFecha(valor)
                {
                    if (!valor) {
                        return null;
                    }

                    const fecha = new Date(valor);

                    return Number.isNaN(fecha.getTime())
                        ? null
                        : fecha;
                }


                function formatearHora(valor)
                {
                    const fecha = obtenerFecha(valor);

                    if (!fecha) {
                        return '—';
                    }

                    return fecha.toLocaleTimeString(
                        'es-MX',
                        {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        }
                    );
                }


                function formatearFechaCompleta(valor)
                {
                    const fecha = obtenerFecha(valor);

                    if (!fecha) {
                        return 'Fecha no disponible';
                    }

                    return fecha.toLocaleString(
                        'es-MX',
                        {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        }
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | OBTENER PRIMER Y ÚLTIMO REGISTRO
                |--------------------------------------------------------------------------
                */

                const primerRegistro = lineaTiempo.length > 0
                    ? lineaTiempo[0].datos
                    : null;

                const ultimoRegistro = lineaTiempo.length > 0
                    ? lineaTiempo[lineaTiempo.length - 1].datos
                    : null;

                const primeraFecha = primerRegistro
                    ? primerRegistro.fecha_gps
                    : (ubicaciones[0]?.fecha_gps ?? null);

                const ultimaFecha = ultimoRegistro
                    ? ultimoRegistro.fecha_gps
                    : (
                        ubicaciones.length > 0
                            ? ubicaciones[ubicaciones.length - 1].fecha_gps
                            : null
                    );

                document.getElementById(
                    'hora-inicio'
                ).textContent = formatearHora(primeraFecha);

                document.getElementById(
                    'hora-fin'
                ).textContent = formatearHora(ultimaFecha);

                document.getElementById(
                    'progreso-inicio'
                ).textContent = formatearHora(primeraFecha);

                document.getElementById(
                    'progreso-fin'
                ).textContent = formatearHora(ultimaFecha);


                /*
                |--------------------------------------------------------------------------
                | DIBUJAR CADA SEGMENTO POR SEPARADO
                |--------------------------------------------------------------------------
                |
                | No se crea una polilínea única entre segmentos distintos.
                |
                */

                const lineasBase = [];
                const lineasRecorridas = [];
                const coordenadasBounds = [];
                let distanciaTotal = 0;

                segmentosPreparados.forEach(
                    function (segmento) {

                        const path = segmento.path;

                        path.forEach(punto =>
                            coordenadasBounds.push(punto)
                        );

                        if (path.length > 1) {

                            const linea = L.polyline(
                                path,
                                {
                                    color: '#64748b',
                                    weight: 5,
                                    opacity: 0.75,
                                    lineCap: 'round',
                                    lineJoin: 'round'
                                }
                            ).addTo(map);

                            lineasBase.push(linea);

                            for (
                                let i = 1;
                                i < path.length;
                                i++
                            ) {
                                distanciaTotal += map.distance(
                                    path[i - 1],
                                    path[i]
                                );
                            }
                        }

                        const lineaProgreso = L.polyline(
                            [],
                            {
                                color: '#0056b3',
                                weight: 7,
                                opacity: 1,
                                lineCap: 'round',
                                lineJoin: 'round'
                            }
                        ).addTo(map);

                        lineasRecorridas.push(lineaProgreso);
                    }
                );

                document.getElementById(
                    'distancia-total'
                ).textContent = distanciaTotal < 1000
                    ? `${Math.round(distanciaTotal)} m`
                    : `${(distanciaTotal / 1000).toFixed(2)} km`;


                /*
                |--------------------------------------------------------------------------
                | MARCADORES DE INICIO Y FIN
                |--------------------------------------------------------------------------
                */

                const primeraPosicion = lineaTiempo.length > 0
                    ? lineaTiempo[0].posicion
                    : coordenadasBounds[0];

                const ultimaPosicion = lineaTiempo.length > 0
                    ? lineaTiempo[lineaTiempo.length - 1].posicion
                    : coordenadasBounds[coordenadasBounds.length - 1];

                if (primeraPosicion) {

                    L.circleMarker(
                        primeraPosicion,
                        {
                            radius: 8,
                            color: '#047857',
                            fillColor: '#10b981',
                            fillOpacity: 1,
                            weight: 3
                        }
                    )
                    .addTo(map)
                    .bindPopup(
                        '<b>Inicio del recorrido</b><br>'
                        + formatearFechaCompleta(primeraFecha)
                    );
                }

                if (
                    ultimaPosicion
                    && lineaTiempo.length > 1
                ) {

                    L.circleMarker(
                        ultimaPosicion,
                        {
                            radius: 8,
                            color: '#b91c1c',
                            fillColor: '#ef4444',
                            fillOpacity: 1,
                            weight: 3
                        }
                    )
                    .addTo(map)
                    .bindPopup(
                        '<b>Fin del recorrido</b><br>'
                        + formatearFechaCompleta(ultimaFecha)
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | ICONO DEL VEHÍCULO
                |--------------------------------------------------------------------------
                */

                const iconoVehiculo = L.divIcon({

                    className: 'sotygeo-marker',

                    html: `
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

                    iconSize: [42, 42],
                    iconAnchor: [21, 21]
                });

                const marcador = L.marker(
                    primeraPosicion ?? coordenadaInicial,
                    {
                        icon: iconoVehiculo,
                        zIndexOffset: 1000
                    }
                ).addTo(map);


                /*
                |--------------------------------------------------------------------------
                | AJUSTAR ZOOM A TODOS LOS SEGMENTOS
                |--------------------------------------------------------------------------
                */

                if (coordenadasBounds.length > 0) {

                    const bounds = L.latLngBounds(
                        coordenadasBounds
                    );

                    map.fitBounds(
                        bounds,
                        {
                            padding: [30, 30],
                            maxZoom: 16
                        }
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | VARIABLES DEL REPRODUCTOR
                |--------------------------------------------------------------------------
                */

                let indice = 0;
                let reproduciendo = false;
                let temporizador = null;


                /*
                |--------------------------------------------------------------------------
                | SIN DATOS
                |--------------------------------------------------------------------------
                */

                if (lineaTiempo.length === 0) {

                    estadoReproductor.textContent = 'Sin registros';

                    [
                        barra,
                        btnPlay,
                        btnInicio,
                        btnAnterior,
                        btnSiguiente,
                        btnFinal,
                        selectorVelocidad
                    ].forEach(control => {
                        control.disabled = true;
                    });

                    setTimeout(
                        () => map.invalidateSize(),
                        150
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | HABILITAR CONTROLES
                |--------------------------------------------------------------------------
                */

                barra.max = Math.max(
                    lineaTiempo.length - 1,
                    0
                );

                barra.value = 0;
                barra.disabled = false;

                btnInicio.disabled = false;
                btnAnterior.disabled = false;
                btnSiguiente.disabled = false;
                btnFinal.disabled = false;
                selectorVelocidad.disabled = false;
                btnPlay.disabled = lineaTiempo.length < 2;


                /*
                |--------------------------------------------------------------------------
                | ACTUALIZAR LÍNEAS RECORRIDAS
                |--------------------------------------------------------------------------
                |
                | Los segmentos anteriores quedan completos; el actual se dibuja
                | hasta el registro reproducido; los siguientes permanecen ocultos.
                |
                */

                function actualizarLineasRecorridas(puntoActual)
                {
                    segmentosPreparados.forEach(
                        function (segmento, indiceSegmento) {

                            const linea = lineasRecorridas[indiceSegmento];

                            if (indiceSegmento < puntoActual.segmento) {

                                linea.setLatLngs(
                                    segmento.path
                                );

                                return;
                            }

                            if (indiceSegmento > puntoActual.segmento) {

                                linea.setLatLngs([]);

                                return;
                            }

                            const limite = Math.max(
                                0,
                                puntoActual.indiceGeometria
                            );

                            const recorridoParcial = segmento.path.slice(
                                0,
                                limite + 1
                            );

                            if (
                                recorridoParcial.length === 0
                                && puntoActual.posicion
                            ) {
                                recorridoParcial.push(
                                    puntoActual.posicion
                                );
                            }

                            linea.setLatLngs(
                                recorridoParcial
                            );
                        }
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | MOSTRAR REGISTRO GPS
                |--------------------------------------------------------------------------
                |
                | La hora y velocidad provienen del registro GPS original.
                | La posición utiliza el punto ajustado a la vialidad.
                |
                */

                function mostrarPunto(
                    nuevoIndice,
                    centrar = true
                ) {

                    indice = Math.max(
                        0,
                        Math.min(
                            nuevoIndice,
                            lineaTiempo.length - 1
                        )
                    );

                    const puntoActual = lineaTiempo[indice];
                    const registro = puntoActual.datos;
                    const posicion = puntoActual.posicion;

                    marcador.setLatLng(posicion);

                    actualizarLineasRecorridas(
                        puntoActual
                    );

                    barra.value = indice;

                    document.getElementById(
                        'contador-puntos'
                    ).textContent =
                        `Punto ${indice + 1} de ${lineaTiempo.length}`;

                    document.getElementById(
                        'dato-hora'
                    ).textContent = formatearFechaCompleta(
                        registro.fecha_gps
                    );

                    const velocidad = numeroValido(
                        registro.velocidad
                    );

                    document.getElementById(
                        'dato-velocidad'
                    ).textContent = velocidad === null
                        ? 'No disponible'
                        : `${velocidad.toFixed(1)} km/h`;

                    document.getElementById(
                        'dato-latitud'
                    ).textContent = Number(
                        posicion[0]
                    ).toFixed(7);

                    document.getElementById(
                        'dato-longitud'
                    ).textContent = Number(
                        posicion[1]
                    ).toFixed(7);

                    if (centrar) {

                        map.panTo(
                            posicion,
                            {
                                animate: true,
                                duration: 0.25
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
                    const multiplicador = parseFloat(
                        selectorVelocidad.value
                    ) || 1;

                    return 500 / multiplicador;
                }


                /*
                |--------------------------------------------------------------------------
                | ACTUALIZAR ESTADO DEL BOTÓN
                |--------------------------------------------------------------------------
                */

                function actualizarBoton()
                {
                    document.getElementById(
                        'icono-reproducir'
                    ).textContent = reproduciendo
                        ? 'pause'
                        : 'play_arrow';

                    document.getElementById(
                        'texto-reproducir'
                    ).textContent = reproduciendo
                        ? 'Pausar'
                        : 'Reproducir';

                    if (reproduciendo) {

                        estadoReproductor.textContent = 'Reproduciendo';

                        estadoReproductor.className =
                            'rounded-full border border-emerald-200 '
                            + 'bg-emerald-50 px-3 py-1 '
                            + 'text-xs font-medium text-emerald-700';

                    } else {

                        estadoReproductor.textContent =
                            indice >= lineaTiempo.length - 1
                                ? 'Finalizado'
                                : 'Pausado';

                        estadoReproductor.className =
                            'rounded-full border border-gray-200 '
                            + 'bg-white px-3 py-1 '
                            + 'text-xs font-medium text-gray-600';
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | DETENER REPRODUCCIÓN
                |--------------------------------------------------------------------------
                */

                function detener()
                {
                    reproduciendo = false;

                    if (temporizador) {
                        clearInterval(temporizador);
                        temporizador = null;
                    }

                    actualizarBoton();
                }


                /*
                |--------------------------------------------------------------------------
                | AVANZAR UN REGISTRO
                |--------------------------------------------------------------------------
                */

                function avanzar()
                {
                    if (indice >= lineaTiempo.length - 1) {
                        detener();
                        return;
                    }

                    mostrarPunto(indice + 1);

                    if (indice >= lineaTiempo.length - 1) {
                        detener();
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | REPRODUCIR
                |--------------------------------------------------------------------------
                */

                function reproducir()
                {
                    if (lineaTiempo.length < 2) {
                        return;
                    }

                    if (indice >= lineaTiempo.length - 1) {
                        mostrarPunto(0);
                    }

                    reproduciendo = true;
                    actualizarBoton();

                    if (temporizador) {
                        clearInterval(temporizador);
                    }

                    temporizador = setInterval(
                        avanzar,
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
                        mostrarPunto(0);
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
                        mostrarPunto(indice - 1);
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
                        mostrarPunto(indice + 1);
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
                        mostrarPunto(lineaTiempo.length - 1);
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
                            parseInt(this.value, 10) || 0
                        );
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | CAMBIAR VELOCIDAD
                |--------------------------------------------------------------------------
                */

                selectorVelocidad.addEventListener(
                    'change',
                    function () {

                        if (!reproduciendo) {
                            return;
                        }

                        if (temporizador) {
                            clearInterval(temporizador);
                        }

                        temporizador = setInterval(
                            avanzar,
                            obtenerIntervalo()
                        );
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | INICIALIZAR REPRODUCTOR
                |--------------------------------------------------------------------------
                */

                mostrarPunto(0, false);

                estadoReproductor.textContent = 'Listo';

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