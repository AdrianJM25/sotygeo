<div x-show="openCreate" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div x-show="openCreate" x-transition.opacity @click="openCreate = false" class="fixed inset-0 bg-[#121314]/50 backdrop-blur-sm"></div>

    <div x-show="openCreate"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
         class="relative bg-white rounded-2xl shadow-xl shadow-black/10 border border-[#56928C]/20 w-full max-w-lg overflow-hidden z-50 font-body">

        <div class="p-4 border-b border-[#56928C]/20 flex justify-between items-center bg-[#56928C]">
            <h2 class="font-heading text-lg font-semibold text-white">Registrar nueva empresa</h2>
            <button type="button" @click="openCreate = false" class="text-white/70 hover:text-white bg-white/10 hover:bg-white/20 rounded-lg p-1.5 transition-colors">
                <span class="material-symbols-outlined text-[18px] block">close</span>
            </button>
        </div>

        <form action="{{ route('empresas.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block mb-2 text-sm font-medium text-[#121314]">Razón social / nombre del cliente <span class="text-[#B8503F]">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                    class="bg-white border {{ $errors->has('nombre') && !old('is_edit') ? 'border-[#B8503F] focus:ring-[#B8503F] focus:border-[#B8503F]' : 'border-[#56928C]/30 focus:ring-[#66C1BA] focus:border-[#66C1BA]' }} text-[#121314] text-sm rounded-xl focus:ring-2 block w-full p-2.5 transition-colors">
                @if($errors->has('nombre') && !old('is_edit'))
                    <span class="text-xs text-[#8a3b2e] mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">error</span>
                        {{ $errors->first('nombre') }}
                    </span>
                @endif
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-[#121314]">RFC (opcional, para facturación)</label>
                <input type="text" name="rfc" value="{{ old('rfc') }}"
                    class="bg-white border border-[#56928C]/30 text-[#121314] text-sm rounded-xl focus:ring-2 focus:ring-[#66C1BA] focus:border-[#66C1BA] block w-full p-2.5 uppercase transition-colors">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-[#121314]">Teléfono corporativo</label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}"
                        class="bg-white border border-[#56928C]/30 text-[#121314] text-sm rounded-xl focus:ring-2 focus:ring-[#66C1BA] focus:border-[#66C1BA] block w-full p-2.5 transition-colors">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-[#121314]">Correo de contacto</label>
                    <input type="email" name="correo" value="{{ old('correo') }}"
                        class="bg-white border {{ $errors->has('correo') && !old('is_edit') ? 'border-[#B8503F] focus:ring-[#B8503F] focus:border-[#B8503F]' : 'border-[#56928C]/30 focus:ring-[#66C1BA] focus:border-[#66C1BA]' }} text-[#121314] text-sm rounded-xl focus:ring-2 block w-full p-2.5 transition-colors">
                    @if($errors->has('correo') && !old('is_edit'))
                        <span class="text-xs text-[#8a3b2e] mt-1 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span>
                            {{ $errors->first('correo') }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center pt-2">
                <input type="checkbox" name="is_active" id="is_active_create" value="1" checked
                    class="w-4 h-4 rounded border-[#56928C]/30 accent-[#66C1BA] focus:ring-2 focus:ring-[#66C1BA]">
                <label for="is_active_create" class="ml-2 text-sm font-medium text-[#121314]">Habilitar acceso a la plataforma (servicio activo)</label>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-[#56928C]/20">
                <button type="button" @click="openCreate = false" class="px-5 py-2.5 text-sm font-medium text-[#56928C] bg-white border border-[#56928C]/30 rounded-xl hover:bg-[#56928C]/5 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-[#66C1BA] rounded-xl hover:bg-[#56928C] transition-colors shadow-sm shadow-black/10">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Guardar empresa
                </button>
            </div>
        </form>
    </div>
</div>