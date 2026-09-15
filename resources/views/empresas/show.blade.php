<!-- ================= MODAL DE DETALLES DE EMPRESA (SHOW) ================= -->
<div x-show="openShow" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4 text-left">

    <div x-show="openShow" x-transition.opacity @click="openShow = false" class="fixed inset-0 bg-[#121314]/50 backdrop-blur-sm"></div>

    <div x-show="openShow"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 sm:scale-95"
         class="relative bg-white rounded-2xl shadow-xl shadow-black/10 border border-[#56928C]/20 w-full max-w-2xl overflow-hidden z-50 font-body">

        <!-- Cabecera del Modal -->
        <div class="p-4 border-b border-[#56928C]/20 flex justify-between items-center bg-[#56928C]">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px] text-[#A6D7D2]">domain</span>
                <h2 class="font-heading text-lg font-semibold text-white">Detalles de la cuenta</h2>
            </div>
            <button type="button" @click="openShow = false" class="text-white/70 hover:text-white bg-white/10 hover:bg-white/20 rounded-lg p-1.5 transition-colors focus:outline-none">
                <span class="material-symbols-outlined text-[18px] block">close</span>
            </button>
        </div>

        <!-- Contenido -->
        <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">

            <!-- Información Corporativa -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-[18px] text-[#66C1BA]">apartment</span>
                    <h3 class="font-heading text-sm font-semibold text-[#56928C]">Información corporativa</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-[#D0EAE4]/30 p-4 rounded-xl border border-[#56928C]/20">
                    <div class="sm:col-span-2">
                        <span class="block text-xs text-[#56928C]/80">Razón social / nombre</span>
                        <span class="text-base font-medium text-[#121314]">{{ $empresa->nombre }}</span>
                    </div>
                    <div>
                        <span class="block text-xs text-[#56928C]/80">RFC</span>
                        <span class="inline-flex items-center gap-1 text-sm font-medium text-[#121314] font-mono">
                            <span class="material-symbols-outlined text-[14px] text-[#66C1BA]">badge</span>
                            {{ $empresa->rfc ?? 'No registrado' }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-xs text-[#56928C]/80">Teléfono de contacto</span>
                        <span class="inline-flex items-center gap-1 text-sm font-medium text-[#121314]">
                            <span class="material-symbols-outlined text-[14px] text-[#66C1BA]">call</span>
                            {{ $empresa->telefono ?? 'No registrado' }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-xs text-[#56928C]/80">Correo electrónico</span>
                        <span class="inline-flex items-center gap-1 text-sm font-medium text-[#121314]">
                            <span class="material-symbols-outlined text-[14px] text-[#66C1BA]">mail</span>
                            {{ $empresa->correo ?? 'No registrado' }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-xs text-[#56928C]/80">Estado del servicio</span>
                        @if($empresa->is_active)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-[#D0EAE4] text-[#56928C] text-xs font-medium border border-[#66C1BA]/30 mt-0.5">
                                <span class="material-symbols-outlined text-[14px] text-[#66C1BA]">check_circle</span>
                                Activo (con acceso)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-[#B8503F]/10 text-[#8a3b2e] text-xs font-medium border border-[#B8503F]/25 mt-0.5">
                                <span class="material-symbols-outlined text-[14px] text-[#B8503F]">pause_circle</span>
                                Suspendido
                            </span>
                        @endif
                    </div>
                    <div>
                        <span class="block text-xs text-[#56928C]/80">Fecha de registro</span>
                        <span class="inline-flex items-center gap-1 text-sm font-medium text-[#121314]">
                            <span class="material-symbols-outlined text-[14px] text-[#66C1BA]">calendar_month</span>
                            {{ $empresa->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Métricas y Uso del Sistema -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-[18px] text-[#66C1BA]">monitoring</span>
                    <h3 class="font-heading text-sm font-semibold text-[#56928C]">Uso de la plataforma (SaaS)</h3>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <!-- Tarjeta Usuarios -->
                    <div class="bg-[#66C1BA]/15 p-3 rounded-xl border border-[#56928C]/20 text-center">
                        <span class="material-symbols-outlined text-[20px] text-[#66C1BA] block mb-1">group</span>
                        <span class="block text-xl font-heading font-bold text-[#121314]">{{ $empresa->usuarios()->count() }}</span>
                        <span class="block text-xs text-[#56928C]/80 mt-0.5">Usuarios</span>
                    </div>
                    <!-- Tarjeta Vehículos -->
                    <div class="bg-[#56928C]/10 p-3 rounded-xl border border-[#56928C]/20 text-center">
                        <span class="material-symbols-outlined text-[20px] text-[#56928C] block mb-1">directions_car</span>
                        <span class="block text-xl font-heading font-bold text-[#121314]">{{ $empresa->vehiculos()->count() }}</span>
                        <span class="block text-xs text-[#56928C]/80 mt-0.5">Vehículos</span>
                    </div>
                    <!-- Tarjeta GPS -->
                    <div class="bg-[#A6D7D2]/25 p-3 rounded-xl border border-[#56928C]/20 text-center">
                        <span class="material-symbols-outlined text-[20px] text-[#56928C] block mb-1">satellite_alt</span>
                        <span class="block text-xl font-heading font-bold text-[#121314]">{{ $empresa->dispositivos()->count() }}</span>
                        <span class="block text-xs text-[#56928C]/80 mt-0.5">Equipos GPS</span>
                    </div>
                    <!-- Tarjeta Flotillas -->
                    <div class="bg-[#121314]/5 p-3 rounded-xl border border-[#56928C]/20 text-center">
                        <span class="material-symbols-outlined text-[20px] text-[#121314] block mb-1">route</span>
                        <span class="block text-xl font-heading font-bold text-[#121314]">{{ $empresa->flotillas()->count() }}</span>
                        <span class="block text-xs text-[#56928C]/80 mt-0.5">Flotillas</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Pie del Modal -->
        <div class="flex justify-end p-4 border-t border-[#56928C]/20 bg-[#D0EAE4]/20">
            <button type="button" @click="openShow = false" class="px-5 py-2 text-sm font-medium text-[#56928C] bg-white border border-[#56928C]/30 rounded-xl hover:bg-[#56928C]/5 transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</div>