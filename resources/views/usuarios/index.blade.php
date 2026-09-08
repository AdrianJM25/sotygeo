<x-app-layout>
    <!-- Estado global para el modal de Crear -->
    <div x-data="{ openCreate: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }} }">

        <!-- Tarjeta Principal -->
        <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded-2xl">
            <!-- Cabecera -->
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-white">
                <h2 class="text-lg font-bold text-gray-800">
                    Gestión de Usuarios y Roles
                </h2>
                <!-- Botón que abre el modal de Crear -->
                <button @click="openCreate = true" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
                    + Nuevo Usuario
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
                            <th scope="col" class="px-6 py-4 font-semibold">Nombre</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Contacto</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Rol / Empresa</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Estado</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($users as $usuario)
                            <tr x-data="{ openEdit: {{ $errors->any() && old('is_edit') == $usuario->id ? 'true' : 'false' }}, openShow: false }" class="hover:bg-gray-50 transition-colors bg-white">

                                <td class="px-6 py-4 font-medium text-gray-900">{{ $usuario->nombre }} {{ $usuario->apellido_paterno }} {{ $usuario->apellido_materno }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-900">{{ $usuario->email }}</div>
                                    <div class="text-xs text-gray-500">{{ $usuario->telefono ?? 'Sin teléfono' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-start gap-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-xs font-medium border border-blue-100">
                                            {{ $usuario->roles->first()->name ?? 'Sin rol' }}
                                        </span>
                                        <!-- Insignia de Empresa -->
                                        @if($usuario->empresa)
                                            <span class="text-[10px] uppercase font-semibold text-gray-400 tracking-wider">
                                                {{ $usuario->empresa->nombre }}
                                            </span>
                                        @else
                                            <span class="text-[10px] uppercase font-semibold text-indigo-400 tracking-wider">
                                                SOTyTECH (Interno)
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($usuario->activo)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-green-50 text-green-700 text-xs font-medium border border-green-100">Activo</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-red-50 text-red-700 text-xs font-medium border border-red-100">Inactivo</span>
                                    @endif
                                </td>

                                <!-- CELDA DE ACCIONES CON ICONOS -->
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1">
                                        <!-- Botón Ver Detalles (Show) -->
                                        <button @click="openShow = true" title="Ver detalles" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>

                                        <!-- Botón Editar -->
                                        <button @click="openEdit = true" title="Editar usuario" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>

                                        @if($usuario->id !== auth()->id())
                                            <!-- Activar / Desactivar -->
                                            <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="inline-block"
                                                  onsubmit="return confirm('{{ $usuario->activo ? '¿Desactivar a este usuario? No podrá iniciar sesión.' : '¿Reactivar a este usuario?' }}');">
                                                @csrf
                                                @method('DELETE')
                                                @if($usuario->activo)
                                                    <button type="submit" title="Desactivar" class="p-1.5 text-amber-500 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                    </button>
                                                @else
                                                    <button type="submit" title="Activar" class="p-1.5 text-green-500 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </button>
                                                @endif
                                            </form>

                                            <!-- Eliminar permanentemente -->
                                            @role('Super Administrador|Administrador de Empresa')
                                                <form action="{{ route('usuarios.eliminar', $usuario) }}" method="POST" class="inline-block"
                                                      onsubmit="return confirm('Esta acción eliminará al usuario de forma PERMANENTE. ¿Continuar?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Eliminar permanentemente" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @endrole
                                        @endif
                                    </div>

                                    <!-- Modales incluidos dentro del TD -->
                                    @include('usuarios.show', ['usuario' => $usuario])
                                    @include('usuarios.edit', ['usuario' => $usuario, 'roles' => $roles, 'empresas' => $empresas])
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 bg-white">
                                    No hay usuarios registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-4 border-t border-gray-100 bg-white">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- Inyectamos el modal de creación (Aquí le pasamos las variables) -->
        @include('usuarios.create', ['roles' => $roles, 'empresas' => $empresas])

    </div>
</x-app-layout>