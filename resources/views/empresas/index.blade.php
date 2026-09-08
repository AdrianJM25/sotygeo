<x-app-layout>
    <!-- Estado global para el modal de Crear -->
    <div x-data="{ openCreate: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }} }">

        <!-- Tarjeta Principal -->
        <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded-2xl">
            <!-- Cabecera -->
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">
                        Gestión de Empresas (SaaS)
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Administra los clientes corporativos y particulares de la plataforma.</p>
                </div>
                
                <button @click="openCreate = true" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors shadow-sm">
                    + Nueva Empresa
                </button>
            </div>

            @if(session('success'))
                <div class="mx-4 mt-4 px-4 py-3 rounded-lg bg-green-50 text-green-700 text-sm border border-green-100">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">Empresa / Cliente</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Contacto</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Métricas</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Estado</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($empresas as $empresa)
                            <tr x-data="{ openEdit: {{ $errors->any() && old('is_edit') == $empresa->id ? 'true' : 'false' }}, openShow: false }" class="hover:bg-gray-50 transition-colors bg-white">

                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $empresa->nombre }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5 font-mono">RFC: {{ $empresa->rfc ?? 'N/D' }}</div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="text-gray-900">{{ $empresa->telefono ?? 'Sin teléfono' }}</div>
                                    <div class="text-xs text-indigo-600 mt-0.5">{{ $empresa->correo ?? 'Sin correo' }}</div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100" title="Usuarios vinculados">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            {{ $empresa->usuarios_count ?? 0 }} Usuarios
                                        </span>
                                        <!-- CAMBIO APLICADO AQUÍ -->
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100" title="Vehículos vinculados">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                                            {{ $empresa->vehiculos_count ?? 0 }} Vehículos
                                        </span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    @if($empresa->is_active)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-green-50 text-green-700 text-xs font-medium border border-green-100">Servicio Activo</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-red-50 text-red-700 text-xs font-medium border border-red-100">Suspendido</span>
                                    @endif
                                </td>

                                <!-- CELDA DE ACCIONES -->
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1">
                                        <!-- Botón Ver Detalles (Show) -->
                                        <button @click="openShow = true" title="Ver detalles de la cuenta" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>

                                        <!-- Botón Editar -->
                                        <button @click="openEdit = true" title="Editar empresa" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>

                                        <!-- Eliminar permanentemente -->
                                        <form action="{{ route('empresas.destroy', $empresa) }}" method="POST" class="inline-block"
                                              onsubmit="return confirm('ATENCIÓN: Eliminar esta empresa borrará TODOS sus usuarios, vehículos y zonas asociadas permanentemente. ¿Estás seguro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Eliminar empresa" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Modales DENTRO del TD -->
                                    @include('empresas.show', ['empresa' => $empresa])
                                    @include('empresas.edit', ['empresa' => $empresa])
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 bg-white">
                                    No hay empresas ni clientes corporativos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($empresas->hasPages())
                <div class="p-4 border-t border-gray-100 bg-white">
                    {{ $empresas->links() }}
                </div>
            @endif
        </div>

        <!-- Inyectamos el modal de creación -->
        @include('empresas.create')

    </div>
</x-app-layout>