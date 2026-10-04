<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SotyGeo') }}</title>

    <!-- 1. Google Fonts: Tipografías Ubuntu y Josefin Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">
    
    <!-- 2. Google Material Symbols Rounded (Para los íconos modernos) -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <!-- 3. Leaflet CSS y JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- 4. Vite (Tailwind y Alpine) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- 5. Asignación rápida de fuentes para no tener que editar tailwind.config.js ahora mismo -->
    <style>
        body { font-family: 'Ubuntu', sans-serif; }
        .font-heading { font-family: 'Josefin Sans', sans-serif; }
        
        /* Asegurar que los íconos se alineen correctamente y evitar que se seleccione el texto del ícono */
        .material-symbols-rounded {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            user-select: none;
        }
    </style>
</head>

<!-- Fondo general con el color off-white (#F4F7F6) para diseño limpio -->
<body class="antialiased bg-[#F4F7F6] text-gray-800" x-data="{ sidebarOpen: false, sidebarMini: false }">

    <!-- Contenedor principal con separación externa -->
    <div class="flex h-screen overflow-hidden relative p-4 gap-4">

        <!-- Fondo oscuro transparente para móviles -->
        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden rounded-none"></div>

        <!-- Inyección del Sidebar -->
        @include('sidebar')

        <!-- Columna Derecha (Header + Contenido) -->
        <div class="flex flex-col flex-1 w-full h-full overflow-hidden gap-4">

            <!-- ================= HEADER EN FORMATO TARJETA FLOTANTE ================= -->
            <header x-data="{
                        openPerfil: false,
                        openNotificaciones: false,
                        busqueda: '',
                        notificacionesCount: 0,
                        modulos: {{ Illuminate\Support\Js::from(
                            collect([
                                ['nombre' => 'Dashboard', 'ruta' => route('dashboard'), 'activo' => request()->routeIs('dashboard')],
                            ])
                            ->when(auth()->user()->hasRole('Super Administrador'), fn($c) => $c->push([
                                'nombre' => 'Empresas', 'ruta' => route('empresas.index'), 'activo' => request()->routeIs('empresas.*'),
                            ]))
                            ->when(auth()->user()->hasAnyRole(['Super Administrador', 'Administrador de Empresa']), fn($c) => $c
                                ->push(['nombre' => 'Usuarios', 'ruta' => route('usuarios.index'), 'activo' => request()->routeIs('usuarios.*')])
                                ->push(['nombre' => 'Flotillas', 'ruta' => route('flotillas.index'), 'activo' => request()->routeIs('flotillas.*')])
                            )
                            ->when(auth()->user()->hasAnyRole(['Super Administrador', 'Administrador de Empresa', 'Gestor de flotilla', 'Cliente Individual']), fn($c) => $c
                                ->push(['nombre' => 'Vehículos', 'ruta' => route('vehiculos.index'), 'activo' => request()->routeIs('vehiculos.*')])
                                ->push(['nombre' => 'Dispositivos', 'ruta' => route('dispositivos.index'), 'activo' => request()->routeIs('dispositivos.*')])
                                ->push(['nombre' => 'Geocercas', 'ruta' => route('zonas.index'), 'activo' => request()->routeIs('zonas.*')])
                            )
                            ->values()
                        ) }},
                        get resultadosBusqueda() {
                            if (this.busqueda.trim() === '') return [];
                            const q = this.busqueda.toLowerCase();
                            return this.modulos.filter(m => m.nombre.toLowerCase().includes(q));
                        }
                    }"
                    class="flex items-center justify-between gap-4 px-6 py-4 bg-white border border-gray-200 shadow-sm rounded-2xl shrink-0 z-20">

                <!-- Botón hamburguesa (sidebar móvil) -->
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-[#56928C] focus:outline-none lg:hidden shrink-0 transition-colors">
                    <span class="material-symbols-rounded">menu</span>
                </button>

                <!-- ===== Buscador de módulos ===== -->
                <div class="hidden sm:block flex-1 max-w-md relative" @click.away="busqueda = ''">
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 group-focus-within:text-[#56928C] transition-colors pointer-events-none">
                            <span class="material-symbols-rounded text-[20px]">search</span>
                        </span>
                        <input type="text" x-model="busqueda" placeholder="Buscar módulo o sección..."
                               class="w-full pl-10 pr-4 py-2 text-sm font-medium bg-gray-50 border border-transparent rounded-xl focus:bg-white focus:ring-2 focus:ring-[#56928C]/20 focus:border-[#56928C] transition-all outline-none text-gray-700">
                    </div>

                    <div x-show="resultadosBusqueda.length > 0"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         style="display: none;"
                         class="absolute mt-2 w-full bg-white border border-gray-100 rounded-xl shadow-lg shadow-gray-200/50 overflow-hidden z-40">
                        <template x-for="modulo in resultadosBusqueda" :key="modulo.nombre">
                            <a :href="modulo.ruta" class="flex items-center justify-between px-4 py-3 text-sm text-gray-700 hover:bg-[#56928C]/5 hover:text-[#56928C] transition-colors font-medium border-b border-gray-50 last:border-0">
                                <span x-text="modulo.nombre"></span>
                                <span x-show="modulo.activo" class="text-[10px] font-bold text-[#56928C] bg-[#56928C]/10 px-2 py-1 rounded-md">Actual</span>
                            </a>
                        </template>
                    </div>

                    <div x-show="busqueda.trim() !== '' && resultadosBusqueda.length === 0"
                         x-transition.opacity style="display: none;"
                         class="absolute mt-2 w-full bg-white border border-gray-100 rounded-xl shadow-lg shadow-gray-200/50 p-4 text-center text-sm font-medium text-gray-400 z-40">
                        No se encontraron módulos para "<span x-text="busqueda" class="text-gray-700"></span>"
                    </div>
                </div>

                <!-- ===== Acciones derecha (Notificaciones, Perfil) ===== -->
                <div class="flex items-center gap-3 shrink-0">

                    <!-- Campanita de notificaciones -->
                    <div class="relative" @click.away="openNotificaciones = false">
                        <button @click="openNotificaciones = !openNotificaciones"
                                class="relative p-2 text-gray-400 hover:text-[#56928C] hover:bg-[#56928C]/10 rounded-xl transition-all focus:outline-none">
                            <span class="material-symbols-rounded">notifications</span>
                            
                            <span x-show="notificacionesCount > 0" style="display:none;" class="absolute top-1 right-1 flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-rose-500 border-2 border-white"></span>
                            </span>
                        </button>

                        <div x-show="openNotificaciones"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             style="display: none;"
                             class="absolute right-0 mt-3 w-80 bg-white border border-gray-100 rounded-2xl shadow-xl shadow-gray-200/50 overflow-hidden z-50">

                            <div class="p-4 border-b border-gray-50 flex items-center justify-between bg-white">
                                <h3 class="text-sm font-heading font-bold text-gray-800">Notificaciones</h3>
                                <span class="text-[11px] font-bold text-[#56928C] bg-[#56928C]/10 px-2 py-1 rounded-md" x-text="notificacionesCount + ' nuevas'"></span>
                            </div>

                            <div class="max-h-80 overflow-y-auto bg-gray-50/30">
                                <div class="flex flex-col items-center justify-center gap-3 py-10 px-4 text-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center">
                                        <span class="material-symbols-rounded text-2xl">notifications_off</span>
                                    </div>
                                    <p class="text-sm font-medium text-gray-500">No tienes alertas pendientes.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-px h-6 bg-gray-200 mx-1 hidden sm:block"></div>

                    <!-- Avatar + Dropdown de perfil -->
                    <div class="relative" @click.away="openPerfil = false">
                        <button @click="openPerfil = !openPerfil"
                                class="flex items-center gap-2.5 px-2 py-1.5 rounded-xl hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-all focus:outline-none">
                            <div class="w-9 h-9 rounded-full overflow-hidden bg-[#2A4D49] text-white flex items-center justify-center text-sm font-heading font-bold shrink-0 shadow-sm">
                                @if(auth()->user()->avatar_url)
                                    <img src="{{ auth()->user()->avatar_url }}" class="w-full h-full object-cover" alt="{{ auth()->user()->nombre_completo }}">
                                @else
                                    {{ mb_strtoupper(mb_substr(auth()->user()->nombre, 0, 1) . mb_substr(auth()->user()->apellido_paterno ?? '', 0, 1)) }}
                                @endif
                            </div>
                            <div class="hidden md:flex flex-col items-start">
                                <span class="text-sm font-bold text-gray-700 max-w-[120px] truncate leading-tight">{{ auth()->user()->nombre }}</span>
                                <span class="text-[11px] font-medium text-[#56928C] leading-tight">Activo</span>
                            </div>
                            <span class="material-symbols-rounded text-gray-400 text-sm transition-transform" :class="{ 'rotate-180': openPerfil }">expand_more</span>
                        </button>

                        <div x-show="openPerfil"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             style="display: none;"
                             class="absolute right-0 mt-3 w-64 bg-white border border-gray-100 rounded-2xl shadow-xl shadow-gray-200/50 overflow-hidden z-50">

                            <div class="p-5 border-b border-gray-50 bg-white flex flex-col items-center text-center">
                                <div class="w-14 h-14 rounded-full overflow-hidden bg-[#2A4D49] text-white flex items-center justify-center text-xl font-heading font-bold mb-3 shadow-sm">
                                    {{ mb_strtoupper(mb_substr(auth()->user()->nombre, 0, 1) . mb_substr(auth()->user()->apellido_paterno ?? '', 0, 1)) }}
                                </div>
                                <p class="text-sm font-bold text-gray-900 truncate w-full">{{ auth()->user()->nombre_completo }}</p>
                                <p class="text-xs font-medium text-gray-400 truncate w-full mt-0.5">{{ auth()->user()->email }}</p>
                            </div>

                            <div class="p-2">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-gray-700 hover:bg-[#56928C]/5 hover:text-[#56928C] rounded-xl transition-colors">
                                    <span class="material-symbols-rounded text-[20px]">manage_accounts</span>
                                    Configuración de Perfil
                                </a>
                                
                                <!-- Botón de Salir -->
                                <form method="POST" action="{{ route('logout') }}" class="m-0 mt-1">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
                                        <span class="material-symbols-rounded text-[20px]">logout</span>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </header>

            <!-- ================= CONTENIDO PRINCIPAL ================= -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto custom-scrollbar">
                {{ $slot }}
            </main>

        </div>
    </div>

    <!-- Scripts Adicionales si los requiere alguna vista -->
    @stack('scripts')
</body>
</html>