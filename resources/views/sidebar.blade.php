<aside :class="{
          'translate-x-0 w-64': sidebarOpen,
          '-translate-x-[120%] w-64': !sidebarOpen,
          'lg:translate-x-0': true,
          'lg:w-20': sidebarMini,
          'lg:w-64': !sidebarMini
       }"
       class="fixed top-4 bottom-4 left-4 z-50 flex flex-col transition-all duration-300 ease-in-out bg-white border border-gray-200 shadow-sm rounded-2xl lg:static lg:inset-auto lg:h-full shrink-0 overflow-hidden">
    
    <!-- Cabecera del Sidebar -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-100 bg-white shrink-0">
        <div class="flex items-center overflow-hidden">
            <svg class="w-8 h-8 text-gray-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span x-show="!sidebarMini" class="ml-2 text-xl font-bold text-gray-800 whitespace-nowrap">SotyGeo</span>
        </div>
        
        <!-- Botón Contraer/Expandir -->
        <button @click="sidebarMini = !sidebarMini" class="hidden lg:block p-1.5 text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-md transition-colors shrink-0">
            <svg x-show="!sidebarMini" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
            <svg x-show="sidebarMini" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
        </button>
    </div>
    
    <!-- Navegación -->
    <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-hidden hover:overflow-y-auto bg-white">
        
        <!-- Mapa en Vivo (Para todos) -->
        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-gray-700' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7l6-2.5 5.447 2.724A1 1 0 0121 8.618v10.764a1 1 0 01-1.447.894L15 17l-6 2.5z"></path></svg>
            <span x-show="!sidebarMini" x-transition.opacity.duration.300ms class="ml-3 whitespace-nowrap">Mapa en Vivo</span>
        </a>

        <!-- EMPRESAS (Exclusivo SOTyTECH) -->
        @role('Super Administrador')
        <a href="{{ route('empresas.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('empresas.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('empresas.*') ? 'text-gray-700' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            <span x-show="!sidebarMini" x-transition.opacity.duration.300ms class="ml-3 whitespace-nowrap">Empresas (SaaS)</span>
        </a>
        @endrole

        <!-- USUARIOS (SOTyTECH y Administradores de Empresa) -->
        @hasanyrole('Super Administrador|Administrador de Empresa')
        <a href="{{ route('usuarios.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('usuarios.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('usuarios.*') ? 'text-gray-700' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span x-show="!sidebarMini" x-transition.opacity.duration.300ms class="ml-3 whitespace-nowrap">Usuarios</span>
        </a>
        @endhasanyrole
        
        <!-- FLOTILLAS (SOTyTECH y Administradores de Empresa) -->
        @hasanyrole('Super Administrador|Administrador de Empresa')
        <a href="{{ route('flotillas.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('flotillas.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('flotillas.*') ? 'text-gray-700' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            <span x-show="!sidebarMini" x-transition.opacity.duration.300ms class="ml-3 whitespace-nowrap">Flotillas</span>
        </a>
        @endhasanyrole

        <!-- MÓDULOS LOGÍSTICOS (Disponibles para todos excepto Conductores) -->
        @hasanyrole('Super Administrador|Administrador de Empresa|Gestor de flotilla|Cliente Individual')
        
        <a href="{{ route('vehiculos.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('vehiculos.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('vehiculos.*') ? 'text-gray-700' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
            <span x-show="!sidebarMini" x-transition.opacity.duration.300ms class="ml-3 whitespace-nowrap">Vehículos</span>
        </a>


        <a href="{{ route('dispositivos.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('dispositivos.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('dispositivos.*') ? 'text-gray-700' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            <span x-show="!sidebarMini" x-transition.opacity.duration.300ms class="ml-3 whitespace-nowrap">Dispositivos</span>
        </a>
        
        <a href="{{ route('zonas.index') }}" class="flex items-center px-3 py-2.5 rounded-lg transition-colors {{ request()->routeIs('zonas.*') ? 'bg-gray-100 text-gray-900 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('zonas.*') ? 'text-gray-700' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
            <span x-show="!sidebarMini" x-transition.opacity.duration.300ms class="ml-3 whitespace-nowrap">Geocercas</span>
        </a>
        
        @endhasanyrole
        
    </nav>
</aside>