<div x-show="openEdit" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 text-left">
    <div x-show="openEdit" x-transition.opacity @click="openEdit = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>

    <div x-show="openEdit"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
         class="relative bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-lg overflow-hidden z-50">

        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800">Editar Empresa</h2>
            <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg p-1.5 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('empresas.update', $empresa) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="is_edit" value="{{ $empresa->id }}">

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">Razón Social / Nombre del Cliente <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $empresa->nombre) }}" required class="bg-gray-50 border {{ $errors->has('nombre') && old('is_edit') == $empresa->id ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 block w-full p-2.5">
                @if($errors->has('nombre') && old('is_edit') == $empresa->id) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('nombre') }}</span> @endif
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900">RFC</label>
                <input type="text" name="rfc" value="{{ old('rfc', $empresa->rfc) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-900 block w-full p-2.5 uppercase">
            </div>

            <!-- Agrupamos Teléfono y Correo en 2 columnas -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Teléfono Corporativo</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $empresa->telefono) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-900 block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Correo de Contacto</label>
                    <input type="email" name="correo" value="{{ old('correo', $empresa->correo) }}" class="bg-gray-50 border {{ $errors->has('correo') && old('is_edit') == $empresa->id ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 block w-full p-2.5">
                    @if($errors->has('correo') && old('is_edit') == $empresa->id) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('correo') }}</span> @endif
                </div>
            </div>

            <div class="flex items-center pt-2">
                <input type="checkbox" name="is_active" id="is_active_{{ $empresa->id }}" value="1" {{ old('is_active', $empresa->is_active) ? 'checked' : '' }} class="w-4 h-4 text-gray-900 bg-gray-100 border-gray-300 rounded focus:ring-gray-900 focus:ring-2">
                <label for="is_active_{{ $empresa->id }}" class="ml-2 text-sm font-medium text-gray-900">
                    Habilitar acceso (Si se desmarca, ningún empleado de esta empresa podrá entrar a SotyGeo)
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <button type="button" @click="openEdit = false" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">Actualizar Datos</button>
            </div>
        </form>
    </div>
</div>