<!-- ================= MODAL DE CREACIÓN ================= -->
<div x-show="openCreate" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div x-show="openCreate" x-transition.opacity @click="openCreate = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>

    <div x-show="openCreate"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
         class="relative bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-xl overflow-hidden z-50">

        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800">Registrar Nueva Flotilla</h2>
            <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg p-1.5 transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- AQUÍ INICIA LA MAGIA DE ALPINE -->
        <form action="{{ route('flotillas.store') }}" method="POST" class="p-6 space-y-6"
              x-data="{
                  empresa_id: '{{ old('empresa_id', auth()->user()->hasRole('Super Administrador') ? '' : auth()->user()->empresa_id) }}',
                  vehiculos: @js($vehiculos ?? []),
                  get vehiculosFiltrados() {
                      if (!this.empresa_id) return [];
                      // Filtra: Que pertenezcan a la empresa Y que no estén asignados a otra flotilla
                      return this.vehiculos.filter(v => v.empresa_id == this.empresa_id && v.flotilla_id === null);
                  }
              }">
            @csrf

            <div class="space-y-4">
                <!-- SECCIÓN MULTI-TENANT (EMPRESA) -->
                @role('Super Administrador')
                    <div class="bg-indigo-50/50 p-3 rounded-xl border border-indigo-100 mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Empresa / Cliente Dueño</label>
                        <!-- Agregado x-model para reactividad -->
                        <select name="empresa_id" x-model="empresa_id" required class="bg-white border {{ $errors->has('empresa_id') && !old('is_edit') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                            <option value="">-- Seleccionar Empresa --</option>
                            @foreach($empresas as $emp)
                                <option value="{{ $emp->id }}" {{ old('empresa_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('empresa_id') && !old('is_edit')) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('empresa_id') }}</span> @endif
                    </div>
                @else
                    <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-200 mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-500">Empresa / Corporativo</label>
                        <input type="text" value="{{ auth()->user()->empresa->nombre ?? 'N/D' }}" disabled class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed">
                        <input type="hidden" name="empresa_id" :value="empresa_id">
                    </div>
                @endrole

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Nombre de la Flotilla</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required class="bg-gray-50 border {{ $errors->has('nombre') && !old('is_edit') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5" placeholder="Ej. Motos Repartidores Jiutepec">
                    @if($errors->has('nombre') && !old('is_edit')) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('nombre') }}</span> @endif
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Usuario Responsable (Opcional)</label>
                    <select name="user_id" class="bg-gray-50 border {{ $errors->has('user_id') && !old('is_edit') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">
                        <option value="">-- Sin asignar / Toda la empresa --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->nombre }} {{ $user->apellido_paterno }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- SECCIÓN ASIGNACIÓN DE VEHÍCULOS -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Vehículos a asignar</label>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 max-h-52 overflow-y-auto shadow-inner">
                        
                        <template x-if="vehiculosFiltrados.length === 0">
                            <div class="text-center py-4">
                                <p class="text-sm text-gray-500">No hay vehículos disponibles o sin asignar para esta empresa.</p>
                            </div>
                        </template>

                        <div class="space-y-2">
                            <template x-for="vehiculo in vehiculosFiltrados" :key="vehiculo.id">
                                <label class="flex items-center p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors shadow-sm">
                                    <input type="checkbox" name="vehiculos[]" :value="vehiculo.id" class="w-4 h-4 text-gray-900 bg-gray-100 border-gray-300 rounded focus:ring-gray-900">
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-gray-900" x-text="vehiculo.nombre"></span>
                                        <span class="block text-xs text-gray-500" x-text="'Placas: ' + (vehiculo.placas || 'N/D')"></span>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Descripción (Opcional)</label>
                    <textarea name="descripcion" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5" placeholder="Detalles de la zona o tipo de unidades...">{{ old('descripcion') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" @click="openCreate = false" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors shadow-sm">Guardar Flotilla</button>
            </div>
        </form>
    </div>
</div>