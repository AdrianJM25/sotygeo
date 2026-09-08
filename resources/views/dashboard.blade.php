<x-app-layout>
    <div class="flex flex-col gap-4 h-[calc(100vh-6rem)]">
        
        <!-- Tarjetas de Resumen / KPIs Rápidos -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 shrink-0">
            <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Unidades Totales</p>
                    <h3 id="kpi-total" class="text-2xl font-bold text-gray-800">0</h3>
                </div>
                <div class="p-3 bg-gray-50 text-gray-700 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                </div>
            </div>

            <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">En Línea</p>
                    <h3 id="kpi-en-linea" class="text-2xl font-bold text-green-600">0</h3>
                </div>
                <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>

            <div class="bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Estado del Servidor</p>
                    <span id="estado-conexion" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200 mt-1">
                        Conectando...
                    </span>
                </div>
                <div class="p-3 bg-gray-50 text-gray-700 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                </div>
            </div>
        </div>

        <!-- Contenedor Principal: Mapa -->
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm flex-1 overflow-hidden relative flex flex-col">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white z-10 shrink-0">
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></span>
                    Monitoreo Geográfico en Vivo
                </h2>
                <span class="text-xs text-gray-400">Actualización automática cada 5s</span>
            </div>
            
            <div id="mapa-rastreo" class="w-full flex-1 z-0"></div>
        </div>
    </div>

    <!-- Script del Mapa Dinámico -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // El mapa inicia centrado en Cuernavaca/Jiutepec como valor por defecto
            var map = L.map('mapa-rastreo', { zoomControl: false }).setView([18.8814, -99.1764], 14);

            L.control.zoom({ position: 'topright' }).addTo(map);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap - SotyGeo'
            }).addTo(map);

            var marcadores = {}; 

            var iconoVehiculo = L.icon({
                // Puedes cambiar esta imagen por un carrito si gustas
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/744/744403.png', 
                iconSize: [32, 32],
                iconAnchor: [16, 16],
                popupAnchor: [0, -16]
            });

            function actualizarUbicaciones() {
                // LLAMAMOS A LA NUEVA API INTERNA DE VEHÍCULOS
                fetch('{{ route("api.vehiculos.en-vivo") }}')
                    .then(response => response.json())
                    .then(data => {
                        let estado = document.getElementById('estado-conexion');
                        estado.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200 mt-1';
                        estado.innerText = 'En Línea';

                        document.getElementById('kpi-total').innerText = data.length;
                        let enLineaCount = 0;

                        data.forEach(vehiculo => {
                            // Solo dibujamos si el vehículo tiene dispositivo Y tiene ubicaciones registradas
                            if (vehiculo.dispositivo && vehiculo.dispositivo.ubicaciones && vehiculo.dispositivo.ubicaciones.length > 0) {
                                enLineaCount++;
                                
                                let ultimaUbicacion = vehiculo.dispositivo.ubicaciones[0];
                                let lat = parseFloat(ultimaUbicacion.latitud);
                                let lng = parseFloat(ultimaUbicacion.longitud);

                                if (!lat || !lng) return;

                                let contenidoPopup = `
                                    <div class="p-1 text-left font-sans">
                                        <b class="text-gray-900 text-sm block mb-1">${vehiculo.nombre}</b>
                                        <div class="text-xs text-gray-600 space-y-1">
                                            <div><b>Modelo:</b> ${vehiculo.marca || ''} ${vehiculo.modelo || 'N/D'}</div>
                                            <div><b>Batería:</b> ${ultimaUbicacion.porcentaje_bateria ?? 'N/D'}% 🔋</div>
                                            <div><b>Velocidad:</b> ${ultimaUbicacion.velocidad} km/h</div>
                                            <div class="text-gray-400 text-[10px] pt-1 border-t border-gray-100">
                                                Actualizado: ${new Date(ultimaUbicacion.fecha_gps).toLocaleTimeString()}
                                            </div>
                                        </div>
                                    </div>
                                `;

                                if (marcadores[vehiculo.id]) {
                                    // Si el marcador ya existe, solo lo movemos suavemente
                                    marcadores[vehiculo.id].setLatLng([lat, lng]);
                                    marcadores[vehiculo.id].getPopup().setContent(contenidoPopup);
                                } else {
                                    // Si no existe, lo creamos
                                    let marcador = L.marker([lat, lng], {icon: iconoVehiculo})
                                        .addTo(map)
                                        .bindPopup(contenidoPopup);
                                    
                                    marcadores[vehiculo.id] = marcador;
                                    
                                    // Centramos el mapa en el primer vehículo que aparezca
                                    if (enLineaCount === 1) {
                                        map.setView([lat, lng], 16);
                                    }
                                }
                            }
                        });

                        document.getElementById('kpi-en-linea').innerText = enLineaCount;
                    })
                    .catch(error => {
                        console.error('Error al obtener posiciones:', error);
                        let estado = document.getElementById('estado-conexion');
                        estado.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200 mt-1';
                        estado.innerText = 'Desconectado';
                    });
            }

            // Primera llamada inmediata
            actualizarUbicaciones();
            
            // Ciclo infinito cada 5 segundos
            setInterval(actualizarUbicaciones, 5000);
        });
    </script>
</x-app-layout>