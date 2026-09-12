<nav x-data="{
        open: false,
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
     class="bg-white border-b border-gray-100">

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 gap-4">

            <div class="flex items-center shrink-0">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- ================= BUSCADOR DE MÓDULOS ================= -->
            <div class="hidden md:block flex-1 max-w-md relative" @click.away="busqueda = ''">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path>
                        </svg>
                    </span>
                    <input type="text" x-model="busqueda" placeholder="Buscar módulo..."
                           class="w-full pl-9 pr-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-gray-900 focus:border-gray-900 transition-colors">
                </div>

                <!-- Resultados -->
                <div x-show="resultadosBusqueda.length > 0"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     style="display: none;"
                     class="absolute mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-lg overflow-hidden z-40">
                    <template x-for="modulo in resultadosBusqueda" :key="modulo.nombre">
                        <a :href="modulo.ruta"
                           class="flex items-center justify-between px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <span x-text="modulo.nombre"></span>
                            <span x-show="modulo.activo" class="text-[10px] font-medium text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">Actual</span>
                        </a>
                    </template>
                </div>

                <!-- Sin resultados -->
                <div x-show="busqueda.trim() !== '' && resultadosBusqueda.length === 0"
                     x-transition.opacity style="display: none;"
                     class="absolute mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-lg p-4 text-center text-sm text-gray-400 z-40">
                    Sin resultados para "<span x-text="busqueda"></span>"
                </div>
            </div>

            <!-- ================= ACCIONES DERECHA ================= -->
            <div class="hidden sm:flex sm:items-center gap-2 shrink-0">

                <!-- Campanita de notificaciones -->
                <div class="relative" @click.away="openNotificaciones = false">
                    <button @click="openNotificaciones = !openNotificaciones"
                            class="relative p-2 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        <span x-show="notificacionesCount > 0" style="display:none;"
                              class="absolute -top-0.5 -right-0.5 flex h-4 w-4">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex items-center justify-center rounded-full h-4 w-4 bg-rose-500 text-white text-[9px] font-bold" x-text="notificacionesCount"></span>
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
                         class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden z-50">

                        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                            <h3 class="text-sm font-bold text-gray-900">Notificaciones</h3>
                            <span class="text-xs text-gray-400" x-text="notificacionesCount + ' nuevas'"></span>
                        </div>

                        <div class="max-h-80 overflow-y-auto">
                            <!-- Placeholder: aquí se listarán las notificaciones reales cuando el módulo esté implementado -->
                            <div class="flex flex-col items-center justify-center gap-2 py-10 px-4 text-center">
                                <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500">No tienes notificaciones nuevas.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Avatar + Dropdown de perfil -->
                <div class="relative" @click.away="openPerfil = false">
                    <button @click="openPerfil = !openPerfil"
                            class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-gray-100 transition-colors focus:outline-none">
                        <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-900 text-white flex items-center justify-center text-xs font-bold shrink-0">
                            @if(auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" class="w-full h-full object-cover" alt="{{ auth()->user()->nombre_completo }}">
                            @else
                                {{ mb_strtoupper(mb_substr(auth()->user()->nombre, 0, 1) . mb_substr(auth()->user()->apellido_paterno, 0, 1)) }}
                            @endif
                        </div>
                        <span class="text-sm font-medium text-gray-700 max-w-[120px] truncate">{{ auth()->user()->nombre }}</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': openPerfil }" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="openPerfil"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         style="display: none;"
                         class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden z-50">

                        <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->nombre_completo }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                Mi Perfil
                            </a>
                        </div>

                        <div class="py-1 border-t border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                    </svg>
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Botón directo de Cerrar Sesión (acceso rápido, sin abrir el menú) -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Cerrar sesión"
                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- ================= MENÚ RESPONSIVE (MÓVIL) ================= -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">

        <!-- Buscador móvil -->
        <div class="px-4 pt-3">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path>
                    </svg>
                </span>
                <input type="text" x-model="busqueda" placeholder="Buscar módulo..."
                       class="w-full pl-9 pr-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-gray-900 focus:border-gray-900">
            </div>
            <template x-if="resultadosBusqueda.length > 0">
                <div class="mt-2 border border-gray-200 rounded-xl overflow-hidden divide-y divide-gray-100">
                    <template x-for="modulo in resultadosBusqueda" :key="modulo.nombre">
                        <a :href="modulo.ruta" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50" x-text="modulo.nombre"></a>
                    </template>
                </div>
            </template>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center gap-3 px-4">
                <div class="w-10 h-10 rounded-full overflow-hidden bg-gray-900 text-white flex items-center justify-center text-sm font-bold shrink-0">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" class="w-full h-full object-cover" alt="">
                    @else
                        {{ mb_strtoupper(mb_substr(auth()->user()->nombre, 0, 1) . mb_substr(auth()->user()->apellido_paterno, 0, 1)) }}
                    @endif
                </div>
                <div class="min-w-0">
                    <div class="font-medium text-base text-gray-800 truncate">{{ auth()->user()->nombre_completo }}</div>
                    <div class="font-medium text-sm text-gray-500 truncate">{{ auth()->user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <button @click="openNotificaciones = !openNotificaciones" class="w-full text-start block ps-3 pe-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 transition duration-150 ease-in-out">
                    Notificaciones
                    <span x-show="notificacionesCount > 0" style="display:none;" class="ml-1 text-xs font-bold text-rose-500" x-text="'(' + notificacionesCount + ')'"></span>
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>