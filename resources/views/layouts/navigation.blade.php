<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SotyGeo') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;500;600;700&family=Ubuntu:wght@300;400;500;700&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap"
        rel="stylesheet"
    >

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

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        .material-symbols-rounded {
            font-family: 'Material Symbols Rounded' !important;
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>

<body
    class="font-['Ubuntu'] antialiased bg-gray-100 text-gray-900"
    x-data="{
        sidebarOpen: false,
        sidebarMini: false
    }"
>

    <div class="relative flex h-screen gap-4 overflow-hidden p-4">

        <template x-if="sidebarOpen">
            <div
                x-transition.opacity
                @click="sidebarOpen = false"
                class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
            ></div>
        </template>

        @include('sidebar')

        <div
            class="flex h-full w-full flex-1 flex-col gap-4 overflow-hidden"
        >

            <header
                x-data="{
                    openPerfil: false,
                    openNotificaciones: false,
                    notificacionesCount: 0
                }"
                class="z-30 flex shrink-0 items-center justify-between
                       gap-4 rounded-2xl border border-gray-200
                       bg-white px-6 py-4 shadow-sm"
            >

                <button
                    type="button"
                    @click="
                        sidebarOpen =
                            !sidebarOpen
                    "
                    class="shrink-0 text-gray-500
                           transition-colors
                           hover:text-[#56928C]
                           focus:outline-none
                           lg:hidden"
                >

                    <span
                        class="select-none whitespace-nowrap
                               material-symbols-rounded
                               text-[22px] font-normal not-italic
                               normal-case leading-none tracking-normal
                               antialiased
                               [font-feature-settings:'liga']
                               [font-variation-settings:'FILL'_0,'wght'_400,'GRAD'_0,'opsz'_24]"
                    >
                        menu
                    </span>

                </button>

                <div
                    id="moduleSearchContainer"
                    class="relative hidden max-w-md flex-1 sm:block"
                >

                    <div class="relative">

                        <span
                            class="pointer-events-none absolute
                                   inset-y-0 left-0 flex
                                   items-center pl-3 text-gray-400"
                        >

                            <span
                                class="select-none whitespace-nowrap
                                       material-symbols-rounded
                                       text-[19px] font-normal not-italic
                                       normal-case leading-none tracking-normal
                                       antialiased
                                       [font-feature-settings:'liga']
                                       [font-variation-settings:'FILL'_0,'wght'_400,'GRAD'_0,'opsz'_24]"
                            >
                                search
                            </span>

                        </span>

                        <input
                            id="moduleSearchInput"
                            type="text"
                            placeholder="Buscar módulo..."
                            autocomplete="off"
                            class="w-full rounded-xl
                                   border border-gray-200
                                   bg-gray-50
                                   py-2.5 pl-10 pr-10
                                   text-sm text-gray-700
                                   transition-colors
                                   placeholder:text-gray-400
                                   focus:border-[#56928C]
                                   focus:ring-[#56928C]"
                        >

                        <button
                            id="moduleSearchClear"
                            type="button"
                            title="Limpiar búsqueda"
                            class="absolute inset-y-0 right-0
                                   hidden items-center pr-3
                                   text-gray-400
                                   transition-colors
                                   hover:text-gray-600"
                        >

                            <span
                                class="select-none whitespace-nowrap
                                       material-symbols-rounded
                                       text-[18px] font-normal not-italic
                                       normal-case leading-none tracking-normal
                                       antialiased
                                       [font-feature-settings:'liga']
                                       [font-variation-settings:'FILL'_0,'wght'_400,'GRAD'_0,'opsz'_24]"
                            >
                                close
                            </span>

                        </button>

                    </div>

                    <div
                        id="moduleSearchDropdown"
                        class="absolute left-0 right-0 z-[100]
                               mt-2 hidden
                               max-h-[360px]
                               overflow-y-auto
                               rounded-xl
                               border border-gray-200
                               bg-white
                               shadow-xl"
                    >

                        <div
                            id="moduleSearchLoading"
                            class="flex items-center gap-3
                                   px-4 py-4
                                   text-sm text-gray-500"
                        >

                            <span
                                class="animate-spin
                                       select-none whitespace-nowrap
                                       material-symbols-rounded
                                       text-[20px] font-normal not-italic
                                       normal-case leading-none tracking-normal
                                       text-[#56928C]
                                       antialiased
                                       [font-feature-settings:'liga']
                                       [font-variation-settings:'FILL'_0,'wght'_400,'GRAD'_0,'opsz'_24]"
                            >
                                progress_activity
                            </span>

                            <span>
                                Cargando módulos...
                            </span>

                        </div>

                        <div
                            id="moduleSearchResults"
                            class="hidden py-1"
                        ></div>

                        <div
                            id="moduleSearchEmpty"
                            class="hidden flex-col
                                   items-center justify-center
                                   gap-1 px-4 py-6
                                   text-center"
                        >

                            <span
                                class="select-none whitespace-nowrap
                                       material-symbols-rounded
                                       text-[30px] font-normal not-italic
                                       normal-case leading-none tracking-normal
                                       text-gray-300
                                       antialiased
                                       [font-feature-settings:'liga']
                                       [font-variation-settings:'FILL'_0,'wght'_400,'GRAD'_0,'opsz'_24]"
                            >
                                search_off
                            </span>

                            <span
                                class="text-sm font-medium text-gray-500"
                            >
                                No se encontró ningún módulo
                            </span>

                            <span
                                id="moduleSearchEmptyText"
                                class="text-xs text-gray-400"
                            ></span>

                        </div>

                        <div
                            id="moduleSearchError"
                            class="hidden flex-col
                                   items-center justify-center
                                   gap-2 px-4 py-6
                                   text-center"
                        >

                            <span
                                class="select-none whitespace-nowrap
                                       material-symbols-rounded
                                       text-[30px] font-normal not-italic
                                       normal-case leading-none tracking-normal
                                       text-red-400
                                       antialiased
                                       [font-feature-settings:'liga']
                                       [font-variation-settings:'FILL'_0,'wght'_400,'GRAD'_0,'opsz'_24]"
                            >
                                error
                            </span>

                            <span
                                class="text-sm font-medium text-gray-600"
                            >
                                No se pudieron cargar los módulos
                            </span>

                            <button
                                id="moduleSearchRetry"
                                type="button"
                                class="text-xs font-medium
                                       text-[#56928C]
                                       hover:underline"
                            >
                                Intentar nuevamente
                            </button>

                        </div>

                    </div>

                </div>

                <div
                    class="flex shrink-0 items-center gap-2"
                >

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
                            class="relative rounded-lg
                                   p-2 text-gray-400
                                   transition-colors
                                   hover:bg-gray-100
                                   hover:text-gray-700
                                   focus:outline-none"
                        >

                            <span
                                class="select-none whitespace-nowrap
                                       material-symbols-rounded
                                       text-[21px] font-normal not-italic
                                       normal-case leading-none tracking-normal
                                       antialiased
                                       [font-feature-settings:'liga']
                                       [font-variation-settings:'FILL'_0,'wght'_400,'GRAD'_0,'opsz'_24]"
                            >
                                notifications
                            </span>

                            <span
                                x-show="
                                    notificacionesCount > 0
                                "
                                class="absolute -right-0.5 -top-0.5
                                       flex h-4 w-4"
                            >

                                <span
                                    class="absolute inline-flex
                                           h-full w-full
                                           animate-ping
                                           rounded-full
                                           bg-rose-400
                                           opacity-75"
                                ></span>

                                <span
                                    class="relative inline-flex
                                           h-4 w-4
                                           items-center justify-center
                                           rounded-full
                                           bg-rose-500
                                           text-[9px]
                                           font-bold text-white"
                                    x-text="
                                        notificacionesCount
                                    "
                                ></span>

                            </span>

                        </button>

                        <template
                            x-if="
                                openNotificaciones
                            "
                        >

                            <div
                                x-transition
                                class="absolute right-0 z-50
                                       mt-2 w-80
                                       overflow-hidden
                                       rounded-2xl
                                       border border-gray-200
                                       bg-white
                                       shadow-xl"
                            >

                                <div
                                    class="flex items-center
                                           justify-between
                                           border-b border-gray-100
                                           bg-gray-50/50
                                           p-4"
                                >

                                    <h3
                                        class="text-sm font-bold
                                               text-gray-900"
                                    >
                                        Notificaciones
                                    </h3>

                                    <span
                                        class="text-xs text-gray-400"
                                        x-text="
                                            notificacionesCount
                                            + ' nuevas'
                                        "
                                    ></span>

                                </div>

                                <div class="max-h-80 overflow-y-auto">

                                    <div
                                        class="flex flex-col
                                               items-center
                                               justify-center
                                               gap-2
                                               px-4 py-10
                                               text-center"
                                    >

                                        <div
                                            class="flex h-10 w-10
                                                   items-center
                                                   justify-center
                                                   rounded-full
                                                   bg-gray-100
                                                   text-gray-400"
                                        >

                                            <span
                                                class="select-none whitespace-nowrap
                                                       material-symbols-rounded
                                                       text-[21px] font-normal not-italic
                                                       normal-case leading-none tracking-normal
                                                       antialiased
                                                       [font-feature-settings:'liga']
                                                       [font-variation-settings:'FILL'_0,'wght'_400,'GRAD'_0,'opsz'_24]"
                                            >
                                                notifications_none
                                            </span>

                                        </div>

                                        <p class="text-sm text-gray-500">
                                            No tienes notificaciones nuevas.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </template>

                    </div>

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
                            class="flex items-center
                                   gap-2 rounded-lg
                                   px-2 py-1.5
                                   transition-colors
                                   hover:bg-gray-100
                                   focus:outline-none"
                        >

                            <div
                                class="flex h-8 w-8
                                       shrink-0
                                       items-center justify-center
                                       overflow-hidden
                                       rounded-full
                                       bg-gray-900
                                       text-xs font-bold
                                       text-white"
                            >

                                @if(auth()->user()->avatar_url)

                                    <img
                                        src="{{ auth()->user()->avatar_url }}"
                                        class="h-full w-full object-cover"
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

                            <span
                                class="hidden max-w-[120px]
                                       truncate text-sm
                                       font-medium text-gray-700
                                       md:inline"
                            >
                                {{ auth()->user()->nombre }}
                            </span>

                            <span
                                class="select-none whitespace-nowrap
                                       material-symbols-rounded
                                       text-[18px] font-normal not-italic
                                       normal-case leading-none tracking-normal
                                       text-gray-400
                                       transition-transform
                                       antialiased
                                       [font-feature-settings:'liga']
                                       [font-variation-settings:'FILL'_0,'wght'_400,'GRAD'_0,'opsz'_24]"
                                :class="{
                                    'rotate-180':
                                        openPerfil
                                }"
                            >
                                expand_more
                            </span>

                        </button>

                        <template
                            x-if="
                                openPerfil
                            "
                        >

                            <div
                                x-transition
                                class="absolute right-0 z-50
                                       mt-2 w-56
                                       overflow-hidden
                                       rounded-2xl
                                       border border-gray-200
                                       bg-white
                                       shadow-xl"
                            >

                                <div
                                    class="border-b
                                           border-gray-100
                                           bg-gray-50/50
                                           p-4"
                                >

                                    <p
                                        class="truncate
                                               text-sm
                                               font-semibold
                                               text-gray-900"
                                    >
                                        {{ auth()->user()->nombre_completo }}
                                    </p>

                                    <p
                                        class="truncate
                                               text-xs
                                               text-gray-400"
                                    >
                                        {{ auth()->user()->email }}
                                    </p>

                                </div>

                                <div class="py-1">

                                    <a
                                        href="{{ route('profile.edit') }}"
                                        class="flex items-center
                                               gap-2.5
                                               px-4 py-2.5
                                               text-sm
                                               text-gray-700
                                               transition-colors
                                               hover:bg-gray-50"
                                    >

                                        <span
                                            class="select-none whitespace-nowrap
                                                   material-symbols-rounded
                                                   text-[18px] font-normal not-italic
                                                   normal-case leading-none tracking-normal
                                                   text-gray-400
                                                   antialiased
                                                   [font-feature-settings:'liga']
                                                   [font-variation-settings:'FILL'_0,'wght'_400,'GRAD'_0,'opsz'_24]"
                                        >
                                            person
                                        </span>

                                        Mi Perfil

                                    </a>

                                </div>

                            </div>

                        </template>

                    </div>

                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                        class="m-0 flex items-center"
                    >

                        @csrf

                        <button
                            type="submit"
                            title="Cerrar Sesión"
                            class="rounded-lg
                                   p-2 text-gray-400
                                   transition-colors
                                   hover:bg-red-50
                                   hover:text-red-600
                                   focus:outline-none"
                        >

                            <span
                                class="select-none whitespace-nowrap
                                       material-symbols-rounded
                                       text-[21px] font-normal not-italic
                                       normal-case leading-none tracking-normal
                                       antialiased
                                       [font-feature-settings:'liga']
                                       [font-variation-settings:'FILL'_0,'wght'_400,'GRAD'_0,'opsz'_24]"
                            >
                                logout
                            </span>

                        </button>

                    </form>

                </div>

            </header>

            <main
                class="flex-1 overflow-x-hidden overflow-y-auto"
            >
                {{ $slot }}
            </main>

        </div>

    </div>

    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const input =
                    document.getElementById(
                        'moduleSearchInput'
                    );

                const container =
                    document.getElementById(
                        'moduleSearchContainer'
                    );

                const dropdown =
                    document.getElementById(
                        'moduleSearchDropdown'
                    );

                const clearButton =
                    document.getElementById(
                        'moduleSearchClear'
                    );

                const loading =
                    document.getElementById(
                        'moduleSearchLoading'
                    );

                const results =
                    document.getElementById(
                        'moduleSearchResults'
                    );

                const empty =
                    document.getElementById(
                        'moduleSearchEmpty'
                    );

                const emptyText =
                    document.getElementById(
                        'moduleSearchEmptyText'
                    );

                const errorBox =
                    document.getElementById(
                        'moduleSearchError'
                    );

                const retryButton =
                    document.getElementById(
                        'moduleSearchRetry'
                    );

                if (
                    !input ||
                    !container ||
                    !dropdown
                ) {
                    return;
                }

                const endpoint =
                    @json(
                        route(
                            'modulos.buscar'
                        )
                    );

                const currentRouteName =
                    @json(
                        request()->route()
                            ? request()->route()->getName()
                            : null
                    );

                let modulos = [];

                let cargando = false;

                let huboError = false;

                function normalizar(
                    texto
                ) {

                    return String(
                        texto ?? ''
                    )
                        .normalize(
                            'NFD'
                        )
                        .replace(
                            /[\u0300-\u036f]/g,
                            ''
                        )
                        .toLowerCase()
                        .trim();

                }

                function esModuloActual(
                    modulo
                ) {

                    if (
                        !currentRouteName ||
                        !modulo.nombre_ruta
                    ) {
                        return false;
                    }

                    if (
                        modulo.nombre_ruta ===
                        'dashboard'
                    ) {

                        return (
                            currentRouteName ===
                            'dashboard'
                        );

                    }

                    if (
                        modulo.nombre_ruta ===
                        'geocercas.historial'
                    ) {

                        return (
                            currentRouteName ===
                            'geocercas.historial'
                        );

                    }

                    const base =
                        modulo.nombre_ruta
                            .split('.')[0];

                    return (
                        currentRouteName ===
                        modulo.nombre_ruta

                        ||

                        currentRouteName
                            .startsWith(
                                base + '.'
                            )
                    );

                }

                function ocultarEstados() {

                    loading
                        .classList
                        .add(
                            'hidden'
                        );

                    results
                        .classList
                        .add(
                            'hidden'
                        );

                    empty
                        .classList
                        .add(
                            'hidden'
                        );

                    empty
                        .classList
                        .remove(
                            'flex'
                        );

                    errorBox
                        .classList
                        .add(
                            'hidden'
                        );

                    errorBox
                        .classList
                        .remove(
                            'flex'
                        );

                }

                function abrirDropdown() {

                    dropdown
                        .classList
                        .remove(
                            'hidden'
                        );

                }

                function cerrarDropdown() {

                    dropdown
                        .classList
                        .add(
                            'hidden'
                        );

                }

                function crearResultado(
                    modulo
                ) {

                    const link =
                        document.createElement(
                            'a'
                        );

                    link.href =
                        modulo.ruta;

                    link.className =
                        'flex items-center justify-between gap-3 ' +
                        'px-4 py-3 text-sm text-gray-700 ' +
                        'transition-colors hover:bg-[#56928C]/10';

                    const izquierda =
                        document.createElement(
                            'div'
                        );

                    izquierda.className =
                        'flex min-w-0 items-center gap-3';

                    const iconoCaja =
                        document.createElement(
                            'span'
                        );

                    iconoCaja.className =
                        'flex h-9 w-9 shrink-0 items-center ' +
                        'justify-center rounded-lg ' +
                        'bg-[#56928C]/10 text-[#56928C]';

                    const icono =
                        document.createElement(
                            'span'
                        );

                    icono.className =
                        "select-none whitespace-nowrap " +
                        "material-symbols-rounded " +
                        "text-[18px] font-normal not-italic " +
                        "normal-case leading-none tracking-normal " +
                        "antialiased " +
                        "[font-feature-settings:'liga'] " +
                        "[font-variation-settings:'FILL'_0,'wght'_400,'GRAD'_0,'opsz'_24]";

                    icono.textContent =
                        'apps';

                    iconoCaja.appendChild(
                        icono
                    );

                    const nombre =
                        document.createElement(
                            'span'
                        );

                    nombre.className =
                        'truncate font-medium';

                    nombre.textContent =
                        modulo.nombre;

                    izquierda.appendChild(
                        iconoCaja
                    );

                    izquierda.appendChild(
                        nombre
                    );

                    const derecha =
                        document.createElement(
                            'div'
                        );

                    derecha.className =
                        'flex shrink-0 items-center gap-2';

                    if (
                        esModuloActual(
                            modulo
                        )
                    ) {

                        const actual =
                            document.createElement(
                                'span'
                            );

                        actual.className =
                            'rounded-full ' +
                            'bg-[#56928C]/10 ' +
                            'px-2 py-1 ' +
                            'text-[10px] ' +
                            'font-semibold ' +
                            'text-[#3A6B66]';

                        actual.textContent =
                            'Actual';

                        derecha.appendChild(
                            actual
                        );

                    }

                    const flecha =
                        document.createElement(
                            'span'
                        );

                    flecha.className =
                        "select-none whitespace-nowrap " +
                        "material-symbols-rounded " +
                        "text-[17px] font-normal not-italic " +
                        "normal-case leading-none tracking-normal " +
                        "text-gray-300 antialiased " +
                        "[font-feature-settings:'liga'] " +
                        "[font-variation-settings:'FILL'_0,'wght'_400,'GRAD'_0,'opsz'_24]";

                    flecha.textContent =
                        'arrow_forward';

                    derecha.appendChild(
                        flecha
                    );

                    link.appendChild(
                        izquierda
                    );

                    link.appendChild(
                        derecha
                    );

                    return link;

                }

                function actualizarResultados() {

                    const texto =
                        normalizar(
                            input.value
                        );

                    if (
                        input.value.length > 0
                    ) {

                        clearButton
                            .classList
                            .remove(
                                'hidden'
                            );

                        clearButton
                            .classList
                            .add(
                                'flex'
                            );

                    }

                    else {

                        clearButton
                            .classList
                            .add(
                                'hidden'
                            );

                        clearButton
                            .classList
                            .remove(
                                'flex'
                            );

                    }

                    if (
                        texto === ''
                    ) {

                        cerrarDropdown();

                        return;

                    }

                    abrirDropdown();

                    ocultarEstados();

                    if (
                        cargando
                    ) {

                        loading
                            .classList
                            .remove(
                                'hidden'
                            );

                        return;

                    }

                    if (
                        huboError
                    ) {

                        errorBox
                            .classList
                            .remove(
                                'hidden'
                            );

                        errorBox
                            .classList
                            .add(
                                'flex'
                            );

                        return;

                    }

                    const filtrados =
                        modulos.filter(

                            function (
                                modulo
                            ) {

                                const nombre =
                                    normalizar(
                                        modulo.nombre
                                    );

                                const nombreRuta =
                                    normalizar(
                                        modulo.nombre_ruta
                                    );

                                return (

                                    nombre.includes(
                                        texto
                                    )

                                    ||

                                    nombreRuta.includes(
                                        texto
                                    )

                                );

                            }

                        );

                    results.innerHTML =
                        '';

                    if (
                        filtrados.length === 0
                    ) {

                        emptyText.textContent =
                            'Búsqueda: '
                            +
                            input.value;

                        empty
                            .classList
                            .remove(
                                'hidden'
                            );

                        empty
                            .classList
                            .add(
                                'flex'
                            );

                        return;

                    }

                    filtrados.forEach(

                        function (
                            modulo
                        ) {

                            results
                                .appendChild(

                                    crearResultado(
                                        modulo
                                    )

                                );

                        }

                    );

                    results
                        .classList
                        .remove(
                            'hidden'
                        );

                }

                async function cargarModulos() {

                    cargando =
                        true;

                    huboError =
                        false;

                    try {

                        const response =

                            await fetch(

                                endpoint,

                                {

                                    method:
                                        'GET',

                                    credentials:
                                        'same-origin',

                                    headers: {

                                        'Accept':
                                            'application/json',

                                        'X-Requested-With':
                                            'XMLHttpRequest'

                                    }

                                }

                            );

                        if (
                            !response.ok
                        ) {

                            throw new Error(

                                'Error HTTP '
                                +
                                response.status

                            );

                        }

                        const datos =

                            await response
                                .json();

                        modulos =

                            Array.isArray(
                                datos
                            )

                                ?

                                datos

                                :

                                [];

                    }

                    catch (
                        error
                    ) {

                        console.error(

                            'Error al cargar módulos:',

                            error

                        );

                        modulos =
                            [];

                        huboError =
                            true;

                    }

                    finally {

                        cargando =
                            false;

                        actualizarResultados();

                    }

                }

                input.addEventListener(

                    'input',

                    actualizarResultados

                );

                input.addEventListener(

                    'focus',

                    function () {

                        if (
                            input.value
                                .trim() !== ''
                        ) {

                            actualizarResultados();

                        }

                    }

                );

                input.addEventListener(

                    'keydown',

                    function (
                        event
                    ) {

                        if (
                            event.key ===
                            'Escape'
                        ) {

                            input.value =
                                '';

                            actualizarResultados();

                            input.blur();

                        }

                        if (
                            event.key ===
                            'Enter'
                        ) {

                            const primerResultado =

                                results
                                    .querySelector(
                                        'a'
                                    );

                            if (
                                primerResultado
                            ) {

                                event
                                    .preventDefault();

                                window.location.href =
                                    primerResultado
                                        .href;

                            }

                        }

                    }

                );

                clearButton
                    .addEventListener(

                        'click',

                        function () {

                            input.value =
                                '';

                            actualizarResultados();

                            input.focus();

                        }

                    );

                retryButton
                    .addEventListener(

                        'click',

                        cargarModulos

                    );

                document.addEventListener(

                    'click',

                    function (
                        event
                    ) {

                        if (

                            !container.contains(
                                event.target
                            )

                        ) {

                            cerrarDropdown();

                        }

                    }

                );

                cargarModulos();

            }

        );

    </script>

</body>

</html>