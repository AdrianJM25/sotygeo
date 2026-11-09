<x-app-layout>

    {{-- ========================================================= --}}
    {{-- LEAFLET + GEOMAN --}}
    {{-- ========================================================= --}}

    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

    <link
        rel="stylesheet"
        href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css"
    >

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.min.js"></script>


    <style>
        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-item {
            animation: fadeSlideUp .35s ease-out both;
        }

        input[type="color"]::-webkit-color-swatch-wrapper {
            padding: 0;
        }

        input[type="color"]::-webkit-color-swatch {
            border: none;
            border-radius: 8px;
        }

        .map-container {
            z-index: 10;
        }

        #mapa-zonas {
            min-height: 600px;
        }
    </style>


    <div
        x-data="{
            busqueda: '',
            estadoZonas: {},

            cargarEstado() {
                fetch('{{ route('api.geocercas.estado') }}')
                    .then(r => {
                        if (!r.ok) {
                            throw new Error('No se pudo cargar el estado de las geocercas');
                        }

                        return r.json();
                    })
                    .then(eventos => {
                        let agrupado = {};

                        eventos.forEach(e => {
                            if (!agrupado[e.zona_id]) {
                                agrupado[e.zona_id] = [];
                            }

                            agrupado[e.zona_id].push(e);
                        });

                        this.estadoZonas = agrupado;
                    })
                    .catch(err => {
                        console.error(
                            'Error cargando estado de geocercas:',
                            err
                        );
                    });
            },

            vehiculosEnZona(zonaId) {
                return this.estadoZonas[zonaId] || [];
            }
        }"

        x-init="
            cargarEstado();
            setInterval(() => cargarEstado(), 15000);
        "

        class="mx-auto max-w-[1600px]
               space-y-5 pb-8
               font-['Ubuntu']"
    >

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div
            class="animate-item flex flex-col gap-4
                   rounded-2xl bg-[#0056b3] p-5
                   shadow-sm
                   sm:flex-row sm:items-center
                   sm:justify-between"
        >

            <div class="flex items-center gap-4">

                <div
                    class="flex h-12 w-12 shrink-0
                           items-center justify-center
                           rounded-xl bg-white/15 text-white"
                >
                    <span class="material-symbols-rounded text-[27px]">
                        map
                    </span>
                </div>


                <div>

                    <h2
                        class="font-['Josefin_Sans']
                               text-xl font-semibold
                               text-white sm:text-2xl"
                    >
                        Geocercas y Zonas
                    </h2>

                    <p class="mt-0.5 text-sm text-blue-100">
                        Delimita regiones para monitoreo, alertas y control de vehículos.
                    </p>

                </div>

            </div>


            <div class="w-full sm:w-72">

                <div class="relative">

                    <span
                        class="material-symbols-rounded
                               absolute left-3 top-1/2
                               -translate-y-1/2
                               text-[19px] text-gray-400"
                    >
                        search
                    </span>


                    <input
                        type="text"
                        x-model="busqueda"
                        placeholder="Buscar zona..."
                        class="w-full rounded-xl
                               border-0 bg-white
                               py-2.5 pl-10 pr-4
                               text-sm text-gray-700
                               shadow-sm
                               focus:ring-2
                               focus:ring-white/40"
                    >

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- MENSAJES --}}
        {{-- ========================================================= --}}

        @if(session('success'))

            <div
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 4000)"
                class="animate-item flex
                       items-center justify-between
                       rounded-xl border
                       border-emerald-200
                       bg-emerald-50
                       px-5 py-3.5
                       text-sm text-emerald-800"
            >

                <span class="flex items-center gap-2 font-medium">

                    <span class="material-symbols-rounded text-[19px]">
                        check_circle
                    </span>

                    {{ session('success') }}

                </span>


                <button
                    type="button"
                    @click="show = false"
                >
                    <span class="material-symbols-rounded text-[18px]">
                        close
                    </span>
                </button>

            </div>

        @endif


        @if(session('warning'))

            <div
                x-data="{ show: true }"
                x-show="show"
                class="animate-item flex
                       items-center justify-between
                       rounded-xl border
                       border-amber-200
                       bg-amber-50
                       px-5 py-3.5
                       text-sm text-amber-800"
            >

                <span class="flex items-center gap-2 font-medium">

                    <span class="material-symbols-rounded text-[19px]">
                        warning
                    </span>

                    {{ session('warning') }}

                </span>


                <button
                    type="button"
                    @click="show = false"
                >
                    <span class="material-symbols-rounded text-[18px]">
                        close
                    </span>
                </button>

            </div>

        @endif


        @if($errors->any())

            <div
                class="animate-item rounded-xl
                       border border-red-200
                       bg-red-50 px-5 py-3.5
                       text-sm text-red-700"
            >

                <div class="mb-2 flex items-center gap-2 font-semibold">

                    <span class="material-symbols-rounded text-[19px]">
                        error
                    </span>

                    Revisa la información de la geocerca.

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


        {{-- ========================================================= --}}
        {{-- CONTENIDO --}}
        {{-- ========================================================= --}}

        <div
            class="grid grid-cols-1
                   items-start gap-5
                   lg:grid-cols-12"
        >

            {{-- ===================================================== --}}
            {{-- PANEL IZQUIERDO --}}
            {{-- ===================================================== --}}

            <div
                class="animate-item flex
                       flex-col gap-5
                       lg:col-span-4"
            >

                {{-- ================================================= --}}
                {{-- FORMULARIO --}}
                {{-- ================================================= --}}

                <div
                    class="overflow-hidden rounded-2xl
                           border border-[#0056b3]/15
                           bg-white shadow-sm"
                >

                    <div
                        class="flex items-center justify-between
                               border-b border-gray-100
                               bg-blue-50/50
                               px-5 py-4"
                    >

                        <h3
                            id="titulo-formulario"
                            class="font-['Josefin_Sans']
                                   font-semibold text-[#003d82]"
                        >
                            Crear nueva zona
                        </h3>

                    </div>


                    <form
                        action="{{ route('zonas.store') }}"
                        method="POST"
                        id="form-zona"
                        class="max-h-[65vh]
                               space-y-5
                               overflow-y-auto p-5"
                    >

                        @csrf


                        <input
                            type="hidden"
                            name="_method"
                            id="metodo-form"
                            value="POST"
                        >


                        <input
                            type="hidden"
                            name="is_edit"
                            id="is-edit-form"
                            value=""
                        >


                        <input
                            type="hidden"
                            name="coordenadas"
                            id="input-coordenadas"
                            value="{{ old('coordenadas') }}"
                        >


                        {{-- ========================================= --}}
                        {{-- PROPIETARIO --}}
                        {{-- ========================================= --}}

                        @role('Super Administrador')

                            <div>

                                <label
                                    class="mb-1.5 block
                                           text-xs font-semibold
                                           uppercase text-gray-600"
                                >
                                    Propietario
                                </label>


                                <select
                                    name="propietario"
                                    id="propietario-zona"
                                    onchange="filtrarVehiculosPorPropietario()"
                                    class="w-full rounded-xl
                                           border-gray-300
                                           text-sm
                                           focus:border-[#0056b3]
                                           focus:ring-[#0056b3]"
                                >

                                    <option value="">
                                        Selecciona un propietario
                                    </option>


                                    <optgroup label="Empresas">

                                        @foreach($empresas as $empresa)

                                            <option
                                                value="empresa:{{ $empresa->id }}"
                                                @selected(
                                                    old('propietario')
                                                    ===
                                                    'empresa:' . $empresa->id
                                                )
                                            >
                                                {{ $empresa->nombre }}
                                            </option>

                                        @endforeach

                                    </optgroup>


                                    <optgroup label="Clientes Individuales">

                                        @foreach($clientes as $cliente)

                                            <option
                                                value="cliente:{{ $cliente->id }}"
                                                @selected(
                                                    old('propietario')
                                                    ===
                                                    'cliente:' . $cliente->id
                                                )
                                            >
                                                {{ $cliente->nombre }}
                                                {{ $cliente->apellido_paterno }}
                                            </option>

                                        @endforeach

                                    </optgroup>

                                </select>


                                <p class="mt-1 text-xs text-gray-400">
                                    Los vehículos se mostrarán según el propietario seleccionado.
                                </p>

                            </div>

                        @endrole


                        {{-- ========================================= --}}
                        {{-- NOMBRE --}}
                        {{-- ========================================= --}}

                        <div>

                            <label
                                class="mb-1.5 block
                                       text-xs font-semibold
                                       uppercase text-gray-600"
                            >
                                Nombre
                            </label>


                            <input
                                type="text"
                                name="nombre"
                                required
                                value="{{ old('nombre') }}"
                                placeholder="Ej. Almacén Central"
                                class="w-full rounded-xl
                                       border-gray-300
                                       text-sm
                                       focus:border-[#0056b3]
                                       focus:ring-[#0056b3]"
                            >

                        </div>


                        {{-- ========================================= --}}
                        {{-- COLOR --}}
                        {{-- ========================================= --}}

                        <div>

                            <label
                                class="mb-1.5 block
                                       text-xs font-semibold
                                       uppercase text-gray-600"
                            >
                                Color en mapa
                            </label>


                            <div
                                class="flex items-center gap-3
                                       rounded-xl border
                                       border-gray-200
                                       bg-gray-50 p-2"
                            >

                                <input
                                    type="color"
                                    name="color_hex"
                                    value="{{ old('color_hex', '#0056B3') }}"
                                    required
                                    class="h-9 w-12
                                           cursor-pointer
                                           rounded-lg border-0
                                           bg-transparent p-0"
                                >


                                <span class="text-xs text-gray-500">
                                    Color de borde y relleno
                                </span>

                            </div>

                        </div>


                        {{-- ========================================= --}}
                        {{-- VEHÍCULOS --}}
                        {{-- ========================================= --}}

                        <div
                            class="border-t
                                   border-gray-100 pt-4"
                        >

                            <label
                                class="mb-2 block
                                       text-xs font-semibold
                                       uppercase text-gray-600"
                            >
                                Vehículos y alertas
                            </label>


                            @role('Super Administrador')

                                <div
                                    id="mensaje-seleccionar-propietario"
                                    class="mb-3 rounded-lg
                                           bg-blue-50 p-3
                                           text-xs text-[#0056b3]"
                                >
                                    Selecciona primero una empresa o cliente para mostrar sus vehículos.
                                </div>

                            @endrole


                            <div class="space-y-2">


                                @forelse($vehiculos as $index => $vehiculo)

                                    @php

                                        if ($vehiculo->user_id) {

                                            $propietarioVehiculo =
                                                'cliente:' . $vehiculo->user_id;

                                        } elseif ($vehiculo->empresa_id) {

                                            $propietarioVehiculo =
                                                'empresa:' . $vehiculo->empresa_id;

                                        } else {

                                            $propietarioVehiculo = '';
                                        }

                                    @endphp


                                    <div
                                        x-data="{ seleccionado: false }"
                                        data-owner="{{ $propietarioVehiculo }}"
                                        class="vehiculo-row
                                               rounded-xl border
                                               border-gray-200
                                               bg-gray-50 p-3
                                               transition-colors"
                                        :class="
                                            seleccionado
                                                ? 'border-[#0056b3]/40 bg-blue-50/40'
                                                : ''
                                        "
                                    >

                                        <label
                                            class="flex cursor-pointer
                                                   items-center gap-3"
                                        >

                                            <input
                                                type="checkbox"
                                                x-model="seleccionado"
                                                class="vehiculo-selector
                                                       h-4 w-4 rounded
                                                       border-gray-300
                                                       text-[#0056b3]
                                                       focus:ring-[#0056b3]"
                                                name="vehiculos[{{ $index }}][id]"
                                                value="{{ $vehiculo->id }}"
                                            >


                                            <div class="flex flex-col">

                                                <span
                                                    class="text-sm
                                                           font-semibold
                                                           text-gray-700"
                                                >
                                                    {{ $vehiculo->nombre }}
                                                </span>

                                                <span
                                                    class="text-[11px]
                                                           text-gray-400"
                                                >
                                                    {{ $vehiculo->placas ?? 'Sin placas' }}
                                                </span>

                                            </div>

                                        </label>


                                        {{-- OPCIONES DEL VEHÍCULO --}}
                                        <div
                                            x-show="seleccionado"
                                            x-collapse
                                            class="mt-4 space-y-3 pl-7"
                                        >

                                            {{-- TIPO DE REGLA --}}
                                            <div>

                                                <label
                                                    class="mb-1 block
                                                           text-[10px]
                                                           font-semibold
                                                           uppercase
                                                           text-gray-500"
                                                >
                                                    Tipo de regla
                                                </label>


                                                <select
                                                    name="vehiculos[{{ $index }}][tipo_regla]"
                                                    :disabled="!seleccionado"
                                                    class="w-full rounded-lg
                                                           border-gray-300
                                                           bg-white text-xs
                                                           focus:border-[#0056b3]
                                                           focus:ring-[#0056b3]"
                                                >

                                                    <option value="informativa">
                                                        Informativa
                                                    </option>

                                                    <option value="restringida">
                                                        Restringida
                                                    </option>

                                                    <option value="permitida">
                                                        Permitida
                                                    </option>

                                                </select>

                                            </div>


                                            {{-- ALERTA ENTRADA --}}
                                            <div>

                                                <input
                                                    type="hidden"
                                                    :disabled="!seleccionado"
                                                    name="vehiculos[{{ $index }}][notificar_entrada]"
                                                    value="0"
                                                >


                                                <label
                                                    class="flex cursor-pointer
                                                           items-center gap-2
                                                           text-xs text-gray-600"
                                                >

                                                    <input
                                                        type="checkbox"
                                                        :disabled="!seleccionado"
                                                        name="vehiculos[{{ $index }}][notificar_entrada]"
                                                        value="1"
                                                        checked
                                                        class="rounded
                                                               border-gray-300
                                                               text-[#0056b3]
                                                               focus:ring-[#0056b3]"
                                                    >

                                                    Generar alerta de entrada

                                                </label>

                                            </div>


                                            {{-- ALERTA SALIDA --}}
                                            <div>

                                                <input
                                                    type="hidden"
                                                    :disabled="!seleccionado"
                                                    name="vehiculos[{{ $index }}][notificar_salida]"
                                                    value="0"
                                                >


                                                <label
                                                    class="flex cursor-pointer
                                                           items-center gap-2
                                                           text-xs text-gray-600"
                                                >

                                                    <input
                                                        type="checkbox"
                                                        :disabled="!seleccionado"
                                                        name="vehiculos[{{ $index }}][notificar_salida]"
                                                        value="1"
                                                        checked
                                                        class="rounded
                                                               border-gray-300
                                                               text-[#0056b3]
                                                               focus:ring-[#0056b3]"
                                                    >

                                                    Generar alerta de salida

                                                </label>

                                            </div>


                                            {{-- PERMANENCIA --}}
                                            <div
                                                class="grid grid-cols-2
                                                       gap-2 border-t
                                                       border-gray-200 pt-3"
                                            >

                                                <div>

                                                    <label
                                                        class="mb-1 block
                                                               text-[10px]
                                                               font-semibold
                                                               uppercase
                                                               text-gray-500"
                                                    >
                                                        Mín. minutos
                                                    </label>


                                                    <input
                                                        type="number"
                                                        min="1"
                                                        :disabled="!seleccionado"
                                                        name="vehiculos[{{ $index }}][permanencia_minima_minutos]"
                                                        placeholder="Sin límite"
                                                        class="w-full rounded-lg
                                                               border-gray-300
                                                               bg-white text-xs
                                                               focus:border-[#0056b3]
                                                               focus:ring-[#0056b3]"
                                                    >

                                                </div>


                                                <div>

                                                    <label
                                                        class="mb-1 block
                                                               text-[10px]
                                                               font-semibold
                                                               uppercase
                                                               text-gray-500"
                                                    >
                                                        Máx. minutos
                                                    </label>


                                                    <input
                                                        type="number"
                                                        min="1"
                                                        :disabled="!seleccionado"
                                                        name="vehiculos[{{ $index }}][permanencia_maxima_minutos]"
                                                        placeholder="Sin límite"
                                                        class="w-full rounded-lg
                                                               border-gray-300
                                                               bg-white text-xs
                                                               focus:border-[#0056b3]
                                                               focus:ring-[#0056b3]"
                                                    >

                                                </div>

                                            </div>

                                        </div>

                                    </div>


                                @empty

                                    <div
                                        class="rounded-lg
                                               bg-gray-50 p-3
                                               text-xs italic
                                               text-gray-500"
                                    >
                                        No tienes vehículos registrados.
                                    </div>

                                @endforelse

                            </div>

                        </div>


                        {{-- ========================================= --}}
                        {{-- INSTRUCCIÓN --}}
                        {{-- ========================================= --}}

                        <div
                            id="alerta-dibujo"
                            class="flex items-start gap-2
                                   rounded-xl border
                                   border-blue-200
                                   bg-blue-50 p-3
                                   text-xs text-[#0056b3]"
                        >

                            <span
                                class="material-symbols-rounded
                                       mt-0.5 text-[17px]"
                            >
                                draw
                            </span>

                            <span>
                                Utiliza la herramienta de polígono del mapa para delimitar la geocerca antes de guardar.
                            </span>

                        </div>


                        {{-- ========================================= --}}
                        {{-- BOTONES --}}
                        {{-- ========================================= --}}

                        <div class="flex gap-3">

                            <button
                                type="submit"
                                id="btn-guardar"
                                disabled
                                class="flex flex-1
                                       items-center justify-center
                                       gap-2 rounded-xl
                                       bg-[#0056b3]
                                       px-5 py-3
                                       text-sm font-medium
                                       text-white
                                       transition-colors
                                       hover:bg-[#004494]
                                       disabled:cursor-not-allowed
                                       disabled:opacity-40"
                            >

                                <span
                                    class="material-symbols-rounded
                                           text-[19px]"
                                >
                                    save
                                </span>


                                <span id="texto-btn-guardar">
                                    Guardar geocerca
                                </span>

                            </button>


                            <button
                                type="button"
                                id="btn-cancelar"
                                onclick="cancelarEdicion()"
                                class="hidden rounded-xl
                                       bg-gray-100
                                       px-5 py-3
                                       text-sm font-medium
                                       text-gray-700
                                       hover:bg-gray-200"
                            >
                                Cancelar
                            </button>

                        </div>

                    </form>

                </div>


                {{-- ================================================= --}}
                {{-- ZONAS REGISTRADAS --}}
                {{-- ================================================= --}}

                <div
                    class="flex max-h-[440px]
                           flex-col overflow-hidden
                           rounded-2xl
                           border border-[#0056b3]/15
                           bg-white shadow-sm"
                >

                    <div
                        class="flex items-center
                               justify-between
                               border-b border-gray-100
                               bg-blue-50/50
                               px-5 py-3"
                    >

                        <h3
                            class="font-['Josefin_Sans']
                                   font-semibold
                                   text-[#003d82]"
                        >
                            Zonas registradas
                        </h3>


                        <span
                            class="rounded-lg
                                   bg-white
                                   px-2.5 py-1
                                   text-xs font-semibold
                                   text-[#0056b3]
                                   shadow-sm"
                        >
                            {{ count($zonas) }}
                        </span>

                    </div>


                    <div
                        class="space-y-1.5
                               overflow-y-auto p-3"
                    >

                        @forelse($zonas as $zona)

                            @php
                                $busquedaTexto =
                                    mb_strtolower(
                                        $zona->nombre
                                    );
                            @endphp


                            <div
                                x-data="{ expandido: false }"
                                x-show="
                                    busqueda === ''
                                    ||
                                    @js($busquedaTexto)
                                        .includes(
                                            busqueda.toLowerCase()
                                        )
                                "
                                class="group rounded-xl
                                       border border-transparent
                                       bg-white
                                       transition-all
                                       hover:border-blue-100"
                            >

                                <div
                                    class="flex items-center
                                           justify-between
                                           rounded-xl p-3
                                           hover:bg-blue-50/30"
                                >

                                    <button
                                        type="button"
                                        @click="enfocarZona('{{ $zona->id }}')"
                                        class="flex min-w-0
                                               flex-1 items-center
                                               gap-3 pr-2
                                               text-left"
                                    >

                                        <span
                                            class="h-3.5 w-3.5
                                                   shrink-0 rounded-full
                                                   border border-black/10
                                                   shadow-sm"
                                            style="background-color: {{ $zona->color_hex }}"
                                        ></span>


                                        <div class="min-w-0">

                                            <div
                                                class="truncate
                                                       text-sm font-semibold
                                                       text-gray-700
                                                       transition-colors
                                                       group-hover:text-[#0056b3]"
                                            >
                                                {{ $zona->nombre }}
                                            </div>


                                            <div
                                                class="truncate
                                                       text-[11px]
                                                       text-gray-400"
                                            >

                                                @if($zona->empresa)

                                                    {{ $zona->empresa->nombre }}

                                                @elseif($zona->usuario)

                                                    {{ $zona->usuario->nombre }}
                                                    {{ $zona->usuario->apellido_paterno }}

                                                @else

                                                    Sin propietario

                                                @endif

                                            </div>

                                        </div>

                                    </button>


                                    <div
                                        class="flex shrink-0
                                               items-center gap-1"
                                    >

                                        {{-- VEHÍCULOS DENTRO --}}
                                        <template
                                            x-if="
                                                vehiculosEnZona(
                                                    '{{ $zona->id }}'
                                                ).length > 0
                                            "
                                        >

                                            <button
                                                type="button"
                                                @click="expandido = !expandido"
                                                class="inline-flex
                                                       items-center gap-1
                                                       rounded-full
                                                       border
                                                       border-emerald-200
                                                       bg-emerald-50
                                                       px-2 py-0.5
                                                       text-[10px]
                                                       font-semibold
                                                       text-emerald-700"
                                            >

                                                <span
                                                    class="h-1.5 w-1.5
                                                           animate-pulse
                                                           rounded-full
                                                           bg-emerald-500"
                                                ></span>


                                                <span
                                                    x-text="
                                                        vehiculosEnZona(
                                                            '{{ $zona->id }}'
                                                        ).length
                                                        + ' dentro'
                                                    "
                                                ></span>

                                            </button>

                                        </template>


                                        {{-- EDITAR --}}
                                        <button
                                            type="button"
                                            onclick="prepararEdicion({{ $zona->id }})"
                                            title="Editar zona"
                                            class="rounded-lg
                                                   p-1.5 text-gray-400
                                                   transition-colors
                                                   hover:bg-blue-50
                                                   hover:text-[#0056b3]"
                                        >
                                            <span
                                                class="material-symbols-rounded
                                                       text-[18px]"
                                            >
                                                edit
                                            </span>
                                        </button>


                                        {{-- ELIMINAR --}}
                                        <form
                                            action="{{ route('zonas.destroy', $zona->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Eliminar definitivamente esta geocerca?');"
                                        >

                                            @csrf
                                            @method('DELETE')


                                            <button
                                                type="submit"
                                                title="Eliminar zona"
                                                class="rounded-lg
                                                       p-1.5 text-gray-400
                                                       transition-colors
                                                       hover:bg-red-50
                                                       hover:text-red-600"
                                            >
                                                <span
                                                    class="material-symbols-rounded
                                                           text-[18px]"
                                                >
                                                    delete
                                                </span>
                                            </button>

                                        </form>

                                    </div>

                                </div>


                                {{-- ===================================== --}}
                                {{-- VEHÍCULOS DENTRO EN VIVO --}}
                                {{-- ===================================== --}}

                                <div
                                    x-show="expandido"
                                    x-collapse
                                    class="space-y-1.5
                                           px-3 pb-3"
                                >

                                    <template
                                        x-for="
                                            v in vehiculosEnZona(
                                                '{{ $zona->id }}'
                                            )
                                        "
                                        :key="v.vehiculo_id"
                                    >

                                        <div
                                            class="flex items-center
                                                   justify-between
                                                   rounded-lg
                                                   px-2.5 py-1.5
                                                   text-xs"
                                            :class="
                                                (
                                                    v.permanencia_maxima_minutos
                                                    &&
                                                    v.minutos_dentro
                                                    >
                                                    v.permanencia_maxima_minutos
                                                )
                                                ?
                                                'bg-red-50 text-red-700'
                                                :
                                                'bg-gray-50 text-gray-600'
                                            "
                                        >

                                            <span
                                                class="font-medium"
                                                x-text="v.vehiculo_nombre"
                                            ></span>


                                            <span
                                                x-text="
                                                    v.minutos_dentro
                                                    + ' min dentro'
                                                "
                                            ></span>

                                        </div>

                                    </template>

                                </div>

                            </div>


                        @empty

                            <div class="py-8 text-center">

                                <span
                                    class="material-symbols-rounded
                                           mb-2 block
                                           text-[36px]
                                           text-[#0056b3]/30"
                                >
                                    map
                                </span>


                                <p class="text-sm text-gray-500">
                                    No hay zonas configuradas.
                                </p>

                            </div>

                        @endforelse

                    </div>

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- MAPA --}}
            {{-- ===================================================== --}}

            <div
                class="map-container animate-item
                       relative min-h-[600px]
                       overflow-hidden rounded-2xl
                       border border-[#0056b3]/15
                       bg-white shadow-sm
                       lg:col-span-8
                       lg:min-h-[calc(100vh-10rem)]"
            >

                <div
                    id="mapa-zonas"
                    class="absolute inset-0
                           h-full w-full"
                ></div>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- JAVASCRIPT --}}
    {{-- ========================================================= --}}

    <script>

        var capasMap = {};
        var capaActual = null;
        var mapaZonas = null;

        var zonasGuardadas = @json($zonas);


        /*
        |--------------------------------------------------------------------------
        | FILTRAR VEHÍCULOS
        |--------------------------------------------------------------------------
        */

        window.filtrarVehiculosPorPropietario =
            function () {

                var select =
                    document.getElementById(
                        'propietario-zona'
                    );


                /*
                | Si el usuario no es Super Admin,
                | no existe este select.
                */

                if (!select) {
                    return;
                }


                var propietario =
                    select.value;


                var filas =
                    document.querySelectorAll(
                        '.vehiculo-row'
                    );


                var visibles = 0;


                filas.forEach(
                    function (fila) {

                        var propietarioVehiculo =
                            fila.getAttribute(
                                'data-owner'
                            );


                        var coincide =
                            propietario !== ''
                            &&
                            propietarioVehiculo
                                ===
                            propietario;


                        if (coincide) {

                            fila.classList.remove(
                                'hidden'
                            );

                            visibles++;

                        } else {

                            fila.classList.add(
                                'hidden'
                            );
                        }


                        var checkbox =
                            fila.querySelector(
                                '.vehiculo-selector'
                            );


                        if (checkbox) {

                            checkbox.disabled =
                                !coincide;


                            if (
                                !coincide
                                &&
                                checkbox.checked
                            ) {

                                checkbox.checked =
                                    false;


                                checkbox.dispatchEvent(
                                    new Event(
                                        'change',
                                        {
                                            bubbles: true
                                        }
                                    )
                                );
                            }
                        }
                    }
                );


                var mensaje =
                    document.getElementById(
                        'mensaje-seleccionar-propietario'
                    );


                if (mensaje) {

                    if (!propietario) {

                        mensaje.innerText =
                            'Selecciona primero una empresa o cliente para mostrar sus vehículos.';

                        mensaje.classList.remove(
                            'hidden'
                        );

                    } else if (
                        visibles === 0
                    ) {

                        mensaje.innerText =
                            'Este propietario no tiene vehículos registrados.';

                        mensaje.classList.remove(
                            'hidden'
                        );

                    } else {

                        mensaje.classList.add(
                            'hidden'
                        );
                    }
                }
            };


        /*
        |--------------------------------------------------------------------------
        | CONVERTIR CAPA A JSON
        |--------------------------------------------------------------------------
        */

        function layerToCoordenadas(
            layer
        ) {

            var latlngs =
                layer.getLatLngs()[0];


            var coordenadas = [];


            latlngs.forEach(
                function (punto) {

                    coordenadas.push({
                        lat: punto.lat,
                        lng: punto.lng
                    });
                }
            );


            return JSON.stringify(
                coordenadas
            );
        }


        /*
        |--------------------------------------------------------------------------
        | LIMPIAR VEHÍCULOS
        |--------------------------------------------------------------------------
        */

        function limpiarVehiculos()
        {

            document.querySelectorAll(
                '.vehiculo-row'
            )
            .forEach(
                function (fila) {

                    var selector =
                        fila.querySelector(
                            '.vehiculo-selector'
                        );


                    if (selector) {

                        selector.checked =
                            false;


                        selector.dispatchEvent(
                            new Event(
                                'change',
                                {
                                    bubbles: true
                                }
                            )
                        );
                    }


                    var regla =
                        fila.querySelector(
                            'select[name*="[tipo_regla]"]'
                        );


                    if (regla) {

                        regla.value =
                            'informativa';
                    }


                    var entrada =
                        fila.querySelector(
                            'input[type="checkbox"][name*="[notificar_entrada]"]'
                        );


                    if (entrada) {
                        entrada.checked = true;
                    }


                    var salida =
                        fila.querySelector(
                            'input[type="checkbox"][name*="[notificar_salida]"]'
                        );


                    if (salida) {
                        salida.checked = true;
                    }


                    var minimo =
                        fila.querySelector(
                            'input[name*="[permanencia_minima_minutos]"]'
                        );


                    if (minimo) {
                        minimo.value = '';
                    }


                    var maximo =
                        fila.querySelector(
                            'input[name*="[permanencia_maxima_minutos]"]'
                        );


                    if (maximo) {
                        maximo.value = '';
                    }
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | INICIAR MAPA
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                /*
                |--------------------------------------------------------------------------
                | VALIDAR LEAFLET
                |--------------------------------------------------------------------------
                */

                if (
                    typeof L ===
                    'undefined'
                ) {

                    console.error(
                        'Leaflet no se cargó correctamente.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | MAPA
                |--------------------------------------------------------------------------
                */

                mapaZonas =
                    L.map(
                        'mapa-zonas'
                    )
                    .setView(
                        [
                            18.8814,
                            -99.1764
                        ],
                        13
                    );


                /*
                |--------------------------------------------------------------------------
                | MAPA BASE
                |--------------------------------------------------------------------------
                */

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
                    mapaZonas
                );


                /*
                |--------------------------------------------------------------------------
                | ZONAS GUARDADAS
                |--------------------------------------------------------------------------
                */

                zonasGuardadas.forEach(
                    function (zona) {

                        if (!zona.geojson) {
                            return;
                        }


                        try {

                            var geoData =
                                typeof zona.geojson
                                ===
                                'string'
                                    ?
                                    JSON.parse(
                                        zona.geojson
                                    )
                                    :
                                    zona.geojson;


                            var grupo =
                                L.geoJSON(
                                    geoData,
                                    {
                                        style: {
                                            color:
                                                zona.color_hex
                                                ||
                                                '#0056B3',

                                            fillColor:
                                                zona.color_hex
                                                ||
                                                '#0056B3',

                                            weight:
                                                2,

                                            fillOpacity:
                                                0.25
                                        }
                                    }
                                )
                                .bindPopup(
                                    '<strong>'
                                    +
                                    zona.nombre
                                    +
                                    '</strong>'
                                )
                                .addTo(
                                    mapaZonas
                                );


                            var capas =
                                grupo.getLayers();


                            if (
                                capas.length > 0
                            ) {

                                capasMap[
                                    zona.id
                                ] =
                                    capas[0];
                            }

                        } catch (error) {

                            console.error(
                                'Error cargando la zona '
                                +
                                zona.id,
                                error
                            );
                        }
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | GEOMAN
                |--------------------------------------------------------------------------
                */

                if (
                    mapaZonas.pm
                ) {

                    mapaZonas.pm.addControls({
                        position:
                            'topleft',

                        drawMarker:
                            false,

                        drawCircleMarker:
                            false,

                        drawPolyline:
                            false,

                        drawRectangle:
                            false,

                        drawCircle:
                            false,

                        drawText:
                            false,

                        editMode:
                            false,

                        dragMode:
                            false,

                        cutPolygon:
                            false,

                        removalMode:
                            false,

                        drawPolygon:
                            true
                    });


                    mapaZonas.pm.setLang(
                        'es'
                    );

                } else {

                    console.error(
                        'Leaflet Geoman no se cargó correctamente.'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | ELEMENTOS
                |--------------------------------------------------------------------------
                */

                var inputCoordenadas =
                    document.getElementById(
                        'input-coordenadas'
                    );


                var btnGuardar =
                    document.getElementById(
                        'btn-guardar'
                    );


                var alertaDibujo =
                    document.getElementById(
                        'alerta-dibujo'
                    );


                var inputColor =
                    document.querySelector(
                        'input[name="color_hex"]'
                    );


                /*
                |--------------------------------------------------------------------------
                | CREAR POLÍGONO
                |--------------------------------------------------------------------------
                */

                mapaZonas.on(
                    'pm:create',
                    function (e) {

                        if (capaActual) {

                            mapaZonas.removeLayer(
                                capaActual
                            );
                        }


                        capaActual =
                            e.layer;


                        var coordenadas =
                            capaActual
                                .getLatLngs()[0];


                        capaActual.setStyle({
                            color:
                                inputColor.value,

                            fillColor:
                                inputColor.value,

                            weight:
                                3,

                            fillOpacity:
                                0.25
                        });


                        inputCoordenadas.value =
                            JSON.stringify(
                                coordenadas
                            );


                        btnGuardar.disabled =
                            false;


                        alertaDibujo.style.display =
                            'none';
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | COLOR --}}
                |--------------------------------------------------------------------------
                */

                if (inputColor) {

                    inputColor.addEventListener(
                        'input',
                        function (e) {

                            if (capaActual) {

                                capaActual.setStyle({
                                    color:
                                        e.target.value,

                                    fillColor:
                                        e.target.value
                                });
                            }


                            Object.values(
                                capasMap
                            )
                            .forEach(
                                function (layer) {

                                    if (
                                        layer.pm
                                        &&
                                        layer.pm.enabled()
                                    ) {

                                        layer.setStyle({
                                            color:
                                                e.target.value,

                                            fillColor:
                                                e.target.value
                                        });
                                    }
                                }
                            );
                        }
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | FILTRADO INICIAL
                |--------------------------------------------------------------------------
                */

                window.filtrarVehiculosPorPropietario();


                /*
                |--------------------------------------------------------------------------
                | COORDENADAS RECUPERADAS
                |--------------------------------------------------------------------------
                */

                if (
                    inputCoordenadas
                    &&
                    inputCoordenadas.value
                ) {

                    btnGuardar.disabled =
                        false;


                    alertaDibujo.style.display =
                        'none';
                }


                /*
                |--------------------------------------------------------------------------
                | FORZAR TAMAÑO CORRECTO DEL MAPA
                |--------------------------------------------------------------------------
                */

                setTimeout(
                    function () {

                        mapaZonas.invalidateSize();

                    },
                    150
                );


                setTimeout(
                    function () {

                        mapaZonas.invalidateSize();

                    },
                    500
                );
            }
        );


        /*
        |--------------------------------------------------------------------------
        | ENFOCAR ZONA
        |--------------------------------------------------------------------------
        */

        window.enfocarZona =
            function (zonaId) {

                if (
                    !mapaZonas
                    ||
                    !capasMap[zonaId]
                ) {
                    return;
                }


                var layer =
                    capasMap[zonaId];


                mapaZonas.flyToBounds(
                    layer.getBounds(),
                    {
                        padding:
                            [
                                50,
                                50
                            ],

                        maxZoom:
                            16,

                        duration:
                            1.2
                    }
                );


                setTimeout(
                    function () {

                        layer.openPopup();

                    },
                    1200
                );
            };


        /*
        |--------------------------------------------------------------------------
        | EDITAR ZONA
        |--------------------------------------------------------------------------
        */

        window.prepararEdicion =
            function (zonaId) {

                var zona =
                    zonasGuardadas.find(
                        function (item) {

                            return (
                                item.id == zonaId
                            );
                        }
                    );


                if (!zona) {
                    return;
                }


                limpiarVehiculos();


                /*
                |--------------------------------------------------------------------------
                | FORMULARIO
                |--------------------------------------------------------------------------
                */

                var formulario =
                    document.getElementById(
                        'form-zona'
                    );


                formulario.action =
                    '/zonas/'
                    +
                    zona.id;


                document.getElementById(
                    'metodo-form'
                ).value =
                    'PUT';


                document.getElementById(
                    'is-edit-form'
                ).value =
                    zona.id;


                document.getElementById(
                    'titulo-formulario'
                ).innerText =
                    'Editar zona: '
                    +
                    zona.nombre;


                document.getElementById(
                    'texto-btn-guardar'
                ).innerText =
                    'Actualizar geocerca';


                document.getElementById(
                    'btn-cancelar'
                )
                .classList.remove(
                    'hidden'
                );


                /*
                |--------------------------------------------------------------------------
                | NOMBRE
                |--------------------------------------------------------------------------
                */

                document.querySelector(
                    'input[name="nombre"]'
                ).value =
                    zona.nombre;


                /*
                |--------------------------------------------------------------------------
                | COLOR
                |--------------------------------------------------------------------------
                */

                document.querySelector(
                    'input[name="color_hex"]'
                ).value =
                    zona.color_hex
                    ||
                    '#0056B3';


                /*
                |--------------------------------------------------------------------------
                | PROPIETARIO
                |--------------------------------------------------------------------------
                */

                var propietarioSelect =
                    document.getElementById(
                        'propietario-zona'
                    );


                if (propietarioSelect) {

                    if (
                        zona.empresa_id
                    ) {

                        propietarioSelect.value =
                            'empresa:'
                            +
                            zona.empresa_id;

                    } else if (
                        zona.user_id
                    ) {

                        propietarioSelect.value =
                            'cliente:'
                            +
                            zona.user_id;

                    } else {

                        propietarioSelect.value =
                            '';
                    }


                    window.filtrarVehiculosPorPropietario();
                }


                /*
                |--------------------------------------------------------------------------
                | VEHÍCULOS
                |--------------------------------------------------------------------------
                */

                if (
                    zona.vehiculos
                    &&
                    zona.vehiculos.length > 0
                ) {

                    zona.vehiculos.forEach(
                        function (vehiculo) {

                            var selector =
                                document.querySelector(
                                    '.vehiculo-selector[value="'
                                    +
                                    vehiculo.id
                                    +
                                    '"]'
                                );


                            if (!selector) {
                                return;
                            }


                            selector.checked =
                                true;


                            selector.dispatchEvent(
                                new Event(
                                    'change',
                                    {
                                        bubbles: true
                                    }
                                )
                            );


                            var fila =
                                selector.closest(
                                    '.vehiculo-row'
                                );


                            if (!fila) {
                                return;
                            }


                            var regla =
                                fila.querySelector(
                                    'select[name*="[tipo_regla]"]'
                                );


                            if (regla) {

                                regla.value =
                                    vehiculo.pivot.tipo_regla
                                    ||
                                    'informativa';
                            }


                            var entrada =
                                fila.querySelector(
                                    'input[type="checkbox"][name*="[notificar_entrada]"]'
                                );


                            if (entrada) {

                                entrada.checked =
                                    vehiculo.pivot.notificar_entrada == 1;
                            }


                            var salida =
                                fila.querySelector(
                                    'input[type="checkbox"][name*="[notificar_salida]"]'
                                );


                            if (salida) {

                                salida.checked =
                                    vehiculo.pivot.notificar_salida == 1;
                            }


                            var minimo =
                                fila.querySelector(
                                    'input[name*="[permanencia_minima_minutos]"]'
                                );


                            if (minimo) {

                                minimo.value =
                                    vehiculo.pivot
                                        .permanencia_minima_minutos
                                    ??
                                    '';
                            }


                            var maximo =
                                fila.querySelector(
                                    'input[name*="[permanencia_maxima_minutos]"]'
                                );


                            if (maximo) {

                                maximo.value =
                                    vehiculo.pivot
                                        .permanencia_maxima_minutos
                                    ??
                                    '';
                            }
                        }
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | DESACTIVAR EDICIÓN ANTERIOR
                |--------------------------------------------------------------------------
                */

                Object.values(
                    capasMap
                )
                .forEach(
                    function (layer) {

                        if (layer.pm) {
                            layer.pm.disable();
                        }
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | EDITAR POLÍGONO
                |--------------------------------------------------------------------------
                */

                var layer =
                    capasMap[
                        zona.id
                    ];


                if (layer) {

                    layer.pm.enable({
                        allowSelfIntersection:
                            false
                    });


                    document.getElementById(
                        'input-coordenadas'
                    ).value =
                        layerToCoordenadas(
                            layer
                        );


                    document.getElementById(
                        'btn-guardar'
                    ).disabled =
                        false;


                    document.getElementById(
                        'alerta-dibujo'
                    ).style.display =
                        'none';


                    layer.off(
                        'pm:edit'
                    );


                    layer.on(
                        'pm:edit',
                        function (e) {

                            document.getElementById(
                                'input-coordenadas'
                            ).value =
                                layerToCoordenadas(
                                    e.target
                                );
                        }
                    );
                }


                window.enfocarZona(
                    zona.id
                );
            };


        /*
        |--------------------------------------------------------------------------
        | CANCELAR EDICIÓN
        |--------------------------------------------------------------------------
        */

        window.cancelarEdicion =
            function () {

                window.location.reload();
            };

    </script>

</x-app-layout>