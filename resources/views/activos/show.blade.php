<div x-show="openShow" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 text-left">
    <div x-show="openShow" x-transition.opacity @click="openShow = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>

    <div x-show="openShow"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
         class="relative bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-2xl overflow-hidden z-50">

        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800">Resumen del Activo</h2>
            <button type="button" @click="openShow = false" class="text-gray-400 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg p-1.5 transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
            
            <div class="grid grid-cols-2 gap-4 bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                <div class="col-span-2">
                    <span class="block text-xs text-gray-500">Nombre de Identificación</span>
                    <span class="text-base font-bold text-gray-900">{{ $activo->nombre }}</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">Clasificación</span>
                    <span class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $activo->tipo) }}</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">Agrupación (Flotilla)</span>
                    <span class="text-sm font-medium text-gray-900">{{ $activo->flotilla->nombre ?? 'Independiente' }}</span>
                </div>
                <div class="col-span-2">
                    <span class="block text-xs text-gray-500">Propietario Legal en SotyGeo</span>
                    <span class="text-sm font-bold text-indigo-700">
                        @if($activo->empresa) {{ $activo->empresa->nombre }} (Corporativo)
                        @elseif($activo->usuario) {{ $activo->usuario->nombre }} (Cliente Particular)
                        @else SOTyTECH (Propiedad de Empresa Rastreadora) @endif
                    </span>
                </div>
            </div>

            <!-- Estatus de Vinculación Hardware/Vehículo -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Tarjeta Hardware -->
                <div class="p-4 rounded-xl border {{ $activo->dispositivo ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Hardware GPS</h4>
                    @if($activo->dispositivo)
                        <div class="text-sm text-green-800 font-medium">Conectado ({{ $activo->dispositivo->modelo ?? 'GPS' }})</div>
                        <div class="text-xs text-green-600 font-mono mt-1">IMEI: {{ $activo->dispositivo->imei ?? 'N/D' }}</div>
                    @else
                        <div class="text-sm text-gray-500 italic">No hay equipo instalado</div>
                    @endif
                </div>

                <!-- Tarjeta Vehículo -->
                <div class="p-4 rounded-xl border {{ $activo->vehiculo ? 'border-blue-200 bg-blue-50' : 'border-gray-200 bg-gray-50' }}">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Ficha de Vehículo</h4>
                    @if($activo->vehiculo)
                        <div class="text-sm text-blue-800 font-medium">{{ $activo->vehiculo->marca }} {{ $activo->vehiculo->modelo }}</div>
                        <div class="text-xs text-blue-600 font-bold mt-1">Placas: {{ $activo->vehiculo->placas ?? 'S/P' }}</div>
                    @else
                        <div class="text-sm text-gray-500 italic">Sin datos de vehículo capturados</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex justify-end p-4 border-t border-gray-100 bg-gray-50/50">
            <button type="button" @click="openShow = false" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</div>