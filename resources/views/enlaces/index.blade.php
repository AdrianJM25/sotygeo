<x-app-layout>

    <x-slot name="titulo">
        Enlaces de Seguimiento
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
                        link
                    </span>
                </div>

                <div>

                    <h1
                        class="font-['Josefin_Sans']
                               text-2xl font-semibold
                               text-white"
                    >
                        Enlaces de Seguimiento
                    </h1>

                    <p class="text-sm text-blue-100">
                        Genera accesos temporales para compartir la ubicación de un vehículo.
                    </p>

                </div>

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- MENSAJES --}}
        {{-- ====================================================== --}}

        @if(session('success'))

            <div
                class="rounded-xl border border-emerald-200
                       bg-emerald-50 px-5 py-4
                       text-sm text-emerald-700"
            >

                <div class="flex items-center gap-2">

                    <span class="material-symbols-rounded">
                        check_circle
                    </span>

                    <span class="font-medium">
                        {{ session('success') }}
                    </span>

                </div>

            </div>

        @endif


        @if($errors->any())

            <div
                class="rounded-xl border border-red-200
                       bg-red-50 px-5 py-4
                       text-sm text-red-700"
            >

                <div class="mb-2 flex items-center gap-2 font-semibold">

                    <span class="material-symbols-rounded">
                        error
                    </span>

                    Revisa la información ingresada.

                </div>

                <ul class="ml-6 list-disc space-y-1">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- ====================================================== --}}
        {{-- ENLACE GENERADO --}}
        {{-- ====================================================== --}}

        @if(session('enlace_generado'))

            <div
                class="rounded-2xl border border-blue-200
                       bg-blue-50 p-5 shadow-sm"
            >

                <div class="flex flex-col gap-4">

                    <div>

                        <div class="flex items-center gap-2">

                            <span
                                class="material-symbols-rounded
                                       text-[#0056b3]"
                            >
                                share
                            </span>

                            <h2
                                class="font-['Josefin_Sans']
                                       text-lg font-semibold
                                       text-[#003d82]"
                            >
                                Enlace generado
                            </h2>

                        </div>

                        <p class="mt-1 text-sm text-gray-600">
                            Copia este enlace y compártelo con la persona que realizará el seguimiento.
                        </p>

                    </div>


                    <div
                        class="flex flex-col gap-3
                               lg:flex-row lg:items-center"
                    >

                        <input
                            type="text"
                            id="enlace-generado"
                            readonly
                            value="{{ session('enlace_generado') }}"
                            class="w-full rounded-xl
                                   border-blue-200 bg-white
                                   text-sm text-gray-700
                                   focus:border-[#0056b3]
                                   focus:ring-[#0056b3]"
                        >


                        <button
                            type="button"
                            onclick="copiarEnlaceGenerado()"
                            class="inline-flex shrink-0
                                   items-center justify-center
                                   gap-2 rounded-xl
                                   bg-[#0056b3]
                                   px-5 py-2.5
                                   text-sm font-medium
                                   text-white transition
                                   hover:bg-[#004494]"
                        >

                            <span class="material-symbols-rounded">
                                content_copy
                            </span>

                            Copiar enlace

                        </button>


                        <a
                            href="{{ session('enlace_generado') }}"
                            target="_blank"
                            class="inline-flex shrink-0
                                   items-center justify-center
                                   gap-2 rounded-xl
                                   border border-[#0056b3]
                                   bg-white px-5 py-2.5
                                   text-sm font-medium
                                   text-[#0056b3]
                                   transition hover:bg-blue-50"
                        >

                            <span class="material-symbols-rounded">
                                open_in_new
                            </span>

                            Abrir

                        </a>

                    </div>

                </div>

            </div>

        @endif


        {{-- ====================================================== --}}
        {{-- CREAR NUEVO ENLACE --}}
        {{-- ====================================================== --}}

        <div
            class="rounded-2xl border
                   border-[#0056b3]/15
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
                        add_link
                    </span>

                    <h2
                        class="font-['Josefin_Sans']
                               font-semibold text-[#003d82]"
                    >
                        Generar nuevo enlace
                    </h2>

                </div>

            </div>


            <form
                method="POST"
                action="{{ route('enlaces.store') }}"
                class="grid grid-cols-1 gap-5 p-5
                       md:grid-cols-3"
            >

                @csrf


                {{-- VEHÍCULO --}}

                <div>

                    <label
                        for="vehiculo_id"
                        class="mb-1.5 block text-xs
                               font-semibold uppercase
                               text-gray-600"
                    >
                        Vehículo
                    </label>

                    <select
                        name="vehiculo_id"
                        id="vehiculo_id"
                        required
                        class="w-full rounded-xl
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
                                    · {{ $vehiculo->placas }}
                                @endif
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- DURACIÓN --}}

                <div>

                    <label
                        for="duracion_horas"
                        class="mb-1.5 block text-xs
                               font-semibold uppercase
                               text-gray-600"
                    >
                        Duración
                    </label>

                    <select
                        name="duracion_horas"
                        id="duracion_horas"
                        required
                        class="w-full rounded-xl
                               border-gray-300
                               text-sm
                               focus:border-[#0056b3]
                               focus:ring-[#0056b3]"
                    >

                        <option
                            value="1"
                            @selected(
                                old('duracion_horas')
                                ==
                                1
                            )
                        >
                            1 hora
                        </option>

                        <option
                            value="3"
                            @selected(
                                old('duracion_horas')
                                ==
                                3
                            )
                        >
                            3 horas
                        </option>

                        <option
                            value="6"
                            @selected(
                                old('duracion_horas')
                                ==
                                6
                            )
                        >
                            6 horas
                        </option>

                        <option
                            value="12"
                            @selected(
                                old('duracion_horas')
                                ==
                                12
                            )
                        >
                            12 horas
                        </option>

                        <option
                            value="24"
                            @selected(
                                old('duracion_horas', 24)
                                ==
                                24
                            )
                        >
                            24 horas
                        </option>

                        <option
                            value="48"
                            @selected(
                                old('duracion_horas')
                                ==
                                48
                            )
                        >
                            2 días
                        </option>

                        <option
                            value="72"
                            @selected(
                                old('duracion_horas')
                                ==
                                72
                            )
                        >
                            3 días
                        </option>

                        <option
                            value="168"
                            @selected(
                                old('duracion_horas')
                                ==
                                168
                            )
                        >
                            7 días
                        </option>

                    </select>

                </div>


                {{-- BOTÓN --}}

                <div class="flex items-end">

                    <button
                        type="submit"
                        class="inline-flex w-full
                               items-center justify-center
                               gap-2 rounded-xl
                               bg-[#0056b3]
                               px-5 py-2.5
                               text-sm font-medium
                               text-white transition
                               hover:bg-[#004494]"
                    >

                        <span class="material-symbols-rounded">
                            link
                        </span>

                        Generar enlace

                    </button>

                </div>

            </form>

        </div>


        {{-- ====================================================== --}}
        {{-- LISTADO DE ENLACES --}}
        {{-- ====================================================== --}}

        <div
            class="overflow-hidden rounded-2xl
                   border border-[#0056b3]/15
                   bg-white shadow-sm"
        >

            <div
                class="flex items-center justify-between
                       border-b border-gray-100
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
                                   font-semibold
                                   text-[#003d82]"
                        >
                            Enlaces generados
                        </h2>

                        <p class="text-xs text-gray-500">
                            Consulta los enlaces vigentes y vencidos.
                        </p>

                    </div>

                </div>


                <span
                    class="rounded-full border
                           border-blue-100
                           bg-white px-3 py-1
                           text-xs font-medium
                           text-[#0056b3]"
                >
                    {{ $enlaces->count() }} enlaces
                </span>

            </div>


            @if($enlaces->isEmpty())

                <div class="px-5 py-12 text-center">

                    <span
                        class="material-symbols-rounded
                               text-5xl text-gray-300"
                    >
                        link_off
                    </span>

                    <h3
                        class="mt-3 font-['Josefin_Sans']
                               font-semibold text-gray-700"
                    >
                        No hay enlaces generados
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Genera un enlace temporal para compartir la ubicación de un vehículo.
                    </p>

                </div>

            @else

                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead class="bg-gray-50">

                            <tr
                                class="text-left text-xs
                                       font-semibold uppercase
                                       tracking-wide text-gray-500"
                            >

                                <th class="px-5 py-3">
                                    Vehículo
                                </th>

                                <th class="px-5 py-3">
                                    Estado
                                </th>

                                <th class="px-5 py-3">
                                    Generado
                                </th>

                                <th class="px-5 py-3">
                                    Expiración
                                </th>

                                <th class="px-5 py-3 text-right">
                                    Acciones
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-100">

                            @foreach($enlaces as $enlace)

                                @php

                                    $vigente =
                                        $enlace->estaVigente();

                                    $url =
                                        route(
                                            'seguimiento.publico',
                                            $enlace->token
                                        );

                                @endphp


                                <tr class="hover:bg-gray-50/70">

                                    {{-- VEHÍCULO --}}

                                    <td class="px-5 py-4">

                                        <div
                                            class="font-medium
                                                   text-gray-800"
                                        >
                                            {{ $enlace->vehiculo?->nombre ?? 'Vehículo eliminado' }}
                                        </div>

                                        @if($enlace->vehiculo?->placas)

                                            <div
                                                class="mt-0.5 text-xs
                                                       text-gray-500"
                                            >
                                                {{ $enlace->vehiculo->placas }}
                                            </div>

                                        @endif

                                    </td>


                                    {{-- ESTADO --}}

                                    <td class="px-5 py-4">

                                        @if($vigente)

                                            <span
                                                class="inline-flex
                                                       items-center gap-1.5
                                                       rounded-full
                                                       border
                                                       border-emerald-200
                                                       bg-emerald-50
                                                       px-2.5 py-1
                                                       text-xs font-medium
                                                       text-emerald-700"
                                            >

                                                <span
                                                    class="h-1.5 w-1.5
                                                           rounded-full
                                                           bg-emerald-500"
                                                ></span>

                                                Vigente

                                            </span>

                                        @else

                                            <span
                                                class="inline-flex
                                                       items-center gap-1.5
                                                       rounded-full
                                                       border
                                                       border-gray-200
                                                       bg-gray-50
                                                       px-2.5 py-1
                                                       text-xs font-medium
                                                       text-gray-600"
                                            >

                                                <span
                                                    class="h-1.5 w-1.5
                                                           rounded-full
                                                           bg-gray-400"
                                                ></span>

                                                Expirado

                                            </span>

                                        @endif

                                    </td>


                                    {{-- GENERADO --}}

                                    <td
                                        class="px-5 py-4
                                               text-sm text-gray-600"
                                    >
                                        {{ $enlace->created_at?->format('d/m/Y H:i') ?? '—' }}
                                    </td>


                                    {{-- EXPIRACIÓN --}}

                                    <td
                                        class="px-5 py-4
                                               text-sm text-gray-600"
                                    >
                                        {{ $enlace->fecha_expiracion->format('d/m/Y H:i') }}
                                    </td>


                                    {{-- ACCIONES --}}

                                    <td class="px-5 py-4">

                                        <div
                                            class="flex justify-end gap-2"
                                        >

                                            @if($vigente)

                                                <button
                                                    type="button"
                                                    onclick='copiarEnlace(@json($url))'
                                                    title="Copiar enlace"
                                                    class="inline-flex h-9 w-9
                                                           items-center
                                                           justify-center
                                                           rounded-lg
                                                           border
                                                           border-gray-200
                                                           bg-white
                                                           text-gray-600
                                                           transition
                                                           hover:border-blue-200
                                                           hover:bg-blue-50
                                                           hover:text-[#0056b3]"
                                                >

                                                    <span
                                                        class="material-symbols-rounded
                                                               text-[19px]"
                                                    >
                                                        content_copy
                                                    </span>

                                                </button>


                                                <a
                                                    href="{{ $url }}"
                                                    target="_blank"
                                                    title="Abrir seguimiento"
                                                    class="inline-flex h-9 w-9
                                                           items-center
                                                           justify-center
                                                           rounded-lg
                                                           border
                                                           border-gray-200
                                                           bg-white
                                                           text-gray-600
                                                           transition
                                                           hover:border-blue-200
                                                           hover:bg-blue-50
                                                           hover:text-[#0056b3]"
                                                >

                                                    <span
                                                        class="material-symbols-rounded
                                                               text-[19px]"
                                                    >
                                                        open_in_new
                                                    </span>

                                                </a>

                                            @endif


                                            <form
                                                method="POST"
                                                action="{{ route('enlaces.destroy', $enlace) }}"
                                                onsubmit="return confirm('¿Deseas cancelar este enlace de seguimiento?');"
                                            >

                                                @csrf
                                                @method('DELETE')


                                                <button
                                                    type="submit"
                                                    title="Cancelar enlace"
                                                    class="inline-flex h-9 w-9
                                                           items-center
                                                           justify-center
                                                           rounded-lg
                                                           border
                                                           border-red-200
                                                           bg-white
                                                           text-red-500
                                                           transition
                                                           hover:bg-red-50"
                                                >

                                                    <span
                                                        class="material-symbols-rounded
                                                               text-[19px]"
                                                    >
                                                        delete
                                                    </span>

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>


    <script>

        function copiarEnlaceGenerado()
        {
            const input =
                document.getElementById(
                    'enlace-generado'
                );


            if (!input) {
                return;
            }


            copiarEnlace(
                input.value
            );
        }


        async function copiarEnlace(
            enlace
        ) {

            try {

                await navigator.clipboard.writeText(
                    enlace
                );


                mostrarMensajeCopiado();

            } catch (error) {

                const temporal =
                    document.createElement(
                        'textarea'
                    );


                temporal.value =
                    enlace;


                document.body.appendChild(
                    temporal
                );


                temporal.select();


                document.execCommand(
                    'copy'
                );


                temporal.remove();


                mostrarMensajeCopiado();
            }
        }


        function mostrarMensajeCopiado()
        {
            const aviso =
                document.createElement(
                    'div'
                );


            aviso.className =
                'fixed bottom-6 right-6 z-[9999] flex items-center gap-2 rounded-xl bg-[#003d82] px-4 py-3 text-sm font-medium text-white shadow-lg';


            aviso.innerHTML =
                `
                    <span class="material-symbols-rounded text-[19px]">
                        check_circle
                    </span>

                    Enlace copiado
                `;


            document.body.appendChild(
                aviso
            );


            setTimeout(
                function () {

                    aviso.remove();

                },
                2200
            );
        }

    </script>

</x-app-layout>