<x-app-layout>
    <x-slot name="titulo">
        Historial de Geocercas
    </x-slot>

    <!-- Contenedor Principal con Alpine.js para animaciones de entrada -->
    <div x-data="{ 
            mounted: false, 
            mostrarFiltros: false 
        }" 
        x-init="setTimeout(() => mounted = true, 100)"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
    >
        <!-- Encabezado de la Página -->
        <div 
            x-show="mounted" 
            x-transition:enter="transition ease-out duration-500" 
            x-transition:enter-start="opacity-0 translate-y-4" 
            x-transition:enter-end="opacity-100 translate-y-0"
            class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4"
        >
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                    Auditoría de Geocercas
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Consulta el historial de entradas, salidas y alertas de tus vehículos.
                </p>
            </div>

            <!-- Botón para alternar Filtros -->
            <button 
                @click="mostrarFiltros = !mostrarFiltros"
                class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                <span x-text="mostrarFiltros ? 'Ocultar Filtros' : 'Mostrar Filtros'"></span>
            </button>
        </div>

        <!-- Panel de Filtros Animado -->
        <div 
            x-show="mostrarFiltros" 
            x-collapse
            class="mb-8"
            x-cloak
        >
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <form action="{{ route('geocercas.historial') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <!-- Rango de Fechas -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha Inicio</label>
                        <input type="datetime-local" name="fecha_inicio" value="{{ request('fecha_inicio') }}" 
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha Fin</label>
                        <input type="datetime-local" name="fecha_fin" value="{{ request('fecha_fin') }}" 
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                    </div>

                    <!-- Vehículo y Zona -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vehículo</label>
                        <select name="vehiculo_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos los vehículos</option>
                            @foreach($vehiculos ?? [] as $vehiculo)
                                <option value="{{ $vehiculo->id }}" {{ request('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                    {{ $vehiculo->placa }} - {{ $vehiculo->alias }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Geocerca</label>
                        <select name="geocerca_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todas las zonas</option>
                            @foreach($geocercas ?? [] as $geocerca)
                                <option value="{{ $geocerca->id }}" {{ request('geocerca_id') == $geocerca->id ? 'selected' : '' }}>
                                    {{ $geocerca->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tipo de Evento y Botones -->
                    <div class="lg:col-span-4 flex flex-col md:flex-row items-end justify-between gap-4 border-t dark:border-gray-700 pt-4 mt-2">
                        <div class="w-full md:w-1/3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de Evento</label>
                            <select name="tipo_evento" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos los eventos</option>
                                <option value="entrada" {{ request('tipo_evento') == 'entrada' ? 'selected' : '' }}>Entrada</option>
                                <option value="salida" {{ request('tipo_evento') == 'salida' ? 'selected' : '' }}>Salida</option>
                                <option value="alerta" {{ request('tipo_evento') == 'alerta' ? 'selected' : '' }}>Alerta</option>
                            </select>
                        </div>
                        
                        <div class="flex gap-3 w-full md:w-auto">
                            <a href="{{ route('geocercas.historial') }}" class="flex-1 md:flex-none text-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg text-sm font-medium transition-colors duration-200">
                                Limpiar
                            </a>
                            <button type="submit" class="flex-1 md:flex-none px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium shadow-sm transition-colors duration-200">
                                Aplicar Filtros
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabla de Resultados con animación secuencial -->
        <div 
            x-show="mounted" 
            x-transition:enter="transition ease-out duration-700 delay-100" 
            x-transition:enter-start="opacity-0 translate-y-8" 
            x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden"
        >
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fecha / Hora</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vehículo</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Geocerca</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Evento</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ubicación</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($eventos as $evento)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 group">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-gray-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($evento->created_at)->format('d M Y, h:i A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $evento->vehiculo->alias ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $evento->vehiculo->placa ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center text-sm text-gray-700 dark:text-gray-300">
                                        <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                        </svg>
                                        {{ $evento->geocerca->nombre ?? 'Zona Eliminada' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($evento->tipo_evento === 'entrada')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                            <span class="w-1.5 h-1.5 mr-1.5 bg-green-600 rounded-full animate-pulse"></span>
                                            Entrada
                                        </span>
                                    @elseif($evento->tipo_evento === 'salida')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                            <span class="w-1.5 h-1.5 mr-1.5 bg-yellow-600 rounded-full"></span>
                                            Salida
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                            <span class="w-1.5 h-1.5 mr-1.5 bg-red-600 rounded-full animate-ping"></span>
                                            Alerta
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="https://maps.google.com/?q={{ $evento->latitud }},{{ $evento->longitud }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 inline-flex items-center transition-colors">
                                        Ver Mapa
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Sin registros</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No se encontraron eventos de geocercas en el rango seleccionado.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            @if($eventos->hasPages())
                <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $eventos->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Soporte para que x-cloak oculte elementos en la carga inicial de Alpine */
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>