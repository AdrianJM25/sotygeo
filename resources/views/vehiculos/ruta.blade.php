<x-app-layout>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-[calc(100vh-6rem)]">
        
        <!-- Cabecera -->
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white z-10 shrink-0">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Historial de Recorrido: {{ $vehiculo->nombre }}</h2>
                <p class="text-xs text-gray-500">Mostrando puntos registrados el día de hoy.</p>
            </div>
            <a href="{{ route('vehiculos.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                Regresar
            </a>
        </div>

        <!-- Alerta si no hay datos -->
        @if($ubicaciones->isEmpty())
            <div class="flex-1 flex items-center justify-center bg-gray-50 z-0">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <h3 class="text-lg font-medium text-gray-900">Sin movimientos aún</h3>
                    <p class="text-sm text-gray-500 mt-1">El GPS no ha reportado ubicaciones el día de hoy.</p>
                </div>
            </div>
        @else
            <!-- Lienzo del Mapa -->
            <div id="mapa-historial" class="w-full flex-1 z-0"></div>
        @endif
    </div>

    @if($ubicaciones->isNotEmpty())
    <!-- Script para trazar la línea -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Pasamos las ubicaciones de la base de datos a JavaScript
            var ubicaciones = @json($ubicaciones);

            // Mapeamos solo las latitudes y longitudes en un arreglo puro para Leaflet
            var coordenadas = ubicaciones.map(function(u) {
                return [parseFloat(u.latitud), parseFloat(u.longitud)];
            });

            // Inicializamos el mapa centrado en el primer punto
            var map = L.map('mapa-historial', { zoomControl: false }).setView(coordenadas[0], 15);
            L.control.zoom({ position: 'topright' }).addTo(map);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap - SotyGeo'
            }).addTo(map);

            // 1. Dibujar la línea de la ruta
            var rutaLinea = L.polyline(coordenadas, {
                color: '#4F46E5', // Indigo-600
                weight: 5,
                opacity: 0.7,
                smoothFactor: 1
            }).addTo(map);

            // 2. Ajustar el "zoom" automáticamente para que se vea toda la ruta en pantalla
            map.fitBounds(rutaLinea.getBounds(), { padding: [50, 50] });

            // 3. Poner un punto verde de "Inicio"
            L.circleMarker(coordenadas[0], {
                color: '#16a34a', // Verde
                fillColor: '#16a34a',
                fillOpacity: 1,
                radius: 6
            }).addTo(map).bindPopup('<b>Inicio del recorrido</b><br>Hora: ' + new Date(ubicaciones[0].fecha_gps).toLocaleTimeString());

            // 4. Poner un marcador tradicional en la "Última posición"
            var ultimoIndice = coordenadas.length - 1;
            L.marker(coordenadas[ultimoIndice]).addTo(map).bindPopup('<b>Posición Actual / Final</b><br>Velocidad: ' + ubicaciones[ultimoIndice].velocidad + ' km/h<br>Hora: ' + new Date(ubicaciones[ultimoIndice].fecha_gps).toLocaleTimeString());
        });
    </script>
    @endif
</x-app-layout>