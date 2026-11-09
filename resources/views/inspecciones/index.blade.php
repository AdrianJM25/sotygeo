<x-app-layout>

    <x-slot name="titulo">
        Inspecciones Vehiculares
    </x-slot>


    <div
        class="mx-auto max-w-[1500px]
               space-y-5 pb-8
               font-['Ubuntu']"
    >

        {{-- ====================================================== --}}
        {{-- HEADER --}}
        {{-- ====================================================== --}}

        <div
            class="flex flex-col gap-4
                   rounded-2xl
                   bg-[#0056b3]
                   p-5 shadow-sm
                   sm:flex-row
                   sm:items-center
                   sm:justify-between"
        >

            <div class="flex items-center gap-4">

                <div
                    class="flex h-12 w-12
                           items-center justify-center
                           rounded-xl
                           bg-white/15
                           text-white"
                >

                    <span
                        class="material-symbols-rounded
                               text-[28px]"
                    >
                        fact_check
                    </span>

                </div>


                <div>

                    <h1
                        class="font-['Josefin_Sans']
                               text-2xl font-semibold
                               text-white"
                    >
                        Inspecciones Vehiculares
                    </h1>

                    <p
                        class="text-sm
                               text-blue-100"
                    >
                        Registra y consulta el estado físico de los vehículos.
                    </p>

                </div>

            </div>


            <div
                class="inline-flex items-center
                       gap-2 rounded-xl
                       bg-white/10
                       px-4 py-2
                       text-sm
                       text-blue-100"
            >

                <span
                    class="material-symbols-rounded
                           text-[19px]"
                >
                    history
                </span>

                {{ $inspecciones->count() }}

                {{ $inspecciones->count() === 1
                    ? 'inspección registrada'
                    : 'inspecciones registradas'
                }}

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- MENSAJE DE ÉXITO --}}
        {{-- ====================================================== --}}

        @if(session('success'))

            <div
                class="flex items-start gap-3
                       rounded-2xl
                       border border-emerald-200
                       bg-emerald-50
                       px-4 py-3
                       text-sm text-emerald-700"
            >

                <span
                    class="material-symbols-rounded
                           text-[21px]"
                >
                    check_circle
                </span>

                <span>
                    {{ session('success') }}
                </span>

            </div>

        @endif


        {{-- ====================================================== --}}
        {{-- ERRORES --}}
        {{-- ====================================================== --}}

        @if($errors->any())

            <div
                class="rounded-2xl
                       border border-red-200
                       bg-red-50
                       px-5 py-4"
            >

                <div
                    class="flex items-center gap-2
                           text-sm font-semibold
                           text-red-700"
                >

                    <span
                        class="material-symbols-rounded
                               text-[20px]"
                    >
                        error
                    </span>

                    No fue posible guardar la inspección.

                </div>


                <ul
                    class="mt-2 ml-7
                           list-disc
                           space-y-1
                           text-sm
                           text-red-600"
                >

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- ====================================================== --}}
        {{-- FORMULARIO --}}
        {{-- ====================================================== --}}

        <div
            class="overflow-hidden
                   rounded-2xl
                   border border-[#0056b3]/15
                   bg-white
                   shadow-sm"
        >

            {{-- CABECERA --}}

            <div
                class="border-b
                       border-gray-100
                       bg-blue-50/50
                       px-5 py-4"
            >

                <div
                    class="flex items-center gap-3"
                >

                    <div
                        class="flex h-10 w-10
                               items-center justify-center
                               rounded-xl
                               bg-blue-100
                               text-[#0056b3]"
                    >

                        <span
                            class="material-symbols-rounded"
                        >
                            playlist_add_check
                        </span>

                    </div>


                    <div>

                        <h2
                            class="font-['Josefin_Sans']
                                   font-semibold
                                   text-[#003d82]"
                        >
                            Nueva inspección
                        </h2>

                        <p
                            class="text-xs
                                   text-gray-500"
                        >
                            Todos los elementos de revisión son obligatorios.
                        </p>

                    </div>

                </div>

            </div>


            <form
                action="{{ route('inspecciones.store') }}"
                method="POST"
                class="p-5"
            >

                @csrf


                {{-- ================================================== --}}
                {{-- VEHÍCULO --}}
                {{-- ================================================== --}}

                <div
                    class="grid grid-cols-1
                           gap-5
                           lg:grid-cols-2"
                >

                    <div>

                        <label
                            for="vehiculo_id"
                            class="mb-1.5 block
                                   text-sm font-medium
                                   text-gray-700"
                        >
                            Vehículo
                            <span class="text-red-500">
                                *
                            </span>
                        </label>


                        <select
                            id="vehiculo_id"
                            name="vehiculo_id"
                            required

                            class="w-full
                                   rounded-xl
                                   border-gray-300
                                   text-sm
                                   focus:border-[#0056b3]
                                   focus:ring-[#0056b3]"
                        >

                            <option value="">
                                Selecciona un vehículo
                            </option>


                            @foreach($vehiculos as $vehiculo)

                                <option
                                    value="{{ $vehiculo->id }}"
                                    @selected(
                                        old('vehiculo_id')
                                        ==
                                        $vehiculo->id
                                    )
                                >
                                    {{ $vehiculo->nombre }}

                                    @if($vehiculo->placas)
                                        — {{ $vehiculo->placas }}
                                    @endif
                                </option>

                            @endforeach

                        </select>


                        @if($vehiculos->isEmpty())

                            <p
                                class="mt-2
                                       text-xs
                                       text-amber-600"
                            >
                                No tienes vehículos disponibles para inspeccionar.
                            </p>

                        @endif

                    </div>


                    {{-- RESPONSABLE --}}

                    <div>

                        <label
                            class="mb-1.5 block
                                   text-sm font-medium
                                   text-gray-700"
                        >
                            Responsable
                        </label>


                        <div
                            class="flex h-[42px]
                                   items-center gap-2
                                   rounded-xl
                                   border border-gray-200
                                   bg-gray-50
                                   px-3
                                   text-sm
                                   text-gray-600"
                        >

                            <span
                                class="material-symbols-rounded
                                       text-[19px]
                                       text-[#0056b3]"
                            >
                                person
                            </span>

                            {{ auth()->user()->nombre
                                ?? auth()->user()->name
                                ?? auth()->user()->email
                            }}

                        </div>

                    </div>

                </div>


                {{-- ================================================== --}}
                {{-- ELEMENTOS --}}
                {{-- ================================================== --}}

                <div class="mt-7">

                    <div
                        class="mb-4
                               flex items-center gap-2"
                    >

                        <span
                            class="material-symbols-rounded
                                   text-[#0056b3]"
                        >
                            checklist
                        </span>

                        <div>

                            <h3
                                class="font-['Josefin_Sans']
                                       font-semibold
                                       text-[#003d82]"
                            >
                                Elementos de revisión
                            </h3>

                            <p
                                class="text-xs
                                       text-gray-500"
                            >
                                Indica el estado de cada elemento.
                            </p>

                        </div>

                    </div>


                    <div
                        class="grid grid-cols-1
                               gap-4
                               xl:grid-cols-2"
                    >

                        @foreach($elementos as $elemento)

                            <div
                                class="rounded-2xl
                                       border border-gray-200
                                       bg-gray-50/40
                                       p-4"
                            >

                                <div
                                    class="flex items-start
                                           justify-between
                                           gap-3"
                                >

                                    <div>

                                        <h4
                                            class="font-['Josefin_Sans']
                                                   font-semibold
                                                   text-gray-800"
                                        >
                                            {{ $elemento->nombre }}
                                        </h4>


                                        @if($elemento->categoria)

                                            <span
                                                class="mt-1
                                                       inline-flex
                                                       rounded-full
                                                       bg-blue-50
                                                       px-2 py-0.5
                                                       text-[11px]
                                                       font-medium
                                                       text-[#0056b3]"
                                            >
                                                {{ $elemento->categoria }}
                                            </span>

                                        @endif

                                    </div>


                                    <span
                                        class="text-xs
                                               text-red-500"
                                    >
                                        Obligatorio
                                    </span>

                                </div>


                                {{-- RESULTADO --}}

                                <div
                                    class="mt-4
                                           grid grid-cols-2
                                           gap-3"
                                >

                                    <label
                                        class="cursor-pointer"
                                    >

                                        <input
                                            type="radio"

                                            name="detalles[{{ $elemento->id }}][resultado]"

                                            value="correcto"

                                            required

                                            @checked(
                                                old(
                                                    'detalles.'
                                                    .
                                                    $elemento->id
                                                    .
                                                    '.resultado'
                                                )
                                                ===
                                                'correcto'
                                            )

                                            class="peer sr-only"
                                        >


                                        <div
                                            class="flex items-center
                                                   justify-center gap-2
                                                   rounded-xl
                                                   border border-gray-200
                                                   bg-white
                                                   px-3 py-3
                                                   text-sm
                                                   font-medium
                                                   text-gray-600
                                                   transition

                                                   peer-checked:border-emerald-400
                                                   peer-checked:bg-emerald-50
                                                   peer-checked:text-emerald-700"
                                        >

                                            <span
                                                class="material-symbols-rounded
                                                       text-[19px]"
                                            >
                                                check_circle
                                            </span>

                                            Correcto

                                        </div>

                                    </label>


                                    <label
                                        class="cursor-pointer"
                                    >

                                        <input
                                            type="radio"

                                            name="detalles[{{ $elemento->id }}][resultado]"

                                            value="incidencia"

                                            required

                                            @checked(
                                                old(
                                                    'detalles.'
                                                    .
                                                    $elemento->id
                                                    .
                                                    '.resultado'
                                                )
                                                ===
                                                'incidencia'
                                            )

                                            class="peer sr-only"
                                        >


                                        <div
                                            class="flex items-center
                                                   justify-center gap-2
                                                   rounded-xl
                                                   border border-gray-200
                                                   bg-white
                                                   px-3 py-3
                                                   text-sm
                                                   font-medium
                                                   text-gray-600
                                                   transition

                                                   peer-checked:border-red-400
                                                   peer-checked:bg-red-50
                                                   peer-checked:text-red-700"
                                        >

                                            <span
                                                class="material-symbols-rounded
                                                       text-[19px]"
                                            >
                                                warning
                                            </span>

                                            Incidencia

                                        </div>

                                    </label>

                                </div>


                                {{-- OBSERVACIÓN --}}

                                <div class="mt-3">

                                    <textarea
                                        name="detalles[{{ $elemento->id }}][observaciones]"

                                        rows="2"

                                        maxlength="1000"

                                        placeholder="Observación del elemento (opcional)"

                                        class="w-full
                                               resize-none
                                               rounded-xl
                                               border-gray-300
                                               text-sm
                                               focus:border-[#0056b3]
                                               focus:ring-[#0056b3]"
                                    >{{ old(
                                        'detalles.'
                                        .
                                        $elemento->id
                                        .
                                        '.observaciones'
                                    ) }}</textarea>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>


                {{-- ================================================== --}}
                {{-- OBSERVACIONES GENERALES --}}
                {{-- ================================================== --}}

                <div class="mt-6">

                    <label
                        for="observaciones"
                        class="mb-1.5 block
                               text-sm font-medium
                               text-gray-700"
                    >
                        Observaciones generales
                    </label>


                    <textarea
                        id="observaciones"

                        name="observaciones"

                        rows="3"

                        maxlength="1000"

                        placeholder="Comentarios generales de la inspección..."

                        class="w-full
                               resize-none
                               rounded-xl
                               border-gray-300
                               text-sm
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >{{ old('observaciones') }}</textarea>

                </div>


                {{-- ================================================== --}}
                {{-- BOTÓN --}}
                {{-- ================================================== --}}

                <div
                    class="mt-6
                           flex justify-end
                           border-t border-gray-100
                           pt-5"
                >

                    <button
                        type="submit"

                        @disabled($vehiculos->isEmpty())

                        class="inline-flex
                               items-center gap-2
                               rounded-xl
                               bg-[#0056b3]
                               px-5 py-2.5
                               text-sm font-medium
                               text-white
                               shadow-sm
                               transition
                               hover:bg-[#004494]
                               disabled:cursor-not-allowed
                               disabled:opacity-50"
                    >

                        <span
                            class="material-symbols-rounded
                                   text-[19px]"
                        >
                            save
                        </span>

                        Guardar inspección

                    </button>

                </div>

            </form>

        </div>


        {{-- ====================================================== --}}
        {{-- HISTORIAL --}}
        {{-- ====================================================== --}}

        <div
            class="overflow-hidden
                   rounded-2xl
                   border border-gray-200
                   bg-white
                   shadow-sm"
        >

            <div
                class="flex items-center
                       justify-between
                       border-b
                       border-gray-100
                       bg-gray-50/60
                       px-5 py-4"
            >

                <div
                    class="flex items-center gap-3"
                >

                    <div
                        class="flex h-10 w-10
                               items-center justify-center
                               rounded-xl
                               bg-blue-50
                               text-[#0056b3]"
                    >

                        <span
                            class="material-symbols-rounded"
                        >
                            history
                        </span>

                    </div>


                    <div>

                        <h2
                            class="font-['Josefin_Sans']
                                   font-semibold
                                   text-[#003d82]"
                        >
                            Historial de inspecciones
                        </h2>

                        <p
                            class="text-xs
                                   text-gray-500"
                        >
                            Últimas inspecciones registradas.
                        </p>

                    </div>

                </div>

            </div>


            @if($inspecciones->isEmpty())

                <div
                    class="px-6 py-14
                           text-center"
                >

                    <div
                        class="mx-auto
                               flex h-14 w-14
                               items-center
                               justify-center
                               rounded-2xl
                               bg-gray-100
                               text-gray-400"
                    >

                        <span
                            class="material-symbols-rounded
                                   text-3xl"
                        >
                            fact_check
                        </span>

                    </div>


                    <h3
                        class="mt-3
                               font-['Josefin_Sans']
                               font-semibold
                               text-gray-700"
                    >
                        Sin inspecciones registradas
                    </h3>

                    <p
                        class="mt-1
                               text-sm
                               text-gray-500"
                    >
                        Las inspecciones guardadas aparecerán aquí.
                    </p>

                </div>


            @else

                <div
                    class="divide-y
                           divide-gray-100"
                >

                    @foreach($inspecciones as $inspeccion)

                        <details
                            class="group"
                        >

                            {{-- ================================================== --}}
                            {{-- RESUMEN --}}
                            {{-- ================================================== --}}

                            <summary
                                class="flex cursor-pointer
                                       list-none
                                       flex-col gap-3
                                       px-5 py-4
                                       transition
                                       hover:bg-gray-50
                                       sm:flex-row
                                       sm:items-center
                                       sm:justify-between"
                            >

                                <div
                                    class="flex
                                           min-w-0
                                           items-center
                                           gap-3"
                                >

                                    <div
                                        class="flex h-10 w-10
                                               shrink-0
                                               items-center
                                               justify-center
                                               rounded-xl

                                               {{ $inspeccion->resultado_general === 'con_incidencias'
                                                    ? 'bg-red-50 text-red-600'
                                                    : 'bg-emerald-50 text-emerald-600'
                                               }}"
                                    >

                                        <span
                                            class="material-symbols-rounded"
                                        >
                                            {{ $inspeccion->resultado_general === 'con_incidencias'
                                                ? 'warning'
                                                : 'check_circle'
                                            }}
                                        </span>

                                    </div>


                                    <div class="min-w-0">

                                        <p
                                            class="truncate
                                                   font-['Josefin_Sans']
                                                   font-semibold
                                                   text-gray-800"
                                        >
                                            {{ $inspeccion->vehiculo?->nombre ?? 'Vehículo' }}
                                        </p>


                                        <div
                                            class="mt-1
                                                   flex flex-wrap
                                                   items-center
                                                   gap-x-3
                                                   gap-y-1
                                                   text-xs
                                                   text-gray-500"
                                        >

                                            <span
                                                class="flex items-center
                                                       gap-1"
                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[15px]"
                                                >
                                                    calendar_today
                                                </span>

                                                {{ $inspeccion->fecha_inspeccion
                                                    ?->format('d/m/Y H:i')
                                                }}

                                            </span>


                                            <span
                                                class="flex items-center
                                                       gap-1"
                                            >

                                                <span
                                                    class="material-symbols-rounded
                                                           text-[15px]"
                                                >
                                                    person
                                                </span>

                                                {{ $inspeccion->usuario?->nombre
                                                    ?? $inspeccion->usuario?->name
                                                    ?? $inspeccion->usuario?->email
                                                    ?? 'Usuario'
                                                }}

                                            </span>

                                        </div>

                                    </div>

                                </div>


                                <div
                                    class="flex items-center
                                           gap-3"
                                >

                                    <span
                                        class="inline-flex
                                               rounded-full
                                               px-3 py-1
                                               text-xs
                                               font-semibold

                                               {{ $inspeccion->resultado_general === 'con_incidencias'
                                                    ? 'bg-red-50 text-red-700'
                                                    : 'bg-emerald-50 text-emerald-700'
                                               }}"
                                    >

                                        {{ $inspeccion->resultado_general === 'con_incidencias'
                                            ? 'Con incidencias'
                                            : 'Correcta'
                                        }}

                                    </span>


                                    <span
                                        class="material-symbols-rounded
                                               text-gray-400
                                               transition
                                               group-open:rotate-180"
                                    >
                                        expand_more
                                    </span>

                                </div>

                            </summary>


                            {{-- ================================================== --}}
                            {{-- DETALLE --}}
                            {{-- ================================================== --}}

                            <div
                                class="border-t
                                       border-gray-100
                                       bg-gray-50/50
                                       px-5 py-5"
                            >

                                <div
                                    class="grid grid-cols-1
                                           gap-3
                                           md:grid-cols-2
                                           xl:grid-cols-4"
                                >

                                    @foreach($inspeccion->detalles as $detalle)

                                        <div
                                            class="rounded-xl
                                                   border border-gray-200
                                                   bg-white
                                                   p-4"
                                        >

                                            <div
                                                class="flex
                                                       items-start
                                                       justify-between
                                                       gap-2"
                                            >

                                                <div>

                                                    <p
                                                        class="font-['Josefin_Sans']
                                                               font-semibold
                                                               text-gray-800"
                                                    >
                                                        {{ $detalle->elemento?->nombre ?? 'Elemento' }}
                                                    </p>


                                                    @if($detalle->elemento?->categoria)

                                                        <p
                                                            class="mt-0.5
                                                                   text-[11px]
                                                                   text-gray-400"
                                                        >
                                                            {{ $detalle->elemento->categoria }}
                                                        </p>

                                                    @endif

                                                </div>


                                                <span
                                                    class="material-symbols-rounded
                                                           text-[21px]

                                                           {{ $detalle->resultado === 'incidencia'
                                                                ? 'text-red-500'
                                                                : 'text-emerald-500'
                                                           }}"
                                                >

                                                    {{ $detalle->resultado === 'incidencia'
                                                        ? 'warning'
                                                        : 'check_circle'
                                                    }}

                                                </span>

                                            </div>


                                            <span
                                                class="mt-3
                                                       inline-flex
                                                       rounded-full
                                                       px-2.5 py-1
                                                       text-xs
                                                       font-medium

                                                       {{ $detalle->resultado === 'incidencia'
                                                            ? 'bg-red-50 text-red-700'
                                                            : 'bg-emerald-50 text-emerald-700'
                                                       }}"
                                            >

                                                {{ $detalle->resultado === 'incidencia'
                                                    ? 'Incidencia'
                                                    : 'Correcto'
                                                }}

                                            </span>


                                            @if($detalle->observaciones)

                                                <p
                                                    class="mt-3
                                                           text-xs
                                                           leading-relaxed
                                                           text-gray-500"
                                                >
                                                    {{ $detalle->observaciones }}
                                                </p>

                                            @endif

                                        </div>

                                    @endforeach

                                </div>


                                {{-- DATOS GENERALES --}}

                                <div
                                    class="mt-4
                                           grid grid-cols-1
                                           gap-4
                                           rounded-xl
                                           border border-gray-200
                                           bg-white
                                           p-4
                                           md:grid-cols-2"
                                >

                                    <div>

                                        <p
                                            class="text-xs
                                                   font-medium
                                                   text-gray-400"
                                        >
                                            Conductor relacionado
                                        </p>

                                        <p
                                            class="mt-1
                                                   text-sm
                                                   text-gray-700"
                                        >

                                            @if($inspeccion->conductor)

                                                {{ trim(
                                                    $inspeccion->conductor->nombre
                                                    . ' '
                                                    . ($inspeccion->conductor->apellido_paterno ?? '')
                                                ) }}

                                            @else

                                                No aplica

                                            @endif

                                        </p>

                                    </div>


                                    <div>

                                        <p
                                            class="text-xs
                                                   font-medium
                                                   text-gray-400"
                                        >
                                            Observaciones generales
                                        </p>

                                        <p
                                            class="mt-1
                                                   text-sm
                                                   text-gray-700"
                                        >
                                            {{ $inspeccion->observaciones
                                                ?: 'Sin observaciones.'
                                            }}
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </details>

                    @endforeach

                </div>

            @endif

        </div>

    </div>

</x-app-layout>