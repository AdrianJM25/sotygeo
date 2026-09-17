<x-app-layout>
    <!-- Incluir CSS y JS de Leaflet y Geoman -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.min.js"></script>

    <style>
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-item { animation: fadeSlideUp 0.4s ease-out both; }
        input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
        input[type="color"]::-webkit-color-swatch { border: none; border-radius: 6px; }
        .map-container { z-index: 10; }
    </style>

    <div x-data="{
            busqueda: '',
            estadoZonas: {},
            cargarEstado() {
                fetch('{{ route('api.geocercas.estado') }}')
                    .then(r => r.json())
                    .then(eventos => {
                        let agrupado = {};
                        eventos.forEach(e => {
                            if (!agrupado[e.zona_id]) agrupado[e.zona_id] = [];
                            agrupado[e.zona_id].push(e);
                        });
                        this.estadoZonas = agrupado;
                    })
                    .catch(err => console.error('Error cargando estado de geocercas:', err));
            },
            vehiculosEnZona(zonaId) {
                return this.estadoZonas[zonaId] || [];
            }
         }"
         x-init="cargarEstado(); setInterval(() => cargarEstado(), 15000)"
         class="space-y-5 pb-8 max-w-[1600px] mx-auto">

        <!-- Header Principal -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-item">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight">Geocercas y Zonas</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Delimita regiones estratégicas para el monitoreo logístico y perimetral.</p>
                </div>
            </div>

            <div class="flex items-center w-full sm:w-72">
                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path>
                        </svg>
                    </span>
                    <input type="text" x-model="busqueda" placeholder="Buscar zona existente..."
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all outline-none text-slate-700">
                </div>
            </div>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="flex items-center justify-between px-5 py-3.5 rounded-xl bg-emerald-50 text-emerald-800 text-sm border border-emerald-200 shadow-sm animate-item" style="animation-delay: 0.1s;">
                <span class="flex items-center gap-2.5 font-medium">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('success') }}
                </span>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        @if(session('warning'))
            <div x-data="{ show: true }" x-show="show" class="flex items-center justify-between px-5 py-3.5 rounded-xl bg-amber-50 text-amber-800 text-sm border border-amber-200 shadow-sm animate-item" style="animation-delay: 0.1s;">
                <span class="flex items-center gap-2.5 font-medium">
                    <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path></svg>
                    {{ session('warning') }}
                </span>
                <button @click="show = false" class="text-amber-500 hover:text-amber-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="px-5 py-3.5 rounded-xl bg-rose-50 text-rose-800 text-sm border border-rose-200 shadow-sm animate-item" style="animation-delay: 0.1s;">
                <ul class="list-disc list-inside space-y-1 font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Layout de Trabajo -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">

            <!-- Panel Izquierdo: Controles -->
            <div class="lg:col-span-4 flex flex-col gap-5 animate-item" style="animation-delay: 0.2s;">

                <!-- Tarjeta Formulario -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                        <h3 id="titulo-formulario" class="font-bold text-slate-800 text-sm uppercase tracking-wider">Crear Nueva Zona</h3>
                    </div>

                    <form action="{{ route('zonas.store') }}" method="POST" id="form-zona" class="p-5 space-y-5 max-h-[60vh] overflow-y-auto custom-scrollbar">
                        @csrf
                        <input type="hidden" name="_method" id="metodo-form" value="POST">
                        <input type="hidden" name="coordenadas" id="input-coordenadas" required>

                        <div>
                            <label class="block mb-1.5 text-xs font-semibold text-slate-600 uppercase">Nombre</label>
                            <input type="text" name="nombre" required placeholder="Ej. Almacén Central"
                                   class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 p-2.5 transition-all text-slate-700 outline-none">
                        </div>

                        <div>
                            <label class="block mb-1.5 text-xs font-semibold text-slate-600 uppercase">Color en mapa</label>
                            <div class="flex items-center gap-3 bg-slate-50 border border-slate-200 p-1.5 rounded-xl">
                                <input type="color" name="color_hex" value="#4F46E5" required
                                       class="w-10 h-8 rounded-lg cursor-pointer bg-transparent border-0 outline-none p-0">
                                <span class="text-xs text-slate-500 font-medium">Color de relleno y borde</span>
                            </div>
                        </div>

                        <!-- SECCIÓN: ASIGNACIÓN DE VEHÍCULOS -->
                        <div class="border-t border-slate-100 pt-4 mt-2">
                            <label class="block mb-2 text-xs font-semibold text-slate-600 uppercase">Asignar Vehículos y Alertas</label>
                            <div class="space-y-2">
                                @forelse($vehiculos as $index => $vehiculo)
                                    <div x-data="{ seleccionado: false }" class="bg-slate-50 border border-slate-200 rounded-lg p-3 transition-colors" :class="{ 'border-indigo-300 bg-indigo-50/30': seleccionado }">
                                        <label class="flex items-center gap-3 cursor-pointer">
                                            <input type="checkbox" x-model="seleccionado" name="vehiculos[{{ $index }}][id]" value="{{ $vehiculo->id }}"
                                                   class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-slate-700">{{ $vehiculo->nombre }}</span>
                                                <span class="text-[10px] text-slate-400">{{ $vehiculo->placas ?? 'Sin Placas' }}</span>
                                            </div>
                                        </label>

                                        <!-- Opciones que aparecen al seleccionar el vehículo -->
                                        <div x-show="seleccionado" x-collapse class="mt-3 pl-7 space-y-3">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Tipo de Regla</label>
                                                <select name="vehiculos[{{ $index }}][tipo_regla]" class="w-full text-xs bg-white border border-slate-200 rounded-lg p-1.5 outline-none focus:border-indigo-500">
                                                    <option value="informativa">Informativa (Solo Historial)</option>
                                                    <option value="restringida">Restringida (Prohibido el paso)</option>
                                                    <option value="permitida">Permitida (Zona segura)</option>
                                                </select>
                                            </div>
                                            <div class="flex flex-col gap-1.5">
                                                <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                                                    <input type="checkbox" name="vehiculos[{{ $index }}][notificar_entrada]" value="1" checked class="rounded text-emerald-500 focus:ring-emerald-500 border-slate-300">
                                                    Generar alerta de Entrada
                                                </label>
                                                <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                                                    <input type="checkbox" name="vehiculos[{{ $index }}][notificar_salida]" value="1" checked class="rounded text-amber-500 focus:ring-amber-500 border-slate-300">
                                                    Generar alerta de Salida
                                                </label>
                                            </div>

                                            <!-- NUEVO: Permanencia mínima / máxima -->
                                            <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-200/70">
                                                <div class="pt-2">
                                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Permanencia mín. (min)</label>
                                                    <input type="number" min="1" name="vehiculos[{{ $index }}][permanencia_minima_minutos]" placeholder="Sin límite"
                                                           class="w-full text-xs bg-white border border-slate-200 rounded-lg p-1.5 outline-none focus:border-indigo-500">
                                                </div>
                                                <div class="pt-2">
                                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Permanencia máx. (min)</label>
                                                    <input type="number" min="1" name="vehiculos[{{ $index }}][permanencia_maxima_minutos]" placeholder="Sin límite"
                                                           class="w-full text-xs bg-white border border-slate-200 rounded-lg p-1.5 outline-none focus:border-indigo-500">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-xs text-slate-500 italic p-2">No tienes vehículos registrados.</div>
                                @endforelse
                            </div>
                        </div>

                        <div id="alerta-dibujo" class="text-xs text-indigo-700 bg-indigo-50 border border-indigo-200/60 p-3 rounded-xl flex items-start gap-2">
                            <svg class="w-4 h-4 text-indigo-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            <span>Utiliza las herramientas en la esquina superior izquierda del mapa para trazar el polígono antes de guardar.</span>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" id="btn-guardar" disabled
                                    class="flex-1 px-5 py-3 text-sm font-bold text-white bg-slate-900 rounded-xl hover:bg-slate-800 transition-all shadow-sm disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                Guardar Geocerca
                            </button>
                            <button type="button" id="btn-cancelar" onclick="cancelarEdicion()"
                                    class="hidden px-5 py-3 text-sm font-bold text-slate-700 bg-slate-100 rounded-xl hover:bg-slate-200 transition-all shadow-sm">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tarjeta Lista de Zonas -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col max-h-[440px]">
                    <div class="px-5 py-3 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                        <h3 class="font-bold text-slate-800 text-sm">Zonas Registradas</h3>
                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-md text-xs font-bold bg-white border border-slate-200 text-slate-600 shadow-sm">
                            {{ count($zonas) }}
                        </span>
                    </div>

                    <div class="overflow-y-auto p-3 space-y-1.5 custom-scrollbar">
                        @forelse($zonas as $zona)
                            @php $busquedaTexto = mb_strtolower($zona->nombre); @endphp
                            <div x-data="{ expandido: false }"
                                 x-show="busqueda === '' || @js($busquedaTexto).includes(busqueda.toLowerCase())"
                                 class="rounded-xl border border-transparent hover:border-slate-100 transition-all bg-white group">

                                <div class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50">
                                    <button type="button" @click="enfocarZona('{{ $zona->id }}')" class="flex items-center gap-3 text-left min-w-0 flex-1 pr-2 focus:outline-none">
                                        <span class="w-3.5 h-3.5 rounded-full shrink-0 shadow-sm border border-black/10" style="background-color: {{ $zona->color_hex }}"></span>
                                        <div class="truncate">
                                            <div class="font-semibold text-slate-700 text-sm truncate group-hover:text-indigo-600 transition-colors">{{ $zona->nombre }}</div>
                                            <div class="text-[11px] text-slate-400">Ver en mapa</div>
                                        </div>
                                    </button>

                                    <div class="flex items-center gap-1 shrink-0">
                                        <!-- Badge "en vivo": cuántos vehículos están dentro ahora -->
                                        <template x-if="vehiculosEnZona('{{ $zona->id }}').length > 0">
                                            <button type="button" @click="expandido = !expandido"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span x-text="vehiculosEnZona('{{ $zona->id }}').length + ' dentro'"></span>
                                            </button>
                                        </template>

                                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button type="button" onclick="prepararEdicion({{ $zona->id }})" title="Editar zona" class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </button>

                                            <form action="{{ route('zonas.destroy', $zona->id) }}" method="POST" onsubmit="return confirm('¿Eliminar definitivamente esta geocerca?');" class="inline-block">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Eliminar zona" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detalle expandible: vehículos dentro, con alerta visual si excedieron su tiempo máximo -->
                                <div x-show="expandido" x-collapse class="px-3 pb-3 space-y-1.5">
                                    <template x-for="v in vehiculosEnZona('{{ $zona->id }}')" :key="v.vehiculo_id">
                                        <div class="flex items-center justify-between text-xs px-2.5 py-1.5 rounded-lg"
                                             :class="(v.permanencia_maxima_minutos && v.minutos_dentro > v.permanencia_maxima_minutos) ? 'bg-rose-50 text-rose-700' : 'bg-slate-50 text-slate-600'">
                                            <span class="font-medium" x-text="v.vehiculo_nombre"></span>
                                            <span x-text="v.minutos_dentro + ' min dentro'"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="w-12 h-12 rounded-full bg-slate-50 text-slate-300 mx-auto flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                </div>
                                <p class="text-sm text-slate-500 font-medium">No hay zonas configuradas</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Panel Derecho: El Mapa -->
            <div class="lg:col-span-8 bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden relative min-h-[600px] lg:min-h-[calc(100vh-10rem)] map-container animate-item" style="animation-delay: 0.3s;">
                <div id="mapa-zonas" class="w-full h-full absolute inset-0 z-0"></div>
            </div>

        </div>
    </div>

    <!-- Lógica JavaScript -->
    <script>
        var capasMap = {};

        document.addEventListener('DOMContentLoaded', function () {
            var map = L.map('mapa-zonas').setView([18.8814, -99.1764], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            var zonasGuardadas = @json($zonas);

            zonasGuardadas.forEach(function(zona) {
                if (zona.geojson) {
                    try {
                        var geoData = typeof zona.geojson === 'string' ? JSON.parse(zona.geojson) : zona.geojson;

                        var layer = L.geoJSON(geoData, {
                            style: {
                                color: zona.color_hex || '#3B82F6',
                                fillColor: zona.color_hex || '#3B82F6',
                                weight: 2,
                                fillOpacity: 0.25
                            }
                        }).bindPopup('<div class="font-sans font-bold text-slate-800">' + zona.nombre + '</div>').addTo(map);

                        var polyLayer = layer.getLayers()[0];
                        capasMap[zona.id] = polyLayer;

                    } catch (err) {
                        console.error('Error parseando geojson para la zona:', zona.id, err);
                    }
                }
            });

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
                drawPolygon: true
            });

            map.pm.setLang('es');

            var capaActual = null;
            var inputCoordenadas = document.getElementById('input-coordenadas');
            var btnGuardar = document.getElementById('btn-guardar');
            var alertaDibujo = document.getElementById('alerta-dibujo');
            var inputColor = document.querySelector('input[name="color_hex"]');

            map.on('pm:create', function(e) {
                if (capaActual) {
                    map.removeLayer(capaActual);
                }

                capaActual = e.layer;
                var coordenadas = capaActual.getLatLngs()[0];

                capaActual.setStyle({
                    color: inputColor.value,
                    fillColor: inputColor.value,
                    weight: 3,
                    fillOpacity: 0.25
                });

                inputCoordenadas.value = JSON.stringify(coordenadas);
                btnGuardar.disabled = false;
                alertaDibujo.style.display = 'none';
            });

            map.on('pm:remove', function(e) {
                if (e.layer === capaActual) {
                    capaActual = null;
                    inputCoordenadas.value = '';
                    btnGuardar.disabled = true;
                    alertaDibujo.style.display = 'flex';
                }
            });

            inputColor.addEventListener('input', function(e) {
                if(capaActual) {
                    capaActual.setStyle({ color: e.target.value, fillColor: e.target.value });
                }
                Object.values(capasMap).forEach(l => {
                    if(l.pm && l.pm.enabled()) {
                        l.setStyle({ color: e.target.value, fillColor: e.target.value });
                    }
                });
            });

            window.enfocarZona = function(zonaId) {
                if (capasMap[zonaId]) {
                    var bounds = capasMap[zonaId].getBounds();
                    map.flyToBounds(bounds, { padding: [50, 50], maxZoom: 16, duration: 1.5 });
                    setTimeout(() => capasMap[zonaId].openPopup(), 1500);
                }
            };

            function layerToWKT(layer) {
                var latlngs = layer.getLatLngs()[0];
                var arr = [];
                latlngs.forEach(ll => arr.push({ lat: ll.lat, lng: ll.lng }));
                return JSON.stringify(arr);
            }

            window.prepararEdicion = function(zonaId) {
                var zona = zonasGuardadas.find(z => z.id == zonaId);
                if(!zona) return;

                document.getElementById('form-zona').action = `/zonas/${zona.id}`;
                document.getElementById('metodo-form').value = 'PUT';
                document.getElementById('titulo-formulario').innerText = 'Editar Zona: ' + zona.nombre;
                document.getElementById('btn-cancelar').classList.remove('hidden');

                document.querySelector('input[name="nombre"]').value = zona.nombre;
                document.querySelector('input[name="color_hex"]').value = zona.color_hex || '#4F46E5';

                document.querySelectorAll('input[type="checkbox"][value]').forEach(cb => {
                    if(cb.checked && cb.name.includes('vehiculos')) {
                        cb.checked = false;
                        cb.dispatchEvent(new Event('change'));
                    }
                });

                if(zona.vehiculos && zona.vehiculos.length > 0) {
                    zona.vehiculos.forEach(v => {
                        let cb = document.querySelector(`input[type="checkbox"][value="${v.id}"]`);
                        if(cb) {
                            cb.checked = true;
                            cb.dispatchEvent(new Event('change'));

                            let baseName = cb.name.replace('[id]', '');
                            let selectRegla = document.querySelector(`select[name="${baseName}[tipo_regla]"]`);
                            let chkEntrada = document.querySelector(`input[name="${baseName}[notificar_entrada]"]`);
                            let chkSalida = document.querySelector(`input[name="${baseName}[notificar_salida]"]`);
                            let inputMin = document.querySelector(`input[name="${baseName}[permanencia_minima_minutos]"]`);
                            let inputMax = document.querySelector(`input[name="${baseName}[permanencia_maxima_minutos]"]`);

                            if(selectRegla) selectRegla.value = v.pivot.tipo_regla;
                            if(chkEntrada) chkEntrada.checked = (v.pivot.notificar_entrada == 1);
                            if(chkSalida) chkSalida.checked = (v.pivot.notificar_salida == 1);
                            if(inputMin) inputMin.value = v.pivot.permanencia_minima_minutos ?? '';
                            if(inputMax) inputMax.value = v.pivot.permanencia_maxima_minutos ?? '';
                        }
                    });
                }

                if (capaActual) {
                    map.removeLayer(capaActual);
                    capaActual = null;
                }

                Object.values(capasMap).forEach(l => {
                    if(l.pm) l.pm.disable();
                });

                let layerAEditar = capasMap[zona.id];
                if(layerAEditar) {
                    layerAEditar.pm.enable({ allowSelfIntersection: false });

                    inputCoordenadas.value = layerToWKT(layerAEditar);
                    btnGuardar.disabled = false;
                    alertaDibujo.style.display = 'none';

                    layerAEditar.on('pm:edit', function(e) {
                        inputCoordenadas.value = layerToWKT(e.target);
                    });
                }

                enfocarZona(zona.id);
            };

            window.cancelarEdicion = function() {
                document.getElementById('form-zona').reset();
                document.getElementById('form-zona').action = "{{ route('zonas.store') }}";
                document.getElementById('metodo-form').value = 'POST';
                document.getElementById('titulo-formulario').innerText = 'Crear Nueva Zona';
                document.getElementById('btn-cancelar').classList.add('hidden');
                inputCoordenadas.value = '';
                btnGuardar.disabled = true;
                alertaDibujo.style.display = 'flex';

                document.querySelectorAll('input[type="checkbox"][value]').forEach(cb => {
                    if(cb.checked && cb.name.includes('vehiculos')) {
                        cb.checked = false;
                        cb.dispatchEvent(new Event('change'));
                    }
                });

                window.location.reload();
            };
        });
    </script>
</x-app-layout>