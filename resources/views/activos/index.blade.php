<x-app-layout>
    <!-- Estado global para el modal de Crear -->
    <div x-data="{ openCreate: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }} }">
        
        <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded-2xl">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Directorio de Activos</h2>
                    <p class="text-xs text-gray-500 mt-0.5">El núcleo del rastreo. Aquí nacen las unidades, personas o cajas que serán monitoreadas.</p>
                </div>
                <button @click="openCreate = true" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors shadow-sm">
                    + Nuevo Activo
                </button>
            </div>
            
            @if(session('success'))
                <div class="mx-4 mt-4 px-4 py-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-100">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mx-4 mt-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100">
                    {{ session('error') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">Activo Identificador</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Propietario / Flotilla</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Hardware GPS</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Vehículo Asignado</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($activos as $activo)
                            <tr x-data="{ openEdit: {{ $errors->any() && old('is_edit') == $activo->id ? 'true' : 'false' }}, openShow: false }" class="hover:bg-gray-50 transition-colors bg-white">
                                
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $activo->nombre }}</div>
                                    <div class="text-xs text-gray-500 capitalize flex items-center gap-1 mt-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                        {{ str_replace('_', ' ', $activo->tipo) }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-start gap-1.5">
                                        <!-- Propietario (Doble Arquitectura SaaS) -->
                                        @if($activo->empresa)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase tracking-wider" title="Pertenece a un corporativo">
                                                {{ $activo->empresa->nombre }}
                                            </span>
                                        @elseif($activo->usuario)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700 border border-green-100 uppercase tracking-wider" title="Pertenece a un cliente particular">
                                                {{ $activo->usuario->nombre }} (Particular)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200 uppercase tracking-wider">
                                                SOTyTECH (Interno)
                                            </span>
                                        @endif

                                        <!-- Flotilla -->
                                        @if($activo->flotilla)
                                            <span class="text-xs text-gray-500 font-medium border-l-2 border-gray-300 pl-2">
                                                Flotilla: {{ $activo->flotilla->nombre }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($activo->dispositivo)
                                        <span class="inline-flex items-center text-xs font-medium text-green-700 bg-green-50 px-2 py-1 rounded-md border border-green-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                            {{ $activo->dispositivo->imei ?? 'GPS Conectado' }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Sin GPS</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @if($activo->vehiculo)
                                        <span class="inline-flex items-center text-xs font-medium text-blue-700 bg-blue-50 px-2 py-1 rounded-md border border-blue-100">
                                            {{ $activo->vehiculo->placas ?? ($activo->vehiculo->marca . ' ' . $activo->vehiculo->modelo) }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">No es vehículo / Sin perfilar</span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1">
                                        <button @click="openShow = true" title="Ver detalles" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>

                                        <button @click="openEdit = true" title="Editar Activo" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        
                                        <form action="{{ route('activos.destroy', $activo) }}" method="POST" class="inline-block" onsubmit="return confirm('ATENCIÓN: Borrar un Activo es una acción destructiva profunda. ¿Estás seguro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Eliminar Activo" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Modales inyectados -->
                                    @include('activos.show', ['activo' => $activo])
                                    @include('activos.edit', ['activo' => $activo])
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 bg-white">
                                    No hay activos registrados en tu cuenta.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($activos->hasPages())
                <div class="p-4 border-t border-gray-100 bg-white">
                    {{ $activos->links() }}
                </div>
            @endif
        </div>

        @include('activos.create')
    </div>
</x-app-layout>