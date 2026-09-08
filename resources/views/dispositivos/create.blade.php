<div x-show="openCreate" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div x-show="openCreate" x-transition.opacity @click="openCreate = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>

    <div x-show="openCreate"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
         class="relative bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-2xl overflow-hidden z-50">

        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800">Registrar Nuevo GPS</h2>
            <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg p-1.5 transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('dispositivos.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- SECCIÓN MULTI-TENANT (Exclusivo SOTyTECH) -->
            @role('Super Administrador')
                <div x-data="{ tipo_dueno: 'empresa' }" class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                    <label class="block mb-3 text-sm font-medium text-gray-900">Propietario del Equipo Físico</label>
                    <div class="flex gap-4 mb-4">
                        <label class="flex items-center text-sm cursor-pointer">
                            <input type="radio" x-model="tipo_dueno" value="empresa" class="text-indigo-600">
                            <span class="ml-2 text-gray-700">Corporativo</span>
                        </label>
                       
                    </div>
                    <div x-show="tipo_dueno === 'empresa'">
                        <select name="empresa_id" class="bg-white border text-gray-900 text-sm rounded-lg block w-full p-2.5">
                            <option value="">-- Interno SOTyTECH --</option>
                            @foreach($empresas as $emp)
                                <option value="{{ $emp->id }}" {{ old('empresa_id') == $emp->id ? 'selected' : '' }}>{{ $emp->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="tipo_dueno === 'particular'" style="display: none;">
                        <select name="user_id" class="bg-white border text-gray-900 text-sm rounded-lg block w-full p-2.5">
                            <option value="">-- Seleccionar Cliente --</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('user_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre }} {{ $cliente->apellido_paterno }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endrole

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">IMEI (Requerido) <span class="text-red-500">*</span></label>
                    <input type="text" name="imei" value="{{ old('imei') }}" required class="bg-gray-50 border {{ $errors->has('imei') && !old('is_edit') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg block w-full p-2.5" placeholder="Ej. 865123045678912">
                    @if($errors->has('imei') && !old('is_edit')) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('imei') }}</span> @endif
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Modelo del Hardware</label>
                    <input type="text" name="modelo" value="{{ old('modelo') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" placeholder="Ej. Skstar, Coban, Sinotrack">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Número SIM (Teléfono del chip)</label>
                    <input type="text" name="numero_sim" value="{{ old('numero_sim') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Capacidad de Batería (mAh)</label>
                    <input type="number" name="capacidad_bateria_mah" value="{{ old('capacidad_bateria_mah') }}" placeholder="Ej. 5000" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Vincular a Vehículo (Opcional)</label>
                    <select name="vehiculo_id" class="bg-gray-50 border {{ $errors->has('vehiculo_id') && !old('is_edit') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg block w-full p-2.5">
                        <option value="">-- Mantener en stock / Sin asignar --</option>
                        @foreach($vehiculos as $vehiculo)
                            <option value="{{ $vehiculo->id }}" {{ old('vehiculo_id') == $vehiculo->id ? 'selected' : '' }}>
                                {{ $vehiculo->nombre }} ({{ $vehiculo->placas ?? 'S/P' }})
                            </option>
                        @endforeach
                    </select>
                    @if($errors->has('vehiculo_id') && !old('is_edit')) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('vehiculo_id') }}</span> @endif
                </div>
            </div>

            <div class="flex items-center pt-2">
                <input type="checkbox" name="modo_reposo" id="modo_reposo_create" value="1" {{ old('modo_reposo') ? 'checked' : '' }} class="w-4 h-4 text-gray-900 bg-gray-100 border-gray-300 rounded focus:ring-gray-900">
                <label for="modo_reposo_create" class="ml-2 text-sm font-medium text-gray-900">El dispositivo está en modo reposo (Ahorro de batería)</label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" @click="openCreate = false" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors shadow-sm">
                    Guardar GPS
                </button>
            </div>
        </form>
    </div>
</div>