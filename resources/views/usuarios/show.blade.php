¿¿<!-- ================= MODAL DE DETALLES (SHOW) ================= -->
<div x-show="openShow" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 text-left">
            
    <div x-show="openShow" x-transition.opacity @click="openShow = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>

    <div x-show="openShow"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
         class="relative bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-2xl overflow-hidden z-50">

        <!-- Cabecera del Modal -->
        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h2 class="text-lg font-bold text-gray-800">Detalles del Usuario</h2>
            <button type="button" @click="openShow = false" class="text-gray-400 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg p-1.5 transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Contenido -->
        <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
            
            <!-- Perfil / Cabecera Interna -->
            <div class="flex items-center gap-5">
                <div class="w-20 h-20 rounded-full bg-gray-300 text-white flex items-center justify-center text-2xl font-bold shrink-0 overflow-hidden shadow-md border-4 border-white ring-1 ring-gray-100">
                    @if($usuario->avatar_url)
                        <img src="{{ $usuario->avatar_url }}" class="w-full h-full object-cover" alt="Foto de perfil">
                    @else
                        {{ mb_strtoupper(mb_substr($usuario->nombre, 0, 1) . mb_substr($usuario->apellido_paterno, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $usuario->nombre }} {{ $usuario->apellido_paterno }} {{ $usuario->apellido_materno }}</h3>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-100">
                            {{ $usuario->roles->first()->name ?? 'Sin rol' }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md {{ $usuario->activo ? 'bg-green-50 text-green-700 border-green-100' : 'bg-red-50 text-red-700 border-red-100' }} text-xs font-semibold border">
                            {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 text-xs font-semibold border border-gray-200">
                            {{ $usuario->empresa->nombre ?? 'SOTyTECH (Interno)' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Información Personal -->
            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Información de Contacto</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                    <div>
                        <span class="block text-xs text-gray-500">Correo Electrónico</span>
                        <span class="text-sm font-medium text-gray-900">{{ $usuario->email }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500">Teléfono Móvil</span>
                        <span class="text-sm font-medium text-gray-900">{{ $usuario->telefono ?? 'No registrado' }}</span>
                    </div>
                </div>
            </div>

            <!-- Información de Dirección -->
            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Ubicación y Dirección</h3>
                <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 space-y-3">
                    @if($usuario->domicilio)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <span class="block text-xs text-gray-500">Calle y Número</span>
                                <span class="text-sm font-medium text-gray-900">{{ $usuario->domicilio->calle ?? 'N/D' }} #{{ $usuario->domicilio->numero_exterior ?? '' }} {{ $usuario->domicilio->numero_interior ? 'Int. ' . $usuario->domicilio->numero_interior : '' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-500">Colonia / Asentamiento</span>
                                <span class="text-sm font-medium text-gray-900">{{ $usuario->domicilio->colonia ?? 'N/D' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-500">Ciudad y Estado</span>
                                <span class="text-sm font-medium text-gray-900">{{ $usuario->domicilio->ciudad ?? 'N/D' }}, {{ $usuario->domicilio->estado ?? 'N/D' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs text-gray-500">Código Postal</span>
                                <span class="text-sm font-medium text-gray-900">{{ $usuario->domicilio->codigo_postal ?? 'N/D' }}</span>
                            </div>
                            @if($usuario->domicilio->referencias)
                            <div class="sm:col-span-2">
                                <span class="block text-xs text-gray-500">Referencias</span>
                                <span class="text-sm font-medium text-gray-900">{{ $usuario->domicilio->referencias }}</span>
                            </div>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-gray-500 italic">No hay registros de dirección asociados a este usuario.</p>
                    @endif
                </div>
            </div>

        </div>

        <!-- Pie del Modal -->
        <div class="flex justify-end p-4 border-t border-gray-100 bg-gray-50/50">
            <button type="button" @click="openShow = false" class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</div>