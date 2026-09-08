<!-- ================= MODAL DE DETALLES DE EMPRESA (SHOW) ================= -->
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
            <h2 class="text-lg font-bold text-gray-800">Detalles de la Cuenta</h2>
            <button type="button" @click="openShow = false" class="text-gray-400 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg p-1.5 transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Contenido -->
        <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
            
            <!-- Información Corporativa -->
            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Información Corporativa</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                    <div class="sm:col-span-2">
                        <span class="block text-xs text-gray-500">Razón Social / Nombre</span>
                        <span class="text-base font-medium text-gray-900">{{ $empresa->nombre }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500">RFC</span>
                        <span class="text-sm font-medium text-gray-900 font-mono">{{ $empresa->rfc ?? 'No registrado' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500">Teléfono de Contacto</span>
                        <span class="text-sm font-medium text-gray-900">{{ $empresa->telefono ?? 'No registrado' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500">Correo Electrónico</span>
                        <span class="text-sm font-medium text-gray-900">{{ $empresa->correo ?? 'No registrado' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500">Estado del Servicio</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md {{ $empresa->is_active ? 'bg-green-50 text-green-700 border-green-100' : 'bg-red-50 text-red-700 border-red-100' }} text-xs font-medium border mt-0.5">
                            {{ $empresa->is_active ? 'Activo (Con acceso)' : 'Suspendido' }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-xs text-gray-500">Fecha de Registro</span>
                        <span class="text-sm font-medium text-gray-900">{{ $empresa->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Métricas y Uso del Sistema -->
            <div>
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Uso de la Plataforma (SaaS)</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <!-- Tarjeta Usuarios -->
                    <div class="bg-white p-3 rounded-xl border border-gray-200 text-center shadow-sm">
                        <span class="block text-xl font-bold text-gray-800">{{ $empresa->usuarios()->count() }}</span>
                        <span class="block text-xs text-gray-500 mt-1">Usuarios</span>
                    </div>
                    <!-- Tarjeta Vehículos (CAMBIO APLICADO AQUÍ) -->
                    <div class="bg-white p-3 rounded-xl border border-gray-200 text-center shadow-sm">
                        <span class="block text-xl font-bold text-gray-800">{{ $empresa->vehiculos()->count() }}</span>
                        <span class="block text-xs text-gray-500 mt-1">Vehículos</span>
                    </div>
                    <!-- Tarjeta GPS -->
                    <div class="bg-white p-3 rounded-xl border border-gray-200 text-center shadow-sm">
                        <span class="block text-xl font-bold text-gray-800">{{ $empresa->dispositivos()->count() }}</span>
                        <span class="block text-xs text-gray-500 mt-1">Equipos GPS</span>
                    </div>
                    <!-- Tarjeta Flotillas -->
                    <div class="bg-white p-3 rounded-xl border border-gray-200 text-center shadow-sm">
                        <span class="block text-xl font-bold text-gray-800">{{ $empresa->flotillas()->count() }}</span>
                        <span class="block text-xs text-gray-500 mt-1">Flotillas</span>
                    </div>
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