<div
    x-show="openCreate"
    style="display: none;"
    class="fixed inset-0 z-[200] flex items-center justify-center p-4"
>

    {{-- FONDO --}}
    <div
        x-show="openCreate"
        x-transition.opacity
        @click="openCreate = false"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"
    ></div>


    {{-- MODAL --}}
    <div
        x-show="openCreate"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
        class="relative z-[201]
               max-h-[90vh] w-full max-w-3xl
               overflow-y-auto rounded-2xl bg-white
               shadow-2xl"
    >

        {{-- CABECERA --}}
        <div
            class="flex items-center justify-between
                   border-b border-gray-200 p-5"
        >

            <div>
                <h2
                    class="font-['Josefin_Sans']
                           text-xl font-semibold text-gray-800"
                >
                    Registrar equipo GPS
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Registra un nuevo dispositivo y, si lo deseas, asígnalo a un vehículo.
                </p>
            </div>

            <button
                type="button"
                @click="openCreate = false"
                class="rounded-lg p-2 text-gray-500
                       transition-colors
                       hover:bg-blue-50 hover:text-[#0056b3]"
            >
                <span class="material-symbols-rounded">
                    close
                </span>
            </button>

        </div>


        <form
            action="{{ route('dispositivos.store') }}"
            method="POST"
            class="p-6"
        >

            @csrf


            {{-- ========================================== --}}
            {{-- PROPIETARIO - SOLO SUPER ADMIN --}}
            {{-- ========================================== --}}

            @role('Super Administrador')

                <div
                    x-data="{
                        tipoDueno:
                            @js(
                                old('user_id')
                                    ? 'particular'
                                    : (
                                        old('empresa_id')
                                            ? 'empresa'
                                            : 'stock'
                                    )
                            )
                    }"
                    class="mb-6 rounded-xl
                           border border-[#0056b3]/15
                           bg-blue-50/50 p-4"
                >

                    <label class="block text-sm font-semibold text-gray-800">
                        Propietario del equipo
                    </label>

                    <p class="mt-1 text-xs text-gray-500">
                        Indica a quién pertenece físicamente el dispositivo.
                    </p>


                    <div class="mt-4 flex flex-wrap gap-4">

                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input
                                type="radio"
                                x-model="tipoDueno"
                                value="stock"
                                class="text-[#0056b3] focus:ring-[#0056b3]"
                            >

                            Stock SOTyTECH
                        </label>


                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input
                                type="radio"
                                x-model="tipoDueno"
                                value="empresa"
                                class="text-[#0056b3] focus:ring-[#0056b3]"
                            >

                            Empresa
                        </label>


                        <label class="flex cursor-pointer items-center gap-2 text-sm">
                            <input
                                type="radio"
                                x-model="tipoDueno"
                                value="particular"
                                class="text-[#0056b3] focus:ring-[#0056b3]"
                            >

                            Cliente Individual
                        </label>

                    </div>


                    {{-- EMPRESA --}}
                    <div
                        x-show="tipoDueno === 'empresa'"
                        class="mt-4"
                    >

                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Empresa
                        </label>

                        <select
                            name="empresa_id"
                            :disabled="tipoDueno !== 'empresa'"
                            class="w-full rounded-xl border-gray-300
                                   bg-white focus:border-[#0056b3]
                                   focus:ring-[#0056b3]"
                        >
                            <option value="">
                                Selecciona una empresa
                            </option>

                            @foreach($empresas as $emp)

                                <option
                                    value="{{ $emp->id }}"
                                    @selected(old('empresa_id') == $emp->id)
                                >
                                    {{ $emp->nombre }}
                                </option>

                            @endforeach
                        </select>

                    </div>


                    {{-- CLIENTE --}}
                    <div
                        x-show="tipoDueno === 'particular'"
                        class="mt-4"
                    >

                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Cliente Individual
                        </label>

                        <select
                            name="user_id"
                            :disabled="tipoDueno !== 'particular'"
                            class="w-full rounded-xl border-gray-300
                                   bg-white focus:border-[#0056b3]
                                   focus:ring-[#0056b3]"
                        >
                            <option value="">
                                Selecciona un cliente
                            </option>

                            @foreach($clientes as $cliente)

                                <option
                                    value="{{ $cliente->id }}"
                                    @selected(old('user_id') == $cliente->id)
                                >
                                    {{ $cliente->nombre }}
                                    {{ $cliente->apellido_paterno }}
                                </option>

                            @endforeach
                        </select>

                    </div>

                </div>

            @endrole


            {{-- ========================================== --}}
            {{-- DATOS GPS --}}
            {{-- ========================================== --}}

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                {{-- IMEI --}}
                <div>

                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        IMEI *
                    </label>

                    <input
                        type="text"
                        name="imei"
                        value="{{ old('imei') }}"
                        required
                        maxlength="20"
                        placeholder="Ej. 865123045678912"
                        class="w-full rounded-xl
                               {{ $errors->has('imei') && !old('is_edit')
                                   ? 'border-red-500'
                                   : 'border-gray-300'
                               }}
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                    @if($errors->has('imei') && !old('is_edit'))
                        <p class="mt-1 text-xs text-red-600">
                            {{ $errors->first('imei') }}
                        </p>
                    @endif

                </div>


                {{-- MODELO --}}
                <div>

                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Modelo del dispositivo
                    </label>

                    <input
                        type="text"
                        name="modelo"
                        value="{{ old('modelo') }}"
                        maxlength="255"
                        placeholder="Ej. Sinotrack ST-901"
                        class="w-full rounded-xl border-gray-300
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                </div>


                {{-- SIM --}}
                <div>

                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Número SIM
                    </label>

                    <input
                        type="text"
                        name="numero_sim"
                        value="{{ old('numero_sim') }}"
                        maxlength="20"
                        placeholder="Ej. 7771234567"
                        class="w-full rounded-xl border-gray-300
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                    <p class="mt-1 text-xs text-gray-400">
                        Número telefónico asociado a la tarjeta SIM.
                    </p>

                </div>


                {{-- BATERÍA --}}
                <div>

                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Capacidad de batería
                    </label>

                    <div class="relative">

                        <input
                            type="number"
                            name="capacidad_bateria_mah"
                            value="{{ old('capacidad_bateria_mah') }}"
                            min="0"
                            placeholder="Ej. 5000"
                            class="w-full rounded-xl border-gray-300
                                   pr-16 focus:border-[#0056b3]
                                   focus:ring-[#0056b3]"
                        >

                        <span
                            class="pointer-events-none absolute
                                   right-4 top-1/2
                                   -translate-y-1/2
                                   text-sm text-gray-400"
                        >
                            mAh
                        </span>

                    </div>

                </div>


                {{-- VEHÍCULO --}}
                <div class="md:col-span-2">

                    <label class="mb-1 block text-sm font-medium text-gray-700">
                        Vehículo asignado
                    </label>

                    <select
                        name="vehiculo_id"
                        class="w-full rounded-xl
                               {{ $errors->has('vehiculo_id') && !old('is_edit')
                                   ? 'border-red-500'
                                   : 'border-gray-300'
                               }}
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                        <option value="">
                            Sin vehículo / Mantener en stock
                        </option>

                        @foreach($vehiculosDisponibles as $vehiculo)

                            <option
                                value="{{ $vehiculo->id }}"
                                @selected(old('vehiculo_id') == $vehiculo->id)
                            >
                                {{ $vehiculo->nombre }}
                                @if($vehiculo->placas)
                                    — {{ $vehiculo->placas }}
                                @endif
                            </option>

                        @endforeach

                    </select>

                    @if($errors->has('vehiculo_id') && !old('is_edit'))

                        <p class="mt-1 text-xs text-red-600">
                            {{ $errors->first('vehiculo_id') }}
                        </p>

                    @endif

                </div>

            </div>


            {{-- ========================================== --}}
            {{-- CONFIGURACIÓN --}}
            {{-- ========================================== --}}

            <div
                class="mt-6 grid grid-cols-1 gap-4
                       rounded-xl border border-gray-200
                       bg-gray-50 p-4 md:grid-cols-2"
            >

                {{-- ACTIVO --}}
                <label class="flex cursor-pointer items-start gap-3">

                    <input type="hidden" name="activo" value="0">

                    <input
                        type="checkbox"
                        name="activo"
                        value="1"
                        @checked(old('activo', 1))
                        class="mt-0.5 rounded
                               border-gray-300 text-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                    <span>
                        <span class="block text-sm font-medium text-gray-800">
                            Dispositivo activo
                        </span>

                        <span class="block text-xs text-gray-500">
                            Permite utilizar este GPS dentro del sistema.
                        </span>
                    </span>

                </label>


                {{-- REPOSO --}}
                <label class="flex cursor-pointer items-start gap-3">

                    <input
                        type="checkbox"
                        name="modo_reposo"
                        value="1"
                        @checked(old('modo_reposo'))
                        class="mt-0.5 rounded
                               border-gray-300 text-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                    <span>
                        <span class="block text-sm font-medium text-gray-800">
                            Modo reposo
                        </span>

                        <span class="block text-xs text-gray-500">
                            Indica que el equipo utiliza ahorro de batería.
                        </span>
                    </span>

                </label>

            </div>


            {{-- BOTONES --}}
            <div
                class="mt-6 flex justify-end gap-3
                       border-t border-gray-200 pt-5"
            >

                <button
                    type="button"
                    @click="openCreate = false"
                    class="rounded-xl border border-gray-300
                           px-4 py-2.5 text-sm font-medium
                           text-gray-700 transition-colors
                           hover:bg-gray-50"
                >
                    Cancelar
                </button>


                <button
                    type="submit"
                    class="inline-flex items-center gap-2
                           rounded-xl bg-[#0056b3]
                           px-4 py-2.5 text-sm font-medium
                           text-white shadow-sm
                           transition-colors hover:bg-[#004494]"
                >
                    <span class="material-symbols-rounded text-[18px]">
                        save
                    </span>

                    Registrar GPS
                </button>

            </div>

        </form>

    </div>

</div>