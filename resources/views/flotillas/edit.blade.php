<!-- ================= MODAL DE EDICIÓN ================= -->
<div x-show="openEdit" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 text-left">
            
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
            <h2 class="text-lg font-bold text-gray-800">Editar Flotilla</h2>
            <button type="button" @click="openEdit = false" class="text-gray-400 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg p-1.5 transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- SE AGREGA EL X-DATA AL FORMULARIO PARA REACTIVIDAD -->
        <form action="{{ route('flotillas.update', $flotilla) }}" method="POST" class="p-6 space-y-6"
              x-data="{
                  empresa_id: '{{ old('empresa_id', $flotilla->empresa_id) }}',
                  flotilla_id: {{ $flotilla->id }},
                  vehiculos: @js($vehiculos ?? []),
                  vehiculosAsignados: @js($flotilla->vehiculos->pluck('id')->toArray()),
                  get vehiculosFiltrados() {
                      if (!this.empresa_id) return [];
                      // Filtra: Vehículos de la empresa seleccionada que no tengan flotilla O que ya pertenezcan a esta
                      return this.vehiculos.filter(v => 
                          v.empresa_id == this.empresa_id && 
                          (v.flotilla_id === null || v.flotilla_id === this.flotilla_id)
                      );
                  }
              }">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="is_edit" value="{{ $flotilla->id }}">

            <div class="space-y-4">

                <!-- SECCIÓN MULTI-TENANT (EMPRESA) -->
                @role('Super Administrador')
                    <div class="bg-indigo-50/50 p-3 rounded-xl border border-indigo-100 mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-900">Empresa / Cliente Dueño</label>
                        <!-- SE AGREGA X-MODEL PARA ESCUCHAR CAMBIOS -->
                        <select name="empresa_id" x-model="empresa_id" required class="bg-white border {{ $errors->has('empresa_id') && old('is_edit') == $flotilla->id ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
                            <option value="">-- Seleccionar Empresa --</option>
                            @foreach($empresas as $emp)
                                <option value="{{ $emp->id }}" {{ old('empresa_id', $flotilla->empresa_id) == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('empresa_id') && old('is_edit') == $flotilla->id) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('empresa_id') }}</span> @endif
                    </div>
                @else
                    <div class="bg-gray-50/50 p-3 rounded-xl border border-gray-200 mb-4">
                        <label class="block mb-2 text-sm font-medium text-gray-500">Empresa / Corporativo</label>
                        <input type="text" value="{{ $flotilla->empresa->nombre ?? 'N/D' }}" disabled class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-lg block w-full p-2.5 cursor-not-allowed">
                        <input type="hidden" name="empresa_id" :value="empresa_id">
                    </div>
                @endrole

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Nombre de la Flotilla</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $flotilla->nombre) }}" required class="bg-gray-50 border {{ $errors->has('nombre') && old('is_edit') == $flotilla->id ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">
                    @if($errors->has('nombre') && old('is_edit') == $flotilla->id) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('nombre') }}</span> @endif
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Usuario Responsable (Opcional)</label>
                    <select name="user_id" class="bg-gray-50 border {{ $errors->has('user_id') && old('is_edit') == $flotilla->id ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">
                        <option value="">-- Sin asignar / Toda la empresa --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ (old('user_id') ?? $flotilla->user_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->nombre }} {{ $user->apellido_paterno }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @if($errors->has('user_id') && old('is_edit') == $flotilla->id) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('user_id') }}</span> @endif
                </div>

                <!-- SECCIÓN ASIGNACIÓN DE VEHÍCULOS (NUEVA) -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Vehículos asignados y disponibles</label>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 max-h-52 overflow-y-auto shadow-inner">
                        
                        <template x-if="vehiculosFiltrados.length === 0">
                            <div class="text-center py-4">
                                <p class="text-sm text-gray-500">No hay vehículos disponibles o asignados a esta empresa.</p>
                            </div>
                        </template>

                        <div class="space-y-2">
                            <template x-for="vehiculo in vehiculosFiltrados" :key="vehiculo.id">
                                <label class="flex items-center p-3 bg-white border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors shadow-sm">
                                    <input type="checkbox" name="vehiculos[]" :value="vehiculo.id" 
                                           :checked="vehiculosAsignados.includes(vehiculo.id)"
                                           class="w-4 h-4 text-gray-900 bg-gray-100 border-gray-300 rounded focus:ring-gray-900">
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
                    <textarea rows="3" name="descripcion" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5">{{ old('descripcion', $flotilla->descripcion) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" @click="openEdit = false" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors shadow-sm">
                    Actualizar Flotilla
                </button>
            </div>
        </form>
    </div>
</div>