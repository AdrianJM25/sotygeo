<x-app-layout>

    <div class="px-4 pb-6">

        <div
            class="bg-white border border-[#0056b3]/20
                   rounded-2xl shadow-sm overflow-hidden"
        >

            {{-- ====================================================== --}}
            {{-- ENCABEZADO --}}
            {{-- ====================================================== --}}

            <div
                class="bg-[#0056b3]
                       px-6 py-6
                       flex flex-col
                       lg:flex-row
                       lg:items-center
                       lg:justify-between
                       gap-4"
            >

                <div>

                    <h1
                        class="font-heading
                               text-2xl
                               font-bold
                               text-white"
                    >
                        Buscar módulos
                    </h1>

                    <p
                        class="mt-1
                               text-sm
                               text-blue-100"
                    >
                        Busca y accede a los módulos disponibles para tu usuario.
                    </p>

                </div>


                {{-- BÚSQUEDA EN LA PÁGINA DE RESULTADOS --}}

                <form
                    method="GET"
                    action="{{ route('modulos.buscar') }}"
                    class="w-full lg:w-[420px]"
                >

                    <div class="relative">

                        <span
                            class="absolute
                                   inset-y-0 left-0
                                   flex items-center
                                   pl-3
                                   text-gray-400
                                   pointer-events-none"
                        >
                            <span
                                class="material-symbols-rounded
                                       text-[19px]"
                            >
                                search
                            </span>
                        </span>


                        <input
                            type="text"
                            name="q"
                            value="{{ $consulta }}"
                            placeholder="Buscar módulo..."
                            autofocus
                            autocomplete="off"
                            class="w-full
                                   pl-10 pr-12 py-2.5
                                   text-sm
                                   text-gray-700
                                   bg-white
                                   border border-white
                                   rounded-xl
                                   focus:border-blue-200
                                   focus:ring-blue-200"
                        >


                        <button
                            type="submit"
                            title="Buscar"
                            class="absolute
                                   inset-y-0 right-0
                                   flex items-center
                                   pr-3
                                   text-[#0056b3]
                                   hover:text-[#003d82]
                                   transition-colors"
                        >
                            <span
                                class="material-symbols-rounded
                                       text-[21px]"
                            >
                                search
                            </span>
                        </button>

                    </div>

                </form>

            </div>


            {{-- ====================================================== --}}
            {{-- INFORMACIÓN DE LA BÚSQUEDA --}}
            {{-- ====================================================== --}}

            <div
                class="px-6 py-4
                       border-b border-gray-100
                       bg-blue-50/40"
            >

                @if($consulta !== '')

                    <p class="text-sm text-gray-600">
                        Resultados para
                        <span class="font-semibold text-[#003d82]">
                            "{{ $consulta }}"
                        </span>
                        — {{ $modulos->count() }}
                        {{ $modulos->count() === 1 ? 'módulo encontrado' : 'módulos encontrados' }}.
                    </p>

                @else

                    <p class="text-sm text-gray-600">
                        Mostrando todos los módulos disponibles para tu usuario.
                    </p>

                @endif

            </div>


            {{-- ====================================================== --}}
            {{-- RESULTADOS --}}
            {{-- ====================================================== --}}

            @if($modulos->isNotEmpty())

                <div
                    class="grid grid-cols-1
                           md:grid-cols-2
                           xl:grid-cols-3
                           gap-4
                           p-6"
                >

                    @foreach($modulos as $modulo)

                        <a
                            href="{{ $modulo['ruta'] }}"
                            class="group
                                   flex items-center
                                   justify-between
                                   gap-4
                                   p-4
                                   border border-gray-200
                                   rounded-xl
                                   hover:border-[#56928C]/50
                                   hover:bg-[#56928C]/5
                                   hover:shadow-sm
                                   transition-all"
                        >

                            <div
                                class="flex items-center
                                       gap-3
                                       min-w-0"
                            >

                                <div
                                    class="w-11 h-11
                                           shrink-0
                                           flex items-center
                                           justify-center
                                           rounded-xl
                                           bg-[#56928C]/10
                                           text-[#56928C]
                                           group-hover:bg-[#56928C]
                                           group-hover:text-white
                                           transition-colors"
                                >
                                    <span
                                        class="material-symbols-rounded"
                                    >
                                        apps
                                    </span>
                                </div>


                                <div class="min-w-0">

                                    <p
                                        class="font-heading
                                               font-semibold
                                               text-gray-800
                                               truncate
                                               group-hover:text-[#3A6B66]"
                                    >
                                        {{ $modulo['nombre'] }}
                                    </p>

                                    <p
                                        class="mt-0.5
                                               text-xs
                                               text-gray-400
                                               truncate"
                                    >
                                        {{ $modulo['nombre_ruta'] }}
                                    </p>

                                </div>

                            </div>


                            <span
                                class="material-symbols-rounded
                                       text-gray-300
                                       group-hover:text-[#56928C]
                                       transition-colors"
                            >
                                arrow_forward
                            </span>

                        </a>

                    @endforeach

                </div>

            @else

                <div
                    class="flex flex-col
                           items-center
                           justify-center
                           gap-2
                           px-6 py-16
                           text-center"
                >

                    <div
                        class="w-14 h-14
                               flex items-center
                               justify-center
                               rounded-full
                               bg-gray-100
                               text-gray-300"
                    >
                        <span
                            class="material-symbols-rounded
                                   text-[30px]"
                        >
                            search_off
                        </span>
                    </div>


                    <h2
                        class="font-heading
                               text-lg
                               font-semibold
                               text-gray-700"
                    >
                        No se encontraron módulos
                    </h2>


                    <p
                        class="max-w-md
                               text-sm
                               text-gray-500"
                    >
                        Intenta buscar con otro nombre o revisa que tu usuario tenga permiso para entrar al módulo.
                    </p>


                    <a
                        href="{{ route('modulos.buscar') }}"
                        class="mt-2
                               inline-flex
                               items-center
                               gap-2
                               text-sm
                               font-medium
                               text-[#0056b3]
                               hover:text-[#003d82]"
                    >
                        <span
                            class="material-symbols-rounded
                                   text-[18px]"
                        >
                            refresh
                        </span>

                        Ver todos los módulos
                    </a>

                </div>

            @endif

        </div>

    </div>

</x-app-layout>
