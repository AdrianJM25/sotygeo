<x-app-layout>

    <div
        x-data="{
            openCreate: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }}
        }"
        class="font-['Ubuntu']"
    >

        <div
            class="overflow-hidden rounded-2xl
                   border border-[#0056b3]/20
                   bg-white shadow-sm shadow-black/5"
        >

            {{-- CABECERA --}}
            <div
                class="flex flex-col gap-4 bg-[#0056b3] p-5
                       sm:flex-row sm:items-center sm:justify-between sm:p-6"
            >

                <div>
                    <h2
                        class="font-['Josefin_Sans']
                               text-xl font-semibold
                               tracking-tight text-white sm:text-2xl"
                    >
                        Gestión de equipos GPS
                    </h2>

                    <p class="mt-1 text-sm text-blue-100">
                        Administra los dispositivos GPS y su vinculación con los vehículos.
                    </p>
                </div>

                <button
                    type="button"
                    @click="openCreate = true"
                    class="inline-flex items-center justify-center gap-2
                           rounded-xl bg-white px-4 py-2.5
                           text-sm font-medium text-[#0056b3]
                           shadow-sm transition-all hover:bg-blue-50
                           active:scale-[0.98]"
                >
                    <span class="material-symbols-rounded text-[20px]">
                        add_location_alt
                    </span>

                    Nuevo GPS
                </button>

            </div>


            {{-- MENSAJE SUCCESS --}}
            @if(session('success'))

                <div
                    class="mx-5 mt-5 flex items-center gap-2
                           rounded-xl border border-[#0056b3]/30
                           bg-blue-50 px-4 py-3
                           text-sm text-[#003d82] sm:mx-6"
                >
                    <span class="material-symbols-rounded text-[18px]">
                        check_circle
                    </span>

                    {{ session('success') }}
                </div>

            @endif


            {{-- MENSAJE ERROR --}}
            @if(session('error'))

                <div
                    class="mx-5 mt-5 flex items-center gap-2
                           rounded-xl border border-red-200
                           bg-red-50 px-4 py-3
                           text-sm text-red-700 sm:mx-6"
                >
                    <span class="material-symbols-rounded text-[18px]">
                        error
                    </span>

                    {{ session('error') }}
                </div>

            @endif


            {{-- ERRORES DE VALIDACIÓN --}}
            @if($errors->any())

                <div
                    class="mx-5 mt-5 rounded-xl
                           border border-red-200 bg-red-50
                           px-4 py-3 text-sm text-red-700 sm:mx-6"
                >

                    <div class="mb-2 flex items-center gap-2 font-medium">
                        <span class="material-symbols-rounded text-[18px]">
                            error
                        </span>

                        Revisa los datos ingresados.
                    </div>

                    <ul class="ml-6 list-disc space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>

            @endif


            {{-- TABLA --}}
            <div class="overflow-x-auto">

                <table class="w-full text-left text-sm text-gray-700">

                    <thead
                        class="border-b border-[#0056b3]/15
                               bg-blue-50/60 text-xs text-[#0056b3]"
                    >
                        <tr>

                            <th class="px-6 py-4 font-semibold">
                                Identificador
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Hardware / SIM
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Propietario
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Vehículo
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Estado
                            </th>

                            <th class="px-6 py-4 text-right font-semibold">
                                Acciones
                            </th>

                        </tr>
                    </thead>


                    <tbody class="divide-y divide-[#0056b3]/10">

                        @forelse($dispositivos as $dispositivo)

                            <tr
                                x-data="{
                                    openEdit:
                                        {{ $errors->any() && old('is_edit') == $dispositivo->id ? 'true' : 'false' }},
                                    openShow: false
                                }"
                                class="bg-white transition-colors hover:bg-blue-50/30"
                            >

                                {{-- IMEI --}}
                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex h-10 w-10 shrink-0
                                                   items-center justify-center
                                                   rounded-lg bg-[#0056b3]/10
                                                   text-[#0056b3]"
                                        >
                                            <span class="material-symbols-rounded">
                                                gps_fixed
                                            </span>
                                        </div>

                                        <div>

                                            <div
                                                class="font-mono font-semibold
                                                       tracking-wide text-[#003d82]"
                                            >
                                                {{ $dispositivo->imei }}
                                            </div>

                                            <div class="mt-1 text-xs text-gray-500">

                                                @if($dispositivo->modo_reposo)

                                                    <span class="inline-flex items-center gap-1 text-amber-600">
                                                        <span class="material-symbols-rounded text-[14px]">
                                                            bedtime
                                                        </span>

                                                        Modo reposo
                                                    </span>

                                                @else

                                                    <span class="inline-flex items-center gap-1">
                                                        <span class="material-symbols-rounded text-[14px]">
                                                            settings
                                                        </span>

                                                        Modo normal
                                                    </span>

                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- HARDWARE --}}
                                <td class="px-6 py-4">

                                    <div class="font-medium text-gray-800">
                                        {{ $dispositivo->modelo ?? 'Sin modelo' }}
                                    </div>

                                    <div class="mt-1 text-xs text-gray-500">
                                        SIM:
                                        {{ $dispositivo->numero_sim ?? 'Sin SIM registrada' }}
                                    </div>

                                    @if($dispositivo->capacidad_bateria_mah)

                                        <div class="mt-1 text-xs text-gray-500">
                                            Batería:
                                            {{ number_format($dispositivo->capacidad_bateria_mah) }} mAh
                                        </div>

                                    @endif

                                </td>


                                {{-- PROPIETARIO --}}
                                <td class="px-6 py-4">

                                    @if($dispositivo->empresa)

                                        <span
                                            class="inline-flex items-center gap-1
                                                   rounded-lg bg-indigo-50 px-2.5 py-1
                                                   text-xs font-medium text-indigo-700"
                                        >
                                            <span class="material-symbols-rounded text-[15px]">
                                                domain
                                            </span>

                                            {{ $dispositivo->empresa->nombre }}
                                        </span>

                                    @elseif($dispositivo->usuario)

                                        <span
                                            class="inline-flex items-center gap-1
                                                   rounded-lg bg-emerald-50 px-2.5 py-1
                                                   text-xs font-medium text-emerald-700"
                                        >
                                            <span class="material-symbols-rounded text-[15px]">
                                                person
                                            </span>

                                            {{ $dispositivo->usuario->nombre }}
                                            {{ $dispositivo->usuario->apellido_paterno }}
                                        </span>

                                    @else

                                        <span
                                            class="inline-flex items-center gap-1
                                                   rounded-lg bg-gray-100 px-2.5 py-1
                                                   text-xs font-medium text-gray-600"
                                        >
                                            <span class="material-symbols-rounded text-[15px]">
                                                inventory_2
                                            </span>

                                            Stock SOTyTECH
                                        </span>

                                    @endif

                                </td>


                                {{-- VEHÍCULO --}}
                                <td class="px-6 py-4">

                                    @if($dispositivo->vehiculo)

                                        <span
                                            class="inline-flex items-center gap-1
                                                   rounded-lg bg-[#0056b3]/10
                                                   px-2.5 py-1
                                                   text-xs font-medium text-[#003d82]"
                                        >
                                            <span class="material-symbols-rounded text-[16px]">
                                                directions_car
                                            </span>

                                            {{ $dispositivo->vehiculo->nombre }}
                                        </span>

                                        @if($dispositivo->vehiculo->placas)

                                            <div class="mt-1 text-xs text-gray-500">
                                                {{ $dispositivo->vehiculo->placas }}
                                            </div>

                                        @endif

                                    @else

                                        <span class="text-xs italic text-gray-400">
                                            Sin vehículo asignado
                                        </span>

                                    @endif

                                </td>


                                {{-- ESTADO --}}
                                <td class="px-6 py-4">

                                    @if($dispositivo->activo)

                                        <span
                                            class="inline-flex items-center gap-1
                                                   rounded-full border border-emerald-200
                                                   bg-emerald-50 px-2.5 py-1
                                                   text-xs font-medium text-emerald-700"
                                        >
                                            <span class="material-symbols-rounded text-[15px]">
                                                check_circle
                                            </span>

                                            Activo
                                        </span>

                                    @else

                                        <span
                                            class="inline-flex items-center gap-1
                                                   rounded-full border border-gray-200
                                                   bg-gray-100 px-2.5 py-1
                                                   text-xs font-medium text-gray-600"
                                        >
                                            <span class="material-symbols-rounded text-[15px]">
                                                block
                                            </span>

                                            Inactivo
                                        </span>

                                    @endif

                                </td>


                                {{-- ACCIONES --}}
                                <td class="whitespace-nowrap px-6 py-4 text-right">

                                    <div class="inline-flex items-center gap-1">

                                        {{-- VER --}}
                                        <button
                                            type="button"
                                            @click="openShow = true"
                                            title="Ver detalles"
                                            class="rounded-lg p-2 text-gray-500
                                                   transition-colors
                                                   hover:bg-[#0056b3]/10
                                                   hover:text-[#0056b3]"
                                        >
                                            <span class="material-symbols-rounded text-[20px]">
                                                visibility
                                            </span>
                                        </button>


                                        {{-- EDITAR --}}
                                        <button
                                            type="button"
                                            @click="openEdit = true"
                                            title="Editar GPS"
                                            class="rounded-lg p-2 text-gray-500
                                                   transition-colors
                                                   hover:bg-[#0056b3]/10
                                                   hover:text-[#0056b3]"
                                        >
                                            <span class="material-symbols-rounded text-[20px]">
                                                edit
                                            </span>
                                        </button>


                                        {{-- ACTIVAR / DESACTIVAR --}}
                                        <form
                                            action="{{ route('dispositivos.estado', $dispositivo) }}"
                                            method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('¿Deseas cambiar el estado de este GPS?');"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                title="{{ $dispositivo->activo ? 'Desactivar GPS' : 'Activar GPS' }}"
                                                class="rounded-lg p-2 transition-colors
                                                       {{
                                                           $dispositivo->activo
                                                               ? 'text-amber-500 hover:bg-amber-50 hover:text-amber-700'
                                                               : 'text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700'
                                                       }}"
                                            >
                                                <span class="material-symbols-rounded text-[20px]">
                                                    power_settings_new
                                                </span>
                                            </button>

                                        </form>


                                        {{-- ELIMINAR --}}
                                        <form
                                            action="{{ route('dispositivos.destroy', $dispositivo) }}"
                                            method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('¿Seguro que deseas eliminar este GPS?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                title="Eliminar GPS"
                                                class="rounded-lg p-2 text-red-500
                                                       transition-colors
                                                       hover:bg-red-50 hover:text-red-700"
                                            >
                                                <span class="material-symbols-rounded text-[20px]">
                                                    delete
                                                </span>
                                            </button>

                                        </form>

                                    </div>


                                    @include(
                                        'dispositivos.show',
                                        [
                                            'dispositivo' => $dispositivo
                                        ]
                                    )


                                    @include(
                                        'dispositivos.edit',
                                        [
                                            'dispositivo' => $dispositivo,
                                            'vehiculos' => $vehiculos,
                                            'empresas' => $empresas,
                                            'clientes' => $clientes
                                        ]
                                    )

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td
                                    colspan="6"
                                    class="px-6 py-12 text-center text-gray-500"
                                >

                                    <span
                                        class="material-symbols-rounded
                                               mb-2 block text-[36px]
                                               text-[#0056b3]/40"
                                    >
                                        satellite_alt
                                    </span>

                                    No hay dispositivos GPS registrados.

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- PAGINACIÓN --}}
            @if($dispositivos->hasPages())

                <div class="border-t border-[#0056b3]/15 bg-white p-4">
                    {{ $dispositivos->links() }}
                </div>

            @endif

        </div>


        @include(
            'dispositivos.create',
            [
                'vehiculosDisponibles' => $vehiculosDisponibles,
                'empresas' => $empresas,
                'clientes' => $clientes
            ]
        )

    </div>

</x-app-layout>