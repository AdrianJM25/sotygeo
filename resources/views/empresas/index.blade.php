<x-app-layout>
    <style>
        .font-heading { font-family: 'Josefin Sans', sans-serif; }
        .font-body { font-family: 'Ubuntu', sans-serif; }
        .modal-empresa[hidden] { display: none !important; }
    </style>

    <div class="font-body">
        <div class="bg-white overflow-hidden shadow-sm shadow-black/5 border border-[#0056b3]/20 rounded-2xl">
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
                    type="button"
                    onclick="abrirModalEmpresa('modal-empresa-create')"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-[#0056b3] text-sm font-medium rounded-xl hover:bg-blue-50 active:scale-[0.98] transition-all shadow-sm shadow-black/20"
                >
                    <span class="material-symbols-rounded text-[20px]">add_business</span>
                    Nueva empresa
                </button>
            </div>

            @if(session('success'))
                <div class="mx-5 sm:mx-6 mt-5 px-4 py-3 rounded-xl bg-emerald-50 text-emerald-800 text-sm border border-emerald-200 flex items-center gap-2">
                    <span class="material-symbols-rounded text-[18px]">check_circle</span>
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mx-5 sm:mx-6 mt-5 px-4 py-3 rounded-xl bg-red-50 text-red-700 text-sm border border-red-200">
                    <div class="font-semibold flex items-center gap-2 mb-1">
                        <span class="material-symbols-rounded text-[18px]">error</span>
                        Revisa la información del formulario.
                    </div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="text-xs text-[#0056b3] bg-blue-50/60 border-b border-[#0056b3]/15">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Empresa / cliente</th>
                            <th class="px-6 py-4 font-semibold">Contacto</th>
                            <th class="px-6 py-4 font-semibold">Métricas</th>
                            <th class="px-6 py-4 font-semibold">Estado</th>
                            <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#0056b3]/10">
                        @forelse($empresas as $empresa)
                            <tr class="hover:bg-blue-50/30 transition-colors bg-white">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 shrink-0 rounded-lg flex items-center justify-center font-heading font-semibold text-sm bg-[#0056b3] text-white shadow-sm">
                                            {{ strtoupper(mb_substr($empresa->nombre, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-medium text-[#003d82] truncate">
                                                {{ $empresa->nombre }}
                                            </div>
                                            <div class="text-xs text-gray-500 mt-0.5 font-mono">
                                                RFC {{ $empresa->rfc ?? 'N/D' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5 text-gray-700">
                                        <span class="material-symbols-rounded text-[16px] text-[#0056b3]">call</span>
                                        {{ $empresa->telefono ?? 'Sin teléfono' }}
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs text-gray-500 mt-1">
                                        <span class="material-symbols-rounded text-[15px] text-[#0056b3]">mail</span>
                                        {{ $empresa->correo ?? 'Sin correo' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1.5 items-start">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-[#0056b3]/10 text-[#003d82]">
                                            <span class="material-symbols-rounded text-[15px]">group</span>
                                            {{ $empresa->usuarios_count }} usuarios
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium bg-blue-50 text-[#0056b3]">
                                            <span class="material-symbols-rounded text-[15px]">directions_car</span>
                                            {{ $empresa->vehiculos_count }} vehículos
                                        </span>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($empresa->is_active)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium border border-emerald-200">
                                            <span class="material-symbols-rounded text-[15px]">check_circle</span>
                                            Servicio activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 text-red-700 text-xs font-medium border border-red-200">
                                            <span class="material-symbols-rounded text-[15px]">pause_circle</span>
                                            Suspendido
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        <button
                                            type="button"
                                            onclick="abrirModalEmpresa('modal-empresa-show-{{ $empresa->id }}')"
                                            title="Ver detalles"
                                            class="p-2 text-gray-500 hover:text-[#0056b3] hover:bg-[#0056b3]/10 rounded-lg transition-colors"
                                        >
                                            <span class="material-symbols-rounded text-[20px]">visibility</span>
                                        </button>

                                        <button
                                            type="button"
                                            onclick="abrirModalEmpresa('modal-empresa-edit-{{ $empresa->id }}')"
                                            title="Editar empresa"
                                            class="p-2 text-gray-500 hover:text-[#0056b3] hover:bg-[#0056b3]/10 rounded-lg transition-colors"
                                        >
                                            <span class="material-symbols-rounded text-[20px]">edit</span>
                                        </button>

                                        <form
                                            action="{{ route('empresas.destroy', $empresa) }}"
                                            method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('ATENCIÓN: eliminar esta empresa puede eliminar registros relacionados según las reglas de la base de datos. ¿Deseas continuar?');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                title="Eliminar empresa"
                                                class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors"
                                            >
                                                <span class="material-symbols-rounded text-[20px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                    <span class="material-symbols-rounded text-[32px] text-[#0056b3]/40 block mb-2">domain_disabled</span>
                                    No hay empresas registradas.
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
    </div>

    {{-- MODAL CREAR --}}
    <div id="modal-empresa-create" class="modal-empresa fixed inset-0 z-50 flex items-center justify-center p-4" hidden>
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="cerrarModalEmpresa('modal-empresa-create')"></div>
        <div class="relative bg-white rounded-2xl shadow-xl border border-[#0056b3]/20 w-full max-w-lg overflow-hidden z-10">
            <div class="p-4 flex justify-between items-center bg-[#0056b3]">
                <h2 class="font-heading text-lg font-semibold text-white">Registrar nueva empresa</h2>
                <button type="button" onclick="cerrarModalEmpresa('modal-empresa-create')" class="text-white p-1.5 rounded-lg hover:bg-white/10">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>

            <form action="{{ route('empresas.store') }}" method="POST" class="p-6 space-y-5">
                @csrf

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-800">Razón social / nombre *</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required class="w-full rounded-xl border-gray-300 focus:border-[#0056b3] focus:ring-[#0056b3]">
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-800">RFC</label>
                    <input type="text" name="rfc" value="{{ old('rfc') }}" class="w-full rounded-xl border-gray-300 uppercase focus:border-[#0056b3] focus:ring-[#0056b3]">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-800">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" class="w-full rounded-xl border-gray-300 focus:border-[#0056b3] focus:ring-[#0056b3]">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-800">Correo</label>
                        <input type="email" name="correo" value="{{ old('correo') }}" class="w-full rounded-xl border-gray-300 focus:border-[#0056b3] focus:ring-[#0056b3]">
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm font-medium text-gray-800">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="rounded border-gray-300 text-[#0056b3] focus:ring-[#0056b3]">
                    Servicio activo
                </label>

                <div class="flex justify-end gap-3 pt-5 border-t border-gray-200">
                    <button type="button" onclick="cerrarModalEmpresa('modal-empresa-create')" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0056b3] text-white hover:bg-[#003d82]">Guardar empresa</button>
                </div>
            </form>
        </div>
    </div>

    @foreach($empresas as $empresa)
        {{-- MODAL VER --}}
        <div id="modal-empresa-show-{{ $empresa->id }}" class="modal-empresa fixed inset-0 z-50 flex items-center justify-center p-4" hidden>
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="cerrarModalEmpresa('modal-empresa-show-{{ $empresa->id }}')"></div>
            <div class="relative bg-white rounded-2xl shadow-xl border border-[#0056b3]/20 w-full max-w-2xl overflow-hidden z-10">
                <div class="p-4 flex justify-between items-center bg-[#0056b3]">
                    <h2 class="font-heading text-lg font-semibold text-white">Detalles de la empresa</h2>
                    <button type="button" onclick="cerrarModalEmpresa('modal-empresa-show-{{ $empresa->id }}')" class="text-white p-1.5 rounded-lg hover:bg-white/10">
                        <span class="material-symbols-rounded">close</span>
                    </button>
                </div>

                <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-blue-50/40 p-4 rounded-xl border border-[#0056b3]/15">
                        <div class="sm:col-span-2">
                            <span class="block text-xs text-gray-500">Razón social / nombre</span>
                            <span class="text-base font-medium text-gray-900">{{ $empresa->nombre }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500">RFC</span>
                            <span class="text-sm font-medium text-gray-900">{{ $empresa->rfc ?? 'No registrado' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500">Teléfono</span>
                            <span class="text-sm font-medium text-gray-900">{{ $empresa->telefono ?? 'No registrado' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500">Correo</span>
                            <span class="text-sm font-medium text-gray-900">{{ $empresa->correo ?? 'No registrado' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500">Estado</span>
                            <span class="text-sm font-medium {{ $empresa->is_active ? 'text-emerald-700' : 'text-red-700' }}">
                                {{ $empresa->is_active ? 'Servicio activo' : 'Suspendido' }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                        <div class="bg-blue-50 p-3 rounded-xl text-center"><strong class="block text-xl">{{ $empresa->usuarios_count }}</strong><span class="text-xs text-gray-500">Usuarios</span></div>
                        <div class="bg-blue-50 p-3 rounded-xl text-center"><strong class="block text-xl">{{ $empresa->vehiculos_count }}</strong><span class="text-xs text-gray-500">Vehículos</span></div>
                        <div class="bg-blue-50 p-3 rounded-xl text-center"><strong class="block text-xl">{{ $empresa->dispositivos_count }}</strong><span class="text-xs text-gray-500">GPS</span></div>
                        <div class="bg-blue-50 p-3 rounded-xl text-center"><strong class="block text-xl">{{ $empresa->flotillas_count }}</strong><span class="text-xs text-gray-500">Flotillas</span></div>
                        <div class="bg-blue-50 p-3 rounded-xl text-center"><strong class="block text-xl">{{ $empresa->zonas_count }}</strong><span class="text-xs text-gray-500">Zonas</span></div>
                    </div>
                </div>

                <div class="flex justify-end p-4 border-t border-gray-200 bg-gray-50">
                    <button type="button" onclick="cerrarModalEmpresa('modal-empresa-show-{{ $empresa->id }}')" class="px-5 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-white">Cerrar</button>
                </div>
            </div>
        </div>

        {{-- MODAL EDITAR --}}
        <div id="modal-empresa-edit-{{ $empresa->id }}" class="modal-empresa fixed inset-0 z-50 flex items-center justify-center p-4" hidden>
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="cerrarModalEmpresa('modal-empresa-edit-{{ $empresa->id }}')"></div>
            <div class="relative bg-white rounded-2xl shadow-xl border border-[#0056b3]/20 w-full max-w-lg overflow-hidden z-10">
                <div class="p-4 flex justify-between items-center bg-[#0056b3]">
                    <h2 class="font-heading text-lg font-semibold text-white">Editar empresa</h2>
                    <button type="button" onclick="cerrarModalEmpresa('modal-empresa-edit-{{ $empresa->id }}')" class="text-white p-1.5 rounded-lg hover:bg-white/10">
                        <span class="material-symbols-rounded">close</span>
                    </button>
                </div>

                <form action="{{ route('empresas.update', $empresa) }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="is_edit" value="{{ $empresa->id }}">

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-800">Razón social / nombre *</label>
                        <input type="text" name="nombre" value="{{ old('is_edit') == $empresa->id ? old('nombre') : $empresa->nombre }}" required class="w-full rounded-xl border-gray-300 focus:border-[#0056b3] focus:ring-[#0056b3]">
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-800">RFC</label>
                        <input type="text" name="rfc" value="{{ old('is_edit') == $empresa->id ? old('rfc') : $empresa->rfc }}" class="w-full rounded-xl border-gray-300 uppercase focus:border-[#0056b3] focus:ring-[#0056b3]">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-800">Teléfono</label>
                            <input type="text" name="telefono" value="{{ old('is_edit') == $empresa->id ? old('telefono') : $empresa->telefono }}" class="w-full rounded-xl border-gray-300 focus:border-[#0056b3] focus:ring-[#0056b3]">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-800">Correo</label>
                            <input type="email" name="correo" value="{{ old('is_edit') == $empresa->id ? old('correo') : $empresa->correo }}" class="w-full rounded-xl border-gray-300 focus:border-[#0056b3] focus:ring-[#0056b3]">
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm font-medium text-gray-800">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            @checked(old('is_edit') == $empresa->id ? old('is_active') : $empresa->is_active)
                            class="rounded border-gray-300 text-[#0056b3] focus:ring-[#0056b3]"
                        >
                        Servicio activo
                    </label>

                    <div class="flex justify-end gap-3 pt-5 border-t border-gray-200">
                        <button type="button" onclick="cerrarModalEmpresa('modal-empresa-edit-{{ $empresa->id }}')" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#0056b3] text-white hover:bg-[#003d82]">Actualizar datos</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <script>
        function abrirModalEmpresa(id) {
            const modal = document.getElementById(id);
            if (!modal) return;

            modal.hidden = false;
            document.body.style.overflow = 'hidden';
        }

        function cerrarModalEmpresa(id) {
            const modal = document.getElementById(id);
            if (!modal) return;

            modal.hidden = true;

            if (!document.querySelector('.modal-empresa:not([hidden])')) {
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') return;

            document.querySelectorAll('.modal-empresa:not([hidden])')
                .forEach(function (modal) {
                    modal.hidden = true;
                });

            document.body.style.overflow = '';
        });

        document.addEventListener('DOMContentLoaded', function () {
            @if($errors->any() && old('is_edit'))
                abrirModalEmpresa('modal-empresa-edit-{{ (int) old('is_edit') }}');
            @elseif($errors->any())
                abrirModalEmpresa('modal-empresa-create');
            @endif
        });
    </script>
</x-app-layout>
