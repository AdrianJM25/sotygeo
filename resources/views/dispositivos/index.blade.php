<x-app-layout>
    <div x-data="{ openCreate: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }} }">
        
        <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded-2xl">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Inventario de GPS</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Administra los equipos físicos y vincúlalos a tus vehículos.</p>
                </div>
                <button @click="openCreate = true" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                    + Nuevo GPS
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
                            <th scope="col" class="px-6 py-4 font-semibold">Identificador (IMEI)</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Hardware / SIM</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Propietario Legal</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Vehículo Vinculado</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($dispositivos as $dispositivo)
                            <tr x-data="{ openEdit: {{ $errors->any() && old('is_edit') == $dispositivo->id ? 'true' : 'false' }}, openShow: false }" class="hover:bg-gray-50 transition-colors bg-white">
                                
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 font-mono tracking-tight">{{ $dispositivo->imei }}</div>
                                    <div class="text-[10px] uppercase font-bold {{ $dispositivo->modo_reposo ? 'text-orange-500' : 'text-green-500' }} mt-0.5">
                                        {{ $dispositivo->modo_reposo ? 'Modo Reposo' : 'Transmitiendo' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-gray-900 font-medium">{{ $dispositivo->modelo ?? 'Modelo Genérico' }}</div>
                                    <div class="text-xs text-gray-500">SIM: {{ $dispositivo->numero_sim ?? 'Sin chip' }}</div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($dispositivo->empresa)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase tracking-wider">
                                            {{ $dispositivo->empresa->nombre }}
                                        </span>
                                    @elseif($dispositivo->usuario)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700 border border-green-100 uppercase tracking-wider">
                                            {{ $dispositivo->usuario->nombre }} (Particular)
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">SOTyTECH (Stock Interno)</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @if($dispositivo->vehiculo)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-medium border border-blue-100">
                                            {{ $dispositivo->vehiculo->nombre }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs italic">En caja (Sin asignar)</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1">
                                        <!-- Botón Show -->
                                        <button @click="openShow = true" title="Ver detalles" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>

                                        <!-- Botón Editar -->
                                        <button @click="openEdit = true" title="Editar GPS" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        
                                        <!-- Eliminar -->
                                        <form action="{{ route('dispositivos.destroy', $dispositivo) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Seguro que deseas eliminar este dispositivo del sistema?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Eliminar GPS" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Modales inyectados -->
                                    @include('dispositivos.show', ['dispositivo' => $dispositivo])
                                    @include('dispositivos.edit', ['dispositivo' => $dispositivo, 'vehiculos' => $vehiculos, 'empresas' => $empresas, 'clientes' => $clientes])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 bg-white">
                                    No hay dispositivos GPS registrados en el sistema.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($dispositivos->hasPages())
                <div class="p-4 border-t border-gray-100 bg-white">
                    {{ $dispositivos->links() }}
                </div>
            @endif
        </div>

        @include('dispositivos.create', ['vehiculos' => $vehiculos, 'empresas' => $empresas, 'clientes' => $clientes])

    </div>
</x-app-layout>