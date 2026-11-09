<x-app-layout>

    <x-slot name="titulo">
        Historial de Rutas
    </x-slot>

    <div class="mx-auto max-w-[1500px] space-y-5 pb-8 font-['Ubuntu']">

        {{-- ====================================================== --}}
        {{-- HEADER --}}
        {{-- ====================================================== --}}

        <div
            class="flex flex-col gap-4 rounded-2xl
                   bg-[#0056b3] p-5 shadow-sm
                   sm:flex-row sm:items-center
                   sm:justify-between"
        >

            <div class="flex items-center gap-4">

                <div
                    class="flex h-12 w-12 items-center
                           justify-center rounded-xl
                           bg-white/15 text-white"
                >

                    <span class="material-symbols-rounded text-[27px]">
                        route
                    </span>

                </div>


                <div>

                    <h1
                        class="font-['Josefin_Sans']
                               text-2xl font-semibold
                               text-white"
                    >
                        Historial de Rutas
                    </h1>

                    <p class="text-sm text-blue-100">
                        Consulta los recorridos históricos de los vehículos a los que tienes acceso.
                    </p>

                </div>

            </div>


            <div
                class="inline-flex items-center gap-2
                       rounded-xl bg-white/10
                       px-4 py-2 text-sm
                       text-blue-100"
            >

                <span class="material-symbols-rounded text-[19px]">
                    directions_car
                </span>

                {{ $vehiculos->count() }}

                {{ $vehiculos->count() === 1 ? 'vehículo disponible' : 'vehículos disponibles' }}

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- CONTENIDO --}}
        {{-- ====================================================== --}}

        <div
            class="overflow-hidden rounded-2xl
                   border border-[#0056b3]/15
                   bg-white shadow-sm"
        >

            <div
                class="border-b border-gray-100
                       bg-blue-50/50 px-5 py-4"
            >

                <div class="flex items-center gap-2">

                    <span
                        class="material-symbols-rounded
                               text-[#0056b3]"
                    >
                        history
                    </span>


                    <div>

                        <h2
                            class="font-['Josefin_Sans']
                                   font-semibold text-[#003d82]"
                        >
                            Seleccionar vehículo
                        </h2>

                        <p class="text-xs text-gray-500">
                            Selecciona una unidad para abrir su reproductor de historial.
                        </p>

                    </div>

                </div>

            </div>


            {{-- ====================================================== --}}
            {{-- SIN VEHÍCULOS --}}
            {{-- ====================================================== --}}

            @if($vehiculos->isEmpty())

                <div class="px-6 py-16 text-center">

                    <div
                        class="mx-auto flex h-16 w-16
                               items-center justify-center
                               rounded-2xl bg-gray-100
                               text-gray-400"
                    >

                        <span class="material-symbols-rounded text-4xl">
                            no_transfer
                        </span>

                    </div>


                    <h3
                        class="mt-4 font-['Josefin_Sans']
                               text-lg font-semibold
                               text-gray-700"
                    >
                        No hay vehículos disponibles
                    </h3>


                    <p
                        class="mx-auto mt-1
                               max-w-lg text-sm
                               text-gray-500"
                    >
                        Actualmente no tienes vehículos asignados o autorizados
                        para consultar su historial de rutas.
                    </p>

                </div>


            @else

                {{-- ====================================================== --}}
                {{-- TARJETAS DE VEHÍCULOS --}}
                {{-- ====================================================== --}}

                <div
                    class="grid grid-cols-1 gap-4 p-5
                           md:grid-cols-2
                           xl:grid-cols-3"
                >

                    @foreach($vehiculos as $vehiculo)

                        <div
                            class="group rounded-2xl
                                   border border-gray-200
                                   bg-white p-5
                                   transition-all
                                   hover:-translate-y-0.5
                                   hover:border-blue-200
                                   hover:shadow-md"
                        >

                            {{-- CABECERA TARJETA --}}

                            <div
                                class="flex items-start
                                       justify-between gap-4"
                            >

                                <div
                                    class="flex h-12 w-12
                                           shrink-0 items-center
                                           justify-center
                                           rounded-xl bg-blue-50
                                           text-[#0056b3]
                                           transition
                                           group-hover:bg-[#0056b3]
                                           group-hover:text-white"
                                >

                                    <span class="material-symbols-rounded">
                                        directions_car
                                    </span>

                                </div>


                                <a
                                    href="{{ route('vehiculos.ruta', $vehiculo) }}"

                                    class="inline-flex
                                           items-center gap-1.5
                                           rounded-xl
                                           bg-[#0056b3]
                                           px-3.5 py-2
                                           text-xs font-medium
                                           text-white
                                           transition
                                           hover:bg-[#004494]"
                                >

                                    <span
                                        class="material-symbols-rounded
                                               text-[18px]"
                                    >
                                        play_circle
                                    </span>

                                    Consultar

                                </a>

                            </div>


                            {{-- INFORMACIÓN --}}

                            <div class="mt-5">

                                <h3
                                    class="font-['Josefin_Sans']
                                           text-lg font-semibold
                                           text-[#003d82]"
                                >
                                    {{ $vehiculo->nombre }}
                                </h3>


                                <div
                                    class="mt-3 space-y-2
                                           text-sm text-gray-500"
                                >

                                    {{-- PLACAS --}}

                                    <div
                                        class="flex items-center gap-2"
                                    >

                                        <span
                                            class="material-symbols-rounded
                                                   text-[17px]
                                                   text-gray-400"
                                        >
                                            pin
                                        </span>

                                        <span>
                                            Placas:
                                        </span>

                                        <span
                                            class="font-medium text-gray-700"
                                        >
                                            {{ $vehiculo->placas ?? 'Sin placas' }}
                                        </span>

                                    </div>


                                    {{-- MARCA / MODELO --}}

                                    @if(
                                        $vehiculo->marca
                                        ||
                                        $vehiculo->modelo
                                    )

                                        <div
                                            class="flex items-center gap-2"
                                        >

                                            <span
                                                class="material-symbols-rounded
                                                       text-[17px]
                                                       text-gray-400"
                                            >
                                                minor_crash
                                            </span>

                                            <span>
                                                Modelo:
                                            </span>

                                            <span
                                                class="font-medium
                                                       text-gray-700"
                                            >
                                                {{ trim(
                                                    ($vehiculo->marca ?? '')
                                                    . ' '
                                                    . ($vehiculo->modelo ?? '')
                                                ) }}
                                            </span>

                                        </div>

                                    @endif


                                    {{-- FLOTA --}}

                                    @if($vehiculo->flotilla)

                                        <div
                                            class="flex items-center gap-2"
                                        >

                                            <span
                                                class="material-symbols-rounded
                                                       text-[17px]
                                                       text-gray-400"
                                            >
                                                local_shipping
                                            </span>

                                            <span>
                                                Flotilla:
                                            </span>

                                            <span
                                                class="font-medium
                                                       text-gray-700"
                                            >
                                                {{ $vehiculo->flotilla->nombre }}
                                            </span>

                                        </div>

                                    @endif

                                </div>

                            </div>


                            {{-- PIE --}}

                            <div
                                class="mt-5 border-t
                                       border-gray-100 pt-4"
                            >

                                <a
                                    href="{{ route('vehiculos.ruta', $vehiculo) }}"

                                    class="flex w-full
                                           items-center
                                           justify-between
                                           rounded-xl
                                           bg-gray-50
                                           px-4 py-3
                                           text-sm font-medium
                                           text-gray-600
                                           transition
                                           hover:bg-blue-50
                                           hover:text-[#0056b3]"
                                >

                                    <span>
                                        Abrir reproductor de ruta
                                    </span>

                                    <span
                                        class="material-symbols-rounded
                                               text-[19px]"
                                    >
                                        arrow_forward
                                    </span>

                                </a>

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </div>

    </div>

</x-app-layout>