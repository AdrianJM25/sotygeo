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

    <div x-data="{
            openCreate: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }},
            busqueda: ''
         }">

        <!-- Tarjeta Principal -->
        <div class="bg-white overflow-hidden shadow-sm border border-gray-200 rounded-2xl">

            <!-- Cabecera -->
            <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500 text-white flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 leading-tight">Gestión de Usuarios y Roles</h2>
                        <p class="text-xs text-gray-400">{{ $users->total() }} {{ Str::plural('usuario', $users->total()) }} registrados</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <!-- Buscador (filtra la página actual) -->
                    <div class="relative flex-1 sm:flex-none sm:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"></path></svg>
                        </span>
                        <input type="text" x-model="busqueda" placeholder="Buscar por nombre, correo o rol..."
                               class="w-full pl-9 pr-3 py-2 text-sm bg-gray-50 border border-gray-300 rounded-lg focus:ring-gray-900 focus:border-gray-900 transition-colors">
                    </div>

                    <button @click="openCreate = true" class="shrink-0 flex items-center gap-1.5 px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                        Nuevo Usuario
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
                            <th scope="col" class="px-6 py-4 font-semibold">Usuario</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Contacto</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Rol / Empresa</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Estado</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($users as $usuario)
                            @php
                                $nombreCompleto = trim("{$usuario->nombre} {$usuario->apellido_paterno} {$usuario->apellido_materno}");
                                $iniciales = mb_strtoupper(mb_substr($usuario->nombre, 0, 1) . mb_substr($usuario->apellido_paterno, 0, 1));
                                $rolNombre = $usuario->roles->first()->name ?? null;
                                $avatarColores = [
                                    'Super Administrador' => 'bg-rose-500',
                                    'Administrador de Empresa' => 'bg-blue-500',
                                    'Gestor de flotilla' => 'bg-orange-500',
                                    'Cliente Individual' => 'bg-cyan-500',
                                    'Conductor' => 'bg-violet-500',
                                ];
                                $avatarColor = $avatarColores[$rolNombre] ?? 'bg-gray-400';
                                $busquedaTexto = mb_strtolower("{$nombreCompleto} {$usuario->email} {$rolNombre}");
                            @endphp

                            <tr x-data="{ openEdit: {{ $errors->any() && old('is_edit') == $usuario->id ? 'true' : 'false' }}, openShow: false }"
                                x-show="busqueda === '' || @js($busquedaTexto).includes(busqueda.toLowerCase())"
                                style="animation-delay: {{ $loop->index * 40 }}ms"
                                class="row-in hover:bg-gray-50 transition-colors bg-white relative">

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <!-- AQUÍ SE MUESTRA LA FOTO O INICIALES -->
                                        <div class="w-9 h-9 rounded-full {{ $avatarColor }} text-white flex items-center justify-center text-xs font-bold shrink-0 overflow-hidden shadow-sm">
                                            @if($usuario->avatar_url)
                                                <img src="{{ $usuario->avatar_url }}" class="w-full h-full object-cover" alt="{{ $nombreCompleto }}">
                                            @else
                                                {{ $iniciales }}
                                            @endif
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $nombreCompleto }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-900">{{ $usuario->email }}</div>
                                    <div class="text-xs text-gray-500">{{ $usuario->telefono ?? 'Sin teléfono' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-start gap-1">
                                        <x-role-badge :rol="$rolNombre" />
                                        @if($usuario->empresa)
                                            <span class="text-[10px] font-medium text-gray-400">{{ $usuario->empresa->nombre }}</span>
                                        @else
                                            <span class="text-[10px] font-medium text-indigo-400">SOTyTECH (Interno)</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <x-estado-badge :activo="$usuario->activo" />
                                </td>

                                <!-- CELDA DE ACCIONES -->
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1">
                                        <button @click="openShow = true" title="Ver detalles" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all hover:scale-105">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>

                                        <button @click="openEdit = true" title="Editar usuario" class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-all hover:scale-105">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>

                                        @if($usuario->id !== auth()->id())
                                            <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="inline-block"
                                                  onsubmit="return confirm('{{ $usuario->activo ? '¿Desactivar a este usuario? No podrá iniciar sesión.' : '¿Reactivar a este usuario?' }}');">
                                                @csrf
                                                @method('DELETE')
                                                @if($usuario->activo)
                                                    <button type="submit" title="Desactivar" class="p-1.5 text-amber-500 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition-all hover:scale-105">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                    </button>
                                                @else
                                                    <button type="submit" title="Activar" class="p-1.5 text-green-500 hover:text-green-700 hover:bg-green-50 rounded-lg transition-all hover:scale-105">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    </button>
                                                @endif
                                            </form>

                                            @role('Super Administrador|Administrador de Empresa')
                                                <form action="{{ route('usuarios.eliminar', $usuario) }}" method="POST" class="inline-block"
                                                      onsubmit="return confirm('Esta acción eliminará al usuario de forma PERMANENTE. ¿Continuar?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Eliminar permanentemente" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all hover:scale-105">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @endrole
                                        @endif
                                    </div>

                                    <!-- TELEPORT PARA SACAR LOS MODALES DE LA TABLA Y EVITAR SUPERPOSICIÓN -->
                                    <template x-teleport="body">
                                        <div>
                                            @include('usuarios.show', ['usuario' => $usuario])
                                            @include('usuarios.edit', ['usuario' => $usuario, 'roles' => $roles, 'empresas' => $empresas])
                                        </div>
                                    </template>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center bg-white">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                        </div>
                                        <p class="text-gray-500 text-sm">Aún no hay usuarios registrados.</p>
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

            @if($users->hasPages())
                <div class="p-4 border-t border-gray-100 bg-white">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <template x-teleport="body">
            @include('usuarios.create', ['roles' => $roles, 'empresas' => $empresas])
        </template>

    </div>
</x-app-layout>