<x-app-layout>
    <style>
        @keyframes rowIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .row-in { animation: rowIn 0.35s ease-out both; }

        @keyframes toastIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .toast-in { animation: toastIn 0.3s ease-out both; }

        @media (prefers-reduced-motion: reduce) {
            .row-in, .toast-in { animation: none; opacity: 1; transform: none; }
        }
    </style>

    <div x-data="{ openCreate: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }}, busqueda: '' }">

        <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded-2xl">
            <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 leading-tight">Directorio de Vehículos</h2>
                        <p class="text-xs text-gray-500">Administra tu flotilla y personaliza cómo se ve cada unidad en el mapa.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <div class="relative flex-1 sm:flex-none sm:w-56">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path></svg>
                        </span>
                        <input type="text" x-model="busqueda" placeholder="Buscar unidad, placas o marca..."
                               class="w-full pl-9 pr-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:ring-gray-900 focus:border-gray-900 transition-colors">
                    </div>
                    <button @click="openCreate = true" class="shrink-0 flex items-center gap-1.5 px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                        Nuevo Vehículo
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="toast-in mx-4 mt-4 flex items-center justify-between px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ session('success') }}
                    </span>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">&times;</button>
                </div>
            @endif
            @if($errors->has('flotilla_id'))
                <div class="toast-in mx-4 mt-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100">
                    {{ $errors->first('flotilla_id') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">Identificación / Alias</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Propietario / Flotilla</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Ficha Técnica</th>
                            <th scope="col" class="px-6 py-4 font-semibold">GPS Asignado</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($vehiculos as $vehiculo)
                            @php
                                $iconoData = \App\Models\Vehiculo::ICONOS[$vehiculo->icono] ?? \App\Models\Vehiculo::ICONOS['sedan'];
                                $colorIcono = $vehiculo->color_icono ?? '#111827';
                                $busquedaTexto = mb_strtolower("{$vehiculo->nombre} {$vehiculo->placas} {$vehiculo->marca} {$vehiculo->modelo} {$vehiculo->tipo_vehiculo}");
                            @endphp
                            <tr x-data="{ openEdit: {{ $errors->any() && old('is_edit') == $vehiculo->id ? 'true' : 'false' }}, openShow: false }"
                                x-show="busqueda === '' || @js($busquedaTexto).includes(busqueda.toLowerCase())"
                                style="animation-delay: {{ $loop->index * 40 }}ms"
                                class="row-in hover:bg-gray-50 transition-colors bg-white">

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <!-- Marcador en miniatura: mismo ícono/color que verá en el mapa -->
                                        <div class="relative w-9 h-9 shrink-0">
                                            <div class="absolute inset-0 rounded-full opacity-25" style="background: {{ $colorIcono }};"></div>
                                            <div class="absolute inset-[3px] rounded-full bg-gray-900 flex items-center justify-center" style="border: 2px solid {{ $colorIcono }};">
                                                <svg class="w-3.5 h-3.5" fill="white" viewBox="0 0 24 24">{!! $iconoData['svg'] !!}</svg>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ $vehiculo->nombre }}</div>
                                            <div class="text-xs text-gray-500 mt-0.5">{{ $iconoData['label'] }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-start gap-1">
                                        @if($vehiculo->empresa)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                {{ $vehiculo->empresa->nombre }}
                                            </span>
                                        @elseif($vehiculo->usuario)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-cyan-50 text-cyan-700 border border-cyan-100">
                                                {{ $vehiculo->usuario->nombre }} (Particular)
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">SOTyTECH (Interno)</span>
                                        @endif

                                        @if($vehiculo->flotilla)
                                            <span class="text-xs text-gray-500 font-medium">Flotilla: {{ $vehiculo->flotilla->nombre }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-gray-900 font-medium">{{ $vehiculo->placas ?? 'Sin placas' }}</div>
                                    <div class="text-xs text-gray-500">{{ $vehiculo->marca ?? 'N/D' }} {{ $vehiculo->modelo ?? '' }} ({{ $vehiculo->anio ?? '-' }})</div>
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
                                            <a href="{{ route('vehiculos.ruta', $vehiculo->id) }}" title="Ver historial de ruta en mapa" class="p-1.5 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-all hover:scale-105 focus:outline-none">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                            </a>
                                        @endif

                                        <button @click="openShow = true" title="Ver detalles" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all hover:scale-105 focus:outline-none">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>

                                        <button @click="openEdit = true" title="Editar vehículo" class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-all hover:scale-105 focus:outline-none">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>

                                        <form action="{{ route('vehiculos.destroy', $vehiculo) }}" method="POST" class="inline-block" onsubmit="return confirm('ATENCIÓN: Se eliminará todo el historial de este vehículo. ¿Estás seguro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Eliminar vehículo" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all hover:scale-105">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>

                                    @include('vehiculos.show', ['vehiculo' => $vehiculo])
                                    @include('vehiculos.edit', ['vehiculo' => $vehiculo])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center bg-white">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                                        </div>
                                        <p class="text-gray-500 text-sm">No hay vehículos registrados en este momento.</p>
                                        <button @click="openCreate = true" class="text-sm font-medium text-gray-900 underline underline-offset-2 hover:text-gray-700">
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
                <div class="p-4 border-t border-gray-100 bg-white">
                    {{ $vehiculos->links() }}
                </div>
            @endif
        </div>

        @include('vehiculos.create')
    </div>
</x-app-layout>