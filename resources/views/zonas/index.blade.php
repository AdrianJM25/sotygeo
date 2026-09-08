<x-app-layout>
    <!-- Incluir CSS y JS de Leaflet Geoman para dibujar -->
    <link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
    <script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.min.js"></script>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 h-[calc(100vh-8rem)]">
        
        <!-- Panel Izquierdo: Formulario y Lista -->
        <div class="flex flex-col gap-4 lg:col-span-1 h-full overflow-hidden">
            
            <!-- Tarjeta Formulario -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-4 shrink-0">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Crear Nueva Zona</h2>
                
                <form action="{{ route('zonas.store') }}" method="POST" id="form-zona" class="space-y-4">
                    @csrf
                    <!-- Input oculto donde se guardarán las coordenadas del mapa -->
                    <input type="hidden" name="coordenadas" id="input-coordenadas" required>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-900">Nombre de la Zona</label>
                        <input type="text" name="nombre" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5" placeholder="Ej. Base Jiutepec">
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-900">Color en el mapa</label>
                        <input type="color" name="color_hex" value="#3B82F6" required class="p-1 h-10 w-full block bg-white border border-gray-300 cursor-pointer rounded-lg">
                    </div>

                    <div id="alerta-dibujo" class="text-xs text-amber-600 bg-amber-50 border border-amber-200 p-2 rounded-lg">
                        ⚠️ Dibuja un polígono en el mapa antes de guardar.
                    </div>

                    <button type="submit" id="btn-guardar" disabled class="w-full px-5 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Guardar Geocerca
                    </button>
                </form>
            </div>

            <!-- Tarjeta Lista de Zonas -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm flex-1 overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800">Zonas Registradas</h3>
                </div>
                <div class="overflow-y-auto p-4 space-y-3">
                    @forelse($zonas as $zona)
                        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <span class="w-4 h-4 rounded-full" style="background-color: {{ $zona->color_hex }}"></span>
                                <span class="font-medium text-gray-700 text-sm">{{ $zona->nombre }}</span>
                            </div>
                            <form action="{{ route('zonas.destroy', $zona->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta zona?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 p-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No hay zonas creadas.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Panel Derecho: El Mapa -->
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden relative min-h-[500px]">
            <div id="mapa-zonas" class="w-full h-full absolute inset-0 z-0"></div>
        </div>
    </div>

    <!-- Lógica del Mapa y Dibujo -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inicializar mapa (Centrado en Jiutepec, Morelos)
            var map = L.map('mapa-zonas').setView([18.8814, -99.1764], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // 1. Cargar las Zonas existentes desde la Base de Datos
            var zonasGuardadas = @json($zonas);
            
            zonasGuardadas.forEach(function(zona) {
                if (zona.geojson) {
                    var geoData = JSON.parse(zona.geojson);
                    L.geoJSON(geoData, {
                        style: {
                            color: zona.color_hex,
                            weight: 2,
                            fillOpacity: 0.2
                        }
                    }).bindPopup('<b>' + zona.nombre + '</b>').addTo(map);
                }
            });

            // 2. Configurar herramientas de dibujo (Geoman)
            map.pm.addControls({
                position: 'topleft',
                drawMarker: false,
                drawCircleMarker: false,
                drawPolyline: false,
                drawRectangle: false,
                drawCircle: false,
                drawText: false,
                editMode: false,
                dragMode: false,
                cutPolygon: false,
                removalMode: true,
                drawPolygon: true, // Solo permitimos polígonos libres
            });

            // Traducción básica de controles
            map.pm.setLang('es');

            var capaActual = null;
            var inputCoordenadas = document.getElementById('input-coordenadas');
            var btnGuardar = document.getElementById('btn-guardar');
            var alertaDibujo = document.getElementById('alerta-dibujo');

            // 3. Evento: Cuando el usuario termina de dibujar un polígono
            map.on('pm:create', function(e) {
                // Si ya había dibujado uno antes, lo borramos (solo 1 zona por registro)
                if (capaActual) {
                    map.removeLayer(capaActual);
                }
                
                capaActual = e.layer;
                
                // Extraer coordenadas
                var coordenadas = capaActual.getLatLngs()[0]; // Obtiene array de {lat, lng}
                
                // Guardar como JSON en el input oculto
                inputCoordenadas.value = JSON.stringify(coordenadas);
                
                // Habilitar botón de guardado
                btnGuardar.disabled = false;
                alertaDibujo.classList.add('hidden');
            });

            // 4. Evento: Si el usuario borra la figura con la herramienta de borrar
            map.on('pm:remove', function(e) {
                if (e.layer === capaActual) {
                    capaActual = null;
                    inputCoordenadas.value = '';
                    btnGuardar.disabled = true;
                    alertaDibujo.classList.remove('hidden');
                }
            });
        });
    </script>
</x-app-layout>