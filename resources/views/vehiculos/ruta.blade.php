<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Historial de Ruta: <span class="text-indigo-600">{{ $vehiculo->nombre }}</span>
            </h2>
            <a href="{{ route('vehiculos.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                &larr; Volver a Vehículos
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Barra de Filtros (Selector de Fecha) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 mb-4">
                <form method="GET" action="{{ route('vehiculos.ruta', $vehiculo->id) }}" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-700">Seleccionar Fecha:</label>
                        <input type="date" name="fecha" id="fecha" 
                               value="{{ request('fecha', $fechaConsulta->format('Y-m-d')) }}" 
                               class="mt-1 block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                            Consultar Ruta
                        </button>
                    </div>
                </form>
            </div>

            <!-- Contenedor del Mapa y Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                
                <!-- Panel Lateral de Estadísticas (1 columna) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 space-y-4">
                    <h3 class="font-bold text-gray-700 border-b pb-2">Resumen del Día</h3>
                    <div>
                        <span class="text-xs text-gray-500 block">Puntos GPS registrados:</span>
                        <span class="text-lg font-semibold text-gray-900">{{ count($ubicaciones) }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block">Fecha consultada:</span>
                        <span class="text-sm font-medium text-gray-900">{{ $fechaConsulta->format('d/m/Y') }}</span>
                    </div>
                    <!-- Puedes agregar más métricas aquí conforme las calcules -->
                </div>

                <!-- El Mapa (3 columnas) -->
                <div class="md:col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg p-2">
                    <div id="mapa-historial" style="height: 550px; width: 100%;" class="rounded-lg z-0"></div>
                </div>

            </div>

        </div>
    </div>

    <!-- Script para inicializar el mapa con Leaflet (Ejemplo conceptual) -->
    @push('scripts')
    <!-- Asegúrate de incluir losCDN de Leaflet en tu layout principal o aquí -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Coordenadas iniciales (si hay ubicaciones tomamos la primera, si no, México por defecto)
            const ubicaciones = @json($ubicaciones);
            
            let latInicial = 19.4326; // Fallback
            let lonInicial = -99.1332;

            if (ubicaciones.length > 0) {
                latInicial = ubicaciones[0].latitud;
                lonInicial = ubicaciones[0].longitud;
            }

            // Inicializar Mapa Leaflet
            const map = L.map('mapa-historial').setView([latInicial, lonInicial], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            if (ubicaciones.length > 0) {
                // Mapear coordenadas para la polilínea
                const latLngs = ubicaciones.map(u => [u.latitud, u.longitud]);

                // Dibujar la línea de la ruta
                const polyline = L.polyline(latLngs, {color: 'indigo', weight: 4}).addTo(map);

                // Ajustar el zoom del mapa para que encaje toda la ruta del día
                map.fitBounds(polyline.getBounds());

                // Agregar marcadores de Inicio y Fin
                L.marker(latLngs[0]).addTo(map).bindPopup("<b>Inicio de ruta</b><br>" + ubicaciones[0].fecha_gps);
                L.marker(latLngs[latLngs.length - 1]).addTo(map).bindPopup("<b>Fin de ruta / Último punto</b><br>" + ubicaciones[ubicaciones.length - 1].fecha_gps);
            } else {
                alert("No hay registros de ruta para esta fecha.");
            }
        });
    </script>
    @endpush
</x-app-layout>