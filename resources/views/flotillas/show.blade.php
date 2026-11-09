<x-app-layout>

    <div class="mx-auto max-w-4xl">

        <div
            class="overflow-hidden rounded-2xl
                   border border-gray-200
                   bg-white shadow-sm"
        >

            <div
                class="flex items-center justify-between
                       border-b border-gray-100
                       bg-gray-50/50 p-5"
            >

                <div>

                    <h1
                        class="text-xl font-bold
                               text-gray-900"
                    >
                        Detalles de la Flotilla
                    </h1>

                    <p class="mt-1 text-sm text-gray-500">
                        {{ $flotilla->nombre }}
                    </p>

                </div>


                <a
                    href="{{ route('flotillas.index') }}"
                    class="rounded-lg border
                           border-gray-300 bg-white
                           px-4 py-2 text-sm
                           text-gray-600"
                >
                    Volver
                </a>

            </div>


            <div class="space-y-6 p-6">

                <div
                    class="grid grid-cols-1 gap-5
                           rounded-xl border border-gray-100
                           bg-gray-50 p-5 sm:grid-cols-2"
                >

                    <div>

                        <p class="text-xs text-gray-500">
                            Nombre
                        </p>

                        <p
                            class="mt-1 font-semibold
                                   text-gray-900"
                        >
                            {{ $flotilla->nombre }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs text-gray-500">
                            Empresa
                        </p>

                        <p
                            class="mt-1 font-semibold
                                   text-gray-900"
                        >
                            {{
                                $flotilla->empresa?->nombre
                                ??
                                'Sin empresa'
                            }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs text-gray-500">
                            Responsable
                        </p>

                        <p
                            class="mt-1 font-semibold
                                   text-gray-900"
                        >

                            @if($flotilla->usuario)

                                {{ $flotilla->usuario->nombre }}

                                {{ $flotilla->usuario->apellido_paterno }}

                            @else

                                Sin asignar

                            @endif

                        </p>

                    </div>


                    <div>

                        <p class="text-xs text-gray-500">
                            Vehículos
                        </p>

                        <p
                            class="mt-1 font-semibold
                                   text-gray-900"
                        >
                            {{ $flotilla->vehiculos->count() }}
                            unidad(es)
                        </p>

                    </div>


                    <div class="sm:col-span-2">

                        <p class="text-xs text-gray-500">
                            Descripción
                        </p>

                        <p class="mt-1 text-sm text-gray-700">

                            {{
                                $flotilla->descripcion
                                ??
                                'Sin descripción'
                            }}

                        </p>

                    </div>

                </div>


                <div>

                    <h2
                        class="mb-3 font-semibold
                               text-gray-900"
                    >
                        Vehículos asignados
                    </h2>


                    <div
                        class="overflow-hidden rounded-xl
                               border border-gray-200"
                    >

                        @forelse($flotilla->vehiculos as $vehiculo)

                            <div
                                class="flex items-center
                                       justify-between border-b
                                       border-gray-100
                                       px-4 py-3 last:border-0"
                            >

                                <div>

                                    <p
                                        class="text-sm font-medium
                                               text-gray-900"
                                    >
                                        {{ $vehiculo->nombre }}
                                    </p>

                                    <p
                                        class="text-xs
                                               text-gray-500"
                                    >
                                        {{
                                            $vehiculo->marca
                                            ??
                                            ''
                                        }}

                                        {{
                                            $vehiculo->modelo
                                            ??
                                            ''
                                        }}

                                        ·

                                        {{
                                            $vehiculo->placas
                                            ??
                                            'Sin placas'
                                        }}
                                    </p>

                                </div>

                            </div>

                        @empty

                            <div
                                class="px-4 py-8
                                       text-center
                                       text-sm text-gray-500"
                            >
                                Esta flotilla todavía
                                no tiene vehículos asignados.
                            </div>

                        @endforelse

                    </div>

                </div>


                <div class="flex justify-end">

                    <a
                        href="{{ route(
                            'flotillas.edit',
                            $flotilla
                        ) }}"
                        class="rounded-lg bg-gray-900
                               px-5 py-2.5 text-sm
                               font-medium text-white"
                    >
                        Editar Flotilla
                    </a>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>