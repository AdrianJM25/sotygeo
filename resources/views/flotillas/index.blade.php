<x-app-layout>

    <div class="space-y-5">

        {{-- MENSAJE DE ÉXITO --}}
        @if(session('success'))

            <div
                class="rounded-xl border border-emerald-200
                       bg-emerald-50 px-4 py-3 text-sm
                       font-medium text-emerald-700"
            >
                {{ session('success') }}
            </div>

        @endif


        {{-- TARJETA PRINCIPAL --}}
        <div
            class="overflow-hidden rounded-2xl
                   border border-gray-200 bg-white shadow-sm"
        >

            {{-- CABECERA --}}
            <div
                class="flex flex-col gap-3 border-b
                       border-gray-100 p-4
                       sm:flex-row sm:items-center
                       sm:justify-between"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-10 w-10 items-center
                               justify-center rounded-xl
                               bg-indigo-500 text-white"
                    >

                        <span class="material-symbols-rounded">
                            local_shipping
                        </span>

                    </div>

                    <div>

                        <h2
                            class="text-lg font-bold
                                   text-gray-900"
                        >
                            Gestión de Flotillas
                        </h2>

                        <p class="text-xs text-gray-400">
                            {{ $flotillas->total() }}
                            flotilla(s) registrada(s)
                        </p>

                    </div>

                </div>


                <div
                    class="flex w-full items-center
                           gap-2 sm:w-auto"
                >

                    <form
                        method="GET"
                        action="{{ route('flotillas.index') }}"
                        class="flex flex-1 gap-2"
                    >

                        <input
                            type="text"
                            name="buscar"
                            value="{{ request('buscar') }}"
                            placeholder="Buscar flotilla..."
                            class="w-full rounded-lg
                                   border-gray-300 bg-gray-50
                                   text-sm focus:border-indigo-500
                                   focus:ring-indigo-500 sm:w-64"
                        >

                        <button
                            type="submit"
                            class="rounded-lg border border-gray-300
                                   bg-white px-3 py-2 text-sm
                                   text-gray-600 hover:bg-gray-50"
                        >
                            Buscar
                        </button>

                    </form>


                    <a
                        href="{{ route('flotillas.create') }}"
                        class="inline-flex shrink-0 items-center
                               gap-2 rounded-lg bg-gray-900
                               px-4 py-2 text-sm font-medium
                               text-white transition hover:bg-gray-800"
                    >

                        <span class="material-symbols-rounded text-[18px]">
                            add
                        </span>

                        Nueva Flotilla

                    </a>

                </div>

            </div>


            {{-- TABLA --}}
            <div class="overflow-x-auto">

                <table
                    class="w-full text-left
                           text-sm text-gray-600"
                >

                    <thead
                        class="border-b border-gray-200
                               bg-gray-50 text-xs uppercase
                               text-gray-500"
                    >

                        <tr>

                            <th class="px-6 py-4">
                                Flotilla / Empresa
                            </th>

                            <th class="px-6 py-4">
                                Descripción
                            </th>

                            <th class="px-6 py-4">
                                Responsable
                            </th>

                            <th class="px-6 py-4">
                                Vehículos
                            </th>

                            <th class="px-6 py-4 text-right">
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse($flotillas as $flotilla)

                            <tr class="hover:bg-gray-50">

                                {{-- FLOTILLA --}}
                                <td class="px-6 py-4">

                                    <div
                                        class="font-medium
                                               text-gray-900"
                                    >
                                        {{ $flotilla->nombre }}
                                    </div>

                                    <div
                                        class="mt-1 text-[10px]
                                               font-semibold uppercase
                                               tracking-wider
                                               text-gray-400"
                                    >

                                        {{
                                            $flotilla->empresa?->nombre
                                            ??
                                            'Sin empresa'
                                        }}

                                    </div>

                                </td>


                                {{-- DESCRIPCIÓN --}}
                                <td
                                    class="max-w-xs px-6 py-4
                                           text-gray-500"
                                >

                                    {{
                                        $flotilla->descripcion
                                        ??
                                        'Sin descripción'
                                    }}

                                </td>


                                {{-- RESPONSABLE --}}
                                <td class="px-6 py-4">

                                    @if($flotilla->usuario)

                                        <span
                                            class="inline-flex rounded-md
                                                   border border-blue-100
                                                   bg-blue-50 px-2 py-1
                                                   text-xs font-medium
                                                   text-blue-700"
                                        >

                                            {{ $flotilla->usuario->nombre }}

                                            {{ $flotilla->usuario->apellido_paterno }}

                                        </span>

                                    @else

                                        <span
                                            class="text-sm
                                                   text-gray-400"
                                        >
                                            Sin asignar
                                        </span>

                                    @endif

                                </td>


                                {{-- VEHÍCULOS --}}
                                <td class="px-6 py-4">

                                    <span
                                        class="font-semibold
                                               text-gray-900"
                                    >
                                        {{ $flotilla->vehiculos->count() }}
                                    </span>

                                    unidades

                                </td>


                                {{-- ACCIONES --}}
                                <td
                                    class="whitespace-nowrap
                                           px-6 py-4 text-right"
                                >

                                    <div
                                        class="inline-flex
                                               items-center gap-1"
                                    >

                                        {{-- VER --}}
                                        <a
                                            href="{{ route(
                                                'flotillas.show',
                                                $flotilla
                                            ) }}"
                                            title="Ver detalles"
                                            class="rounded-lg p-2
                                                   text-gray-500
                                                   hover:bg-gray-100
                                                   hover:text-gray-900"
                                        >

                                            <span
                                                class="material-symbols-rounded
                                                       text-[20px]"
                                            >
                                                visibility
                                            </span>

                                        </a>


                                        {{-- EDITAR --}}
                                        <a
                                            href="{{ route(
                                                'flotillas.edit',
                                                $flotilla
                                            ) }}"
                                            title="Editar"
                                            class="rounded-lg p-2
                                                   text-blue-500
                                                   hover:bg-blue-50
                                                   hover:text-blue-700"
                                        >

                                            <span
                                                class="material-symbols-rounded
                                                       text-[20px]"
                                            >
                                                edit
                                            </span>

                                        </a>


                                        {{-- ELIMINAR --}}
                                        <form
                                            action="{{ route(
                                                'flotillas.destroy',
                                                $flotilla
                                            ) }}"
                                            method="POST"
                                            class="inline-block"
                                            onsubmit="
                                                return confirm(
                                                    '¿Seguro que deseas eliminar esta flotilla?'
                                                );
                                            "
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                title="Eliminar"
                                                class="rounded-lg p-2
                                                       text-red-500
                                                       hover:bg-red-50
                                                       hover:text-red-700"
                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[20px]"
                                                >
                                                    delete
                                                </span>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="5"
                                    class="px-6 py-16 text-center"
                                >

                                    <div
                                        class="flex flex-col
                                               items-center gap-3"
                                    >

                                        <span
                                            class="material-symbols-rounded
                                                   text-5xl text-gray-300"
                                        >
                                            local_shipping
                                        </span>

                                        <p
                                            class="text-sm
                                                   text-gray-500"
                                        >
                                            Aún no hay flotillas registradas.
                                        </p>

                                        <a
                                            href="{{ route(
                                                'flotillas.create'
                                            ) }}"
                                            class="text-sm font-medium
                                                   text-gray-900 underline"
                                        >
                                            Crear la primera flotilla
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            @if($flotillas->hasPages())

                <div
                    class="border-t border-gray-100
                           p-4"
                >
                    {{ $flotillas->links() }}
                </div>

            @endif

        </div>

    </div>

</x-app-layout>