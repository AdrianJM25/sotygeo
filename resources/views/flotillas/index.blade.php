<x-app-layout>
    <!-- Estado global para el modal de Crear -->
    <div x-data="{ openCreate: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }} }">
        
        <!-- Tarjeta Principal -->
        <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded-2xl">
            <!-- Cabecera -->
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h2 class="text-lg font-bold text-gray-800">
                    Gestión de Flotillas
                </h2>
                <!-- Botón que abre el modal de Crear -->
                <button @click="openCreate = true" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                    + Nueva Flotilla
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

            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">Flotilla / Empresa</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Descripción</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Responsable</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Activos</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($flotillas as $flotilla)
                            <!-- Estado local para el modal de Editar específico de esta fila -->
                            <!-- Estado local para los modales específicos de esta fila -->
<tr x-data="{ openEdit: {{ $errors->any() && old('is_edit') == $flotilla->id ? 'true' : 'false' }}, openShow: false }" class="hover:bg-gray-50 transition-colors bg-white">
    
    <td class="px-6 py-4">
        <div class="font-medium text-gray-900">{{ $flotilla->nombre }}</div>
        <div class="mt-0.5">
            @if($flotilla->empresa)
                <span class="text-[10px] uppercase font-semibold text-gray-400 tracking-wider">
                    {{ $flotilla->empresa->nombre }}
                </span>
            @else
                <span class="text-[10px] uppercase font-semibold text-indigo-400 tracking-wider">
                    SOTyTECH (Interno)
                </span>
            @endif
        </div>
    </td>
    
    <td class="px-6 py-4 text-gray-500 max-w-xs truncate">{{ $flotilla->descripcion ?? 'Sin descripción' }}</td>
    
    <td class="px-6 py-4">
        @if($flotilla->usuario)
            <span class="inline-flex items-center px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-medium border border-blue-100">
                {{ $flotilla->usuario->nombre }} {{ $flotilla->usuario->apellido_paterno }}
            </span>
        @else
            <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-50 text-gray-500 text-xs font-medium border border-gray-200">
                Sin asignar
            </span>
        @endif
    </td>
    
    <td class="px-6 py-4">
        <span class="text-gray-900 font-semibold">{{ $flotilla->activos->count() }}</span> activos
    </td>
    
    <!-- Celda de Acciones con Iconos -->
    <td class="px-6 py-4 text-right whitespace-nowrap">
        <div class="inline-flex items-center space-x-1">
            <!-- Botón Ver Detalles (Show) -->
            <button @click="openShow = true" title="Ver detalles de flotilla" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            </button>

            <!-- Botón Editar -->
            <button @click="openEdit = true" title="Editar flotilla" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </button>
            
            <!-- Eliminar -->
            <form action="{{ route('flotillas.destroy', $flotilla) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Seguro que deseas eliminar esta flotilla? Los activos dentro de ella no se borrarán, solo quedarán sin flotilla asignada.');">
                @csrf
                @method('DELETE')
                <button type="submit" title="Eliminar flotilla" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </form>
        </div>

        <!-- Inyectamos los modales DENTRO del <td> -->
        @include('flotillas.show', ['flotilla' => $flotilla])
        @include('flotillas.edit', ['flotilla' => $flotilla, 'users' => $users, 'empresas' => $empresas])
    </td>
</tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 bg-white">
                                    No hay flotillas registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($flotillas->hasPages())
                <div class="p-4 border-t border-gray-100 bg-white">
                    {{ $flotillas->links() }}
                </div>
            @endif
        </div>

        <!-- Inyectamos el modal de creación al final, enviando la variable $empresas -->
        @include('flotillas.create', ['users' => $users, 'empresas' => $empresas])

    </div>
</x-app-layout>