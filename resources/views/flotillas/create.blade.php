<x-app-layout>

    <div class="mx-auto max-w-4xl">

        <div
            class="overflow-hidden rounded-2xl
                   border border-gray-200 bg-white shadow-sm"
        >

            {{-- HEADER --}}
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
                        Registrar Nueva Flotilla
                    </h1>

                    <p
                        class="mt-1 text-sm
                               text-gray-500"
                    >
                        Registra la flotilla y asigna
                        los vehículos correspondientes.
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
                action="{{ route('flotillas.store') }}"
                method="POST"
                class="space-y-6 p-6"
            >

                @csrf


                {{-- ERRORES --}}
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


                {{-- EMPRESA --}}
                @role('Super Administrador')

                    <div>

                        <label
                            class="mb-2 block
                                   text-sm font-medium
                                   text-gray-900"
                        >
                            Empresa / Cliente
                        </label>

                        <select
                            name="empresa_id"
                            required
                            class="w-full rounded-lg
                                   border-gray-300
                                   focus:border-indigo-500
                                   focus:ring-indigo-500"
                        >

                            <option value="">
                                Seleccionar empresa
                            </option>

                            @foreach($empresas as $empresa)

                                <option
                                    value="{{ $empresa->id }}"
                                    @selected(
                                        old('empresa_id')
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
                        value="{{ auth()->user()->empresa_id }}"
                    >

                    <div>

                        <label
                            class="mb-2 block
                                   text-sm font-medium
                                   text-gray-900"
                        >
                            Empresa
                        </label>

                        <input
                            type="text"
                            value="{{
                                auth()->user()
                                    ->empresa?->nombre
                                ??
                                'Sin empresa'
                            }}"
                            disabled
                            class="w-full rounded-lg
                                   border-gray-200
                                   bg-gray-100
                                   text-gray-500"
                        >

                    </div>

                @endrole


                {{-- NOMBRE --}}
                <div>

                    <label
                        class="mb-2 block
                               text-sm font-medium
                               text-gray-900"
                    >
                        Nombre de la Flotilla
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        value="{{ old('nombre') }}"
                        required
                        placeholder="Ej. Reparto Cuernavaca"
                        class="w-full rounded-lg
                               border-gray-300
                               focus:border-indigo-500
                               focus:ring-indigo-500"
                    >

                </div>


                {{-- RESPONSABLE --}}
                <div>

                    <label
                        class="mb-2 block
                               text-sm font-medium
                               text-gray-900"
                    >
                        Responsable
                    </label>

                    <select
                        name="user_id"
                        class="w-full rounded-lg
                               border-gray-300
                               focus:border-indigo-500
                               focus:ring-indigo-500"
                    >

                        <option value="">
                            Sin responsable
                        </option>

                        @foreach($users as $user)

                            <option
                                value="{{ $user->id }}"
                                @selected(
                                    old('user_id')
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


                {{-- VEHÍCULOS --}}
                <div>

                    <label
                        class="mb-2 block
                               text-sm font-medium
                               text-gray-900"
                    >
                        Vehículos a asignar
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
                                       border-gray-200 bg-white
                                       p-3 hover:bg-gray-50"
                            >

                                <input
                                    type="checkbox"
                                    name="vehiculos[]"
                                    value="{{ $vehiculo->id }}"
                                    @checked(
                                        in_array(
                                            $vehiculo->id,
                                            old(
                                                'vehiculos',
                                                []
                                            )
                                        )
                                    )
                                    class="rounded
                                           border-gray-300
                                           text-indigo-600
                                           focus:ring-indigo-500"
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

                                        Placas:
                                        {{
                                            $vehiculo->placas
                                            ??
                                            'N/D'
                                        }}

                                        @role('Super Administrador')

                                            · Empresa:
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

                            <div
                                class="py-8 text-center
                                       text-sm text-gray-500"
                            >
                                No hay vehículos disponibles
                                sin flotilla.
                            </div>

                        @endforelse

                    </div>

                </div>


                {{-- DESCRIPCIÓN --}}
                <div>

                    <label
                        class="mb-2 block
                               text-sm font-medium
                               text-gray-900"
                    >
                        Descripción
                    </label>

                    <textarea
                        name="descripcion"
                        rows="4"
                        placeholder="Descripción de la flotilla..."
                        class="w-full rounded-lg
                               border-gray-300
                               focus:border-indigo-500
                               focus:ring-indigo-500"
                    >{{ old('descripcion') }}</textarea>

                </div>


                {{-- BOTONES --}}
                <div
                    class="flex justify-end gap-3
                           border-t border-gray-100 pt-5"
                >

                    <a
                        href="{{ route('flotillas.index') }}"
                        class="rounded-lg border
                               border-gray-300 bg-white
                               px-5 py-2.5 text-sm
                               font-medium text-gray-700
                               hover:bg-gray-50"
                    >
                        Cancelar
                    </a>


                    <button
                        type="submit"
                        class="rounded-lg bg-gray-900
                               px-5 py-2.5 text-sm
                               font-medium text-white
                               hover:bg-gray-800"
                    >
                        Guardar Flotilla
                    </button>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>