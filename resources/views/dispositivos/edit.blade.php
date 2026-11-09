@php
    if ($dispositivo->empresa_id) {
        $propietarioActual = 'empresa:' . $dispositivo->empresa_id;
    } elseif ($dispositivo->user_id) {
        $propietarioActual = 'cliente:' . $dispositivo->user_id;
    } else {
        $propietarioActual = 'stock';
    }

    if (old('is_edit') == $dispositivo->id) {
        $propietarioActual = old(
            'propietario',
            $propietarioActual
        );
    }
@endphp


<div
    x-show="openEdit"
    style="display: none;"
    class="fixed inset-0 z-[200]
           flex items-center justify-center
           p-4 text-left"
>

    {{-- FONDO --}}
    <div
        x-show="openEdit"
        x-transition.opacity
        @click="openEdit = false"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"
    ></div>


    {{-- MODAL --}}
    <div
        x-show="openEdit"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
        class="relative z-[201]
               max-h-[90vh] w-full max-w-3xl
               overflow-y-auto rounded-2xl
               bg-white shadow-2xl"
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
                    Editar equipo GPS
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Modifica la información y configuración del dispositivo.
                </p>

            </div>


            <button
                type="button"
                @click="openEdit = false"
                class="rounded-lg p-2 text-gray-500
                       transition-colors
                       hover:bg-blue-50 hover:text-[#0056b3]"
            >
                <span class="material-symbols-rounded">
                    close
                </span>
            </button>

        </div>


        {{-- FORMULARIO --}}
        <form
            action="{{ route('dispositivos.update', $dispositivo) }}"
            method="POST"
            class="p-6"
        >

            @csrf
            @method('PUT')


            <input
                type="hidden"
                name="is_edit"
                value="{{ $dispositivo->id }}"
            >


            {{-- ================================================= --}}
            {{-- PROPIETARIO --}}
            {{-- ================================================= --}}

            @role('Super Administrador')

                <div
                    class="mb-6 rounded-xl
                           border border-[#0056b3]/15
                           bg-blue-50/50 p-4"
                >

                    <label
                        class="block text-sm
                               font-semibold text-gray-800"
                    >
                        Propietario del equipo
                    </label>

                    <p class="mt-1 text-xs text-gray-500">
                        Selecciona a quién pertenece físicamente el dispositivo GPS.
                    </p>


                    <select
                        name="propietario"
                        class="mt-4 w-full rounded-xl
                               border-gray-300 bg-white
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                        {{-- STOCK --}}
                        <option
                            value="stock"
                            @selected(
                                $propietarioActual === 'stock'
                            )
                        >
                            Stock SOTyTECH
                        </option>


                        {{-- EMPRESAS --}}
                        <optgroup label="Empresas">

                            @foreach($empresas as $emp)

                                <option
                                    value="empresa:{{ $emp->id }}"
                                    @selected(
                                        $propietarioActual ===
                                        'empresa:' . $emp->id
                                    )
                                >
                                    {{ $emp->nombre }}
                                </option>

                            @endforeach

                        </optgroup>


                        {{-- CLIENTES --}}
                        <optgroup label="Clientes Individuales">

                            @foreach($clientes as $cliente)

                                <option
                                    value="cliente:{{ $cliente->id }}"
                                    @selected(
                                        $propietarioActual ===
                                        'cliente:' . $cliente->id
                                    )
                                >
                                    {{ $cliente->nombre }}
                                    {{ $cliente->apellido_paterno }}
                                </option>

                            @endforeach

                        </optgroup>

                    </select>


                    @if(
                        $errors->has('propietario')
                        &&
                        old('is_edit') == $dispositivo->id
                    )

                        <p class="mt-1 text-xs text-red-600">
                            {{ $errors->first('propietario') }}
                        </p>

                    @endif

                </div>

            @endrole


            {{-- ================================================= --}}
            {{-- DATOS DEL GPS --}}
            {{-- ================================================= --}}

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                {{-- IMEI --}}
                <div>

                    <label
                        class="mb-1 block
                               text-sm font-medium text-gray-700"
                    >
                        IMEI *
                    </label>


                    <input
                        type="text"
                        name="imei"
                        maxlength="20"
                        required
                        value="{{
                            old('is_edit') == $dispositivo->id
                                ? old('imei')
                                : $dispositivo->imei
                        }}"
                        class="w-full rounded-xl
                               {{
                                   $errors->has('imei')
                                   &&
                                   old('is_edit') == $dispositivo->id
                                       ? 'border-red-500'
                                       : 'border-gray-300'
                               }}
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >


                    @if(
                        $errors->has('imei')
                        &&
                        old('is_edit') == $dispositivo->id
                    )

                        <p class="mt-1 text-xs text-red-600">
                            {{ $errors->first('imei') }}
                        </p>

                    @endif

                </div>


                {{-- MODELO --}}
                <div>

                    <label
                        class="mb-1 block
                               text-sm font-medium text-gray-700"
                    >
                        Modelo del dispositivo
                    </label>


                    <input
                        type="text"
                        name="modelo"
                        maxlength="255"
                        value="{{
                            old('is_edit') == $dispositivo->id
                                ? old('modelo')
                                : $dispositivo->modelo
                        }}"
                        class="w-full rounded-xl
                               border-gray-300
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                </div>


                {{-- SIM --}}
                <div>

                    <label
                        class="mb-1 block
                               text-sm font-medium text-gray-700"
                    >
                        Número SIM
                    </label>


                    <input
                        type="text"
                        name="numero_sim"
                        maxlength="20"
                        value="{{
                            old('is_edit') == $dispositivo->id
                                ? old('numero_sim')
                                : $dispositivo->numero_sim
                        }}"
                        class="w-full rounded-xl
                               border-gray-300
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                </div>


                {{-- BATERÍA --}}
                <div>

                    <label
                        class="mb-1 block
                               text-sm font-medium text-gray-700"
                    >
                        Capacidad de batería
                    </label>


                    <div class="relative">

                        <input
                            type="number"
                            name="capacidad_bateria_mah"
                            min="0"
                            value="{{
                                old('is_edit') == $dispositivo->id
                                    ? old('capacidad_bateria_mah')
                                    : $dispositivo->capacidad_bateria_mah
                            }}"
                            class="w-full rounded-xl
                                   border-gray-300 pr-16
                                   focus:border-[#0056b3]
                                   focus:ring-[#0056b3]"
                        >


                        <span
                            class="pointer-events-none
                                   absolute right-4 top-1/2
                                   -translate-y-1/2
                                   text-sm text-gray-400"
                        >
                            mAh
                        </span>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- VEHÍCULO --}}
                {{-- ================================================= --}}

                <div class="md:col-span-2">

                    <label
                        class="mb-1 block
                               text-sm font-medium text-gray-700"
                    >
                        Vehículo asignado
                    </label>


                    <select
                        name="vehiculo_id"
                        class="w-full rounded-xl
                               {{
                                   $errors->has('vehiculo_id')
                                   &&
                                   old('is_edit') == $dispositivo->id
                                       ? 'border-red-500'
                                       : 'border-gray-300'
                               }}
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                        <option value="">
                            Sin vehículo / Enviar a stock
                        </option>


                        @foreach($vehiculos as $vehiculo)

                            @if(
                                !$vehiculo->dispositivo
                                ||
                                $vehiculo->dispositivo->id == $dispositivo->id
                            )

                                <option
                                    value="{{ $vehiculo->id }}"
                                    @selected(
                                        (
                                            old('is_edit') == $dispositivo->id
                                                ? old('vehiculo_id')
                                                : $dispositivo->vehiculo_id
                                        ) == $vehiculo->id
                                    )
                                >

                                    {{ $vehiculo->nombre }}

                                    @if($vehiculo->placas)
                                        — {{ $vehiculo->placas }}
                                    @endif

                                    @if(
                                        $vehiculo->id ==
                                        $dispositivo->vehiculo_id
                                    )
                                        — Actual
                                    @endif

                                </option>

                            @endif

                        @endforeach

                    </select>


                    @if(
                        $errors->has('vehiculo_id')
                        &&
                        old('is_edit') == $dispositivo->id
                    )

                        <p class="mt-1 text-xs text-red-600">
                            {{ $errors->first('vehiculo_id') }}
                        </p>

                    @endif

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- CONFIGURACIÓN --}}
            {{-- ================================================= --}}

            <div
                class="mt-6 grid grid-cols-1 gap-4
                       rounded-xl border border-gray-200
                       bg-gray-50 p-4 md:grid-cols-2"
            >

                {{-- ACTIVO --}}
                <label
                    class="flex cursor-pointer
                           items-start gap-3"
                >

                    <input
                        type="hidden"
                        name="activo"
                        value="0"
                    >


                    <input
                        type="checkbox"
                        name="activo"
                        value="1"
                        @checked(
                            old('is_edit') == $dispositivo->id
                                ? old('activo')
                                : $dispositivo->activo
                        )
                        class="mt-0.5 rounded
                               border-gray-300 text-[#0056b3]
                               focus:ring-[#0056b3]"
                    >


                    <span>

                        <span
                            class="block text-sm
                                   font-medium text-gray-800"
                        >
                            Dispositivo activo
                        </span>

                        <span
                            class="block text-xs text-gray-500"
                        >
                            Habilita el uso del GPS en el sistema.
                        </span>

                    </span>

                </label>


                {{-- REPOSO --}}
                <label
                    class="flex cursor-pointer
                           items-start gap-3"
                >

                    <input
                        type="hidden"
                        name="modo_reposo"
                        value="0"
                    >


                    <input
                        type="checkbox"
                        name="modo_reposo"
                        value="1"
                        @checked(
                            old('is_edit') == $dispositivo->id
                                ? old('modo_reposo')
                                : $dispositivo->modo_reposo
                        )
                        class="mt-0.5 rounded
                               border-gray-300 text-[#0056b3]
                               focus:ring-[#0056b3]"
                    >


                    <span>

                        <span
                            class="block text-sm
                                   font-medium text-gray-800"
                        >
                            Modo reposo
                        </span>

                        <span
                            class="block text-xs text-gray-500"
                        >
                            Configuración de ahorro de batería.
                        </span>

                    </span>

                </label>

            </div>


            {{-- ================================================= --}}
            {{-- BOTONES --}}
            {{-- ================================================= --}}

            <div
                class="mt-6 flex justify-end gap-3
                       border-t border-gray-200 pt-5"
            >

                <button
                    type="button"
                    @click="openEdit = false"
                    class="rounded-xl
                           border border-gray-300
                           px-4 py-2.5
                           text-sm font-medium
                           text-gray-700
                           transition-colors
                           hover:bg-gray-50"
                >
                    Cancelar
                </button>


                <button
                    type="submit"
                    class="inline-flex items-center gap-2
                           rounded-xl bg-[#0056b3]
                           px-4 py-2.5
                           text-sm font-medium text-white
                           transition-colors
                           hover:bg-[#004494]"
                >

                    <span
                        class="material-symbols-rounded
                               text-[18px]"
                    >
                        save
                    </span>

                    Guardar cambios

                </button>

            </div>

        </form>

    </div>

</div>