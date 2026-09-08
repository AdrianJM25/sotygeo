<div x-show="openShow" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 text-left">
    <div x-show="openShow" x-transition.opacity @click="openShow = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>

    <div x-show="openShow"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
         class="relative bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-xl overflow-hidden z-50">

        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800">Ficha del Dispositivo</h2>
            <button type="button" @click="openShow = false" class="text-gray-400 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg p-1.5 transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-6 space-y-6">
            
            <div class="flex items-center justify-between p-4 rounded-xl {{ $dispositivo->modo_reposo ? 'bg-orange-50 border border-orange-100' : 'bg-green-50 border border-green-100' }}">
                <div>
                    <span class="block text-xs font-bold uppercase tracking-wider {{ $dispositivo->modo_reposo ? 'text-orange-600' : 'text-green-600' }}">
                        Estado Actual
                    </span>
                    <span class="text-lg font-bold {{ $dispositivo->modo_reposo ? 'text-orange-800' : 'text-green-800' }}">
                        {{ $dispositivo->modo_reposo ? 'MODO REPOSO (Sleep)' : 'TRANSMITIENDO' }}
                    </span>
                </div>
                <div class="p-2 rounded-full {{ $dispositivo->modo_reposo ? 'bg-orange-200 text-orange-600' : 'bg-green-200 text-green-600' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <span class="block text-xs text-gray-500">IMEI Numérico</span>
                    <span class="text-lg font-mono font-bold text-gray-900 tracking-widest">{{ $dispositivo->imei }}</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">Modelo</span>
                    <span class="text-sm font-medium text-gray-900">{{ $dispositivo->modelo ?? 'N/D' }}</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">SIM Card</span>
                    <span class="text-sm font-medium text-gray-900">{{ $dispositivo->numero_sim ?? 'N/D' }}</span>
                </div>
                <div class="col-span-2">
                    <span class="block text-xs text-gray-500 mb-1">Dueño de Hardware</span>
                    @if($dispositivo->empresa)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">Empresa: {{ $dispositivo->empresa->nombre }}</span>
                    @elseif($dispositivo->usuario)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-green-50 text-green-700 border border-green-100">Particular: {{ $dispositivo->usuario->nombre }}</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">SOTyTECH</span>
                    @endif
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <span class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Vehículo Enlazado</span>
                @if($dispositivo->vehiculo)
                    <div class="text-sm font-bold text-blue-700">{{ $dispositivo->vehiculo->nombre }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ $dispositivo->vehiculo->marca }} {{ $dispositivo->vehiculo->modelo }} (Placas: {{ $dispositivo->vehiculo->placas ?? 'S/P' }})</div>
                @else
                    <div class="text-sm text-gray-500 italic">El equipo se encuentra en inventario (Sin instalación).</div>
                @endif
            </div>

        </div>

        <div class="flex justify-end p-4 border-t border-gray-100 bg-gray-50/50">
            <button type="button" @click="openShow = false" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</div>