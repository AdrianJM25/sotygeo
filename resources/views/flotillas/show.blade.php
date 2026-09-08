<!-- ================= MODAL DE DETALLES (SHOW) ================= -->
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
            <h2 class="text-lg font-bold text-gray-800">Detalles de la Flotilla</h2>
            <button type="button" @click="openShow = false" class="text-gray-400 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg p-1.5 transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Contenido -->
        <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                
                <div class="sm:col-span-2">
                    <span class="block text-xs text-gray-500">Nombre de la Flotilla</span>
                    <span class="text-base font-bold text-gray-900">{{ $flotilla->nombre }}</span>
                </div>

                <div>
                    <span class="block text-xs text-gray-500">Empresa / Corporativo Dueño</span>
                    <span class="text-sm font-medium text-indigo-700">
                        {{ $flotilla->empresa->nombre ?? 'SOTyTECH (Interno)' }}
                    </span>
                </div>

                <div>
                    <span class="block text-xs text-gray-500">Usuario Responsable (Gestor)</span>
                    <span class="text-sm font-medium text-gray-900">
                        @if($flotilla->usuario)
                            {{ $flotilla->usuario->nombre }} {{ $flotilla->usuario->apellido_paterno }}
                        @else
                            <span class="text-gray-400 italic">Sin asignar</span>
                        @endif
                    </span>
                </div>

                <div>
                    <span class="block text-xs text-gray-500">Total de Activos / Vehículos</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-xs font-medium border border-blue-100 mt-0.5">
                        {{ $flotilla->activos->count() }} Unidades
                    </span>
                </div>

                <div>
                    <span class="block text-xs text-gray-500">Fecha de Creación</span>
                    <span class="text-sm font-medium text-gray-900">{{ $flotilla->created_at->format('d/m/Y') }}</span>
                </div>

                <div class="sm:col-span-2 mt-2 pt-2 border-t border-gray-200/60">
                    <span class="block text-xs text-gray-500 mb-1">Descripción / Notas operativas</span>
                    <p class="text-sm text-gray-800 bg-white p-3 rounded-lg border border-gray-200">
                        {{ $flotilla->descripcion ?? 'No se proporcionaron notas o descripciones para esta flotilla.' }}
                    </p>
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