<!-- ================= MODAL DE EDICIÓN ================= -->

<div
    id="vehiculo-edit-{{ $vehiculo->id }}"
    class="vehiculo-modal fixed inset-0 z-[120] hidden items-center justify-center p-4 text-left"
    aria-hidden="true"
>
    <div
        class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"
        data-close-vehiculo-modal
    ></div>

    <div class="relative z-10 bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-3xl overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800">Editar Vehículo</h2>

            <button
                type="button"
                data-close-vehiculo-modal
                class="text-gray-400 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg p-1.5 transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form
            action="{{ route('vehiculos.update', $vehiculo) }}"
            method="POST"
            enctype="multipart/form-data"
            class="p-6 space-y-6 max-h-[78vh] overflow-y-auto"
        >
            @csrf
            @method('PUT')

            <input type="hidden" name="is_edit" value="{{ $vehiculo->id }}">

            @role('Super Administrador')
                @php
                    $tipoDuenoEdit = old(
                        'user_id',
                        $vehiculo->user_id
                    )
                        ? 'particular'
                        : 'empresa';
                @endphp

                <div
                    class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100"
                    data-owner-group
                >
                    <label class="block mb-3 text-sm font-medium text-gray-900">
                        Propietario del Vehículo
                    </label>

                    <div class="flex flex-wrap gap-4 mb-4">
                        <label class="flex items-center text-sm cursor-pointer">
                            <input
                                type="radio"
                                name="tipo_dueno_ui_edit_{{ $vehiculo->id }}"
                                value="empresa"
                                data-owner-type
                                {{ $tipoDuenoEdit === 'empresa' ? 'checked' : '' }}
                                class="text-indigo-600"
                            >
                            <span class="ml-2 text-gray-700">Corporativo / SOTyTECH</span>
                        </label>

                        <label class="flex items-center text-sm cursor-pointer">
                            <input
                                type="radio"
                                name="tipo_dueno_ui_edit_{{ $vehiculo->id }}"
                                value="particular"
                                data-owner-type
                                {{ $tipoDuenoEdit === 'particular' ? 'checked' : '' }}
                                class="text-indigo-600"
                            >
                            <span class="ml-2 text-gray-700">Particular</span>
                        </label>
                    </div>

                    <div data-owner-section="empresa">
                        <select
                            name="empresa_id"
                            data-owner-select="empresa"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                        >
                            <option value="">-- Uso Interno SOTyTECH --</option>

                            @foreach($empresas as $emp)
                                <option
                                    value="{{ $emp->id }}"
                                    {{ old('empresa_id', $vehiculo->empresa_id) == $emp->id ? 'selected' : '' }}
                                >
                                    {{ $emp->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div
                        data-owner-section="particular"
                        class="hidden"
                    >
                        <select
                            name="user_id"
                            data-owner-select="particular"
                            class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                        >
                            <option value="">-- Seleccionar Cliente --</option>

                            @foreach($clientes as $cliente)
                                <option
                                    value="{{ $cliente->id }}"
                                    {{ old('user_id', $vehiculo->user_id) == $cliente->id ? 'selected' : '' }}
                                >
                                    {{ $cliente->nombre }} {{ $cliente->apellido_paterno }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endrole


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">
                        Nombre o Alias
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        value="{{ old('nombre', $vehiculo->nombre) }}"
                        required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                    >
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">
                        Tipo de Vehículo
                        <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="tipo_vehiculo"
                        required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                    >
                        <option value="automovil" {{ old('tipo_vehiculo', $vehiculo->tipo_vehiculo) === 'automovil' ? 'selected' : '' }}>Automóvil</option>
                        <option value="motocicleta" {{ old('tipo_vehiculo', $vehiculo->tipo_vehiculo) === 'motocicleta' ? 'selected' : '' }}>Motocicleta</option>
                        <option value="camioneta" {{ old('tipo_vehiculo', $vehiculo->tipo_vehiculo) === 'camioneta' ? 'selected' : '' }}>Camioneta</option>
                        <option value="camion" {{ old('tipo_vehiculo', $vehiculo->tipo_vehiculo) === 'camion' ? 'selected' : '' }}>Camión Pesado</option>
                        <option value="caja_seca" {{ old('tipo_vehiculo', $vehiculo->tipo_vehiculo) === 'caja_seca' ? 'selected' : '' }}>Caja Seca / Remolque</option>
                    </select>
                </div>

                @unless(auth()->user()->hasRole('Cliente Individual'))
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900">
                            Flotilla
                        </label>

                        <select
                            name="flotilla_id"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                        >
                            <option value="">-- Sin asignar --</option>

                            @foreach($flotillas as $flota)
                                <option
                                    value="{{ $flota->id }}"
                                    {{ old('flotilla_id', $vehiculo->flotilla_id) == $flota->id ? 'selected' : '' }}
                                >
                                    {{ $flota->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endunless
            </div>


            <div class="pt-4 border-t border-gray-100">
                <h3 class="text-sm font-bold text-gray-700 mb-4">
                    Cómo se verá en el mapa
                </h3>

                @php
                    $iconosEdit = \App\Models\Vehiculo::iconosDisponibles();
                    $iconoEdit = old('icono', $vehiculo->icono);
                @endphp

                @if(count($iconosEdit))
                    <div class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-7 gap-2 mb-4">
                        @foreach($iconosEdit as $icono)
                            <label class="cursor-pointer">
                                <input
                                    type="radio"
                                    name="icono"
                                    value="{{ $icono['path'] }}"
                                    class="peer sr-only"
                                    {{ $iconoEdit === $icono['path'] ? 'checked' : '' }}
                                >

                                <div class="h-16 rounded-xl border border-gray-200 bg-white flex flex-col items-center justify-center gap-1 peer-checked:border-blue-600 peer-checked:ring-2 peer-checked:ring-blue-100 hover:bg-gray-50">
                                    <img
                                        src="{{ $icono['url'] }}"
                                        alt="{{ $icono['label'] }}"
                                        class="w-7 h-7 object-contain"
                                    >
                                    <span class="text-[9px] text-gray-500 truncate max-w-[70px]">
                                        {{ $icono['label'] }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-xs font-medium text-gray-700">
                            Color del marcador
                        </label>

                        <input
                            type="color"
                            name="color_icono"
                            value="{{ old('color_icono', $vehiculo->color_icono ?? '#111827') }}"
                            class="w-full h-10 rounded-lg border border-gray-300 bg-white p-1"
                        >
                    </div>

                    <div>
                        <label class="block mb-2 text-xs font-medium text-gray-700">
                            Reemplazar con ícono personalizado
                        </label>

                        <input
                            type="file"
                            name="icono_file"
                            accept=".png,.jpg,.jpeg,.webp,.svg"
                            class="block w-full text-xs text-gray-600 border border-gray-300 rounded-lg p-2"
                        >
                    </div>
                </div>
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Placas</label>
                    <input type="text" name="placas" value="{{ old('placas', $vehiculo->placas) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 uppercase">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Año</label>
                    <input type="number" name="anio" min="1900" max="2100" value="{{ old('anio', $vehiculo->anio) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>

                <div class="md:col-span-2">
                    <label class="block mb-2 text-sm font-medium text-gray-900">VIN</label>
                    <input type="text" name="vin" value="{{ old('vin', $vehiculo->vin) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 uppercase">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Marca</label>
                    <input type="text" name="marca" value="{{ old('marca', $vehiculo->marca) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Modelo</label>
                    <input type="text" name="modelo" value="{{ old('modelo', $vehiculo->modelo) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Color</label>
                    <input type="text" name="color" value="{{ old('color', $vehiculo->color) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Rendimiento km/L</label>
                    <input type="number" step="0.01" min="0" name="rendimiento_km_litro" value="{{ old('rendimiento_km_litro', $vehiculo->rendimiento_km_litro) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Vencimiento del seguro</label>
                    <input
                        type="date"
                        name="vencimiento_seguro"
                        value="{{ old('vencimiento_seguro', optional($vehiculo->vencimiento_seguro)->format('Y-m-d')) }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                    >
                </div>
            </div>


            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                <button
                    type="button"
                    data-close-vehiculo-modal
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                >
                    Cancelar
                </button>

                <button
                    type="submit"
                    class="px-5 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800"
                >
                    Actualizar Vehículo
                </button>
            </div>
        </form>
    </div>
</div>
