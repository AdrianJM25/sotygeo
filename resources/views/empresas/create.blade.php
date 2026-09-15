<div x-show="openCreate" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div x-show="openCreate" x-transition.opacity @click="openCreate = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

    <div x-show="openCreate"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
         class="relative bg-white rounded-2xl shadow-xl shadow-black/10 border border-[#0056b3]/20 w-full max-w-lg overflow-hidden z-50 font-body">

        <div class="p-4 border-b border-[#0056b3]/20 flex justify-between items-center bg-[#0056b3]">
            <h2 class="font-heading text-lg font-semibold text-white">Registrar nueva empresa</h2>
            <button type="button" @click="openCreate = false" class="text-white/70 hover:text-white bg-white/10 hover:bg-white/20 rounded-lg p-1.5 transition-colors">
                <span class="material-symbols-outlined text-[18px] block">close</span>
            </button>
        </div>

        <form action="{{ route('empresas.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-800">Razón social / nombre del cliente <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                    class="bg-white border {{ $errors->has('nombre') && !old('is_edit') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-[#0056b3]/30 focus:ring-[#0056b3] focus:border-[#0056b3]' }} text-gray-800 text-sm rounded-xl focus:ring-2 block w-full p-2.5 transition-colors">
                @if($errors->has('nombre') && !old('is_edit'))
                    <span class="text-xs text-red-600 mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">error</span>
                        {{ $errors->first('nombre') }}
                    </span>
                @endif
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-800">RFC (opcional, para facturación)</label>
                <input type="text" name="rfc" value="{{ old('rfc') }}"
                    class="bg-white border border-[#0056b3]/30 text-gray-800 text-sm rounded-xl focus:ring-2 focus:ring-[#0056b3] focus:border-[#0056b3] block w-full p-2.5 uppercase transition-colors">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-800">Teléfono corporativo</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}"
                        class="bg-white border border-[#0056b3]/30 text-gray-800 text-sm rounded-xl focus:ring-2 focus:ring-[#0056b3] focus:border-[#0056b3] block w-full p-2.5 transition-colors">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-800">Correo de contacto</label>
                    <input type="email" name="correo" value="{{ old('correo') }}"
                        class="bg-white border {{ $errors->has('correo') && !old('is_edit') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-[#0056b3]/30 focus:ring-[#0056b3] focus:border-[#0056b3]' }} text-gray-800 text-sm rounded-xl focus:ring-2 block w-full p-2.5 transition-colors">
                    @if($errors->has('correo') && !old('is_edit'))
                        <span class="text-xs text-red-600 mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span>
                            {{ $errors->first('correo') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center pt-2">
                <input type="checkbox" name="is_active" id="is_active_create" value="1" checked
                    class="w-4 h-4 rounded border-[#0056b3]/30 text-[#0056b3] accent-[#0056b3] focus:ring-2 focus:ring-[#0056b3]">
                <label for="is_active_create" class="ml-2 text-sm font-medium text-gray-800">Habilitar acceso a la plataforma (servicio activo)</label>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-[#0056b3]/20">
                <button type="button" @click="openCreate = false" class="px-5 py-2.5 text-sm font-medium text-[#0056b3] bg-white border border-[#0056b3]/30 rounded-xl hover:bg-blue-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-[#0056b3] rounded-xl hover:bg-[#003d82] transition-colors shadow-sm shadow-black/10">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Guardar empresa
                </button>
            </div>
        </form>
    </div>
</div>