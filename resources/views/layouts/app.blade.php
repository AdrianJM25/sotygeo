<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SotyGeo') }}</title>


    {{-- ====================================================== --}}
    {{-- FUENTES --}}
    {{-- ====================================================== --}}

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;500;600;700&family=Ubuntu:wght@300;400;500;700&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap"
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
    />

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9coqIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""
    ></script>


    {{-- ====================================================== --}}
    {{-- VITE --}}
    {{-- ====================================================== --}}

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    {{-- ====================================================== --}}
    {{-- ESTILOS GENERALES --}}
    {{-- ====================================================== --}}

    <style>

        [x-cloak] {
            display: none !important;
        }

        .font-heading {
            font-family: 'Josefin Sans', sans-serif;
        }

        .font-body {
            font-family: 'Ubuntu', sans-serif;
        }

        .material-symbols-rounded {
            font-family: 'Material Symbols Rounded';
            font-weight: normal;
            font-style: normal;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;

            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24;
        }

    </style>

</head>


<body

    class="h-screen overflow-hidden font-body antialiased bg-gray-100 text-gray-900"

    x-data="{
        sidebarOpen: false,
        sidebarMini: false
    }"

>


    {{-- ====================================================== --}}
    {{-- CONTENEDOR GENERAL --}}
    {{-- ====================================================== --}}

    <div class="flex h-screen overflow-hidden relative p-4 gap-4">


        {{-- ================================================== --}}
        {{-- FONDO SIDEBAR EN MÓVIL --}}
        {{-- ================================================== --}}

        <div

            x-show="sidebarOpen"

            x-cloak

            x-transition.opacity

            @click="sidebarOpen = false"

            class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"

        ></div>


        {{-- ================================================== --}}
        {{-- SIDEBAR --}}
        {{-- ================================================== --}}

        @include('sidebar')


        {{-- ================================================== --}}
        {{-- COLUMNA PRINCIPAL --}}
        {{-- ================================================== --}}

        <div

            class="flex min-h-0 flex-col flex-1
                   w-full h-full
                   overflow-hidden
                   gap-4"

        >


            {{-- ================================================== --}}
            {{-- GENERACIÓN DE MÓDULOS PARA EL BUSCADOR --}}
            {{-- ================================================== --}}

            @php

                $modulosBusqueda = [];


                /*
                |--------------------------------------------------------------------------
                | MAPA EN VIVO / DASHBOARD
                |--------------------------------------------------------------------------
                */

                $modulosBusqueda[] = [

                    'nombre' => 'Mapa en Vivo',

                    'ruta' => route('dashboard'),

                    'activo' => request()->routeIs('dashboard'),

                ];


                /*
                |--------------------------------------------------------------------------
                | EMPRESAS
                |--------------------------------------------------------------------------
                */

                if (
                    auth()->user()->hasRole(
                        'Super Administrador'
                    )
                ) {

                    $modulosBusqueda[] = [

                        'nombre' => 'Empresas',

                        'ruta' => route('empresas.index'),

                        'activo' => request()->routeIs(
                            'empresas.*'
                        ),

                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | USUARIOS
                |--------------------------------------------------------------------------
                */

                if (
                    auth()->user()->hasAnyRole([

                        'Super Administrador',

                        'Administrador de Empresa',

                    ])
                ) {

                    $modulosBusqueda[] = [

                        'nombre' => 'Usuarios',

                        'ruta' => route('usuarios.index'),

                        'activo' => request()->routeIs(
                            'usuarios.*'
                        ),

                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | CONDUCTORES
                |--------------------------------------------------------------------------
                */

                if (
                    auth()->user()->hasAnyRole([

                        'Super Administrador',

                        'Administrador de Empresa',

                        'Gestor de flotilla',

                    ])
                ) {

                    $modulosBusqueda[] = [

                        'nombre' => 'Conductores',

                        'ruta' => route(
                            'conductores.index'
                        ),

                        'activo' => request()->routeIs(
                            'conductores.*'
                        ),

                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | FLOTILLAS
                |--------------------------------------------------------------------------
                */

                if (
                    auth()->user()->hasAnyRole([

                        'Super Administrador',

                        'Administrador de Empresa',

                    ])
                ) {

                    $modulosBusqueda[] = [

                        'nombre' => 'Flotillas',

                        'ruta' => route(
                            'flotillas.index'
                        ),

                        'activo' => request()->routeIs(
                            'flotillas.*'
                        ),

                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | OPERACIÓN LOGÍSTICA
                |--------------------------------------------------------------------------
                */

                if (
                    auth()->user()->hasAnyRole([

                        'Super Administrador',

                        'Administrador de Empresa',

                        'Gestor de flotilla',

                        'Cliente Individual',

                    ])
                ) {


                    /*
                    |----------------------------------------------------------
                    | VEHÍCULOS
                    |----------------------------------------------------------
                    */

                    $modulosBusqueda[] = [

                        'nombre' => 'Vehículos',

                        'ruta' => route(
                            'vehiculos.index'
                        ),

                        'activo' => request()->routeIs(
                            'vehiculos.*'
                        ),

                    ];


                    /*
                    |----------------------------------------------------------
                    | DISPOSITIVOS
                    |----------------------------------------------------------
                    */

                    $modulosBusqueda[] = [

                        'nombre' => 'Dispositivos',

                        'ruta' => route(
                            'dispositivos.index'
                        ),

                        'activo' => request()->routeIs(
                            'dispositivos.*'
                        ),

                    ];


                    /*
                    |----------------------------------------------------------
                    | GEOCERCAS
                    |----------------------------------------------------------
                    */

                    $modulosBusqueda[] = [

                        'nombre' => 'Geocercas',

                        'ruta' => route(
                            'zonas.index'
                        ),

                        'activo' => request()->routeIs(
                            'zonas.*'
                        ),

                    ];


                    /*
                    |----------------------------------------------------------
                    | HISTORIAL DE ZONAS
                    |----------------------------------------------------------
                    */

                    $modulosBusqueda[] = [

                        'nombre' => 'Historial Zonas',

                        'ruta' => route(
                            'geocercas.historial'
                        ),

                        'activo' => request()->routeIs(
                            'geocercas.historial'
                        ),

                    ];

                }

            @endphp


            {{-- ================================================== --}}
            {{-- HEADER --}}
            {{-- ================================================== --}}

            <header

                x-data="{

                    openPerfil: false,

                    openNotificaciones: false,

                    busqueda: '',

                    notificacionesCount: 0,

                    modulos: @js($modulosBusqueda),

                    get resultadosBusqueda() {

                        const texto = this.busqueda
                            .trim()
                            .toLowerCase();

                        if (texto === '') {
                            return [];
                        }

                        return this.modulos.filter(
                            modulo =>
                                modulo.nombre
                                    .toLowerCase()
                                    .includes(texto)
                        );

                    },

                    abrirPrimerResultado() {

                        if (
                            this.resultadosBusqueda.length > 0
                        ) {

                            window.location.href =
                                this.resultadosBusqueda[0].ruta;

                        }

                    }

                }"

                class="flex items-center
                       justify-between
                       gap-4
                       px-6 py-4
                       bg-white
                       border border-gray-200
                       shadow-sm
                       rounded-2xl
                       shrink-0
                       z-30"

            >


                {{-- ================================================== --}}
                {{-- BOTÓN MENÚ MÓVIL --}}
                {{-- ================================================== --}}

                <button

                    type="button"

                    @click="sidebarOpen = !sidebarOpen"

                    class="text-gray-500
                           hover:text-[#56928C]
                           focus:outline-none
                           lg:hidden
                           shrink-0"

                >

                    <span class="material-symbols-rounded">
                        menu
                    </span>

                </button>


                {{-- ================================================== --}}
                {{-- BUSCADOR DE MÓDULOS --}}
                {{-- ================================================== --}}

                <div

                    class="hidden sm:block
                           flex-1
                           max-w-md
                           relative"

                    @click.outside="busqueda = ''"

                >


                    {{-- INPUT --}}

                    <div class="relative">


                        {{-- ICONO BUSCAR --}}

                        <span

                            class="absolute
                                   inset-y-0 left-0
                                   flex items-center
                                   pl-3
                                   text-gray-400
                                   pointer-events-none"

                        >

                            <span
                                class="material-symbols-rounded
                                       text-[19px]"
                            >
                                search
                            </span>

                        </span>


                        <input

                            type="text"

                            x-model="busqueda"

                            @keydown.escape="
                                busqueda = ''
                            "

                            @keydown.enter.prevent="
                                abrirPrimerResultado()
                            "

                            placeholder="Buscar módulo..."

                            autocomplete="off"

                            class="w-full
                                   pl-10 pr-10 py-2.5
                                   text-sm
                                   text-gray-700
                                   bg-gray-50
                                   border border-gray-200
                                   rounded-xl
                                   focus:border-[#56928C]
                                   focus:ring-[#56928C]
                                   transition-colors"

                        >


                        {{-- BOTÓN LIMPIAR --}}

                        <button

                            type="button"

                            x-show="busqueda.length > 0"

                            x-cloak

                            @click="
                                busqueda = ''
                            "

                            class="absolute
                                   inset-y-0 right-0
                                   flex items-center
                                   pr-3
                                   text-gray-400
                                   hover:text-gray-600"

                        >

                            <span
                                class="material-symbols-rounded
                                       text-[18px]"
                            >
                                close
                            </span>

                        </button>

                    </div>


                    {{-- ================================================== --}}
                    {{-- PANEL DE RESULTADOS --}}
                    {{-- ================================================== --}}

                    <div

                        x-show="
                            busqueda.trim() !== ''
                        "

                        x-cloak


                        x-transition:enter="
                            transition
                            ease-out
                            duration-150
                        "

                        x-transition:enter-start="
                            opacity-0
                            -translate-y-1
                        "

                        x-transition:enter-end="
                            opacity-100
                            translate-y-0
                        "

                        x-transition:leave="
                            transition
                            ease-in
                            duration-100
                        "

                        x-transition:leave-start="
                            opacity-100
                        "

                        x-transition:leave-end="
                            opacity-0
                        "


                        class="absolute
                               left-0 right-0
                               mt-2
                               bg-white
                               border border-gray-200
                               rounded-xl
                               shadow-xl
                               overflow-hidden
                               z-[100]"

                    >


                        {{-- ========================================== --}}
                        {{-- RESULTADOS ENCONTRADOS --}}
                        {{-- ========================================== --}}

                        <template

                            x-if="
                                resultadosBusqueda.length > 0
                            "

                        >

                            <div class="py-1">


                                <template

                                    x-for="
                                        modulo in resultadosBusqueda
                                    "

                                    :key="modulo.nombre"

                                >

                                    <a

                                        :href="modulo.ruta"

                                        @click="
                                            busqueda = ''
                                        "

                                        class="flex
                                               items-center
                                               justify-between
                                               gap-3
                                               px-4 py-3
                                               text-sm
                                               text-gray-700
                                               hover:bg-[#56928C]/10
                                               transition-colors"

                                    >

                                        <div

                                            class="flex
                                                   items-center
                                                   gap-3
                                                   min-w-0"

                                        >

                                            <span

                                                class="w-8 h-8
                                                       shrink-0
                                                       flex
                                                       items-center
                                                       justify-center
                                                       rounded-lg
                                                       bg-[#56928C]/10
                                                       text-[#56928C]"

                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[18px]"
                                                >
                                                    apps
                                                </span>

                                            </span>


                                            <span

                                                x-text="
                                                    modulo.nombre
                                                "

                                                class="font-medium
                                                       truncate"

                                            ></span>

                                        </div>


                                        <div

                                            class="flex
                                                   items-center
                                                   gap-2
                                                   shrink-0"

                                        >

                                            <span

                                                x-show="
                                                    modulo.activo
                                                "

                                                class="text-[10px]
                                                       font-semibold
                                                       text-[#3A6B66]
                                                       bg-[#56928C]/10
                                                       px-2 py-1
                                                       rounded-full"

                                            >
                                                Actual
                                            </span>


                                            <span
                                                class="material-symbols-rounded
                                                       text-[17px]
                                                       text-gray-300"
                                            >
                                                arrow_forward
                                            </span>

                                        </div>

                                    </a>

                                </template>

                            </div>

                        </template>


                        {{-- ========================================== --}}
                        {{-- SIN RESULTADOS --}}
                        {{-- ========================================== --}}

                        <template

                            x-if="
                                resultadosBusqueda.length === 0
                            "

                        >

                            <div

                                class="flex flex-col
                                       items-center
                                       justify-center
                                       gap-1
                                       px-4 py-6
                                       text-center"

                            >

                                <span

                                    class="material-symbols-rounded
                                           text-[30px]
                                           text-gray-300"

                                >
                                    search_off
                                </span>


                                <span

                                    class="text-sm
                                           font-medium
                                           text-gray-500"

                                >
                                    No se encontró ningún módulo
                                </span>


                                <span

                                    class="text-xs
                                           text-gray-400"

                                    x-text="
                                        'Búsqueda: ' + busqueda
                                    "

                                ></span>

                            </div>

                        </template>

                    </div>

                </div>


                {{-- ================================================== --}}
                {{-- ACCIONES DERECHA --}}
                {{-- ================================================== --}}

                <div

                    class="flex
                           items-center
                           gap-2
                           shrink-0"

                >


                    {{-- ================================================== --}}
                    {{-- NOTIFICACIONES --}}
                    {{-- ================================================== --}}

                    <div

                        class="relative"

                        @click.outside="
                            openNotificaciones = false
                        "

                    >

                        <button

                            type="button"

                            @click="
                                openNotificaciones =
                                    !openNotificaciones
                            "

                            class="relative
                                   p-2
                                   text-gray-400
                                   hover:text-gray-700
                                   hover:bg-gray-100
                                   rounded-lg
                                   transition-colors
                                   focus:outline-none"

                        >

                            <span class="material-symbols-rounded">
                                notifications
                            </span>


                            <span

                                x-show="
                                    notificacionesCount > 0
                                "

                                x-cloak

                                class="absolute
                                       -top-0.5
                                       -right-0.5
                                       flex
                                       h-4 w-4"

                            >

                                <span

                                    class="animate-ping
                                           absolute
                                           inline-flex
                                           h-full w-full
                                           rounded-full
                                           bg-rose-400
                                           opacity-75"

                                ></span>


                                <span

                                    class="relative
                                           inline-flex
                                           items-center
                                           justify-center
                                           rounded-full
                                           h-4 w-4
                                           bg-rose-500
                                           text-white
                                           text-[9px]
                                           font-bold"

                                    x-text="
                                        notificacionesCount
                                    "

                                ></span>

                            </span>

                        </button>


                        {{-- PANEL NOTIFICACIONES --}}

                        <div

                            x-show="openNotificaciones"

                            x-cloak


                            x-transition:enter="
                                transition
                                ease-out
                                duration-150
                            "

                            x-transition:enter-start="
                                opacity-0
                                -translate-y-2
                                scale-95
                            "

                            x-transition:enter-end="
                                opacity-100
                                translate-y-0
                                scale-100
                            "


                            x-transition:leave="
                                transition
                                ease-in
                                duration-100
                            "

                            x-transition:leave-start="
                                opacity-100
                                scale-100
                            "

                            x-transition:leave-end="
                                opacity-0
                                scale-95
                            "


                            class="absolute
                                   right-0
                                   mt-2
                                   w-80
                                   bg-white
                                   border border-gray-200
                                   rounded-2xl
                                   shadow-xl
                                   overflow-hidden
                                   z-50"

                        >


                            <div

                                class="p-4
                                       border-b
                                       border-gray-100
                                       flex
                                       items-center
                                       justify-between
                                       bg-gray-50/50"

                            >

                                <h3

                                    class="text-sm
                                           font-bold
                                           text-gray-900"

                                >
                                    Notificaciones
                                </h3>


                                <span

                                    class="text-xs
                                           text-gray-400"

                                    x-text="
                                        notificacionesCount
                                        + ' nuevas'
                                    "

                                ></span>

                            </div>


                            <div

                                class="max-h-80
                                       overflow-y-auto"

                            >

                                <div

                                    class="flex
                                           flex-col
                                           items-center
                                           justify-center
                                           gap-2
                                           py-10 px-4
                                           text-center"

                                >

                                    <div

                                        class="w-10 h-10
                                               rounded-full
                                               bg-gray-100
                                               text-gray-400
                                               flex
                                               items-center
                                               justify-center"

                                    >

                                        <span
                                            class="material-symbols-rounded"
                                        >
                                            notifications_none
                                        </span>

                                    </div>


                                    <p

                                        class="text-sm
                                               text-gray-500"

                                    >
                                        No tienes notificaciones nuevas.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- ================================================== --}}
                    {{-- PERFIL --}}
                    {{-- ================================================== --}}

                    <div

                        class="relative"

                        @click.outside="
                            openPerfil = false
                        "

                    >

                        <button

                            type="button"

                            @click="
                                openPerfil =
                                    !openPerfil
                            "

                            class="flex
                                   items-center
                                   gap-2
                                   px-2 py-1.5
                                   rounded-lg
                                   hover:bg-gray-100
                                   transition-colors
                                   focus:outline-none"

                        >


                            {{-- AVATAR --}}

                            <div

                                class="w-8 h-8
                                       rounded-full
                                       overflow-hidden
                                       bg-gray-900
                                       text-white
                                       flex
                                       items-center
                                       justify-center
                                       text-xs
                                       font-bold
                                       shrink-0"

                            >

                                @if(auth()->user()->avatar_url)

                                    <img

                                        src="{{ auth()->user()->avatar_url }}"

                                        class="w-full
                                               h-full
                                               object-cover"

                                        alt="{{ auth()->user()->nombre_completo }}"

                                    >

                                @else

                                    {{

                                        mb_strtoupper(

                                            mb_substr(
                                                auth()->user()->nombre,
                                                0,
                                                1
                                            )

                                            .

                                            mb_substr(
                                                auth()->user()->apellido_paterno,
                                                0,
                                                1
                                            )

                                        )

                                    }}

                                @endif

                            </div>


                            {{-- NOMBRE --}}

                            <span

                                class="hidden md:inline
                                       text-sm
                                       font-medium
                                       text-gray-700
                                       max-w-[120px]
                                       truncate"

                            >

                                {{ auth()->user()->nombre }}

                            </span>


                            {{-- FLECHA --}}

                            <span

                                class="material-symbols-rounded
                                       text-[18px]
                                       text-gray-400
                                       transition-transform"

                                :class="{
                                    'rotate-180':
                                        openPerfil
                                }"

                            >
                                expand_more
                            </span>

                        </button>


                        {{-- DROPDOWN PERFIL --}}

                        <div

                            x-show="openPerfil"

                            x-cloak


                            x-transition:enter="
                                transition
                                ease-out
                                duration-150
                            "

                            x-transition:enter-start="
                                opacity-0
                                -translate-y-2
                                scale-95
                            "

                            x-transition:enter-end="
                                opacity-100
                                translate-y-0
                                scale-100
                            "


                            x-transition:leave="
                                transition
                                ease-in
                                duration-100
                            "

                            x-transition:leave-start="
                                opacity-100
                                scale-100
                            "

                            x-transition:leave-end="
                                opacity-0
                                scale-95
                            "


                            class="absolute
                                   right-0
                                   mt-2
                                   w-56
                                   bg-white
                                   border border-gray-200
                                   rounded-2xl
                                   shadow-xl
                                   overflow-hidden
                                   z-50"

                        >


                            <div

                                class="p-4
                                       border-b
                                       border-gray-100
                                       bg-gray-50/50"

                            >

                                <p

                                    class="text-sm
                                           font-semibold
                                           text-gray-900
                                           truncate"

                                >

                                    {{ auth()->user()->nombre_completo }}

                                </p>


                                <p

                                    class="text-xs
                                           text-gray-400
                                           truncate"

                                >

                                    {{ auth()->user()->email }}

                                </p>

                            </div>


                            <div class="py-1">

                                <a

                                    href="{{ route('profile.edit') }}"

                                    class="flex
                                           items-center
                                           gap-2.5
                                           px-4 py-2.5
                                           text-sm
                                           text-gray-700
                                           hover:bg-gray-50
                                           transition-colors"

                                >

                                    <span

                                        class="material-symbols-rounded
                                               text-[18px]
                                               text-gray-400"

                                    >
                                        person
                                    </span>

                                    Mi Perfil

                                </a>

                            </div>

                        </div>

                    </div>


                    {{-- ================================================== --}}
                    {{-- CERRAR SESIÓN --}}
                    {{-- ================================================== --}}

                    <form

                        method="POST"

                        action="{{ route('logout') }}"

                        class="m-0 flex items-center"

                    >

                        @csrf


                        <button

                            type="submit"

                            title="Cerrar Sesión"

                            class="p-2
                                   text-gray-400
                                   hover:text-red-600
                                   hover:bg-red-50
                                   rounded-lg
                                   transition-colors
                                   focus:outline-none"

                        >

                            <span

                                class="material-symbols-rounded
                                       text-[21px]"

                            >
                                logout
                            </span>

                        </button>

                    </form>

                </div>

            </header>


            {{-- ================================================== --}}
            {{-- CONTENIDO PRINCIPAL --}}
            {{-- ================================================== --}}

            <main

                class="min-h-0 flex-1
                       overflow-x-hidden
                       overflow-y-auto"

            >

                {{ $slot }}

            </main>

        </div>

    </div>

</body>

</html>