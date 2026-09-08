<x-app-layout>
    <div x-data="{ openCreate: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }} }">
        
        <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded-2xl">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Directorio de Vehículos</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Administra tu flotilla y asigna el perfil a cada unidad.</p>
                </div>
                <button @click="openCreate = true" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                    + Nuevo Vehículo
                </button>
            </div>
            
            @if(session('success'))
                <div class="mx-4 mt-4 px-4 py-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-100">
                    {{ session('success') }}
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
                            <tr x-data="{ openEdit: {{ $errors->any() && old('is_edit') == $vehiculo->id ? 'true' : 'false' }}, openShow: false }" class="hover:bg-gray-50 transition-colors bg-white">
                                
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $vehiculo->nombre }}</div>
                                    <div class="text-xs text-gray-500 capitalize mt-0.5">{{ $vehiculo->tipo_vehiculo }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-start gap-1">
                                        @if($vehiculo->empresa)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase tracking-wider">
                                                {{ $vehiculo->empresa->nombre }}
                                            </span>
                                        @elseif($vehiculo->usuario)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700 border border-green-100 uppercase tracking-wider">
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
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium border bg-green-50 text-green-700 border-green-200">
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
                                        
                                        <!-- NUEVO BOTÓN: Ver Historial de Ruta -->
                                        <!-- Solo se muestra si el vehículo tiene un GPS asignado -->
                                        @if($vehiculo->dispositivo)
                                            <a href="{{ route('vehiculos.ruta', $vehiculo->id) }}" title="Ver historial de ruta en mapa" class="p-1.5 text-blue-600 hover:text-blue-900 hover:bg-blue-50 rounded-lg transition-colors focus:outline-none">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                                            </a>
                                        @endif

                                        <!-- Ver Detalles (Show) -->
                                        <button @click="openShow = true" title="Ver detalles" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>

                                        <!-- Editar -->
                                        <button @click="openEdit = true" title="Editar vehículo" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        
                                        <!-- Eliminar -->
                                        <form action="{{ route('vehiculos.destroy', $vehiculo) }}" method="POST" class="inline-block" onsubmit="return confirm('ATENCIÓN: Se eliminará todo el historial de este vehículo. ¿Estás seguro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Eliminar vehículo" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Modales DENTRO del TD -->
                                    @include('vehiculos.show', ['vehiculo' => $vehiculo])
                                    @include('vehiculos.edit', ['vehiculo' => $vehiculo])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 bg-white">
                                    No hay vehículos registrados en este momento.
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