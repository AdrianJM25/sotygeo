@props(['seleccionado' => 'sedan', 'colorSeleccionado' => '#111827'])

<div x-data="{ icono: '{{ $seleccionado }}', color: '{{ $colorSeleccionado }}' }">
    <label class="block mb-2 text-sm font-medium text-gray-900">Icono en el mapa</label>

    <div class="grid grid-cols-4 gap-2">
        @foreach (\App\Models\Vehiculo::ICONOS as $clave => $data)
            <button type="button" @click="icono = '{{ $clave }}'"
                    :class="icono === '{{ $clave }}' ? 'border-gray-900 bg-gray-900 shadow-sm' : 'border-gray-200 bg-gray-50 hover:bg-gray-100 hover:border-gray-300'"
                    class="flex flex-col items-center justify-center gap-1.5 py-3 rounded-xl border-2 transition-all">
                <svg class="w-6 h-6 transition-colors" :fill="icono === '{{ $clave }}' ? '#ffffff' : '#6B7280'" viewBox="0 0 24 24">
                    {!! $data['svg'] !!}
                </svg>
                <span :class="icono === '{{ $clave }}' ? 'text-white' : 'text-gray-500'" class="text-[10px] font-medium text-center leading-tight px-1 transition-colors">
                    {{ $data['label'] }}
                </span>
            </button>
        @endforeach
    </div>

    <input type="hidden" name="icono" :value="icono">

    <div class="flex items-center gap-3 mt-4">
        <label class="text-sm font-medium text-gray-900">Color del marcador</label>
        <input type="color" name="color_icono" x-model="color" class="w-9 h-9 rounded-lg border border-gray-300 cursor-pointer p-0.5">
        <span class="text-xs text-gray-400 font-mono" x-text="color"></span>
    </div>

    <!-- Vista previa: así se ve exactamente en el Mapa en Vivo -->
    <div class="mt-4 flex items-center gap-3 bg-gray-50 border border-gray-100 rounded-xl p-3"
         x-data="{ iconos: {{ Illuminate\Support\Js::from(\App\Models\Vehiculo::ICONOS) }} }">
        <div class="relative w-9 h-9 shrink-0">
            <div class="absolute inset-0 rounded-full opacity-30" :style="`background:${color}`"></div>
            <div class="absolute inset-[3px] rounded-full bg-gray-900 flex items-center justify-center" :style="`border:2px solid ${color}`">
                <svg class="w-3.5 h-3.5" fill="white" viewBox="0 0 24 24" x-html="iconos[icono]?.svg"></svg>
            </div>
        </div>
        <span class="text-xs text-gray-400">Así se verá en el Mapa en Vivo</span>
    </div>
</div>