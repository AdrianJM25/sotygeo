<!-- ================= MODAL DE DETALLES (SHOW) ================= -->
<template x-teleport="body">
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
            <h2 class="text-lg font-bold text-gray-800">Ficha Técnica del Vehículo</h2>
            <button type="button" @click="openShow = false" class="text-gray-400 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg p-1.5 transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">

            @php
                $iconoData = \App\Models\Vehiculo::ICONOS[$vehiculo->icono] ?? \App\Models\Vehiculo::ICONOS['sedan'];
                $colorIcono = $vehiculo->color_icono ?? '#111827';
            @endphp

            <div class="bg-gray-900 rounded-xl p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="relative w-11 h-11 shrink-0">
                        <div class="absolute inset-0 rounded-full opacity-30" style="background: {{ $colorIcono }};"></div>
                        <div class="absolute inset-[3px] rounded-full bg-gray-800 flex items-center justify-center" style="border: 2px solid {{ $colorIcono }};">
                            <svg class="w-5 h-5" fill="white" viewBox="0 0 24 24">{!! $iconoData['svg'] !!}</svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-white text-xl font-bold leading-tight">{{ $vehiculo->nombre }}</h3>
                        <span class="text-gray-400 text-sm">{{ $iconoData['label'] }} · {{ str_replace('_', ' ', $vehiculo->tipo_vehiculo) }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="block text-gray-400 text-xs uppercase tracking-wider mb-1">Dueño Legal</span>
                    @if($vehiculo->empresa)
                        <span class="inline-block bg-indigo-500/20 text-indigo-300 text-xs px-2 py-1 rounded font-medium">{{ $vehiculo->empresa->nombre }}</span>
                    @elseif($vehiculo->usuario)
                        <span class="inline-block bg-cyan-500/20 text-cyan-300 text-xs px-2 py-1 rounded font-medium">{{ $vehiculo->usuario->nombre }} (Particular)</span>
                    @else
                        <span class="inline-block bg-gray-700 text-gray-300 text-xs px-2 py-1 rounded font-medium">SOTyTECH</span>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Identificación Física</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                    <div class="sm:col-span-2">
                        <span class="block text-xs text-gray-500">Marca y Modelo</span>
                        <span class="text-base font-bold text-gray-900">{{ $vehiculo->marca ?? 'S/M' }} {{ $vehiculo->modelo ?? '' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500">Año</span>
                        <span class="text-sm font-medium text-gray-900">{{ $vehiculo->anio ?? 'N/D' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500">Color</span>
                        <span class="text-sm font-medium text-gray-900">{{ $vehiculo->color ?? 'N/D' }}</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="block text-xs text-gray-500">Placas</span>
                        <span class="text-sm font-bold text-gray-900 uppercase">{{ $vehiculo->placas ?? 'No registradas' }}</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="block text-xs text-gray-500">Número de Serie (VIN)</span>
                        <span class="text-sm font-medium text-gray-900 font-mono">{{ $vehiculo->vin ?? 'No registrado' }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                    <h3 class="text-xs font-bold text-indigo-500 uppercase tracking-wider mb-2">Asignación Operativa</h3>
                    <div class="space-y-2">
                        <div>
                            <span class="block text-xs text-indigo-400/80">Flotilla</span>
                            <span class="text-sm font-medium text-indigo-900">{{ $vehiculo->flotilla->nombre ?? 'Independiente' }}</span>
                        </div>
                    </div>
                </div>

                <div class="{{ $vehiculo->dispositivo ? 'bg-emerald-50/50 border-emerald-100' : 'bg-gray-50/50 border-gray-200' }} p-4 rounded-xl border">
                    <h3 class="text-xs font-bold {{ $vehiculo->dispositivo ? 'text-emerald-600' : 'text-gray-400' }} uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        @if($vehiculo->dispositivo)
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        @endif
                        Hardware GPS
                    </h3>

                    @if($vehiculo->dispositivo)
                        <div class="space-y-1">
                            <div>
                                <span class="block text-[11px] text-emerald-500/80">IMEI</span>
                                <span class="text-sm font-mono font-medium text-emerald-900">{{ $vehiculo->dispositivo->imei }}</span>
                            </div>
                            @if($vehiculo->dispositivo->numero_sim)
                                <div>
                                    <span class="block text-[11px] text-emerald-500/80">SIM</span>
                                    <span class="text-sm font-medium text-emerald-900">{{ $vehiculo->dispositivo->numero_sim }}</span>
                                </div>
                            @endif
                            @if($vehiculo->dispositivo->modelo)
                                <div>
                                    <span class="block text-[11px] text-emerald-500/80">Modelo</span>
                                    <span class="text-sm font-medium text-emerald-900">{{ $vehiculo->dispositivo->modelo }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-sm font-medium text-gray-500">
                            Este vehículo aún no tiene un dispositivo GPS enlazado.
                        </div>
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
</template>