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

                $usuarioActual = auth()->user();

                $modulosBusqueda = [];

                /*
                |--------------------------------------------------------------------------
                | FN.23 - NOTIFICACIONES DEL USUARIO AUTENTICADO
                |--------------------------------------------------------------------------
                */

                $notificacionesHeader = $usuarioActual
                    ->notifications()
                    ->latest()
                    ->take(10)
                    ->get()
                    ->map(function ($notificacion) {

                        return [
                            'id' => $notificacion->id,
                            'tipo' => $notificacion->data['tipo'] ?? 'sistema',
                            'titulo' => $notificacion->data['titulo'] ?? 'Notificación',
                            'mensaje' => $notificacion->data['mensaje'] ?? '',
                            'url' => $notificacion->data['url'] ?? null,
                            'datos' => $notificacion->data['datos'] ?? [],
                            'leida' => !is_null($notificacion->read_at),
                            'fecha' => $notificacion->created_at?->format('d/m/Y H:i:s'),
                            'hace' => $notificacion->created_at?->diffForHumans(),
                        ];

                    })
                    ->values();

                $notificacionesNoLeidas = $usuarioActual
                    ->unreadNotifications()
                    ->count();


                /*
                |--------------------------------------------------------------------------
                | MAPA EN VIVO / DASHBOARD
                |--------------------------------------------------------------------------
                */

                if ($usuarioActual->can('ver mapas')) {

                    $modulosBusqueda[] = [
                        'nombre' => 'Mapa en Vivo / Dashboard',
                        'buscar' => 'mapa dashboard monitoreo rastreo en vivo inicio',
                        'ruta' => route('dashboard'),
                        'activo' => request()->routeIs('dashboard'),
                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | EMPRESAS
                |--------------------------------------------------------------------------
                */

                if (
                    $usuarioActual->hasRole(
                        'Super Administrador'
                    )
                ) {

                    $modulosBusqueda[] = [
                        'nombre' => 'Empresas',
                        'buscar' => 'empresas clientes saas corporativos',
                        'ruta' => route('empresas.index'),
                        'activo' => request()->routeIs('empresas.*'),
                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | USUARIOS
                |--------------------------------------------------------------------------
                */

                if (
                    $usuarioActual->hasAnyRole([
                        'Super Administrador',
                        'Administrador de Empresa',
                    ])
                ) {

                    $modulosBusqueda[] = [
                        'nombre' => 'Usuarios',
                        'buscar' => 'usuarios roles cuentas personas',
                        'ruta' => route('usuarios.index'),
                        'activo' => request()->routeIs('usuarios.*'),
                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | CONDUCTORES
                |--------------------------------------------------------------------------
                */

                if (
                    $usuarioActual->hasAnyRole([
                        'Super Administrador',
                        'Administrador de Empresa',
                        'Gestor de flotilla',
                    ])
                ) {

                    $modulosBusqueda[] = [
                        'nombre' => 'Conductores',
                        'buscar' => 'conductores choferes licencias',
                        'ruta' => route('conductores.index'),
                        'activo' => request()->routeIs('conductores.*'),
                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | FLOTILLAS
                |--------------------------------------------------------------------------
                */

                if (
                    $usuarioActual->hasAnyRole([
                        'Super Administrador',
                        'Administrador de Empresa',
                    ])
                ) {

                    $modulosBusqueda[] = [
                        'nombre' => 'Flotillas',
                        'buscar' => 'flotillas grupos unidades vehiculos',
                        'ruta' => route('flotillas.index'),
                        'activo' => request()->routeIs('flotillas.*'),
                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | VEHÍCULOS
                |--------------------------------------------------------------------------
                */

                if ($usuarioActual->can('gestionar vehiculos')) {

                    $modulosBusqueda[] = [
                        'nombre' => 'Vehículos',
                        'buscar' => 'vehiculos unidades autos coches',
                        'ruta' => route('vehiculos.index'),
                        'activo' => request()->routeIs('vehiculos.*'),
                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | HISTORIAL DE RUTAS
                |--------------------------------------------------------------------------
                */

                if ($usuarioActual->can('ver historial de rutas')) {

                    $modulosBusqueda[] = [
                        'nombre' => 'Historial de rutas',
                        'buscar' => 'historial rutas recorridos reproductor trayectos gps',
                        'ruta' => route('rutas.index'),
                        'activo' => request()->routeIs('rutas.*')
                            || request()->routeIs('vehiculos.ruta'),
                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | INSPECCIONES
                |--------------------------------------------------------------------------
                */

                if ($usuarioActual->can('gestionar inspecciones')) {

                    $modulosBusqueda[] = [
                        'nombre' => 'Inspecciones vehiculares',
                        'buscar' => 'inspecciones vehiculares revisión revision incidencias checklist',
                        'ruta' => route('inspecciones.index'),
                        'activo' => request()->routeIs('inspecciones.*'),
                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | DISPOSITIVOS GPS
                |--------------------------------------------------------------------------
                */

                if ($usuarioActual->can('gestionar dispositivos')) {

                    $modulosBusqueda[] = [
                        'nombre' => 'Dispositivos GPS',
                        'buscar' => 'dispositivos gps equipos rastreadores',
                        'ruta' => route('dispositivos.index'),
                        'activo' => request()->routeIs('dispositivos.*'),
                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | GEOCERCAS
                |--------------------------------------------------------------------------
                */

                if ($usuarioActual->can('gestionar zonas')) {

                    $modulosBusqueda[] = [
                        'nombre' => 'Geocercas / Zonas',
                        'buscar' => 'geocercas zonas áreas areas perímetros perimetros',
                        'ruta' => route('zonas.index'),
                        'activo' => request()->routeIs('zonas.*'),
                    ];


                    $modulosBusqueda[] = [
                        'nombre' => 'Historial de zonas',
                        'buscar' => 'historial zonas geocercas entradas salidas eventos',
                        'ruta' => route('geocercas.historial'),
                        'activo' => request()->routeIs('geocercas.historial'),
                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | REPORTES FN.15 - FN.20
                |--------------------------------------------------------------------------
                */

                if ($usuarioActual->can('gestionar reportes')) {

                    $modulosBusqueda[] = [
                        'nombre' => 'Reporte - Kilometraje y recorridos',
                        'buscar' => 'reportes reporte kilometraje recorridos rutas distancia fn15',
                        'ruta' => route('reportes.kilometraje'),
                        'activo' => request()->routeIs('reportes.kilometraje*'),
                    ];


                    $modulosBusqueda[] = [
                        'nombre' => 'Reporte - Alertas y exceso de velocidad',
                        'buscar' => 'reportes reporte alertas velocidad exceso fn16',
                        'ruta' => route('reportes.alertas'),
                        'activo' => request()->routeIs('reportes.alertas*'),
                    ];


                    $modulosBusqueda[] = [
                        'nombre' => 'Reporte - Visitas y tiempos en zonas',
                        'buscar' => 'reportes reporte zonas geocercas visitas tiempos fn17',
                        'ruta' => route('reportes.zonas'),
                        'activo' => request()->routeIs('reportes.zonas*'),
                    ];


                    $modulosBusqueda[] = [
                        'nombre' => 'Reporte - Histórico de inspecciones',
                        'buscar' => 'reportes reporte inspecciones historico histórico incidencias fn18',
                        'ruta' => route('reportes.inspecciones'),
                        'activo' => request()->routeIs('reportes.inspecciones*'),
                    ];


                    $modulosBusqueda[] = [
                        'nombre' => 'Reporte - Utilización de flotillas',
                        'buscar' => 'reportes reporte utilización utilizacion flotillas uso fn19',
                        'ruta' => route('reportes.flotillas'),
                        'activo' => request()->routeIs('reportes.flotillas*'),
                    ];


                    $modulosBusqueda[] = [
                        'nombre' => 'Reporte - Actividad por conductor',
                        'buscar' => 'reportes reporte conductores actividad chofer fn20',
                        'ruta' => route('reportes.conductores'),
                        'activo' => request()->routeIs('reportes.conductores*'),
                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | ENLACES DE SEGUIMIENTO TEMPORAL
                |--------------------------------------------------------------------------
                */

                if (
                    $usuarioActual->can(
                        'gestionar enlaces temporales'
                    )
                ) {

                    $modulosBusqueda[] = [
                        'nombre' => 'Enlaces de seguimiento',
                        'buscar' => 'enlaces seguimiento temporal compartir rastreo publico público',
                        'ruta' => route('enlaces.index'),
                        'activo' => request()->routeIs('enlaces.*'),
                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | RESPALDOS FN.21 / FN.22
                |--------------------------------------------------------------------------
                */

                if (
                    $usuarioActual->hasRole(
                        'Super Administrador'
                    )
                ) {

                    $modulosBusqueda[] = [
                        'nombre' => 'Respaldos de base de datos',
                        'buscar' => 'respaldos respaldo backup restaurar restauración restauracion base de datos',
                        'ruta' => route('backups.index'),
                        'activo' => request()->routeIs('backups.*'),
                    ];

                }


                /*
                |--------------------------------------------------------------------------
                | NOTIFICACIONES FN.23
                |--------------------------------------------------------------------------
                */

                $modulosBusqueda[] = [
                    'nombre' => 'Notificaciones',
                    'buscar' => 'notificaciones alertas avisos campana eventos',
                    'ruta' => route('notificaciones.index'),
                    'activo' => request()->routeIs('notificaciones.index'),
                ];

            @endphp



            {{-- ================================================== --}}
            {{-- HEADER --}}
            {{-- ================================================== --}}

            <header

                x-data="{ openPerfil: false }"

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
                    id="buscador-modulos-contenedor"
                    class="hidden sm:block flex-1 max-w-md relative"
                >

                    <div class="relative">

                        <span
                            class="absolute inset-y-0 left-0
                                   flex items-center pl-3
                                   text-gray-400 pointer-events-none"
                        >
                            <span class="material-symbols-rounded text-[19px]">
                                search
                            </span>
                        </span>


                        <input
                            id="buscador-modulos-input"
                            type="text"
                            placeholder="Buscar módulo..."
                            autocomplete="off"
                            class="w-full
                                   pl-10 pr-10 py-2.5
                                   text-sm text-gray-700
                                   bg-gray-50
                                   border border-gray-200
                                   rounded-xl
                                   focus:border-[#56928C]
                                   focus:ring-[#56928C]
                                   transition-colors"
                        >


                        <button
                            id="buscador-modulos-limpiar"
                            type="button"
                            title="Limpiar búsqueda"
                            class="absolute inset-y-0 right-0
                                   hidden items-center pr-3
                                   text-gray-400 hover:text-gray-600"
                        >
                            <span class="material-symbols-rounded text-[18px]">
                                close
                            </span>
                        </button>

                    </div>


                    <div
                        id="buscador-modulos-panel"
                        class="absolute left-0 right-0 mt-2
                               hidden
                               bg-white
                               border border-gray-200
                               rounded-xl
                               shadow-xl
                               overflow-hidden
                               z-[100]"
                    >
                        <div
                            id="buscador-modulos-resultados"
                            class="py-1"
                        ></div>
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
                        id="notificaciones-container"
                        class="relative"
                    >

                        <button
                            id="notificaciones-boton"
                            type="button"
                            title="Notificaciones"
                            aria-expanded="false"
                            aria-controls="notificaciones-panel"
                            class="relative p-2 text-gray-400
                                   hover:text-gray-700 hover:bg-gray-100
                                   rounded-lg transition-colors
                                   focus:outline-none"
                        >
                            <span class="material-symbols-rounded">
                                notifications
                            </span>

                            <span
                                id="notificaciones-contador"
                                class="absolute -top-1 -right-1
                                       min-w-[18px] h-[18px]
                                       items-center justify-center
                                       rounded-full bg-rose-500 px-1
                                       text-[10px] font-bold leading-none
                                       text-white ring-2 ring-white
                                       {{ $notificacionesNoLeidas > 0 ? 'inline-flex' : 'hidden' }}"
                            >
                                {{ $notificacionesNoLeidas > 99 ? '99+' : $notificacionesNoLeidas }}
                            </span>
                        </button>


                        {{-- PANEL DE NOTIFICACIONES --}}

                        <div
                            id="notificaciones-panel"
                            class="absolute right-0 mt-2 hidden w-96
                                   overflow-hidden rounded-2xl
                                   border border-gray-200 bg-white
                                   shadow-xl z-[100]"
                        >

                            <div
                                class="flex items-center justify-between gap-3
                                       border-b border-gray-100
                                       bg-gray-50/60 px-4 py-3"
                            >
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900">
                                        Notificaciones
                                    </h3>

                                    <p
                                        id="notificaciones-resumen"
                                        class="mt-0.5 text-xs text-gray-400"
                                    >
                                        {{ $notificacionesNoLeidas === 1
                                            ? '1 sin leer'
                                            : $notificacionesNoLeidas . ' sin leer' }}
                                    </p>
                                </div>

                                <button
                                    id="notificaciones-marcar-todas"
                                    type="button"
                                    class="text-xs font-medium
                                           text-[#0056b3] hover:text-[#003d82]
                                           {{ $notificacionesNoLeidas > 0 ? '' : 'hidden' }}"
                                >
                                    Marcar todas
                                </button>
                            </div>


                            <div
                                id="notificaciones-lista"
                                class="max-h-96 overflow-y-auto"
                            >
                                @forelse($notificacionesHeader as $notificacion)

                                    @php
                                        $tipoNotificacion = strtolower(
                                            (string) ($notificacion['tipo'] ?? '')
                                        );

                                        $iconoNotificacion = 'notifications';

                                        if (str_contains($tipoNotificacion, 'gps')) {
                                            $iconoNotificacion = 'gps_off';
                                        } elseif (
                                            str_contains($tipoNotificacion, 'zona')
                                            || str_contains($tipoNotificacion, 'geocerca')
                                        ) {
                                            $iconoNotificacion = 'share_location';
                                        } elseif (
                                            str_contains($tipoNotificacion, 'velocidad')
                                            || str_contains($tipoNotificacion, 'alerta')
                                        ) {
                                            $iconoNotificacion = 'speed';
                                        } elseif (str_contains($tipoNotificacion, 'inspeccion')) {
                                            $iconoNotificacion = 'fact_check';
                                        } elseif (str_contains($tipoNotificacion, 'licencia')) {
                                            $iconoNotificacion = 'badge';
                                        }
                                    @endphp

                                    <button
                                        type="button"
                                        data-notificacion-id="{{ $notificacion['id'] }}"
                                        data-notificacion-url="{{ $notificacion['url'] ?? '' }}"
                                        data-notificacion-leida="{{ $notificacion['leida'] ? '1' : '0' }}"
                                        class="notificacion-item w-full
                                               border-b border-gray-100
                                               px-4 py-3 text-left transition-colors
                                               last:border-b-0 hover:bg-gray-50
                                               {{ !$notificacion['leida']
                                                    ? 'bg-blue-50/60'
                                                    : 'bg-white' }}"
                                    >
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="mt-0.5 flex h-9 w-9 shrink-0
                                                       items-center justify-center rounded-xl
                                                       {{ !$notificacion['leida']
                                                            ? 'bg-[#0056b3]/10 text-[#0056b3]'
                                                            : 'bg-gray-100 text-gray-400' }}"
                                            >
                                                <span class="material-symbols-rounded text-[19px]">
                                                    {{ $iconoNotificacion }}
                                                </span>
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-start justify-between gap-2">
                                                    <p class="truncate text-sm font-semibold text-gray-800">
                                                        {{ $notificacion['titulo'] }}
                                                    </p>

                                                    @if(!$notificacion['leida'])
                                                        <span
                                                            class="notificacion-punto mt-1 h-2 w-2 shrink-0
                                                                   rounded-full bg-[#0056b3]"
                                                        ></span>
                                                    @endif
                                                </div>

                                                <p
                                                    class="mt-1 line-clamp-2 text-xs
                                                           leading-relaxed text-gray-500"
                                                >
                                                    {{ $notificacion['mensaje'] }}
                                                </p>

                                                <p class="mt-1.5 text-[11px] text-gray-400">
                                                    {{ $notificacion['hace'] ?? $notificacion['fecha'] }}
                                                </p>
                                            </div>
                                        </div>
                                    </button>

                                @empty

                                    <div
                                        id="notificaciones-vacio"
                                        class="flex flex-col items-center justify-center
                                               gap-2 px-4 py-10 text-center"
                                    >
                                        <div
                                            class="flex h-10 w-10 items-center justify-center
                                                   rounded-full bg-gray-100 text-gray-400"
                                        >
                                            <span class="material-symbols-rounded">
                                                notifications_none
                                            </span>
                                        </div>

                                        <p class="text-sm text-gray-500">
                                            No tienes notificaciones.
                                        </p>
                                    </div>

                                @endforelse
                            </div>

                        </div>

                    </div>


                    {{-- ================================================== --}}
                    {{-- PERFIL --}}
                    {{-- ================================================== --}}

                    @php
                        $usuarioHeader = auth()->user();

                        $inicialesHeader =
                            mb_strtoupper(
                                mb_substr(
                                    $usuarioHeader->nombre ?? 'U',
                                    0,
                                    1
                                )
                                .
                                mb_substr(
                                    $usuarioHeader->apellido_paterno ?? '',
                                    0,
                                    1
                                )
                            );

                        $avatarHeaderUrl =
                            $usuarioHeader->avatar
                                ? route(
                                    'profile.avatar.show'
                                )
                                    .
                                    '?v='
                                    .
                                    (
                                        $usuarioHeader
                                            ->updated_at
                                            ?->timestamp
                                        ??
                                        time()
                                    )
                                : null;
                    @endphp


                    <div
                        id="perfil-menu-contenedor"
                        class="relative"
                    >

                        <button
                            id="perfil-menu-boton"
                            type="button"
                            aria-haspopup="true"
                            aria-expanded="false"
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
                                       bg-[#0056b3]
                                       border border-gray-200
                                       text-white
                                       flex
                                       items-center
                                       justify-center
                                       text-xs
                                       font-bold
                                       shrink-0"
                            >

                                <span
                                    id="perfil-menu-iniciales"
                                    @if($avatarHeaderUrl)
                                        class="hidden"
                                    @endif
                                >
                                    {{ $inicialesHeader ?: 'U' }}
                                </span>


                                @if($avatarHeaderUrl)

                                    <img
                                        id="perfil-menu-avatar"
                                        src="{{ $avatarHeaderUrl }}"
                                        class="w-full
                                               h-full
                                               object-cover"
                                        alt="{{ $usuarioHeader->nombre_completo }}"
                                        onerror="
                                            this.classList.add('hidden');
                                            document.getElementById(
                                                'perfil-menu-iniciales'
                                            )?.classList.remove('hidden');
                                        "
                                    >

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
                                {{ $usuarioHeader->nombre }}
                            </span>


                            {{-- FLECHA --}}
                            <span
                                id="perfil-menu-flecha"
                                class="material-symbols-rounded
                                       text-[18px]
                                       text-gray-400
                                       transition-transform
                                       duration-200"
                            >
                                expand_more
                            </span>

                        </button>


                        {{-- DROPDOWN PERFIL --}}
                        <div
                            id="perfil-menu-dropdown"
                            class="hidden
                                   absolute
                                   right-0
                                   mt-2
                                   w-60
                                   bg-white
                                   border border-gray-200
                                   rounded-2xl
                                   shadow-xl
                                   overflow-hidden
                                   z-[120]"
                        >

                            <div
                                class="p-4
                                       border-b
                                       border-gray-100
                                       bg-gray-50/70"
                            >

                                <div
                                    class="flex
                                           items-center
                                           gap-3"
                                >

                                    <div
                                        class="w-10 h-10
                                               rounded-full
                                               overflow-hidden
                                               bg-[#0056b3]
                                               text-white
                                               flex
                                               items-center
                                               justify-center
                                               text-sm
                                               font-bold
                                               shrink-0"
                                    >

                                        <span
                                            id="perfil-dropdown-iniciales"
                                            @if($avatarHeaderUrl)
                                                class="hidden"
                                            @endif
                                        >
                                            {{ $inicialesHeader ?: 'U' }}
                                        </span>


                                        @if($avatarHeaderUrl)

                                            <img
                                                src="{{ $avatarHeaderUrl }}"
                                                class="w-full
                                                       h-full
                                                       object-cover"
                                                alt="{{ $usuarioHeader->nombre_completo }}"
                                                onerror="
                                                    this.classList.add('hidden');
                                                    document.getElementById(
                                                        'perfil-dropdown-iniciales'
                                                    )?.classList.remove('hidden');
                                                "
                                            >

                                        @endif

                                    </div>


                                    <div class="min-w-0">

                                        <p
                                            class="text-sm
                                                   font-semibold
                                                   text-gray-900
                                                   truncate"
                                        >
                                            {{ $usuarioHeader->nombre_completo }}
                                        </p>

                                        <p
                                            class="text-xs
                                                   text-gray-400
                                                   truncate"
                                        >
                                            {{ $usuarioHeader->email }}
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <div class="py-1">

                                <a
                                    href="{{ route('profile.edit') }}"
                                    class="flex
                                           items-center
                                           gap-2.5
                                           px-4 py-3
                                           text-sm
                                           font-medium
                                           text-gray-700
                                           hover:bg-blue-50
                                           hover:text-[#0056b3]
                                           transition-colors"
                                >

                                    <span
                                        class="material-symbols-rounded
                                               text-[19px]
                                               text-[#0056b3]"
                                    >
                                        person
                                    </span>

                                    Mi perfil

                                </a>

                            </div>

                        </div>

                    </div>


                    <script>
                        document.addEventListener(
                            'DOMContentLoaded',
                            function () {

                                const contenedor =
                                    document.getElementById(
                                        'perfil-menu-contenedor'
                                    );

                                const boton =
                                    document.getElementById(
                                        'perfil-menu-boton'
                                    );

                                const dropdown =
                                    document.getElementById(
                                        'perfil-menu-dropdown'
                                    );

                                const flecha =
                                    document.getElementById(
                                        'perfil-menu-flecha'
                                    );


                                if (
                                    !contenedor
                                    ||
                                    !boton
                                    ||
                                    !dropdown
                                ) {
                                    return;
                                }


                                function cerrarMenu()
                                {
                                    dropdown.classList.add(
                                        'hidden'
                                    );

                                    boton.setAttribute(
                                        'aria-expanded',
                                        'false'
                                    );

                                    if (flecha) {
                                        flecha.classList.remove(
                                            'rotate-180'
                                        );
                                    }
                                }


                                function abrirMenu()
                                {
                                    dropdown.classList.remove(
                                        'hidden'
                                    );

                                    boton.setAttribute(
                                        'aria-expanded',
                                        'true'
                                    );

                                    if (flecha) {
                                        flecha.classList.add(
                                            'rotate-180'
                                        );
                                    }
                                }


                                boton.addEventListener(
                                    'click',
                                    function (event) {

                                        event.stopPropagation();

                                        const abierto =
                                            !dropdown.classList.contains(
                                                'hidden'
                                            );

                                        if (abierto) {
                                            cerrarMenu();
                                        } else {
                                            abrirMenu();
                                        }
                                    }
                                );


                                dropdown.addEventListener(
                                    'click',
                                    function (event) {
                                        event.stopPropagation();
                                    }
                                );


                                document.addEventListener(
                                    'click',
                                    function () {
                                        cerrarMenu();
                                    }
                                );


                                document.addEventListener(
                                    'keydown',
                                    function (event) {

                                        if (
                                            event.key
                                            ===
                                            'Escape'
                                        ) {
                                            cerrarMenu();
                                        }
                                    }
                                );

                            }
                        );
                    </script>


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



    {{-- ====================================================== --}}
    {{-- BUSCADOR DE MÓDULOS SIN DEPENDER DE ALPINE --}}
    {{-- ====================================================== --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const contenedor =
                document.getElementById(
                    'buscador-modulos-contenedor'
                );

            const input =
                document.getElementById(
                    'buscador-modulos-input'
                );

            const panel =
                document.getElementById(
                    'buscador-modulos-panel'
                );

            const resultados =
                document.getElementById(
                    'buscador-modulos-resultados'
                );

            const limpiar =
                document.getElementById(
                    'buscador-modulos-limpiar'
                );

            if (
                !contenedor
                ||
                !input
                ||
                !panel
                ||
                !resultados
                ||
                !limpiar
            ) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | IMPORTANTE:
            | Esta lista YA VIENE FILTRADA POR LOS ROLES/PERMISOS DEL USUARIO
            | desde PHP. El buscador no inventa ni agrega accesos.
            |--------------------------------------------------------------------------
            */

            const modulos =
                @json($modulosBusqueda);


            function normalizar(texto) {

                return String(texto || '')
                    .normalize('NFD')
                    .replace(
                        /[\u0300-\u036f]/g,
                        ''
                    )
                    .toLowerCase()
                    .trim();
            }


            function cerrarPanel() {

                panel.classList.add(
                    'hidden'
                );
            }


            function abrirPanel() {

                panel.classList.remove(
                    'hidden'
                );
            }


            function limpiarBusqueda() {

                input.value =
                    '';

                resultados.innerHTML =
                    '';

                limpiar.classList.add(
                    'hidden'
                );

                limpiar.classList.remove(
                    'flex'
                );

                cerrarPanel();

                input.focus();
            }


            function crearResultado(
                modulo
            ) {

                const enlace =
                    document.createElement(
                        'a'
                    );

                enlace.href =
                    modulo.ruta;

                enlace.className =
                    'flex items-center justify-between gap-3 '
                    +
                    'px-4 py-3 text-sm text-gray-700 '
                    +
                    'hover:bg-[#56928C]/10 transition-colors';


                const izquierda =
                    document.createElement(
                        'div'
                    );

                izquierda.className =
                    'flex items-center gap-3 min-w-0';


                const cajaIcono =
                    document.createElement(
                        'span'
                    );

                cajaIcono.className =
                    'w-8 h-8 shrink-0 flex items-center '
                    +
                    'justify-center rounded-lg '
                    +
                    'bg-[#56928C]/10 text-[#56928C]';


                const icono =
                    document.createElement(
                        'span'
                    );

                icono.className =
                    'material-symbols-rounded text-[18px]';

                icono.textContent =
                    'apps';


                const nombre =
                    document.createElement(
                        'span'
                    );

                nombre.className =
                    'font-medium truncate';

                nombre.textContent =
                    modulo.nombre;


                cajaIcono.appendChild(
                    icono
                );

                izquierda.appendChild(
                    cajaIcono
                );

                izquierda.appendChild(
                    nombre
                );


                const derecha =
                    document.createElement(
                        'div'
                    );

                derecha.className =
                    'flex items-center gap-2 shrink-0';


                if (modulo.activo) {

                    const actual =
                        document.createElement(
                            'span'
                        );

                    actual.className =
                        'text-[10px] font-semibold '
                        +
                        'text-[#3A6B66] '
                        +
                        'bg-[#56928C]/10 '
                        +
                        'px-2 py-1 rounded-full';

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
                    'material-symbols-rounded '
                    +
                    'text-[17px] text-gray-300';

                flecha.textContent =
                    'arrow_forward';

                derecha.appendChild(
                    flecha
                );


                enlace.appendChild(
                    izquierda
                );

                enlace.appendChild(
                    derecha
                );


                return enlace;
            }


            function crearSinResultados(
                texto
            ) {

                const vacio =
                    document.createElement(
                        'div'
                    );

                vacio.className =
                    'flex flex-col items-center justify-center '
                    +
                    'gap-1 px-4 py-6 text-center';


                const icono =
                    document.createElement(
                        'span'
                    );

                icono.className =
                    'material-symbols-rounded '
                    +
                    'text-[30px] text-gray-300';

                icono.textContent =
                    'search_off';


                const titulo =
                    document.createElement(
                        'span'
                    );

                titulo.className =
                    'text-sm font-medium text-gray-500';

                titulo.textContent =
                    'No se encontró ningún módulo';


                const detalle =
                    document.createElement(
                        'span'
                    );

                detalle.className =
                    'text-xs text-gray-400';

                detalle.textContent =
                    'Búsqueda: '
                    +
                    texto;


                vacio.appendChild(
                    icono
                );

                vacio.appendChild(
                    titulo
                );

                vacio.appendChild(
                    detalle
                );


                return vacio;
            }


            function obtenerCoincidencias() {

                const texto =
                    normalizar(
                        input.value
                    );

                if (texto === '') {
                    return [];
                }


                return modulos.filter(
                    function (modulo) {

                        const contenido =
                            normalizar(
                                (
                                    modulo.nombre
                                    || ''
                                )
                                +
                                ' '
                                +
                                (
                                    modulo.buscar
                                    || ''
                                )
                            );

                        return contenido.includes(
                            texto
                        );
                    }
                );
            }


            function renderizar() {

                const textoOriginal =
                    input.value.trim();

                resultados.innerHTML =
                    '';


                if (textoOriginal === '') {

                    limpiar.classList.add(
                        'hidden'
                    );

                    limpiar.classList.remove(
                        'flex'
                    );

                    cerrarPanel();

                    return;
                }


                limpiar.classList.remove(
                    'hidden'
                );

                limpiar.classList.add(
                    'flex'
                );


                const coincidencias =
                    obtenerCoincidencias();


                if (
                    coincidencias.length
                    ===
                    0
                ) {

                    resultados.appendChild(
                        crearSinResultados(
                            textoOriginal
                        )
                    );

                    abrirPanel();

                    return;
                }


                coincidencias.forEach(
                    function (modulo) {

                        resultados.appendChild(
                            crearResultado(
                                modulo
                            )
                        );
                    }
                );


                abrirPanel();
            }


            input.addEventListener(
                'input',
                renderizar
            );


            input.addEventListener(
                'focus',
                function () {

                    if (
                        input.value.trim()
                        !==
                        ''
                    ) {
                        renderizar();
                    }
                }
            );


            input.addEventListener(
                'keydown',
                function (event) {

                    if (
                        event.key
                        ===
                        'Escape'
                    ) {

                        limpiarBusqueda();

                        return;
                    }


                    if (
                        event.key
                        ===
                        'Enter'
                    ) {

                        const coincidencias =
                            obtenerCoincidencias();

                        if (
                            coincidencias.length
                            >
                            0
                        ) {

                            event.preventDefault();

                            window.location.href =
                                coincidencias[0].ruta;
                        }
                    }
                }
            );


            limpiar.addEventListener(
                'click',
                limpiarBusqueda
            );


            document.addEventListener(
                'click',
                function (event) {

                    if (
                        !contenedor.contains(
                            event.target
                        )
                    ) {

                        cerrarPanel();
                    }
                }
            );

        });
    </script>


    {{-- ====================================================== --}}
    {{-- FN.23 - LÓGICA DE CAMPANA DE NOTIFICACIONES --}}
    {{-- ====================================================== --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const contenedor = document.getElementById('notificaciones-container');
            const boton = document.getElementById('notificaciones-boton');
            const panel = document.getElementById('notificaciones-panel');
            const contador = document.getElementById('notificaciones-contador');
            const resumen = document.getElementById('notificaciones-resumen');
            const marcarTodas = document.getElementById('notificaciones-marcar-todas');
            const lista = document.getElementById('notificaciones-lista');

            if (!contenedor || !boton || !panel || !contador || !resumen || !lista) {
                return;
            }

            const csrf = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content') || '';

            const recientesUrl = @json(route('notificaciones.recientes'));
            const leerTodasUrl = @json(route('notificaciones.leer-todas'));


            function iconoPorTipo(tipo) {
                const valor = String(tipo || '').toLowerCase();

                if (valor.includes('gps')) return 'gps_off';
                if (valor.includes('zona') || valor.includes('geocerca')) return 'share_location';
                if (valor.includes('velocidad') || valor.includes('alerta')) return 'speed';
                if (valor.includes('inspeccion')) return 'fact_check';
                if (valor.includes('licencia')) return 'badge';

                return 'notifications';
            }


            function actualizarContador(cantidad) {
                const numero = Number(cantidad || 0);

                contador.textContent = numero > 99 ? '99+' : String(numero);
                resumen.textContent = numero === 1
                    ? '1 sin leer'
                    : numero + ' sin leer';

                contador.classList.toggle('hidden', numero === 0);
                contador.classList.toggle('inline-flex', numero > 0);

                if (marcarTodas) {
                    marcarTodas.classList.toggle('hidden', numero === 0);
                }
            }


            function crearEstadoVacio() {
                const vacio = document.createElement('div');
                vacio.id = 'notificaciones-vacio';
                vacio.className = 'flex flex-col items-center justify-center gap-2 px-4 py-10 text-center';

                const iconoContenedor = document.createElement('div');
                iconoContenedor.className = 'flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400';

                const icono = document.createElement('span');
                icono.className = 'material-symbols-rounded';
                icono.textContent = 'notifications_none';

                const texto = document.createElement('p');
                texto.className = 'text-sm text-gray-500';
                texto.textContent = 'No tienes notificaciones.';

                iconoContenedor.appendChild(icono);
                vacio.appendChild(iconoContenedor);
                vacio.appendChild(texto);

                return vacio;
            }


            function crearNotificacion(notificacion) {
                const item = document.createElement('button');
                item.type = 'button';
                item.dataset.notificacionId = notificacion.id;
                item.dataset.notificacionUrl = notificacion.url || '';
                item.dataset.notificacionLeida = notificacion.leida ? '1' : '0';
                item.className = 'notificacion-item w-full border-b border-gray-100 px-4 py-3 text-left transition-colors last:border-b-0 hover:bg-gray-50 ' +
                    (notificacion.leida ? 'bg-white' : 'bg-blue-50/60');

                const fila = document.createElement('div');
                fila.className = 'flex items-start gap-3';

                const iconoCaja = document.createElement('div');
                iconoCaja.className = 'mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl ' +
                    (notificacion.leida
                        ? 'bg-gray-100 text-gray-400'
                        : 'bg-[#0056b3]/10 text-[#0056b3]');

                const icono = document.createElement('span');
                icono.className = 'material-symbols-rounded text-[19px]';
                icono.textContent = iconoPorTipo(notificacion.tipo);
                iconoCaja.appendChild(icono);

                const contenido = document.createElement('div');
                contenido.className = 'min-w-0 flex-1';

                const encabezado = document.createElement('div');
                encabezado.className = 'flex items-start justify-between gap-2';

                const titulo = document.createElement('p');
                titulo.className = 'truncate text-sm font-semibold text-gray-800';
                titulo.textContent = notificacion.titulo || 'Notificación';
                encabezado.appendChild(titulo);

                if (!notificacion.leida) {
                    const punto = document.createElement('span');
                    punto.className = 'notificacion-punto mt-1 h-2 w-2 shrink-0 rounded-full bg-[#0056b3]';
                    encabezado.appendChild(punto);
                }

                const mensaje = document.createElement('p');
                mensaje.className = 'mt-1 line-clamp-2 text-xs leading-relaxed text-gray-500';
                mensaje.textContent = notificacion.mensaje || '';

                const fecha = document.createElement('p');
                fecha.className = 'mt-1.5 text-[11px] text-gray-400';
                fecha.textContent = notificacion.hace || notificacion.fecha || '';

                contenido.appendChild(encabezado);
                contenido.appendChild(mensaje);
                contenido.appendChild(fecha);

                fila.appendChild(iconoCaja);
                fila.appendChild(contenido);
                item.appendChild(fila);

                return item;
            }


            function renderizar(notificaciones) {
                lista.innerHTML = '';

                if (!Array.isArray(notificaciones) || notificaciones.length === 0) {
                    lista.appendChild(crearEstadoVacio());
                    return;
                }

                notificaciones.forEach(function (notificacion) {
                    lista.appendChild(crearNotificacion(notificacion));
                });
            }


            async function cargarNotificaciones() {
                try {
                    const respuesta = await fetch(recientesUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        cache: 'no-store'
                    });

                    if (!respuesta.ok) {
                        return;
                    }

                    const datos = await respuesta.json();

                    actualizarContador(datos.no_leidas || 0);
                    renderizar(datos.notificaciones || []);

                } catch (error) {
                    console.error('No fue posible actualizar las notificaciones.', error);
                }
            }


            async function marcarComoLeida(id) {
                try {
                    const respuesta = await fetch(
                        '/notificaciones/' + encodeURIComponent(id) + '/leer',
                        {
                            method: 'PATCH',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrf
                            },
                            credentials: 'same-origin'
                        }
                    );

                    return respuesta.ok;

                } catch (error) {
                    console.error('No fue posible marcar la notificación como leída.', error);
                    return false;
                }
            }


            boton.addEventListener('click', function (event) {
                event.stopPropagation();

                const abrir = panel.classList.contains('hidden');
                panel.classList.toggle('hidden', !abrir);
                boton.setAttribute('aria-expanded', abrir ? 'true' : 'false');

                if (abrir) {
                    cargarNotificaciones();
                }
            });


            document.addEventListener('click', function (event) {
                if (!contenedor.contains(event.target)) {
                    panel.classList.add('hidden');
                    boton.setAttribute('aria-expanded', 'false');
                }
            });


            lista.addEventListener('click', async function (event) {
                const item = event.target.closest('.notificacion-item');

                if (!item) {
                    return;
                }

                const id = item.dataset.notificacionId;
                const url = item.dataset.notificacionUrl || '';
                const leida = item.dataset.notificacionLeida === '1';

                if (!leida && id) {
                    await marcarComoLeida(id);
                }

                if (url) {
                    window.location.href = url;
                    return;
                }

                await cargarNotificaciones();
            });


            if (marcarTodas) {
                marcarTodas.addEventListener('click', async function () {
                    try {
                        const respuesta = await fetch(leerTodasUrl, {
                            method: 'PATCH',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': csrf
                            },
                            credentials: 'same-origin'
                        });

                        if (respuesta.ok) {
                            await cargarNotificaciones();
                        }

                    } catch (error) {
                        console.error('No fue posible marcar todas las notificaciones.', error);
                    }
                });
            }


            setInterval(cargarNotificaciones, 10000);

        });
    </script>

    
    {{-- ====================================================== --}}
    {{-- FN.24 - LÓGICA DE INACTIVIDAD DE USUARIO --}}
    {{-- ====================================================== --}}
    @include('partials.sidebar-toggle')
    @include('partials.inactividad')
</body>

</html>