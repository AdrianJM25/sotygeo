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
         class="relative bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-3xl overflow-hidden z-50">

        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800">Registrar Nuevo Vehículo</h2>
            <button type="button" @click="openCreate = false" class="text-gray-400 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg p-1.5 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <form action="{{ route('vehiculos.store') }}" method="POST" class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
            @csrf

            @role('Super Administrador')
                <div x-data="{ tipo_dueno: 'empresa' }" class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100">
                    <label class="block mb-3 text-sm font-medium text-gray-900">¿A quién le pertenece este vehículo?</label>
                    <div class="flex gap-4 mb-4">
                        <label class="flex items-center text-sm cursor-pointer">
                            <input type="radio" x-model="tipo_dueno" value="empresa" class="text-indigo-600">
                            <span class="ml-2 text-gray-700">Corporativo (Empresa)</span>
                        </label>
                        <label class="flex items-center text-sm cursor-pointer">
                            <input type="radio" x-model="tipo_dueno" value="particular" class="text-indigo-600">
                            <span class="ml-2 text-gray-700">Particular</span>
                        </label>
                    </div>
                    <div x-show="tipo_dueno === 'empresa'">
                        <select name="empresa_id" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                            <option value="">-- Uso Interno SOTyTECH --</option>
                            @foreach($empresas as $emp)
                                <option value="{{ $emp->id }}" {{ old('empresa_id') == $emp->id ? 'selected' : '' }}>{{ $emp->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="tipo_dueno === 'particular'" style="display: none;">
                        <select name="user_id" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                            <option value="">-- Seleccionar Cliente Particular --</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}" {{ old('user_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre }} {{ $cliente->apellido_paterno }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endrole

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">Nombre o Alias (Identificador) <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Unidad 01 - Reparto Sur" required class="bg-gray-50 border {{ $errors->has('nombre') && !old('is_edit') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 block w-full p-2.5">
                    @if($errors->has('nombre') && !old('is_edit')) <span class="text-xs text-red-600 mt-1 block">{{ $errors->first('nombre') }}</span> @endif
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Tipo de Vehículo <span class="text-red-500">*</span></label>
                    <select name="tipo_vehiculo" required class="bg-gray-50 border {{ $errors->has('tipo_vehiculo') && !old('is_edit') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 block w-full p-2.5">
                        <option value="automovil" {{ old('tipo_vehiculo') == 'automovil' ? 'selected' : '' }}>Automóvil</option>
                        <option value="motocicleta" {{ old('tipo_vehiculo') == 'motocicleta' ? 'selected' : '' }}>Motocicleta</option>
                        <option value="camioneta" {{ old('tipo_vehiculo') == 'camioneta' ? 'selected' : '' }}>Camioneta</option>
                        <option value="camion" {{ old('tipo_vehiculo') == 'camion' ? 'selected' : '' }}>Camión Pesado</option>
                        <option value="caja_seca" {{ old('tipo_vehiculo') == 'caja_seca' ? 'selected' : '' }}>Caja Seca / Remolque</option>
                    </select>
                </div>

                @unless(auth()->user()->hasRole('Cliente Individual'))
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Flotilla (Grupo)</label>
                    <select name="flotilla_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-900 block w-full p-2.5">
                        <option value="">-- Sin asignar (Independiente) --</option>
                        @foreach($flotillas as $flota)
                            <option value="{{ $flota->id }}" {{ old('flotilla_id') == $flota->id ? 'selected' : '' }}>{{ $flota->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                @endunless
            </div>

            <!-- ================= PERSONALIZACIÓN EN EL MAPA ================= -->
            <div class="pt-2 border-t border-gray-100">
                <h3 class="text-sm font-bold text-gray-700 mb-4 mt-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7l6-2.5 5.447 2.724A1 1 0 0121 8.618v10.764a1 1 0 01-1.447.894L15 17l-6 2.5z" /></svg>
                    Cómo se verá en el mapa
                </h3>
                <x-selector-icono-vehiculo :seleccionado="old('icono', 'sedan')" :color-seleccionado="old('color_icono', '#111827')" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-gray-100 mt-2">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Placas</label>
                    <input type="text" name="placas" value="{{ old('placas') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 uppercase">
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">VIN (Número de Serie) <span class="text-xs text-gray-400 font-normal ml-1">Ideal para inventario</span></label>
                    <input type="text" name="vin" value="{{ old('vin') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 uppercase">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Marca</label>
                    <input type="text" name="marca" value="{{ old('marca') }}" placeholder="Ej. Nissan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Modelo</label>
                    <input type="text" name="modelo" value="{{ old('modelo') }}" placeholder="Ej. Versa" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Año</label>
                    <input type="number" name="anio" value="{{ old('anio') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Color</label>
                    <input type="text" name="color" value="{{ old('color') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <button type="button" @click="openCreate = false" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800">Guardar Vehículo</button>
            </div>
        </form>
    </div>
</div>