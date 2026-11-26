<x-app-layout>
    <style>

        .font-heading {
            font-family: 'Josefin Sans', sans-serif;
        }

        .font-body {
            font-family: 'Ubuntu', sans-serif;
        }

        @keyframes rowIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .row-in {
            animation: rowIn 0.35s ease-out both;
        }

        .vehiculo-modal.is-open {
            display: flex !important;
        }

        @media (prefers-reduced-motion: reduce) {
            .row-in {
                animation: none;
                opacity: 1;
                transform: none;
            }
        }
    </style>


    <div class="font-body">
        <div class="bg-white overflow-hidden shadow-sm shadow-black/5 border border-[#0056b3]/20 rounded-2xl">

            <div
                class="p-5 sm:p-6 flex flex-col
                       gap-4 bg-[#0056b3]
                       lg:flex-row lg:items-center
                       lg:justify-between"
            >

                <div>

                    <h2
                        class="font-heading text-xl
                               sm:text-2xl font-semibold
                               tracking-tight text-white"
                    >
                        Gestión de vehículos
                    </h2>

                    <p class="mt-1 text-sm text-blue-100">
                        Administra las unidades, su propietario, flotilla, ficha técnica y equipo GPS asignado.
                    </p>

                </div>


                <div
                    class="flex w-full flex-col gap-2
                           sm:flex-row sm:items-center
                           lg:w-auto"
                >

                    <div class="relative w-full sm:w-72">

                        <span
                            class="absolute inset-y-0 left-0
                                   flex items-center pl-3
                                   text-[#0056b3]/70"
                        >
                            <svg
                                class="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"
                                ></path>
                            </svg>
                        </span>

                        <input
                            id="vehiculos-busqueda"
                            type="text"
                            placeholder="Buscar unidad, placas o marca..."
                            autocomplete="off"
                            class="w-full rounded-xl
                                   border-white/80 bg-white
                                   py-2.5 pl-9 pr-3
                                   text-sm text-gray-700
                                   shadow-sm
                                   placeholder:text-gray-400
                                   focus:border-white
                                   focus:ring-2 focus:ring-white"
                        >

                    </div>


                    <button
                        type="button"
                        data-open-vehiculo-modal="vehiculo-create-modal"
                        class="inline-flex shrink-0
                               items-center justify-center
                               gap-2 rounded-xl bg-white
                               px-4 py-2.5 text-sm
                               font-medium text-[#0056b3]
                               shadow-sm shadow-black/20
                               transition-all
                               hover:bg-blue-50
                               active:scale-[0.98]
                               focus-visible:outline-none
                               focus-visible:ring-2
                               focus-visible:ring-white
                               focus-visible:ring-offset-2
                               focus-visible:ring-offset-[#0056b3]"
                    >
                        <svg
                            class="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="2.5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 4.5v15m7.5-7.5h-15"
                            ></path>
                        </svg>

                        Nuevo vehículo
                    </button>

                </div>

            </div>


            @if(session('success'))
                <div
                    data-toast
                    class="mx-5 sm:mx-6 mt-5 flex items-center justify-between px-4 py-3 rounded-xl bg-emerald-50 text-emerald-800 text-sm border border-emerald-200"
                >
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>

                        {{ session('success') }}
                    </span>

                    <button
                        type="button"
                        data-close-toast
                        class="text-emerald-500 hover:text-emerald-700"
                    >
                        &times;
                    </button>
                </div>
            @endif


            @if($errors->any())
                <div class="mx-5 sm:mx-6 mt-5 px-4 py-3 rounded-xl bg-red-50 text-red-700 text-sm border border-red-200">
                    <div class="font-semibold mb-1">
                        Revisa los datos del formulario.
                    </div>

                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700">

                    <thead class="text-xs text-[#0056b3] bg-blue-50/60 border-b border-[#0056b3]/15">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Identificación / Alias</th>
                            <th class="px-6 py-4 font-semibold">Propietario / Flotilla</th>
                            <th class="px-6 py-4 font-semibold">Ficha Técnica</th>
                            <th class="px-6 py-4 font-semibold">GPS Asignado</th>
                            <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>


                    <tbody class="divide-y divide-[#0056b3]/10">

                        @forelse($vehiculos as $vehiculo)

                            @php
                                $colorIcono =
                                    $vehiculo->color_icono
                                    ??
                                    '#111827';

                                $busquedaTexto =
                                    mb_strtolower(
                                        trim(
                                            "{$vehiculo->nombre} "
                                            .
                                            "{$vehiculo->placas} "
                                            .
                                            "{$vehiculo->marca} "
                                            .
                                            "{$vehiculo->modelo} "
                                            .
                                            "{$vehiculo->tipo_vehiculo}"
                                        )
                                    );
                            @endphp


                            <tr
                                data-vehiculo-row
                                data-search="{{ $busquedaTexto }}"
                                style="animation-delay: {{ $loop->index * 40 }}ms"
                                class="row-in hover:bg-blue-50/30 transition-colors bg-white"
                            >

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">

                                        <div class="relative w-9 h-9 shrink-0">
                                            <div
                                                class="absolute inset-0 rounded-full opacity-25"
                                                style="background: {{ $colorIcono }};"
                                            ></div>

                                            <div
                                                class="absolute inset-[3px] rounded-full bg-gray-900 flex items-center justify-center overflow-hidden"
                                                style="border: 2px solid {{ $colorIcono }};"
                                            >
                                                @if($vehiculo->icono_url)
                                                    <img
                                                        src="{{ $vehiculo->icono_url }}"
                                                        class="w-4 h-4 object-contain"
                                                        alt=""
                                                    >
                                                @endif
                                            </div>
                                        </div>


                                        <div>
                                            <div class="font-semibold text-[#003d82]">
                                                {{ $vehiculo->nombre }}
                                            </div>

                                            <div class="text-xs text-gray-500 mt-0.5 capitalize">
                                                {{ str_replace('_', ' ', $vehiculo->tipo_vehiculo) }}
                                            </div>
                                        </div>

                                    </div>
                                </td>


                                <td class="px-6 py-4">

                                    <div class="flex flex-col items-start gap-1">

                                        @if($vehiculo->empresa)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-[#0056b3]/10 text-[#003d82] border border-[#0056b3]/15">
                                                {{ $vehiculo->empresa->nombre }}
                                            </span>

                                        @elseif($vehiculo->usuario)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-blue-50 text-[#0056b3] border border-blue-100">
                                                {{ $vehiculo->usuario->nombre }} (Particular)
                                            </span>

                                        @else
                                            <span class="text-xs text-gray-400 italic">
                                                SOTyTECH (Interno)
                                            </span>
                                        @endif


                                        @if($vehiculo->flotilla)
                                            <span class="text-xs text-gray-500 font-medium">
                                                Flotilla: {{ $vehiculo->flotilla->nombre }}
                                            </span>
                                        @endif

                                    </div>
                                </td>


                                <td class="px-6 py-4">
                                    <div class="text-[#003d82] font-medium">
                                        {{ $vehiculo->placas ?? 'Sin placas' }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $vehiculo->marca ?? 'N/D' }}
                                        {{ $vehiculo->modelo ?? '' }}
                                        ({{ $vehiculo->anio ?? '-' }})
                                    </div>

                                    <button
                                        type="button"
                                        data-open-vehiculo-modal="vehiculo-corte-{{ $vehiculo->id }}"
                                        class="inline-flex items-center gap-1 text-[11px] text-[#0056b3] font-semibold mt-1 hover:text-[#003d82] bg-blue-50 px-2 py-0.5 rounded-lg border border-[#0056b3]/15 transition-colors"
                                    >
                                        Corte de ruta:
                                        {{ $vehiculo->horas_corte_ruta ?? 24 }}h
                                    </button>
                                </td>


                                <td class="px-6 py-4">
                                    @if($vehiculo->dispositivo)
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium border bg-emerald-50 text-emerald-700 border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            IMEI: {{ $vehiculo->dispositivo->imei }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium border bg-gray-50 text-gray-500 border-gray-200">
                                            Pendiente Enlace GPS
                                        </span>
                                    @endif
                                </td>


                                <td class="px-6 py-4 text-right whitespace-nowrap">

                                    <div class="inline-flex items-center space-x-1">

                                        @if($vehiculo->dispositivo)
                                            <a
                                                href="{{ route('vehiculos.ruta', $vehiculo->id) }}"
                                                title="Ver historial de ruta"
                                                class="p-1.5 text-[#0056b3] hover:text-[#003d82] hover:bg-[#0056b3]/10 rounded-lg transition-colors"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                                </svg>
                                            </a>
                                        @endif


                                        <button
                                            type="button"
                                            data-open-vehiculo-modal="vehiculo-show-{{ $vehiculo->id }}"
                                            title="Ver detalles"
                                            class="p-1.5 text-gray-500 hover:text-[#0056b3] hover:bg-[#0056b3]/10 rounded-lg transition-colors"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>


                                        <button
                                            type="button"
                                            data-open-vehiculo-modal="vehiculo-edit-{{ $vehiculo->id }}"
                                            title="Editar vehículo"
                                            class="p-1.5 text-[#0056b3] hover:text-[#003d82] hover:bg-[#0056b3]/10 rounded-lg transition-colors"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>


                                        <form
                                            action="{{ route('vehiculos.destroy', $vehiculo) }}"
                                            method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('ATENCIÓN: Se eliminará todo el historial y las rutas de este vehículo. ¿Estás seguro?');"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                title="Eliminar vehículo"
                                                class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>

                                    </div>


                                    <!-- MODAL VER -->
                                    <div
                                        id="vehiculo-show-{{ $vehiculo->id }}"
                                        class="vehiculo-modal fixed inset-0 z-[120] hidden items-center justify-center p-4 text-left"
                                        aria-hidden="true"
                                    >
                                        <div
                                            class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"
                                            data-close-vehiculo-modal
                                        ></div>

                                        <div class="relative z-10 bg-white rounded-2xl shadow-xl border border-[#0056b3]/20 w-full max-w-2xl overflow-hidden">

                                            <div class="p-4 flex justify-between items-center bg-[#0056b3]">
                                                <h2 class="font-heading text-lg font-semibold text-white">
                                                    Ficha Técnica del Vehículo
                                                </h2>

                                                <button
                                                    type="button"
                                                    data-close-vehiculo-modal
                                                    class="text-white/80 hover:text-white hover:bg-white/10 rounded-lg p-1.5 transition-colors"
                                                >
                                                    ✕
                                                </button>
                                            </div>


                                            <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">

                                                <div class="bg-[#003d82] rounded-xl p-4 flex items-center justify-between gap-4">

                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="w-11 h-11 rounded-full bg-gray-800 flex items-center justify-center overflow-hidden"
                                                            style="border: 2px solid {{ $colorIcono }};"
                                                        >
                                                            @if($vehiculo->icono_url)
                                                                <img
                                                                    src="{{ $vehiculo->icono_url }}"
                                                                    class="w-6 h-6 object-contain"
                                                                    alt=""
                                                                >
                                                            @endif
                                                        </div>

                                                        <div>
                                                            <h3 class="text-white text-xl font-bold">
                                                                {{ $vehiculo->nombre }}
                                                            </h3>

                                                            <span class="text-gray-400 text-sm">
                                                                {{ str_replace('_', ' ', $vehiculo->tipo_vehiculo) }}
                                                            </span>
                                                        </div>
                                                    </div>


                                                    <div class="text-right">
                                                        <span class="block text-gray-400 text-xs uppercase mb-1">
                                                            Propietario
                                                        </span>

                                                        <span class="text-white text-sm">
                                                            @if($vehiculo->empresa)
                                                                {{ $vehiculo->empresa->nombre }}
                                                            @elseif($vehiculo->usuario)
                                                                {{ $vehiculo->usuario->nombre }}
                                                            @else
                                                                SOTyTECH
                                                            @endif
                                                        </span>
                                                    </div>

                                                </div>


                                                <div class="grid grid-cols-2 gap-4 bg-blue-50/40 p-4 rounded-xl border border-[#0056b3]/15">

                                                    <div>
                                                        <span class="block text-xs text-gray-500">Marca</span>
                                                        <span class="text-sm font-semibold text-gray-900">{{ $vehiculo->marca ?? 'N/D' }}</span>
                                                    </div>

                                                    <div>
                                                        <span class="block text-xs text-gray-500">Modelo</span>
                                                        <span class="text-sm font-semibold text-gray-900">{{ $vehiculo->modelo ?? 'N/D' }}</span>
                                                    </div>

                                                    <div>
                                                        <span class="block text-xs text-gray-500">Año</span>
                                                        <span class="text-sm font-semibold text-gray-900">{{ $vehiculo->anio ?? 'N/D' }}</span>
                                                    </div>

                                                    <div>
                                                        <span class="block text-xs text-gray-500">Color</span>
                                                        <span class="text-sm font-semibold text-gray-900">{{ $vehiculo->color ?? 'N/D' }}</span>
                                                    </div>

                                                    <div>
                                                        <span class="block text-xs text-gray-500">Placas</span>
                                                        <span class="text-sm font-semibold text-gray-900">{{ $vehiculo->placas ?? 'No registradas' }}</span>
                                                    </div>

                                                    <div>
                                                        <span class="block text-xs text-gray-500">VIN</span>
                                                        <span class="text-sm font-semibold text-gray-900">{{ $vehiculo->vin ?? 'No registrado' }}</span>
                                                    </div>

                                                    <div>
                                                        <span class="block text-xs text-gray-500">Flotilla</span>
                                                        <span class="text-sm font-semibold text-gray-900">{{ $vehiculo->flotilla->nombre ?? 'Independiente' }}</span>
                                                    </div>

                                                    <div>
                                                        <span class="block text-xs text-gray-500">GPS</span>
                                                        <span class="text-sm font-semibold text-gray-900">
                                                            {{ $vehiculo->dispositivo->imei ?? 'Sin GPS asignado' }}
                                                        </span>
                                                    </div>

                                                </div>

                                            </div>


                                            <div class="flex justify-end p-4 border-t border-[#0056b3]/15 bg-gray-50">
                                                <button
                                                    type="button"
                                                    data-close-vehiculo-modal
                                                    class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                                                >
                                                    Cerrar
                                                </button>
                                            </div>

                                        </div>
                                    </div>


                                    <!-- MODAL CORTE DE RUTA -->
                                    <div
                                        id="vehiculo-corte-{{ $vehiculo->id }}"
                                        class="vehiculo-modal fixed inset-0 z-[120] hidden items-center justify-center p-4 text-left"
                                        aria-hidden="true"
                                    >
                                        <div
                                            class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"
                                            data-close-vehiculo-modal
                                        ></div>

                                        <div class="relative z-10 bg-white rounded-2xl shadow-xl border border-[#0056b3]/20 max-w-sm w-full p-6">

                                            <div class="flex items-center justify-between mb-4">
                                                <h3 class="text-base font-bold text-gray-900">
                                                    Periodo de Registro de Ruta
                                                </h3>

                                                <button
                                                    type="button"
                                                    data-close-vehiculo-modal
                                                    class="text-gray-400 hover:text-gray-600"
                                                >
                                                    ✕
                                                </button>
                                            </div>


                                            <p class="text-xs text-gray-500 mb-5 leading-relaxed">
                                                Define cada cuántas horas se segmentará el historial de ruta de
                                                <span class="font-semibold text-gray-800">
                                                    {{ $vehiculo->nombre }}
                                                </span>.
                                            </p>


                                            <form
                                                action="{{ route('vehiculos.actualizar-corte', $vehiculo->id) }}"
                                                method="POST"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <div class="mb-5">
                                                    <label class="block text-xs font-semibold text-gray-700 uppercase mb-2">
                                                        Horas por segmento
                                                    </label>

                                                    <select
                                                        name="horas_corte_ruta"
                                                        class="w-full text-sm border-gray-300 rounded-xl focus:ring-[#0056b3] focus:border-[#0056b3]"
                                                    >
                                                        @foreach([6, 12, 24, 48, 72, 168] as $horas)
                                                            <option
                                                                value="{{ $horas }}"
                                                                {{ ($vehiculo->horas_corte_ruta ?? 24) == $horas ? 'selected' : '' }}
                                                            >
                                                                Cada {{ $horas }} horas
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>


                                                <div class="flex justify-end gap-2">
                                                    <button
                                                        type="button"
                                                        data-close-vehiculo-modal
                                                        class="px-4 py-2 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200"
                                                    >
                                                        Cancelar
                                                    </button>

                                                    <button
                                                        type="submit"
                                                        class="px-4 py-2 text-xs font-medium text-white bg-[#0056b3] rounded-xl hover:bg-[#003d82] transition-colors"
                                                    >
                                                        Guardar Cambios
                                                    </button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>


                                    @include('vehiculos.edit', ['vehiculo' => $vehiculo])

                                </td>
                            </tr>


                        @empty

                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center bg-white">
                                    <div class="flex flex-col items-center gap-3">

                                        <div class="w-12 h-12 rounded-full bg-blue-50 text-[#0056b3]/50 flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>

                                        <p class="text-gray-500 text-sm">
                                            No hay vehículos registrados en este momento.
                                        </p>

                                        <button
                                            type="button"
                                            data-open-vehiculo-modal="vehiculo-create-modal"
                                            class="text-sm font-medium text-[#0056b3] underline underline-offset-2 hover:text-[#003d82]"
                                        >
                                            Registra el primero
                                        </button>

                                    </div>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>


            @if($vehiculos->hasPages())
                <div class="p-4 border-t border-[#0056b3]/15 bg-white">
                    {{ $vehiculos->links() }}
                </div>
            @endif

        </div>


        @include('vehiculos.create')

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /*
            |--------------------------------------------------------------------------
            | MOVER MODALES AL BODY
            |--------------------------------------------------------------------------
            | Las filas de la tabla usan animación con transform. Un ancestro con
            | transform cambia el contexto de position: fixed y hace que el modal
            | quede atrapado dentro de la tabla. Moviéndolos al body recuperan el
            | comportamiento de modal de pantalla completa.
            |--------------------------------------------------------------------------
            */

            document
                .querySelectorAll('.vehiculo-modal')
                .forEach(function (modal) {
                    document.body.appendChild(modal);
                });


            function abrirModal(id) {
                const modal =
                    document.getElementById(id);

                if (!modal) {
                    return;
                }

                modal.classList.add('is-open');
                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');

                document.body.style.overflow =
                    'hidden';
            }


            function cerrarModal(modal) {
                if (!modal) {
                    return;
                }

                modal.classList.remove('is-open');
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');

                if (!document.querySelector('.vehiculo-modal.is-open')) {
                    document.body.style.overflow =
                        '';
                }
            }


            document
                .querySelectorAll('[data-open-vehiculo-modal]')
                .forEach(function (boton) {

                    boton.addEventListener(
                        'click',
                        function () {

                            abrirModal(
                                boton.dataset.openVehiculoModal
                            );
                        }
                    );
                });


            document
                .querySelectorAll('[data-close-vehiculo-modal]')
                .forEach(function (boton) {

                    boton.addEventListener(
                        'click',
                        function () {

                            cerrarModal(
                                boton.closest('.vehiculo-modal')
                            );
                        }
                    );
                });


            document.addEventListener(
                'keydown',
                function (event) {

                    if (event.key !== 'Escape') {
                        return;
                    }

                    document
                        .querySelectorAll('.vehiculo-modal.is-open')
                        .forEach(cerrarModal);
                }
            );


            /* Buscador */
            const buscar =
                document.getElementById('vehiculos-busqueda');

            const filas =
                document.querySelectorAll('[data-vehiculo-row]');

            if (buscar) {

                buscar.addEventListener(
                    'input',
                    function () {

                        const texto =
                            buscar.value
                                .toLowerCase()
                                .trim();

                        filas.forEach(
                            function (fila) {

                                const contenido =
                                    (
                                        fila.dataset.search
                                        ||
                                        ''
                                    )
                                    .toLowerCase();

                                fila.style.display =
                                    contenido.includes(texto)
                                        ? ''
                                        : 'none';
                            }
                        );
                    }
                );
            }


            /* Propietario empresa / particular */
            document
                .querySelectorAll('[data-owner-group]')
                .forEach(function (grupo) {

                    const radios =
                        grupo.querySelectorAll('[data-owner-type]');

                    const empresa =
                        grupo.querySelector('[data-owner-section="empresa"]');

                    const particular =
                        grupo.querySelector('[data-owner-section="particular"]');

                    const selectEmpresa =
                        grupo.querySelector('[data-owner-select="empresa"]');

                    const selectParticular =
                        grupo.querySelector('[data-owner-select="particular"]');


                    function actualizar(tipo) {

                        if (empresa) {
                            empresa.classList.toggle(
                                'hidden',
                                tipo !== 'empresa'
                            );
                        }

                        if (particular) {
                            particular.classList.toggle(
                                'hidden',
                                tipo !== 'particular'
                            );
                        }

                        if (selectEmpresa) {
                            selectEmpresa.disabled =
                                tipo !== 'empresa';

                            if (tipo !== 'empresa') {
                                selectEmpresa.value = '';
                            }
                        }

                        if (selectParticular) {
                            selectParticular.disabled =
                                tipo !== 'particular';

                            if (tipo !== 'particular') {
                                selectParticular.value = '';
                            }
                        }
                    }


                    radios.forEach(
                        function (radio) {

                            radio.addEventListener(
                                'change',
                                function () {

                                    if (radio.checked) {
                                        actualizar(
                                            radio.value
                                        );
                                    }
                                }
                            );
                        }
                    );


                    const seleccionado =
                        grupo.querySelector('[data-owner-type]:checked');

                    actualizar(
                        seleccionado
                            ? seleccionado.value
                            : 'empresa'
                    );
                });


            /* Toast */
            document
                .querySelectorAll('[data-close-toast]')
                .forEach(function (boton) {

                    boton.addEventListener(
                        'click',
                        function () {

                            boton
                                .closest('[data-toast]')
                                ?.remove();
                        }
                    );
                });


            setTimeout(
                function () {

                    document
                        .querySelector('[data-toast]')
                        ?.remove();
                },
                4000
            );


            /* Reabrir el formulario correcto si hubo validación */
            @if($errors->any())
                @if(old('is_edit'))
                    abrirModal(
                        'vehiculo-edit-{{ (int) old('is_edit') }}'
                    );
                @else
                    abrirModal(
                        'vehiculo-create-modal'
                    );
                @endif
            @endif

        });
    </script>

</x-app-layout>
