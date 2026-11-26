<x-app-layout>

    <style>
        .font-heading {
            font-family: 'Josefin Sans', sans-serif;
        }

        .font-body {
            font-family: 'Ubuntu', sans-serif;
        }
    </style>


    <div class="space-y-5 font-body">

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
                   border border-[#0056b3]/20 bg-white
                   shadow-sm shadow-black/5"
        >

            {{-- CABECERA --}}
            <div
                class="flex flex-col gap-4
                       bg-[#0056b3] p-5 sm:p-6
                       lg:flex-row lg:items-center
                       lg:justify-between"
            >

                <div>

                    <h2
                        class="font-heading text-xl
                               font-semibold tracking-tight
                               text-white sm:text-2xl"
                    >
                        Gestión de flotillas
                    </h2>

                    <p class="mt-1 text-sm text-blue-100">
                        Administra los grupos de vehículos y su organización operativa.
                    </p>

                </div>


                <div
                    class="flex w-full flex-col gap-2
                           sm:flex-row sm:items-center
                           lg:w-auto"
                >

                    <form
                        method="GET"
                        action="{{ route('flotillas.index') }}"
                        class="flex flex-1 gap-2"
                    >

                        <div class="relative flex-1 sm:w-64">

                            <span
                                class="absolute inset-y-0 left-0
                                       flex items-center pl-3
                                       text-[#0056b3]/70"
                            >
                                <span
                                    class="material-symbols-rounded
                                           text-[18px]"
                                >
                                    search
                                </span>
                            </span>

                            <input
                                type="text"
                                name="buscar"
                                value="{{ request('buscar') }}"
                                placeholder="Buscar flotilla..."
                                class="w-full rounded-xl
                                       border-white/80 bg-white
                                       py-2.5 pl-9 pr-3 text-sm
                                       text-gray-700 shadow-sm
                                       placeholder:text-gray-400
                                       focus:border-white
                                       focus:ring-2 focus:ring-white"
                            >

                        </div>


                        <button
                            type="submit"
                            class="rounded-xl border
                                   border-white/80 bg-white
                                   px-4 py-2.5 text-sm
                                   font-medium text-[#0056b3]
                                   shadow-sm transition-all
                                   hover:bg-blue-50
                                   active:scale-[0.98]"
                        >
                            Buscar
                        </button>

                    </form>


                    <a
                        href="{{ route('flotillas.create') }}"
                        class="inline-flex shrink-0
                               items-center justify-center
                               gap-2 rounded-xl bg-white
                               px-4 py-2.5 text-sm
                               font-medium text-[#0056b3]
                               shadow-sm shadow-black/20
                               transition-all hover:bg-blue-50
                               active:scale-[0.98]"
                    >

                        <span
                            class="material-symbols-rounded
                                   text-[20px]"
                        >
                            add
                        </span>

                        Nueva flotilla

                    </a>

                </div>

            </div>


            {{-- TABLA --}}
            <div class="overflow-x-auto">

                <table
                    class="w-full text-left
                           text-sm text-gray-700"
                >

                    <thead
                        class="border-b border-[#0056b3]/15
                               bg-blue-50/60 text-xs
                               text-[#0056b3]"
                    >

                        <tr>

                            <th class="px-6 py-4 font-semibold">
                                Flotilla / Empresa
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Descripción
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Responsable
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Vehículos
                            </th>

                            <th class="px-6 py-4 font-semibold text-right">
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-[#0056b3]/10">

                        @forelse($flotillas as $flotilla)

                            <tr class="transition-colors hover:bg-blue-50/30">

                                {{-- FLOTILLA --}}
                                <td class="px-6 py-4">

                                    <div
                                        class="font-medium
                                               text-[#003d82]"
                                    >
                                        {{ $flotilla->nombre }}
                                    </div>

                                    <div
                                        class="mt-1 text-[10px]
                                               font-semibold uppercase
                                               tracking-wider
                                               text-gray-500"
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
                                            class="inline-flex rounded-lg
                                                   border border-[#0056b3]/15
                                                   bg-[#0056b3]/10 px-2 py-1
                                                   text-xs font-medium
                                                   text-[#003d82]"
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
                                               text-[#003d82]"
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
                                                   transition-colors
                                                   hover:bg-[#0056b3]/10
                                                   hover:text-[#0056b3]"
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
                                                   text-[#0056b3]
                                                   transition-colors
                                                   hover:bg-[#0056b3]/10
                                                   hover:text-[#003d82]"
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
                                                   text-5xl text-[#0056b3]/30"
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
                                                   text-[#0056b3] underline
                                                   underline-offset-2
                                                   hover:text-[#003d82]"
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
                    class="border-t border-[#0056b3]/15
                           bg-white p-4"
                >
                    {{ $flotillas->links() }}
                </div>

            @endif

        </div>

    </div>

</x-app-layout>