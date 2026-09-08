<div x-show="openEdit" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div x-show="openEdit" x-transition.opacity @click="openEdit = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>

    <div x-show="openEdit"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
         class="relative bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-xl overflow-hidden z-50">

        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800">Editar Activo</h2>
            <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg p-1.5 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('activos.update', $activo) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="is_edit" value="{{ $activo->id }}">

            <div class="space-y-4">
                
                @role('Super Administrador')
                    <div x-data="{ tipo_dueno: '{{ $activo->empresa_id ? 'empresa' : 'particular' }}' }" class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100 mb-4">
                        <label class="block mb-3 text-sm font-medium text-gray-900">Propietario del Activo</label>
                        
                        <div class="flex gap-4 mb-4">
                            <label class="flex items-center text-sm cursor-pointer">
                                <input type="radio" x-model="tipo_dueno" value="empresa" class="text-indigo-600">
                                <span class="ml-2 text-gray-700">Corporativo</span>
                            </label>
                            <label class="flex items-center text-sm cursor-pointer">
                                <input type="radio" x-model="tipo_dueno" value="particular" class="text-indigo-600">
                                <span class="ml-2 text-gray-700">Particular</span>
                            </label>
                        </div>

                        <div x-show="tipo_dueno === 'empresa'">
                            <select name="empresa_id" class="bg-white border text-gray-900 text-sm rounded-lg block w-full p-2.5">
                                <option value="">-- Interno SOTyTECH --</option>
                                @foreach($empresas as $emp)
                                    <option value="{{ $emp->id }}" {{ old('empresa_id', $activo->empresa_id) == $emp->id ? 'selected' : '' }}>{{ $emp->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div x-show="tipo_dueno === 'particular'" style="display: none;">
                            <select name="user_id" class="bg-white border text-gray-900 text-sm rounded-lg block w-full p-2.5">
                                <option value="">-- Seleccionar Cliente Particular --</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ old('user_id', $activo->user_id) == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre }} {{ $cliente->apellido_paterno }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endrole

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Nombre del Activo <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre', $activo->nombre) }}" required class="bg-gray-50 border {{ $errors->has('nombre') && old('is_edit') == $activo->id ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 block w-full p-2.5">
                    @if($errors->has('nombre') && old('is_edit') == $activo->id) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('nombre') }}</span> @endif
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Tipo de Activo <span class="text-red-500">*</span></label>
                        <select name="tipo" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-900 block w-full p-2.5">
                            @foreach(['auto'=>'Vehículo Ligero', 'moto'=>'Motocicleta', 'camion'=>'Camión Pesado', 'caja_seca'=>'Caja Seca', 'maquinaria'=>'Maquinaria', 'persona'=>'Persona', 'mascota'=>'Mascota', 'otro'=>'Otro'] as $key => $val)
                                <option value="{{ $key }}" {{ old('tipo', $activo->tipo) == $key ? 'selected' : '' }}>{{ $val }}</option>
                            @endforeach
                        </select>
                    </div>

                    @unless(auth()->user()->hasRole('Cliente Individual'))
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">Flotilla Asignada</label>
                        <select name="flotilla_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-900 block w-full p-2.5">
                            <option value="">-- Ninguna (Independiente) --</option>
                            @foreach($flotillas as $flota)
                                <option value="{{ $flota->id }}" {{ old('flotilla_id', $activo->flotilla_id) == $flota->id ? 'selected' : '' }}>{{ $flota->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endunless
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <button type="button" @click="openEdit = false" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">Actualizar Activo</button>
            </div>
        </form>
    </div>
</div>