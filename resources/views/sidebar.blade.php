<style>
    @keyframes navItemIn {
        from { opacity: 0; transform: translateX(-8px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .nav-item-in { animation: navItemIn 0.35s ease-out both; }
    .nav-item-in:nth-child(1) { animation-delay: 0.04s; }
    .nav-item-in:nth-child(2) { animation-delay: 0.08s; }
    .nav-item-in:nth-child(3) { animation-delay: 0.12s; }
    .nav-item-in:nth-child(4) { animation-delay: 0.16s; }
    .nav-item-in:nth-child(5) { animation-delay: 0.20s; }
    .nav-item-in:nth-child(6) { animation-delay: 0.24s; }
</style>

<aside :class="{
         'translate-x-0 w-64': sidebarOpen,
         '-translate-x-[120%] w-64': !sidebarOpen,
         'lg:translate-x-0': true,
         'lg:w-20': sidebarMini,
         'lg:w-64': !sidebarMini
       }"
       class="fixed top-4 bottom-4 left-4 z-50 flex flex-col transition-all duration-300 ease-in-out bg-white/95 backdrop-blur-md border border-[#0056b3]/20 shadow-lg rounded-2xl lg:static lg:inset-auto lg:h-full shrink-0 font-ubuntu relative">

    <!-- Pestaña flotante para contraer/expandir -->
    <button @click="sidebarMini = !sidebarMini" 
            class="hidden lg:flex absolute -right-3 top-10 w-6 h-6 bg-white border border-[#0056b3]/20 rounded-full shadow-md items-center justify-center text-[#0056b3] hover:bg-[#0056b3] hover:text-white transition-colors z-50">
        <span class="material-symbols-rounded text-sm" x-show="!sidebarMini">chevron_left</span>
        <span class="material-symbols-rounded text-sm" x-show="sidebarMini" style="display: none;">chevron_right</span>
    </button>

    <!-- Cabecera del Sidebar -->
    <div class="flex items-center h-16 px-4 border-b border-[#0056b3]/10 shrink-0">
        <div class="flex items-center overflow-hidden w-full">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#3380d0] to-[#0056b3] flex items-center justify-center shrink-0 shadow-md">
                <span class="material-symbols-rounded text-white">satellite_alt</span>
            </div>
            <span x-show="!sidebarMini" class="ml-3 text-2xl font-josefin font-bold text-[#003d82] whitespace-nowrap tracking-tight">SotyGeo</span>
        </div>
    </div>

    <!-- Navegación -->
    <nav class="flex-1 px-3 py-5 space-y-2 overflow-hidden hover:overflow-y-auto scrollbar-hide">
        
        <!-- Mapa en Vivo -->
        <a href="{{ route('dashboard') }}" class="nav-item-in group flex items-center px-2.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-[#0056b3]/10' : 'hover:bg-gray-50' }}">
            <span class="w-10 h-10 flex items-center justify-center rounded-xl transition-all shrink-0 {{ request()->routeIs('dashboard') ? 'bg-[#0056b3] text-white shadow-md shadow-[#0056b3]/30' : 'bg-transparent text-[#0056b3] group-hover:bg-[#0056b3]/10' }}">
                <span class="material-symbols-rounded">explore</span>
            </span>
            <span x-show="!sidebarMini" x-transition.opacity class="ml-3 text-[15px] whitespace-nowrap {{ request()->routeIs('dashboard') ? 'text-[#003d82] font-bold' : 'text-gray-600 font-medium group-hover:text-[#0056b3]' }}">Mapa en Vivo</span>
        </a>

        <!-- EMPRESAS -->
        @role('Super Administrador')
        <a href="{{ route('empresas.index') }}" class="nav-item-in group flex items-center px-2.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('empresas.*') ? 'bg-[#0056b3]/10' : 'hover:bg-gray-50' }}">
            <span class="w-10 h-10 flex items-center justify-center rounded-xl transition-all shrink-0 {{ request()->routeIs('empresas.*') ? 'bg-[#0056b3] text-white shadow-md shadow-[#0056b3]/30' : 'bg-transparent text-[#0056b3] group-hover:bg-[#0056b3]/10' }}">
                <span class="material-symbols-rounded">domain</span>
            </span>
            <span x-show="!sidebarMini" x-transition.opacity class="ml-3 text-[15px] whitespace-nowrap {{ request()->routeIs('empresas.*') ? 'text-[#003d82] font-bold' : 'text-gray-600 font-medium group-hover:text-[#0056b3]' }}">Empresas</span>
        </a>
        @endrole

        <!-- ADMINISTRACIÓN -->
        @hasanyrole('Super Administrador|Administrador de Empresa')
        <a href="{{ route('usuarios.index') }}" class="nav-item-in group flex items-center px-2.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('usuarios.*') ? 'bg-[#0056b3]/10' : 'hover:bg-gray-50' }}">
            <span class="w-10 h-10 flex items-center justify-center rounded-xl transition-all shrink-0 {{ request()->routeIs('usuarios.*') ? 'bg-[#0056b3] text-white shadow-md shadow-[#0056b3]/30' : 'bg-transparent text-[#0056b3] group-hover:bg-[#0056b3]/10' }}">
                <span class="material-symbols-rounded">group</span>
            </span>
            <span x-show="!sidebarMini" x-transition.opacity class="ml-3 text-[15px] whitespace-nowrap {{ request()->routeIs('usuarios.*') ? 'text-[#003d82] font-bold' : 'text-gray-600 font-medium group-hover:text-[#0056b3]' }}">Usuarios</span>
        </a>

        <a href="{{ route('flotillas.index') }}" class="nav-item-in group flex items-center px-2.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('flotillas.*') ? 'bg-[#0056b3]/10' : 'hover:bg-gray-50' }}">
            <span class="w-10 h-10 flex items-center justify-center rounded-xl transition-all shrink-0 {{ request()->routeIs('flotillas.*') ? 'bg-[#0056b3] text-white shadow-md shadow-[#0056b3]/30' : 'bg-transparent text-[#0056b3] group-hover:bg-[#0056b3]/10' }}">
                <span class="material-symbols-rounded">local_shipping</span>
            </span>
            <span x-show="!sidebarMini" x-transition.opacity class="ml-3 text-[15px] whitespace-nowrap {{ request()->routeIs('flotillas.*') ? 'text-[#003d82] font-bold' : 'text-gray-600 font-medium group-hover:text-[#0056b3]' }}">Flotillas</span>
        </a>
        @endhasanyrole

        <!-- MÓDULOS LOGÍSTICOS -->
        @hasanyrole('Super Administrador|Administrador de Empresa|Gestor de flotilla|Cliente Individual')
        <a href="{{ route('vehiculos.index') }}" class="nav-item-in group flex items-center px-2.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('vehiculos.*') ? 'bg-[#0056b3]/10' : 'hover:bg-gray-50' }}">
            <span class="w-10 h-10 flex items-center justify-center rounded-xl transition-all shrink-0 {{ request()->routeIs('vehiculos.*') ? 'bg-[#0056b3] text-white shadow-md shadow-[#0056b3]/30' : 'bg-transparent text-[#0056b3] group-hover:bg-[#0056b3]/10' }}">
                <span class="material-symbols-rounded">directions_car</span>
            </span>
            <span x-show="!sidebarMini" x-transition.opacity class="ml-3 text-[15px] whitespace-nowrap {{ request()->routeIs('vehiculos.*') ? 'text-[#003d82] font-bold' : 'text-gray-600 font-medium group-hover:text-[#0056b3]' }}">Vehículos</span>
        </a>

        <a href="{{ route('dispositivos.index') }}" class="nav-item-in group flex items-center px-2.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('dispositivos.*') ? 'bg-[#0056b3]/10' : 'hover:bg-gray-50' }}">
            <span class="w-10 h-10 flex items-center justify-center rounded-xl transition-all shrink-0 {{ request()->routeIs('dispositivos.*') ? 'bg-[#0056b3] text-white shadow-md shadow-[#0056b3]/30' : 'bg-transparent text-[#0056b3] group-hover:bg-[#0056b3]/10' }}">
                <span class="material-symbols-rounded">router</span>
            </span>
            <span x-show="!sidebarMini" x-transition.opacity class="ml-3 text-[15px] whitespace-nowrap {{ request()->routeIs('dispositivos.*') ? 'text-[#003d82] font-bold' : 'text-gray-600 font-medium group-hover:text-[#0056b3]' }}">Dispositivos</span>
        </a>

        <a href="{{ route('zonas.index') }}" class="nav-item-in group flex items-center px-2.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('zonas.*') ? 'bg-[#0056b3]/10' : 'hover:bg-gray-50' }}">
            <span class="w-10 h-10 flex items-center justify-center rounded-xl transition-all shrink-0 {{ request()->routeIs('zonas.*') ? 'bg-[#0056b3] text-white shadow-md shadow-[#0056b3]/30' : 'bg-transparent text-[#0056b3] group-hover:bg-[#0056b3]/10' }}">
                <span class="material-symbols-rounded">share_location</span>
            </span>
            <span x-show="!sidebarMini" x-transition.opacity class="ml-3 text-[15px] whitespace-nowrap {{ request()->routeIs('zonas.*') ? 'text-[#003d82] font-bold' : 'text-gray-600 font-medium group-hover:text-[#0056b3]' }}">Geocercas</span>
        </a>

        <a href="{{ route('geocercas.historial') }}" class="nav-item-in group flex items-center px-2.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('geocercas.historial') ? 'bg-[#0056b3]/10' : 'hover:bg-gray-50' }}">
            <span class="w-10 h-10 flex items-center justify-center rounded-xl transition-all shrink-0 {{ request()->routeIs('geocercas.historial') ? 'bg-[#0056b3] text-white shadow-md shadow-[#0056b3]/30' : 'bg-transparent text-[#0056b3] group-hover:bg-[#0056b3]/10' }}">
                <span class="material-symbols-rounded">history</span>
            </span>
            <span x-show="!sidebarMini" x-transition.opacity class="ml-3 text-[15px] whitespace-nowrap {{ request()->routeIs('geocercas.historial') ? 'text-[#003d82] font-bold' : 'text-gray-600 font-medium group-hover:text-[#0056b3]' }}">Historial de Zonas</span>
        </a>
        @endhasanyrole

    </nav>
</aside>