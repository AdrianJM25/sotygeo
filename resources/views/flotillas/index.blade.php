¿<x-app-layout>
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

    <!-- Estado global para el modal de Crear y Búsqueda -->
    <div x-data="{ 
            openCreate: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }},
            busqueda: ''
         }">
        
        <!-- Tarjeta Principal -->
        <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded-2xl">
            
            <!-- Cabecera -->
            <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 leading-tight">Gestión de Flotillas</h2>
                        <p class="text-xs text-gray-400">{{ $flotillas->total() }} {{ Str::plural('flotilla', $flotillas->total()) }} registradas</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <!-- Buscador (filtra la página actual) -->
                    <div class="relative flex-1 sm:flex-none sm:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path></svg>
                        </span>
                        <input type="text" x-model="busqueda" placeholder="Buscar flotilla o gestor..."
                               class="w-full pl-9 pr-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:ring-gray-900 focus:border-gray-900 transition-colors">
                    </div>

                    <!-- Botón Nueva Flotilla -->
                    <button @click="openCreate = true" class="shrink-0 flex items-center gap-1.5 px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                        Nueva Flotilla
                    </button>
                </div>
            </div>
            
            <!-- Alertas -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="toast-in mx-4 mt-4 flex items-center justify-between px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ session('success') }}
                    </span>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="toast-in mx-4 mt-4 flex items-center justify-between px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-100">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path></svg>
                        {{ session('error') }}
                    </span>
                    <button @click="show = false" class="text-red-500 hover:text-red-700">&times;</button>
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
                            <th scope="col" class="px-6 py-4 font-semibold">Vehículos</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($flotillas as $flotilla)
                            @php
                                $gestorNombre = $flotilla->usuario ? $flotilla->usuario->nombre . ' ' . $flotilla->usuario->apellido_paterno : 'Sin asignar';
                                $empresaNombre = $flotilla->empresa ? $flotilla->empresa->nombre : 'SOTyTECH (Interno)';
                                $busquedaTexto = mb_strtolower("{$flotilla->nombre} {$flotilla->descripcion} {$gestorNombre} {$empresaNombre}");
                            @endphp

                            <tr x-data="{ openEdit: {{ $errors->any() && old('is_edit') == $flotilla->id ? 'true' : 'false' }}, openShow: false }" 
                                x-show="busqueda === '' || @js($busquedaTexto).includes(busqueda.toLowerCase())"
                                style="animation-delay: {{ $loop->index * 40 }}ms"
                                class="row-in hover:bg-gray-50 transition-colors bg-white relative">
                                
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
                                
                                <td class="px-6 py-4 text-gray-500 max-w-xs truncate" title="{{ $flotilla->descripcion }}">
                                    {{ $flotilla->descripcion ?? 'Sin descripción' }}
                                </td>
                                
                                <td class="px-6 py-4">
                                    @if($flotilla->usuario)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-medium border border-blue-100">
                                            {{ $gestorNombre }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-50 text-gray-500 text-xs font-medium border border-gray-200">
                                            Sin asignar
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4">
                                    <span class="text-gray-900 font-semibold">{{ $flotilla->vehiculos->count() }}</span> unidades
                                </td>
                                
                                <!-- Celda de Acciones con Iconos -->
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1">
                                        <!-- Botón Ver Detalles (Show) -->
                                        <button @click="openShow = true" title="Ver detalles de flotilla" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none hover:scale-105">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>

                                        <!-- Botón Editar -->
                                        <button @click="openEdit = true" title="Editar flotilla" class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors focus:outline-none hover:scale-105">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        
                                        <!-- Eliminar -->
                                        <form action="{{ route('flotillas.destroy', $flotilla) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Seguro que deseas eliminar esta flotilla? Los vehículos dentro de ella no se borrarán, solo quedarán sin flotilla asignada.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Eliminar flotilla" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors hover:scale-105">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- TELEPORT PARA LOS MODALES DE ESTA FILA -->
                                    <template x-teleport="body">
                                        <div>
                                            @include('flotillas.show', ['flotilla' => $flotilla])
                                            @include('flotillas.edit', ['flotilla' => $flotilla, 'users' => $users, 'empresas' => $empresas])
                                        </div>
                                    </template>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center text-gray-500 bg-white">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        </div>
                                        <p class="text-gray-500 text-sm">Aún no hay flotillas registradas.</p>
                                        <button @click="openCreate = true" class="text-sm font-medium text-gray-900 underline underline-offset-2 hover:text-gray-700">
                                            Crea la primera flotilla
                                        </button>
                                    </div>
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

        <!-- Inyectamos el modal de creación al final mediante teleport -->
        <template x-teleport="body">
            @include('flotillas.create', ['users' => $users, 'empresas' => $empresas])
        </template>

    </div>
</x-app-layout>