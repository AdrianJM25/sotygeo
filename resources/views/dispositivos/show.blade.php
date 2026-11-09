<div
    x-show="openShow"
    style="display: none;"
    class="fixed inset-0 z-[200]
           flex items-center justify-center
           p-4 text-left"
>

    {{-- FONDO --}}
    <div
        x-show="openShow"
        x-transition.opacity
        @click="openShow = false"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"
    ></div>


    {{-- MODAL --}}
    <div
        x-show="openShow"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
        class="relative z-[201]
               w-full max-w-xl
               overflow-hidden rounded-2xl
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
                    Detalles del equipo GPS
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Información registrada del dispositivo.
                </p>
            </div>


            <button
                type="button"
                @click="openShow = false"
                class="rounded-lg p-2 text-gray-500
                       transition-colors
                       hover:bg-blue-50 hover:text-[#0056b3]"
            >
                <span class="material-symbols-rounded">
                    close
                </span>
            </button>

        </div>


        <div class="space-y-5 p-6">

            {{-- ESTADO ADMINISTRATIVO --}}
            <div
                class="flex items-center justify-between
                       rounded-xl border
                       {{
                           $dispositivo->activo
                               ? 'border-emerald-200 bg-emerald-50'
                               : 'border-gray-200 bg-gray-50'
                       }}
                       p-4"
            >

                <div>

                    <span class="block text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Estado del dispositivo
                    </span>

                    <span
                        class="mt-1 block text-lg font-semibold
                               {{
                                   $dispositivo->activo
                                       ? 'text-emerald-700'
                                       : 'text-gray-600'
                               }}"
                    >
                        {{ $dispositivo->activo ? 'ACTIVO' : 'INACTIVO' }}
                    </span>

                </div>


                <span
                    class="material-symbols-rounded text-[30px]
                           {{
                               $dispositivo->activo
                                   ? 'text-emerald-600'
                                   : 'text-gray-400'
                           }}"
                >
                    {{ $dispositivo->activo ? 'check_circle' : 'block' }}
                </span>

            </div>


            {{-- MODO --}}
            <div
                class="flex items-center gap-3 rounded-xl
                       border border-[#0056b3]/15
                       bg-blue-50/50 p-4"
            >

                <span class="material-symbols-rounded text-[#0056b3]">
                    {{ $dispositivo->modo_reposo ? 'bedtime' : 'settings' }}
                </span>

                <div>

                    <span class="block text-xs font-medium text-gray-500">
                        Modo de operación configurado
                    </span>

                    <span class="text-sm font-semibold text-[#003d82]">
                        {{
                            $dispositivo->modo_reposo
                                ? 'Modo reposo'
                                : 'Modo normal'
                        }}
                    </span>

                </div>

            </div>


            {{-- INFORMACIÓN --}}
            <div class="grid grid-cols-2 gap-5">

                <div class="col-span-2">

                    <span class="block text-xs text-gray-500">
                        IMEI
                    </span>

                    <span
                        class="font-mono text-lg font-semibold
                               tracking-wider text-[#003d82]"
                    >
                        {{ $dispositivo->imei }}
                    </span>

                </div>


                <div>

                    <span class="block text-xs text-gray-500">
                        Modelo
                    </span>

                    <span class="text-sm font-medium text-gray-900">
                        {{ $dispositivo->modelo ?? 'No registrado' }}
                    </span>

                </div>


                <div>

                    <span class="block text-xs text-gray-500">
                        Número SIM
                    </span>

                    <span class="text-sm font-medium text-gray-900">
                        {{ $dispositivo->numero_sim ?? 'No registrado' }}
                    </span>

                </div>


                <div>

                    <span class="block text-xs text-gray-500">
                        Capacidad de batería
                    </span>

                    <span class="text-sm font-medium text-gray-900">
                        @if($dispositivo->capacidad_bateria_mah)
                            {{ number_format($dispositivo->capacidad_bateria_mah) }} mAh
                        @else
                            No registrada
                        @endif
                    </span>

                </div>


                <div>

                    <span class="block text-xs text-gray-500">
                        Propietario
                    </span>

                    @if($dispositivo->empresa)

                        <span class="text-sm font-medium text-gray-900">
                            {{ $dispositivo->empresa->nombre }}
                        </span>

                    @elseif($dispositivo->usuario)

                        <span class="text-sm font-medium text-gray-900">
                            {{ $dispositivo->usuario->nombre }}
                            {{ $dispositivo->usuario->apellido_paterno }}
                        </span>

                    @else

                        <span class="text-sm font-medium text-gray-900">
                            Stock SOTyTECH
                        </span>

                    @endif

                </div>

            </div>


            {{-- VEHÍCULO --}}
            <div
                class="rounded-xl border border-gray-200
                       bg-gray-50 p-4"
            >

                <span
                    class="mb-2 block text-xs font-semibold
                           uppercase tracking-wider text-gray-500"
                >
                    Vehículo vinculado
                </span>


                @if($dispositivo->vehiculo)

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-10 w-10
                                   items-center justify-center
                                   rounded-lg bg-[#0056b3]/10
                                   text-[#0056b3]"
                        >
                            <span class="material-symbols-rounded">
                                directions_car
                            </span>
                        </div>

                        <div>

                            <div class="text-sm font-semibold text-[#003d82]">
                                {{ $dispositivo->vehiculo->nombre }}
                            </div>

                            <div class="mt-0.5 text-xs text-gray-500">
                                {{ $dispositivo->vehiculo->marca }}
                                {{ $dispositivo->vehiculo->modelo }}

                                @if($dispositivo->vehiculo->placas)
                                    · Placas:
                                    {{ $dispositivo->vehiculo->placas }}
                                @endif
                            </div>

                        </div>

                    </div>

                @else

                    <div class="flex items-center gap-2 text-sm text-gray-500">

                        <span class="material-symbols-rounded text-[18px]">
                            inventory_2
                        </span>

                        El dispositivo no tiene un vehículo asignado.

                    </div>

                @endif

            </div>

        </div>


        {{-- PIE --}}
        <div
            class="flex justify-end
                   border-t border-gray-200
                   bg-gray-50/50 p-4"
        >

            <button
                type="button"
                @click="openShow = false"
                class="rounded-xl border border-gray-300
                       bg-white px-5 py-2.5
                       text-sm font-medium text-gray-700
                       transition-colors hover:bg-gray-50"
            >
                Cerrar
            </button>

        </div>

    </div>

</div>