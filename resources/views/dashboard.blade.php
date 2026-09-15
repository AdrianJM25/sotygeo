<x-app-layout>
    <!-- 1. FUENTES DE GOOGLE -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;600;700&family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- 2. LIBRERÍAS DE LEAFLET -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        :root {
            /* Paleta de colores ajustada al tono azul */
            --sg-blanco: #FFFFFF;
            --sg-fondo: #f8fafc;
            --sg-borde: #e2e8f0;
            --sg-azul-claro: #e6f0fa;
            --sg-azul-principal: #0056b3; /* Azul de la imagen de referencia */
            --sg-azul-oscuro: #003d82;
            --sg-texto-gris: #64748b;
        }

        /* Tipografía */
        .font-josefin { font-family: 'Josefin Sans', sans-serif; }
        .font-ubuntu { font-family: 'Ubuntu', sans-serif; }

        body {
            font-family: 'Ubuntu', sans-serif;
            background-color: var(--sg-fondo);
        }

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
            0% { background-color: var(--sg-azul-claro); }
            100% { background-color: var(--sg-blanco); }
        }
        .kpi-flash { animation: kpiFlash 0.8s ease; }

        .vehiculo-marker { position: relative; width: 34px; height: 34px; }
        .vehiculo-marker-pulse {
            position: absolute; inset: 0; border-radius: 9999px;
            background: rgba(0, 86, 179, 0.25);
            animation: markerPulse 2.2s ease-out infinite;
        }
        .vehiculo-marker-dot {
            position: absolute; inset: 7px; border-radius: 9999px;
            background: var(--sg-blanco); border: 2px solid var(--sg-azul-principal);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .vehiculo-marker-dot svg { stroke: var(--sg-azul-principal); }
        
        @keyframes markerPulse {
            0% { transform: scale(0.6); opacity: 0.9; }
            100% { transform: scale(1.9); opacity: 0; }
        }

        /* Popups de Leaflet */
        .sotygeo-popup .leaflet-popup-content-wrapper {
            border-radius: 1rem; padding: 0;
            background: var(--sg-blanco);
            color: var(--sg-azul-oscuro);
            border: 1px solid var(--sg-borde);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            font-family: 'Ubuntu', sans-serif;
        }
        .sotygeo-popup .leaflet-popup-content { margin: 0; width: 220px !important; }
        .sotygeo-popup .leaflet-popup-tip { background: var(--sg-blanco); border-top: 1px solid var(--sg-borde); border-left: 1px solid var(--sg-borde); }

        .zona-tooltip {
            background: var(--sg-blanco) !important; border: 1px solid var(--sg-azul-principal) !important;
            border-radius: 0.5rem !important; color: var(--sg-azul-oscuro) !important;
            font-weight: 600; font-size: 0.75rem; padding: 4px 10px !important;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            font-family: 'Josefin Sans', sans-serif;
        }
        .zona-tooltip::before { display: none; }

        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

        @media (prefers-reduced-motion: reduce) {
            .card-in { animation: none; opacity: 1; transform: none; }
            .vehiculo-marker-pulse { animation: none; }
            .kpi-flash { animation: none; }
        }
    </style>

    <div class="flex flex-col gap-4 min-h-[500px] h-[calc(100vh-6rem)] font-ubuntu">

        <!-- Tarjetas de Resumen / KPIs -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 shrink-0">
            <div class="kpi-card card-in bg-white border border-[#e2e8f0] p-4 rounded-2xl shadow-sm flex items-center justify-between transition-shadow hover:shadow-md">
                <div>
                    <p class="text-sm font-medium text-slate-500 font-josefin">Unidades Totales</p>
                    <h3 id="kpi-total" data-valor="0" class="text-3xl font-bold text-[#0056b3] mt-1 font-josefin">0</h3>
                </div>
                <div class="p-3 bg-white text-[#0056b3] rounded-xl border border-[#e2e8f0]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                </div>
            </div>

            <div class="kpi-card card-in bg-white border border-[#e2e8f0] p-4 rounded-2xl shadow-sm flex items-center justify-between transition-shadow hover:shadow-md">
                <div>
                    <p class="text-sm font-medium text-slate-500 font-josefin">En Línea</p>
                    <h3 id="kpi-en-linea" data-valor="0" class="text-3xl font-bold text-[#0056b3] mt-1 font-josefin">0</h3>
                </div>
                <div class="p-3 bg-white text-[#0056b3] rounded-xl border border-[#e2e8f0]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>

            <div class="kpi-card card-in bg-white border border-[#e2e8f0] p-4 rounded-2xl shadow-sm flex items-center justify-between transition-shadow hover:shadow-md">
                <div>
                    <p class="text-sm font-medium text-slate-500 font-josefin">Geocercas Activas</p>
                    <h3 id="kpi-zonas" data-valor="0" class="text-3xl font-bold text-[#0056b3] mt-1 font-josefin">0</h3>
                </div>
                <div class="p-3 bg-white text-[#0056b3] rounded-xl border border-[#e2e8f0]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path></svg>
                </div>
            </div>

            <div class="card-in bg-white border border-[#e2e8f0] p-4 rounded-2xl shadow-sm flex items-center justify-between transition-shadow hover:shadow-md">
                <div>
                    <p class="text-sm font-medium text-slate-500 font-josefin">Estado del Servidor</p>
                    <span id="estado-conexion" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-50 text-slate-600 border border-slate-200 mt-1.5 transition-colors duration-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-pulse"></span>
                        Conectando...
                    </span>
                </div>
                <div class="p-3 bg-white text-slate-400 rounded-xl border border-[#e2e8f0]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                </div>
            </div>
        </div>

        <!-- Contenedor Principal: Mapa y Panel de Alertas -->
        <div class="flex flex-col lg:flex-row gap-4 flex-1 overflow-hidden">
            
            <!-- MAPA -->
            <div class="card-in bg-white border border-[#e2e8f0] rounded-2xl shadow-sm flex-1 overflow-hidden relative flex flex-col min-h-[400px] lg:min-h-0" style="animation-delay: 0.20s;">
                <div class="p-4 border-b border-[#e2e8f0] flex justify-between items-center bg-white z-[400] shrink-0">
                    <h2 class="text-base font-bold text-[#0056b3] font-josefin flex items-center gap-2">
                        <span class="relative flex w-2.5 h-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#0056b3] opacity-50"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#0056b3]"></span>
                        </span>
                        Monitoreo Geográfico en Vivo
                    </h2>
                    <span class="text-xs text-slate-500">
                        Actualización cada 5s · <span id="ultima-actualizacion">esperando datos…</span>
                    </span>
                </div>
                <div id="mapa-rastreo" class="w-full flex-1 z-0 min-h-[300px]"></div>
            </div>

            <!-- PANEL DE ALERTAS -->
            <div class="card-in bg-white border border-[#e2e8f0] rounded-2xl shadow-sm w-full lg:w-96 flex flex-col shrink-0 min-h-[300px] lg:min-h-0" style="animation-delay: 0.25s;">
                <div class="p-4 border-b border-[#e2e8f0] flex justify-between items-center bg-white shrink-0">
                    <h2 class="text-base font-bold text-slate-800 font-josefin flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        Alertas Recientes
                    </h2>
                    <span class="bg-slate-100 text-slate-600 border border-slate-200 text-xs font-bold px-2 py-0.5 rounded-full" id="contador-alertas">0</span>
                </div>
                
                <div class="flex-1 overflow-y-auto scrollbar-hide p-4 space-y-3 bg-[#f8fafc]" id="lista-alertas">
                    <div class="text-center text-sm text-slate-500 py-8">Cargando alertas...</div>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var map = L.map('mapa-rastreo', { zoomControl: false }).setView([18.8814, -99.1764], 14);
            L.control.zoom({ position: 'topright' }).addTo(map);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            setTimeout(function() {
                map.invalidateSize();
            }, 300);

            var marcadores = {};
            var ultimaActualizacionTs = null;

            var iconoVehiculo = L.divIcon({
                className: '',
                html: `
                    <div class="vehiculo-marker">
                        <div class="vehiculo-marker-pulse"></div>
                        <div class="vehiculo-marker-dot">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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

            function cargarZonas() {
                fetch('{{ route("api.zonas.en-vivo") }}')
                    .then(r => r.json())
                    .then(geojson => {
                        L.geoJSON(geojson, {
                            style: function (feature) {
                                var color = feature.properties.color || '#0056b3';
                                return { color: color, weight: 2, fillColor: color, fillOpacity: 0.10, dashArray: '6 4' };
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

            function actualizarUbicaciones() {
                fetch('{{ route("api.vehiculos.en-vivo") }}')
                    .then(response => response.json())
                    .then(data => {
                        let estado = document.getElementById('estado-conexion');
                        estado.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[#e6f0fa] text-[#0056b3] border border-[#dbe4f0] mt-1.5 transition-colors duration-300 font-josefin';
                        estado.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-[#0056b3]"></span> En Línea';

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
                                    <div class="font-ubuntu">
                                        <div class="px-4 py-3 border-b border-gray-100 bg-[#f8fafc] rounded-t-2xl">
                                            <b class="text-[#0056b3] text-sm font-josefin">${vehiculo.nombre}</b>
                                        </div>
                                        <div class="px-4 py-3 text-xs text-slate-600 space-y-1.5">
                                            <div class="flex justify-between"><span class="text-slate-400">Modelo</span><span class="font-medium text-slate-700">${vehiculo.marca || ''} ${vehiculo.modelo || 'N/D'}</span></div>
                                            <div class="flex justify-between"><span class="text-slate-400">Batería</span><span class="font-medium text-slate-700">${ultimaUbicacion.porcentaje_bateria ?? 'N/D'}%</span></div>
                                            <div class="flex justify-between"><span class="text-slate-400">Velocidad</span><span class="font-medium text-slate-700">${ultimaUbicacion.velocidad} km/h</span></div>
                                            <div class="text-slate-400 text-[10px] pt-2 border-t border-gray-100 mt-1">
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
                        let estado = document.getElementById('estado-conexion');
                        estado.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 border border-slate-200 mt-1.5 transition-colors duration-300 font-josefin';
                        estado.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Desconectado';
                    });
            }

            function cargarAlertas() {
                fetch('{{ route("api.alertas.recientes") }}')
                    .then(r => r.json())
                    .then(alertas => {
                        let contenedor = document.getElementById('lista-alertas');
                        document.getElementById('contador-alertas').innerText = alertas.length;
                        
                        if (alertas.length === 0) {
                            contenedor.innerHTML = '<div class="text-center text-sm text-slate-400 py-8">Sin alertas recientes</div>';
                            return;
                        }

                        let html = '';
                        alertas.forEach(alerta => {
                            let esSalida = alerta.tipo.includes('salida');
                            let color = esSalida ? 'text-slate-600 bg-white border-slate-200' : 'text-blue-600 bg-white border-blue-100';
                            let icon = esSalida 
                                ? 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1' 
                                : 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z';
                            
                            html += `
                                <div class="p-3 bg-white border border-[#e2e8f0] rounded-xl shadow-sm flex gap-3 hover:shadow-md transition-shadow">
                                    <div class="shrink-0 mt-0.5">
                                        <div class="w-8 h-8 rounded-full ${color} flex items-center justify-center border">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${icon}"></path></svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-slate-800 font-josefin">${alerta.vehiculo ? alerta.vehiculo.nombre : 'Vehículo Desconocido'}</p>
                                        <p class="text-xs text-slate-500 mt-0.5 leading-snug">${alerta.mensaje}</p>
                                        <span class="text-[10px] text-slate-400 font-medium mt-1 block">${new Date(alerta.created_at).toLocaleString()}</span>
                                    </div>
                                </div>
                            `;
                        });
                        contenedor.innerHTML = html;
                    })
                    .catch(e => console.error('Error cargando alertas:', e));
            }

            setInterval(function () {
                let span = document.getElementById('ultima-actualizacion');
                if (!span || !ultimaActualizacionTs) return;
                let segundos = Math.floor((Date.now() - ultimaActualizacionTs) / 1000);
                span.innerText = segundos < 2 ? 'justo ahora' : `hace ${segundos}s`;
            }, 1000);

            cargarZonas();
            actualizarUbicaciones();
            cargarAlertas();

            setInterval(actualizarUbicaciones, 5000);
            setInterval(cargarAlertas, 10000);
        });
    </script>
</x-app-layout>