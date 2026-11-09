<x-app-layout>

    <div class="mx-auto max-w-4xl">

        <div
            class="overflow-hidden rounded-2xl
                   border border-gray-200 bg-white shadow-sm"
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
                        Editar Flotilla
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
                           text-gray-600 hover:bg-gray-50"
                >
                    Volver
                </a>

            </div>


            <form
                action="{{ route(
                    'flotillas.update',
                    $flotilla
                ) }}"
                method="POST"
                class="space-y-6 p-6"
            >

                @csrf
                @method('PUT')


                @if($errors->any())

                    <div
                        class="rounded-xl border
                               border-red-200 bg-red-50
                               px-4 py-3"
                    >

                        <ul
                            class="list-disc space-y-1
                                   pl-5 text-sm text-red-600"
                        >

                            @foreach($errors->all() as $error)

                                <li>
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                @endif


                @role('Super Administrador')

                    <div>

                        <label
                            class="mb-2 block
                                   text-sm font-medium"
                        >
                            Empresa
                        </label>

                        <select
                            name="empresa_id"
                            required
                            class="w-full rounded-lg
                                   border-gray-300"
                        >

                            @foreach($empresas as $empresa)

                                <option
                                    value="{{ $empresa->id }}"
                                    @selected(
                                        old(
                                            'empresa_id',
                                            $flotilla->empresa_id
                                        )
                                        ==
                                        $empresa->id
                                    )
                                >

                                    {{ $empresa->nombre }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                @else

                    <input
                        type="hidden"
                        name="empresa_id"
                        value="{{ $flotilla->empresa_id }}"
                    >

                @endrole


                <div>

                    <label
                        class="mb-2 block
                               text-sm font-medium"
                    >
                        Nombre
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        required
                        value="{{
                            old(
                                'nombre',
                                $flotilla->nombre
                            )
                        }}"
                        class="w-full rounded-lg
                               border-gray-300"
                    >

                </div>


                <div>

                    <label
                        class="mb-2 block
                               text-sm font-medium"
                    >
                        Responsable
                    </label>

                    <select
                        name="user_id"
                        class="w-full rounded-lg
                               border-gray-300"
                    >

                        <option value="">
                            Sin responsable
                        </option>

                        @foreach($users as $user)

                            <option
                                value="{{ $user->id }}"
                                @selected(
                                    old(
                                        'user_id',
                                        $flotilla->user_id
                                    )
                                    ==
                                    $user->id
                                )
                            >

                                {{ $user->nombre }}
                                {{ $user->apellido_paterno }}

                                @role('Super Administrador')
                                    —
                                    {{
                                        $user->empresa?->nombre
                                        ??
                                        'Sin empresa'
                                    }}
                                @endrole

                            </option>

                        @endforeach

                    </select>

                </div>


                @php

                    $vehiculosSeleccionados =
                        old(
                            'vehiculos',
                            $flotilla->vehiculos
                                ->pluck('id')
                                ->toArray()
                        );

                @endphp


                <div>

                    <label
                        class="mb-2 block
                               text-sm font-medium"
                    >
                        Vehículos
                    </label>


                    <div
                        class="max-h-72 space-y-2
                               overflow-y-auto rounded-xl
                               border border-gray-200
                               bg-gray-50 p-3"
                    >

                        @forelse($vehiculos as $vehiculo)

                            <label
                                class="flex cursor-pointer
                                       items-center gap-3
                                       rounded-lg border
                                       border-gray-200 bg-white p-3"
                            >

                                <input
                                    type="checkbox"
                                    name="vehiculos[]"
                                    value="{{ $vehiculo->id }}"
                                    @checked(
                                        in_array(
                                            $vehiculo->id,
                                            $vehiculosSeleccionados
                                        )
                                    )
                                    class="rounded
                                           border-gray-300
                                           text-indigo-600"
                                >

                                <div>

                                    <p
                                        class="text-sm font-medium
                                               text-gray-900"
                                    >
                                        {{ $vehiculo->nombre }}
                                    </p>

                                    <p class="text-xs text-gray-500">

                                        {{
                                            $vehiculo->placas
                                            ??
                                            'Sin placas'
                                        }}

                                        @role('Super Administrador')

                                            ·
                                            {{
                                                $vehiculo
                                                    ->empresa?->nombre
                                                ??
                                                'Sin empresa'
                                            }}

                                        @endrole

                                    </p>

                                </div>

                            </label>

                        @empty

                            <p
                                class="py-6 text-center
                                       text-sm text-gray-500"
                            >
                                No hay vehículos disponibles.
                            </p>

                        @endforelse

                    </div>

                </div>


                <div>

                    <label
                        class="mb-2 block
                               text-sm font-medium"
                    >
                        Descripción
                    </label>

                    <textarea
                        name="descripcion"
                        rows="4"
                        class="w-full rounded-lg
                               border-gray-300"
                    >{{ old(
                        'descripcion',
                        $flotilla->descripcion
                    ) }}</textarea>

                </div>


                <div
                    class="flex justify-end gap-3
                           border-t border-gray-100 pt-5"
                >

                    <a
                        href="{{ route('flotillas.index') }}"
                        class="rounded-lg border
                               border-gray-300 px-5 py-2.5
                               text-sm text-gray-700"
                    >
                        Cancelar
                    </a>


                    <button
                        type="submit"
                        class="rounded-lg bg-gray-900
                               px-5 py-2.5 text-sm
                               font-medium text-white"
                    >
                        Actualizar Flotilla
                    </button>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>