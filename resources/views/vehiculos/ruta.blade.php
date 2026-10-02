<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Historial de Ruta: <span class="text-indigo-600">{{ $vehiculo->nombre ?? $vehiculo->placa }}</span>
            </h2>
            <a href="{{ route('vehiculos.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                &larr; Volver a Vehículos
            </a>
        </div>
    </x-slot>

    <!-- 1. CARGAMOS EL CSS DE LEAFLET DIRECTAMENTE AQUÍ -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

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
                    <div id="resumen-horas" class="hidden">
                        <span class="text-xs text-gray-500 block">Hora de inicio:</span>
                        <span id="hora-inicio" class="text-sm font-medium text-gray-900">—</span>
                    </div>
                    <div id="resumen-horas-fin" class="hidden">
                        <span class="text-xs text-gray-500 block">Hora de fin / último punto:</span>
                        <span id="hora-fin" class="text-sm font-medium text-gray-900">—</span>
                    </div>
                    <div class="pt-2 border-t">
                        <span class="text-xs text-gray-500 block mb-1">Leyenda:</span>
                        <div class="flex items-center gap-2 text-xs text-gray-600 mb-1">
                            <span class="inline-block w-3 h-3 rounded-full bg-green-500"></span> Inicio de ruta
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-600 mb-1">
                            <span class="inline-block w-3 h-3 rounded-full bg-indigo-600"></span> Punto GPS
                        </div>
                        <div class="flex items-center gap-2 text-xs text-gray-600">
                            <span class="inline-block w-3 h-3 rounded-full bg-red-500"></span> Fin de ruta
                        </div>
                    </div>
                </div>

                <!-- El Mapa (3 columnas) -->
                <div class="md:col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg p-2 relative z-0">
                    <!-- Es vital que este div tenga una altura definida -->
                    <div id="mapa-historial" style="height: 550px; width: 100%; border: 1px solid #e2e8f0;" class="rounded-lg"></div>
                </div>

            </div>
        </div>
    </div>

    <!-- 2. CARGAMOS EL JS DE LEAFLET Y EL PLUGIN DE FLECHAS DE DIRECCIÓN -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-polylinedecorator@1.6.0/dist/leaflet.polylineDecorator.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ubicaciones = @json($ubicaciones);
            console.log("Ubicaciones cargadas:", ubicaciones);

            let latInicial = 19.4326;
            let lonInicial = -99.1332;

            if (ubicaciones.length > 0) {
                latInicial = ubicaciones[0].latitud;
                lonInicial = ubicaciones[0].longitud;
            }

            // Usamos renderer de canvas para que cientos de puntos no afecten el rendimiento
            const map = L.map('mapa-historial', { renderer: L.canvas() }).setView([latInicial, lonInicial], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            function formatearFechaLocal(fechaISO) {
                if (!fechaISO) return 'N/D';
                const d = new Date(fechaISO);
                if (isNaN(d.getTime())) return 'N/D';
                return d.toLocaleString('es-MX', {
                    day: '2-digit', month: '2-digit', year: 'numeric',
                    hour: '2-digit', minute: '2-digit', second: '2-digit'
                });
            }

            if (ubicaciones.length > 0) {
                const latLngs = ubicaciones.map(u => [parseFloat(u.latitud), parseFloat(u.longitud)]);

                // Línea de la ruta
                const polyline = L.polyline(latLngs, { color: '#4f46e5', weight: 4, opacity: 0.85 }).addTo(map);

                // Flechas de dirección a lo largo de toda la ruta, indicando hacia dónde avanza
                L.polylineDecorator(polyline, {
                    patterns: [
                        {
                            offset: '4%',
                            repeat: '6%',
                            symbol: L.Symbol.arrowHead({
                                pixelSize: 11,
                                polygon: false,
                                pathOptions: { stroke: true, weight: 2, color: '#4f46e5', opacity: 0.9 }
                            })
                        }
                    ]
                }).addTo(map);

                map.fitBounds(polyline.getBounds(), { padding: [20, 20] });

                // Un puntito (circleMarker) en CADA coordenada que mandó el GPS
                ubicaciones.forEach((u, idx) => {
                    const lat = parseFloat(u.latitud);
                    const lon = parseFloat(u.longitud);
                    const esInicio = idx === 0;
                    const esFin = idx === ubicaciones.length - 1;

                    let color = '#4f46e5'; // puntos intermedios: índigo
                    let radio = 4;
                    if (esInicio) { color = '#22c55e'; radio = 7; } // inicio: verde
                    if (esFin && ubicaciones.length > 1) { color = '#ef4444'; radio = 7; } // fin: rojo

                    L.circleMarker([lat, lon], {
                        radius: radio,
                        color: '#ffffff',
                        weight: 1,
                        fillColor: color,
                        fillOpacity: 0.9
                    }).bindPopup(
                        `<b>${esInicio ? 'Inicio de ruta' : (esFin ? 'Fin de ruta' : 'Punto #' + (idx + 1))}</b><br>` +
                        `<span style="font-size:12px;color:#555;">${formatearFechaLocal(u.fecha_gps)}</span><br>` +
                        `<span style="font-size:12px;color:#555;">Velocidad: ${u.velocidad ?? 'N/D'} km/h</span>`
                    ).addTo(map);
                });

                const primerPunto = ubicaciones[0];
                const ultimoPunto = ubicaciones[ubicaciones.length - 1];

                const horaInicioTexto = formatearFechaLocal(primerPunto.fecha_gps);
                const horaFinTexto = formatearFechaLocal(ultimoPunto.fecha_gps);

                document.getElementById('resumen-horas').classList.remove('hidden');
                document.getElementById('resumen-horas-fin').classList.remove('hidden');
                document.getElementById('hora-inicio').innerText = horaInicioTexto;
                document.getElementById('hora-fin').innerText = horaFinTexto;
            }
        });
    </script>
</x-app-layout>