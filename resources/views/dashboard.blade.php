<x-app-layout>
    <style>
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .card-in { animation: cardIn 0.5s ease-out both; }
        .card-in:nth-child(1) { animation-delay: 0.05s; }
        .card-in:nth-child(2) { animation-delay: 0.10s; }
        .card-in:nth-child(3) { animation-delay: 0.15s; }
        .card-in:nth-child(4) { animation-delay: 0.20s; }

        @keyframes kpiFlash {
            0% { background-color: var(--flash-color, #ECFDF5); }
            100% { background-color: #FFFFFF; }
        }
        .kpi-flash { animation: kpiFlash 0.8s ease; }

        .vehiculo-marker { position: relative; width: 34px; height: 34px; }
        .vehiculo-marker-pulse {
            position: absolute; inset: 0; border-radius: 9999px;
            background: rgba(16, 185, 129, 0.35);
            animation: markerPulse 2.2s ease-out infinite;
        }
        .vehiculo-marker-dot {
            position: absolute; inset: 7px; border-radius: 9999px;
            background: #111827; border: 2px solid #10B981;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.25);
        }
        @keyframes markerPulse {
            0% { transform: scale(0.6); opacity: 0.9; }
            100% { transform: scale(1.9); opacity: 0; }
        }

        .sotygeo-popup .leaflet-popup-content-wrapper {
            border-radius: 1rem; padding: 0;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12), 0 8px 10px -6px rgba(0,0,0,0.08);
        }
        .sotygeo-popup .leaflet-popup-content { margin: 0; width: 220px !important; }
        .sotygeo-popup .leaflet-popup-tip { background: white; }

        .zona-tooltip {
            background: white !important; border: 1px solid #E5E7EB !important;
            border-radius: 0.5rem !important; color: #111827 !important;
            font-weight: 600; font-size: 0.75rem; padding: 4px 10px !important;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        .zona-tooltip::before { display: none; }

        @media (prefers-reduced-motion: reduce) {
            .card-in { animation: none; opacity: 1; transform: none; }
            .vehiculo-marker-pulse { animation: none; }
            .kpi-flash { animation: none; }
        }
    </style>

    <div class="flex flex-col gap-4 h-[calc(100vh-6rem)]">

        <!-- Tarjetas de Resumen / KPIs -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 shrink-0">

            <div class="kpi-card card-in bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between transition-shadow hover:shadow-md" style="--flash-color:#EEF2FF">
                <div>
                    <p class="text-sm font-medium text-gray-500">Unidades Totales</p>
                    <h3 id="kpi-total" data-valor="0" class="text-3xl font-bold text-gray-900 mt-1">0</h3>
                </div>
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                </div>
            </div>

            <div class="kpi-card card-in bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between transition-shadow hover:shadow-md" style="--flash-color:#ECFDF5">
                <div>
                    <p class="text-sm font-medium text-gray-500">En Línea</p>
                    <h3 id="kpi-en-linea" data-valor="0" class="text-3xl font-bold text-emerald-600 mt-1">0</h3>
                </div>
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>

            <div class="kpi-card card-in bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between transition-shadow hover:shadow-md" style="--flash-color:#F5F3FF">
                <div>
                    <p class="text-sm font-medium text-gray-500">Geocercas Activas</p>
                    <h3 id="kpi-zonas" data-valor="0" class="text-3xl font-bold text-violet-600 mt-1">0</h3>
                </div>
                <div class="p-3 bg-violet-50 text-violet-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
                </div>
            </div>

            <div class="card-in bg-white border border-gray-200 p-4 rounded-2xl shadow-sm flex items-center justify-between transition-shadow hover:shadow-md">
                <div>
                    <p class="text-sm font-medium text-gray-500">Estado del Servidor</p>
                    <span id="estado-conexion" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-50 text-yellow-700 border border-yellow-200 mt-1.5 transition-colors duration-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span>
                        Conectando...
                    </span>
                </div>
                <div class="p-3 bg-gray-50 text-gray-700 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                </div>
            </div>
        </div>

        <!-- Contenedor Principal: Mapa -->
        <div class="card-in bg-white border border-gray-200 rounded-2xl shadow-sm flex-1 overflow-hidden relative flex flex-col">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white z-10 shrink-0">
                <h2 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <span class="relative flex w-2.5 h-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    Monitoreo Geográfico en Vivo
                </h2>
                <span class="text-xs text-gray-400">
                    Actualización cada 5s · <span id="ultima-actualizacion">esperando datos…</span>
                </span>
            </div>

            <div id="mapa-rastreo" class="w-full flex-1 z-0"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var map = L.map('mapa-rastreo', { zoomControl: false }).setView([18.8814, -99.1764], 14);
            L.control.zoom({ position: 'topright' }).addTo(map);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap - SotyGeo'
            }).addTo(map);

            var marcadores = {};
            var ultimaActualizacionTs = null;

            var iconoVehiculo = L.divIcon({
                className: '',
                html: `
                    <div class="vehiculo-marker">
                        <div class="vehiculo-marker-pulse"></div>
                        <div class="vehiculo-marker-dot">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1"/>
                            </svg>
                        </div>
                    </div>`,
                iconSize: [34, 34],
                iconAnchor: [17, 17],
                popupAnchor: [0, -22],
            });

            function animarConteo(el, hasta, duracion) {
                var desde = parseInt(el.dataset.valor || '0', 10);
                el.dataset.valor = hasta;
                if (desde === hasta) { el.innerText = hasta; return; }

                var tarjeta = el.closest('.kpi-card');
                if (tarjeta) {
                    tarjeta.classList.remove('kpi-flash');
                    void tarjeta.offsetWidth;
                    tarjeta.classList.add('kpi-flash');
                }

                var inicio = performance.now();
                function paso(ahora) {
                    var progreso = Math.min((ahora - inicio) / duracion, 1);
                    el.innerText = Math.round(desde + (hasta - desde) * progreso);
                    if (progreso < 1) requestAnimationFrame(paso);
                }
                requestAnimationFrame(paso);
            }

            // ===== Zonas / Geocercas (se cargan una vez, no se mueven) =====
            function cargarZonas() {
                fetch('{{ route("api.zonas.en-vivo") }}')
                    .then(r => r.json())
                    .then(geojson => {
                        L.geoJSON(geojson, {
                            style: function (feature) {
                                var color = feature.properties.color || '#6366F1';
                                return { color: color, weight: 2, fillColor: color, fillOpacity: 0.12, dashArray: '6 4' };
                            },
                            onEachFeature: function (feature, layer) {
                                layer.bindTooltip(feature.properties.nombre, {
                                    direction: 'center', className: 'zona-tooltip'
                                });
                            }
                        }).addTo(map);

                        animarConteo(document.getElementById('kpi-zonas'), geojson.features.length, 500);
                    })
                    .catch(err => console.error('Error cargando zonas:', err));
            }

            // ===== Vehículos (cada 5s) =====
            function actualizarUbicaciones() {
                fetch('{{ route("api.vehiculos.en-vivo") }}')
                    .then(response => response.json())
                    .then(data => {
                        let estado = document.getElementById('estado-conexion');
                        estado.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200 mt-1.5 transition-colors duration-300';
                        estado.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> En Línea';

                        animarConteo(document.getElementById('kpi-total'), data.length, 500);
                        let enLineaCount = 0;

                        data.forEach(vehiculo => {
                            if (vehiculo.dispositivo && vehiculo.dispositivo.ubicaciones && vehiculo.dispositivo.ubicaciones.length > 0) {
                                enLineaCount++;

                                let ultimaUbicacion = vehiculo.dispositivo.ubicaciones[0];
                                let lat = parseFloat(ultimaUbicacion.latitud);
                                let lng = parseFloat(ultimaUbicacion.longitud);
                                if (!lat || !lng) return;

                                let contenidoPopup = `
                                    <div class="font-sans">
                                        <div class="px-4 py-3 border-b border-gray-100">
                                            <b class="text-gray-900 text-sm">${vehiculo.nombre}</b>
                                        </div>
                                        <div class="px-4 py-3 text-xs text-gray-600 space-y-1.5">
                                            <div class="flex justify-between"><span class="text-gray-400">Modelo</span><span class="font-medium text-gray-800">${vehiculo.marca || ''} ${vehiculo.modelo || 'N/D'}</span></div>
                                            <div class="flex justify-between"><span class="text-gray-400">Batería</span><span class="font-medium text-gray-800">${ultimaUbicacion.porcentaje_bateria ?? 'N/D'}%</span></div>
                                            <div class="flex justify-between"><span class="text-gray-400">Velocidad</span><span class="font-medium text-gray-800">${ultimaUbicacion.velocidad} km/h</span></div>
                                            <div class="text-gray-400 text-[10px] pt-2 border-t border-gray-100 mt-1">
                                                Actualizado: ${new Date(ultimaUbicacion.fecha_gps).toLocaleTimeString()}
                                            </div>
                                        </div>
                                    </div>
                                `;

                                if (marcadores[vehiculo.id]) {
                                    marcadores[vehiculo.id].setLatLng([lat, lng]);
                                    marcadores[vehiculo.id].getPopup().setContent(contenidoPopup);
                                } else {
                                    let marcador = L.marker([lat, lng], { icon: iconoVehiculo })
                                        .addTo(map)
                                        .bindPopup(contenidoPopup, { className: 'sotygeo-popup' });

                                    marcadores[vehiculo.id] = marcador;

                                    if (enLineaCount === 1) {
                                        map.setView([lat, lng], 16);
                                    }
                                }
                            }
                        });

                        animarConteo(document.getElementById('kpi-en-linea'), enLineaCount, 500);
                        ultimaActualizacionTs = Date.now();
                    })
                    .catch(error => {
                        console.error('Error al obtener posiciones:', error);
                        let estado = document.getElementById('estado-conexion');
                        estado.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200 mt-1.5 transition-colors duration-300';
                        estado.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Desconectado';
                    });
            }

            setInterval(function () {
                let span = document.getElementById('ultima-actualizacion');
                if (!span || !ultimaActualizacionTs) return;
                let segundos = Math.floor((Date.now() - ultimaActualizacionTs) / 1000);
                span.innerText = segundos < 2 ? 'justo ahora' : `hace ${segundos}s`;
            }, 1000);

            cargarZonas();
            actualizarUbicaciones();
            setInterval(actualizarUbicaciones, 5000);
        });
    </script>
</x-app-layout>