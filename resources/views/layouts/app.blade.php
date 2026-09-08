<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'SotyGeo') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    
    <!-- Fondo general gris claro para resaltar los paneles blancos -->
    <body class="font-sans antialiased bg-gray-100 text-gray-900" x-data="{ sidebarOpen: false, sidebarMini: false }">
        
        <!-- Contenedor principal con separación externa (p-4) y entre elementos (gap-4) -->
        <div class="flex h-screen overflow-hidden relative p-4 gap-4">
            
            <!-- Fondo oscuro transparente para móviles -->
            <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden rounded-none"></div>
            
            <!-- Inyección del Sidebar -->
            @include('sidebar')

            <!-- Columna Derecha (Header + Contenido) con separación vertical (gap-4) -->
            <div class="flex flex-col flex-1 w-full h-full overflow-hidden gap-4">
                
                <!-- Header en formato Tarjeta -->
                <header class="flex items-center justify-between px-6 py-4 bg-white border border-gray-200 shadow-sm rounded-2xl shrink-0 z-10">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none lg:hidden">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                    </div>
                    <div class="text-gray-600 font-medium text-sm">
                        {{ Auth::user()->name ?? 'Administrador' }}
                    </div>
                </header>

                <!-- Contenido Principal -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto">
                    {{ $slot }}
                </main>
                
            </div>
        </div>
    </body>
</html>