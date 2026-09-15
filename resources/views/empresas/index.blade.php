<x-app-layout>
    {{--
        ────────────────────────────────────────────────────────────────
        REQUISITO IMPORTANTE (una sola vez, a nivel de proyecto):
        Agrega esto al <head> de tu layout principal
        (resources/views/layouts/app.blade.php o el componente x-app-layout),
        no aquí, porque este archivo solo renderiza el <body>.

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;500;600;700&family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

        Paleta usada en esta vista (basada en el diseño SotyGeo):
        #003d82 azul oscuro · #0056b3 azul primario · #3380d0 azul claro
        ────────────────────────────────────────────────────────────────
    --}}

    <style>
        .font-heading { font-family: 'Josefin Sans', sans-serif; }
        .font-body    { font-family: 'Ubuntu', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
            vertical-align: middle;
            line-height: 1;
        }
    </style>

    <!-- Estado global para el modal de Crear -->
    <div x-data="{ openCreate: {{ $errors->any() && !old('is_edit') ? 'true' : 'false' }} }}" class="font-body">

        <!-- Tarjeta Principal -->
        <div class="bg-white overflow-hidden shadow-sm shadow-black/5 border border-[#0056b3]/20 rounded-2xl">

            <!-- Cabecera -->
            <div class="p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-[#0056b3]">
                <div>
                    <h2 class="font-heading text-xl sm:text-2xl font-semibold tracking-tight text-white">
                        Gestión de empresas
                    </h2>
                    <p class="text-sm text-blue-100 mt-1">
                        Administra los clientes corporativos y particulares de la plataforma.
                    </p>
                </div>

                <button
                    @click="openCreate = true"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-[#0056b3] text-sm font-medium rounded-xl hover:bg-blue-50 active:scale-[0.98] transition-all shadow-sm shadow-black/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#0056b3]"
                >
                    <span class="material-symbols-outlined text-[20px]">add_business</span>
                    Nueva empresa
                </button>
            </div>

            @if(session('success'))
                <div class="mx-5 sm:mx-6 mt-5 px-4 py-3 rounded-xl bg-blue-50 text-[#003d82] text-sm border border-[#0056b3]/30 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-[#0056b3]">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="text-xs text-[#0056b3] bg-blue-50/60 border-b border-[#0056b3]/15">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">Empresa / cliente</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Contacto</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Métricas</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Estado</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#0056b3]/10">
                        @forelse ($empresas as $empresa)
                            @php
                                // Avatar de iniciales: alterna 3 tonos de la paleta azul según la fila.
                                $avatarPalette = [
                                    ['bg' => '#0056b3', 'text' => '#FFFFFF'],
                                    ['bg' => '#3380d0', 'text' => '#FFFFFF'],
                                    ['bg' => '#e6f0fa', 'text' => '#003d82'],
                                ];
                                $avatarColor = $avatarPalette[$loop->index % count($avatarPalette)];
                            @endphp
                            <tr x-data="{ openEdit: {{ $errors->any() && old('is_edit') == $empresa->id ? 'true' : 'false' }}, openShow: false }}" class="hover:bg-blue-50/30 transition-colors bg-white">

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-9 w-9 shrink-0 rounded-lg flex items-center justify-center font-heading font-semibold text-sm shadow-sm"
                                            style="background-color: {{ $avatarColor['bg'] }}; color: {{ $avatarColor['text'] }};"
                                        >
                                            {{ strtoupper(substr($empresa->nombre, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-medium text-[#003d82] truncate">{{ $empresa->nombre }}</div>
                                            <div class="text-xs text-gray-500 mt-0.5 font-mono">RFC {{ $empresa->rfc ?? 'N/D' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5 text-gray-700">
                                        <span class="material-symbols-outlined text-[16px] text-[#0056b3]">call</span>
                                        {{ $empresa->telefono ?? 'Sin teléfono' }}
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs text-gray-500 mt-1">
                                        <span class="material-symbols-outlined text-[15px] text-[#0056b3]">mail</span>
                                        {{ $empresa->correo ?? 'Sin correo' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1.5 items-start">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-[#0056b3]/10 text-[#003d82]" title="Usuarios vinculados">
                                            <span class="material-symbols-outlined text-[15px]">group</span>
                                            {{ $empresa->usuarios_count ?? 0 }} usuarios
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-blue-50 text-[#0056b3]" title="Vehículos vinculados">
                                            <span class="material-symbols-outlined text-[15px]">directions_car</span>
                                            {{ $empresa->vehiculos_count ?? 0 }} vehículos
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($empresa->is_active)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-200">
                                            <span class="material-symbols-outlined text-[15px] text-emerald-600">check_circle</span>
                                            Servicio activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-xs font-medium border border-red-200">
                                            <span class="material-symbols-outlined text-[15px] text-red-600">pause_circle</span>
                                            Suspendido
                                        </span>
                                    @endif
                                </td>

                                <!-- CELDA DE ACCIONES -->
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        <!-- Botón Ver Detalles (Show) -->
                                        <button @click="openShow = true" title="Ver detalles de la cuenta" class="p-2 text-gray-500 hover:text-[#0056b3] hover:bg-[#0056b3]/10 rounded-lg transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056b3]">
                                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                                        </button>

                                        <!-- Botón Editar -->
                                        <button @click="openEdit = true" title="Editar empresa" class="p-2 text-gray-500 hover:text-[#0056b3] hover:bg-[#0056b3]/10 rounded-lg transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#0056b3]">
                                            <span class="material-symbols-outlined text-[20px]">edit</span>
                                        </button>

                                        <!-- Eliminar permanentemente -->
                                        <form action="{{ route('empresas.destroy', $empresa) }}" method="POST" class="inline-block"
                                              onsubmit="return confirm('ATENCIÓN: Eliminar esta empresa borrará TODOS sus usuarios, vehículos y zonas asociadas permanentemente. ¿Estás seguro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Eliminar empresa" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-500">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
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
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500 bg-white">
                                    <span class="material-symbols-outlined text-[32px] text-[#0056b3]/40 block mb-2">domain_disabled</span>
                                    No hay empresas ni clientes corporativos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($empresas->hasPages())
                <div class="p-4 border-t border-[#0056b3]/15 bg-white">
                    {{ $empresas->links() }}
                </div>
            @endif
        </div>

        <!-- Inyectamos el modal de creación -->
        @include('empresas.create')

    </div>
</x-app-layout>