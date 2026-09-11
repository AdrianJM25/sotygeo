@props(['seleccionado' => null, 'colorSeleccionado' => '#111827'])

@php
    $iconosDisponibles = \App\Models\Vehiculo::iconosDisponibles();
    
    // Ajuste por si tenías guardados íconos de pruebas anteriores sin "images/"
    $iconoActual = $seleccionado;
    if ($iconoActual && str_starts_with($iconoActual, 'icons_vehiculos/') && !str_starts_with($iconoActual, 'icons_vehiculos/personalizados')) {
        $iconoActual = 'images/' . $iconoActual;
    }
    
    $primero = $iconosDisponibles[0]['path'] ?? null;
@endphp

<div x-data="{
        icono: '{{ $iconoActual ?? $primero }}',
        color: '{{ $colorSeleccionado }}',
        customPreview: null,
        elegirPredefinido(ruta) {
            this.icono = ruta;
            this.customPreview = null;
            this.$refs.iconoFile.value = '';
        },
        previewArchivo(event) {
            const file = event.target.files[0];
            if (file) { this.customPreview = URL.createObjectURL(file); }
        },
        quitarPersonalizado() {
            this.customPreview = null;
            this.$refs.iconoFile.value = '';
        },
        get previewSrc() {
            if (this.customPreview) return this.customPreview;
            if (!this.icono) return '';
            
            // Si es personalizado, va a Storage. Si no, va a public/images.
            if (this.icono.startsWith('icons_vehiculos/personalizados')) {
                return `{{ rtrim(Storage::disk('public')->url(''), '/') }}/${this.icono}`;
            }
            return `{{ rtrim(asset(''), '/') }}/${this.icono}`;
        }
     }">

    <label class="block mb-2 text-sm font-medium text-gray-900">Icono en el mapa</label>

    @if(empty($iconosDisponibles))
        <div class="text-xs text-amber-600 bg-amber-50 border border-amber-100 rounded-lg p-3 mb-2">
            No se encontraron íconos en <code>public/images/icons_vehiculos</code>. Verifica que copiaste las imágenes ahí.
        </div>
    @endif

    <div class="grid grid-cols-5 sm:grid-cols-6 gap-2">
        @foreach ($iconosDisponibles as $item)
            <button type="button" @click="elegirPredefinido('{{ $item['path'] }}')"
                    :class="(icono === '{{ $item['path'] }}' && !customPreview) ? 'border-gray-900 bg-gray-100 shadow-sm' : 'border-gray-200 bg-gray-50 hover:bg-gray-100 hover:border-gray-300'"
                    class="flex flex-col items-center justify-center gap-1 py-2.5 rounded-xl border-2 transition-all">
                <!-- SE ELIMINÓ STORAGE AQUÍ, AHORA USA LA URL DIRECTA -->
                <img src="{{ $item['url'] }}" class="w-7 h-7 object-contain" alt="{{ $item['label'] }}">
                <span class="text-[9px] font-medium text-gray-500 text-center leading-tight px-0.5">{{ $item['label'] }}</span>
            </button>
        @endforeach

        <!-- Subir ícono propio -->
        <label class="flex flex-col items-center justify-center gap-1 py-2.5 rounded-xl border-2 border-dashed cursor-pointer transition-all"
               :class="customPreview ? 'border-gray-900 bg-gray-100' : 'border-gray-300 bg-gray-50 hover:bg-gray-100 hover:border-gray-400'">
            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3.75 3.75M12 9.75l3.75 3.75M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
            </svg>
            <span class="text-[9px] font-medium text-gray-500">Subir propio</span>
            <input type="file" name="icono_file" x-ref="iconoFile" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="hidden" @change="previewArchivo($event)">
        </label>
    </div>

    <input type="hidden" name="icono" :value="icono">

    <div class="flex items-center gap-3 mt-4">
        <label class="text-sm font-medium text-gray-900">Color del marcador</label>
        <input type="color" name="color_icono" x-model="color" class="w-9 h-9 rounded-lg border border-gray-300 cursor-pointer p-0.5">
        <span class="text-xs text-gray-400 font-mono" x-text="color"></span>
    </div>

    <div class="mt-4 flex items-center gap-3 bg-gray-50 border border-gray-100 rounded-xl p-3">
        <div class="relative w-9 h-9 shrink-0">
            <div class="absolute inset-0 rounded-full opacity-30" :style="`background:${color}`"></div>
            <div class="absolute inset-[3px] rounded-full bg-gray-900 flex items-center justify-center overflow-hidden" :style="`border:2px solid ${color}`">
                <img :src="previewSrc" class="w-4 h-4 object-contain" alt="Vista previa">
            </div>
        </div>
        <div class="flex-1">
            <span class="text-xs text-gray-400 block">Así se verá en el Mapa en Vivo</span>
            <button type="button" x-show="customPreview" @click="quitarPersonalizado()" class="text-[11px] text-red-500 hover:text-red-700 underline underline-offset-2">
                Quitar imagen subida
            </button>
        </div>
    </div>
</div>