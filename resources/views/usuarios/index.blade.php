<x-app-layout>
    <style>
        @keyframes rowIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .row-in {
            animation: rowIn 0.35s ease-out both;
        }

        @keyframes toastIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .toast-in {
            animation: toastIn 0.3s ease-out both;
        }

        .usuario-modal {
            display: none;
        }

        .usuario-modal.is-open {
            display: flex;
        }

        @media (prefers-reduced-motion: reduce) {
            .row-in,
            .toast-in {
                animation: none;
                opacity: 1;
                transform: none;
            }
        }
    

        /* ====================================================== */
        /* RECORTADOR DE FOTO DE PERFIL */
        /* ====================================================== */

        .avatar-crop-modal {
            display: none;
        }

        .avatar-crop-modal.is-open {
            display: flex;
        }

        .avatar-crop-viewport {
            position: relative;
            width: min(72vw, 420px);
            aspect-ratio: 1 / 1;
            overflow: hidden;
            background: #f3f4f6;
            cursor: grab;
            user-select: none;
            touch-action: none;
        }

        .avatar-crop-viewport.is-dragging {
            cursor: grabbing;
        }

        .avatar-crop-image {
            position: absolute;
            top: 0;
            left: 0;
            max-width: none;
            max-height: none;
            pointer-events: none;
            user-select: none;
            transform-origin: top left;
            will-change: transform;
        }

        .avatar-crop-mask {
            position: absolute;
            inset: 0;
            border-radius: 9999px;
            pointer-events: none;
            box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.58);
            border: 3px solid rgba(255, 255, 255, 0.95);
        }

    </style>

    <div class="font-body">

        {{-- ====================================================== --}}
        {{-- TARJETA PRINCIPAL --}}
        {{-- ====================================================== --}}

        <div class="bg-white overflow-hidden shadow-sm shadow-black/5 border border-[#0056b3]/20 rounded-2xl">

            <div class="p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-[#0056b3]">

                <div>
                    <h2 class="font-heading text-xl sm:text-2xl font-semibold tracking-tight text-white">
                        Gestión de usuarios
                    </h2>

                    <p class="text-sm text-blue-100 mt-1">
                        Administra las cuentas, roles, empresas y estado de acceso de los usuarios.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full lg:w-auto">

                    <div class="relative w-full sm:w-72">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-[#0056b3]/70">
                            <span class="material-symbols-rounded text-[18px]">
                                search
                            </span>
                        </span>

                        <input
                            type="text"
                            id="usuarios-busqueda"
                            placeholder="Buscar por nombre, correo o rol..."
                            class="w-full pl-9 pr-3 py-2.5 text-sm bg-white border border-white/70 rounded-xl text-gray-700 placeholder:text-gray-400 focus:ring-2 focus:ring-white focus:border-white transition-colors shadow-sm"
                        >
                    </div>

                    <button
                        type="button"
                        data-open-modal="usuario-create-modal"
                        class="shrink-0 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-[#0056b3] text-sm font-medium rounded-xl hover:bg-blue-50 active:scale-[0.98] transition-all shadow-sm shadow-black/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-offset-2 focus-visible:ring-offset-[#0056b3]"
                    >
                        <span class="material-symbols-rounded text-[20px]">
                            person_add
                        </span>

                        Nuevo usuario
                    </button>

                </div>

            </div>


            {{-- ====================================================== --}}
            {{-- MENSAJES --}}
            {{-- ====================================================== --}}

            @if(session('success'))
                <div class="toast-in mx-5 sm:mx-6 mt-5 flex items-center gap-2 px-4 py-3 rounded-xl bg-emerald-50 text-emerald-800 text-sm border border-emerald-200">
                    <span class="material-symbols-rounded text-[18px]">
                        check_circle
                    </span>

                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="toast-in mx-5 sm:mx-6 mt-5 flex items-center gap-2 px-4 py-3 rounded-xl bg-red-50 text-red-700 text-sm border border-red-200">
                    <span class="material-symbols-rounded text-[18px]">
                        error
                    </span>

                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mx-5 sm:mx-6 mt-5 px-4 py-3 rounded-xl bg-red-50 text-red-700 text-sm border border-red-200">
                    <div class="font-semibold mb-1">
                        Revisa la información del formulario.
                    </div>

                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {{-- ====================================================== --}}
            {{-- TABLA --}}
            {{-- ====================================================== --}}

            <div class="overflow-x-auto">

                <table class="w-full text-sm text-left text-gray-700">

                    <thead class="text-xs text-[#0056b3] bg-blue-50/60 border-b border-[#0056b3]/15">
                        <tr>
                            <th class="px-6 py-4 font-semibold">
                                Usuario
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Contacto
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Rol / Empresa
                            </th>

                            <th class="px-6 py-4 font-semibold">
                                Estado
                            </th>

                            <th class="px-6 py-4 font-semibold text-right">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#0056b3]/10">

                        @forelse($users as $usuario)

                            @php
                                $nombreCompleto = trim(
                                    "{$usuario->nombre} {$usuario->apellido_paterno} {$usuario->apellido_materno}"
                                );

                                $iniciales =
                                    mb_strtoupper(
                                        mb_substr($usuario->nombre, 0, 1)
                                        .
                                        mb_substr($usuario->apellido_paterno, 0, 1)
                                    );

                                $rolNombre =
                                    $usuario->roles->first()->name
                                    ?? 'Sin rol';

                                $avatarColores = [
                                    'Super Administrador' => 'bg-rose-500',
                                    'Administrador de Empresa' => 'bg-blue-500',
                                    'Gestor de flotilla' => 'bg-orange-500',
                                    'Cliente Individual' => 'bg-cyan-500',
                                    'Conductor' => 'bg-violet-500',
                                    'Chofer' => 'bg-violet-500',
                                ];

                                $avatarColor =
                                    $avatarColores[$rolNombre]
                                    ?? 'bg-gray-400';

                                $busquedaTexto =
                                    mb_strtolower(
                                        "{$nombreCompleto} {$usuario->email} {$rolNombre} "
                                        .
                                        ($usuario->empresa?->nombre ?? '')
                                    );
                            @endphp

                            <tr
                                data-usuario-row
                                data-search="{{ $busquedaTexto }}"
                                style="animation-delay: {{ $loop->index * 40 }}ms"
                                class="row-in hover:bg-blue-50/30 transition-colors bg-white"
                            >

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">

                                        <div class="relative w-9 h-9 rounded-full {{ $avatarColor }} text-white flex items-center justify-center text-xs font-bold shrink-0 overflow-hidden shadow-sm">

                                            <span>
                                                {{ $iniciales }}
                                            </span>

                                            @if($usuario->avatar)
                                                <img
                                                    src="{{ route('usuarios.avatar', $usuario) }}?v={{ $usuario->updated_at?->timestamp ?? time() }}"
                                                    class="absolute inset-0 w-full h-full object-cover"
                                                    alt="{{ $nombreCompleto }}"
                                                    onerror="this.style.display='none';"
                                                >
                                            @endif

                                        </div>

                                        <span class="font-medium text-gray-900">
                                            {{ $nombreCompleto }}
                                        </span>

                                    </div>
                                </td>


                                <td class="px-6 py-4">
                                    <div class="text-gray-900">
                                        {{ $usuario->email }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $usuario->telefono ?? 'Sin teléfono' }}
                                    </div>
                                </td>


                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-start gap-1">

                                        <x-role-badge :rol="$rolNombre" />

                                        @if($usuario->empresa)
                                            <span class="text-[10px] font-medium text-gray-400">
                                                {{ $usuario->empresa->nombre }}
                                            </span>
                                        @else
                                            <span class="text-[10px] font-medium text-indigo-400">
                                                SOTyTECH (Interno)
                                            </span>
                                        @endif

                                    </div>
                                </td>


                                <td class="px-6 py-4">
                                    <x-estado-badge :activo="$usuario->activo" />
                                </td>


                                <td class="px-6 py-4 text-right whitespace-nowrap">

                                    <div class="inline-flex items-center space-x-1">

                                        <button
                                            type="button"
                                            data-open-modal="usuario-show-{{ $usuario->id }}"
                                            title="Ver detalles"
                                            class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all hover:scale-105"
                                        >
                                            <span class="material-symbols-rounded text-[20px]">
                                                visibility
                                            </span>
                                        </button>


                                        <button
                                            type="button"
                                            data-open-modal="usuario-edit-{{ $usuario->id }}"
                                            title="Editar usuario"
                                            class="p-1.5 text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-all hover:scale-105"
                                        >
                                            <span class="material-symbols-rounded text-[20px]">
                                                edit_square
                                            </span>
                                        </button>


                                        @if(
                                            Route::has('usuarios.reenviar-acceso')
                                            &&
                                            $usuario->id !== auth()->id()
                                        )
                                            <form
                                                action="{{ route('usuarios.reenviar-acceso', $usuario) }}"
                                                method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('¿Enviar un nuevo enlace para crear o restablecer la contraseña de este usuario?');"
                                            >
                                                @csrf

                                                <button
                                                    type="submit"
                                                    title="Reenviar acceso"
                                                    class="p-1.5 text-violet-500 hover:text-violet-700 hover:bg-violet-50 rounded-lg transition-all hover:scale-105"
                                                >
                                                    <span class="material-symbols-rounded text-[20px]">
                                                        forward_to_inbox
                                                    </span>
                                                </button>
                                            </form>
                                        @endif


                                        @if($usuario->id !== auth()->id())

                                            <form
                                                action="{{ route('usuarios.destroy', $usuario) }}"
                                                method="POST"
                                                class="inline-block"
                                                onsubmit="return confirm('{{ $usuario->activo ? '¿Desactivar a este usuario? No podrá iniciar sesión.' : '¿Reactivar a este usuario?' }}');"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                @if($usuario->activo)
                                                    <button
                                                        type="submit"
                                                        title="Desactivar"
                                                        class="p-1.5 text-amber-500 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition-all hover:scale-105"
                                                    >
                                                        <span class="material-symbols-rounded text-[20px]">
                                                            block
                                                        </span>
                                                    </button>
                                                @else
                                                    <button
                                                        type="submit"
                                                        title="Activar"
                                                        class="p-1.5 text-green-500 hover:text-green-700 hover:bg-green-50 rounded-lg transition-all hover:scale-105"
                                                    >
                                                        <span class="material-symbols-rounded text-[20px]">
                                                            check_circle
                                                        </span>
                                                    </button>
                                                @endif
                                            </form>


                                            @role('Super Administrador|Administrador de Empresa')
                                                <form
                                                    action="{{ route('usuarios.eliminar', $usuario) }}"
                                                    method="POST"
                                                    class="inline-block"
                                                    onsubmit="return confirm('Esta acción eliminará al usuario de forma PERMANENTE. ¿Continuar?');"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        title="Eliminar permanentemente"
                                                        class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all hover:scale-105"
                                                    >
                                                        <span class="material-symbols-rounded text-[20px]">
                                                            delete
                                                        </span>
                                                    </button>
                                                </form>
                                            @endrole

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center bg-white">

                                    <div class="flex flex-col items-center gap-3">

                                        <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center">
                                            <span class="material-symbols-rounded">
                                                group_off
                                            </span>
                                        </div>

                                        <p class="text-gray-500 text-sm">
                                            Aún no hay usuarios registrados.
                                        </p>

                                        <button
                                            type="button"
                                            data-open-modal="usuario-create-modal"
                                            class="text-sm font-medium text-[#0056b3] underline underline-offset-2 hover:text-[#003d82]"
                                        >
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


        {{-- ====================================================== --}}
        {{-- MODAL CREAR --}}
        {{-- ====================================================== --}}

        <div
            id="usuario-create-modal"
            class="usuario-modal fixed inset-0 z-[80] items-center justify-center p-4"
            aria-hidden="true"
        >

            <div
                class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                data-close-modal
            ></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto z-10">

                <div class="sticky top-0 z-10 p-5 border-b border-gray-200 bg-white flex items-center justify-between">

                    <div>
                        <h3 class="font-heading text-xl font-semibold text-gray-900">
                            Registrar usuario
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Captura los datos del nuevo usuario. SotyGeo enviará a su correo un enlace para crear su contraseña.
                        </p>
                    </div>

                    <button
                        type="button"
                        data-close-modal
                        class="p-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100"
                    >
                        <span class="material-symbols-rounded">
                            close
                        </span>
                    </button>

                </div>

                <form
                    action="{{ route('usuarios.store') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="p-6"
                >
                    @csrf

                    <div class="mb-5 flex items-start gap-3 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                        <span class="material-symbols-rounded mt-0.5 text-[20px]">
                            mark_email_read
                        </span>

                        <div>
                            <div class="font-semibold">
                                Acceso inicial por correo
                            </div>

                            <p class="mt-1 text-xs leading-relaxed text-blue-700">
                                Al registrar la cuenta, SotyGeo enviará automáticamente un enlace al correo del usuario para que establezca su propia contraseña.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre *
                            </label>

                            <input
                                type="text"
                                name="nombre"
                                value="{{ old('is_edit') ? '' : old('nombre') }}"
                                required
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>


                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Apellido paterno *
                            </label>

                            <input
                                type="text"
                                name="apellido_paterno"
                                value="{{ old('is_edit') ? '' : old('apellido_paterno') }}"
                                required
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>


                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Apellido materno
                            </label>

                            <input
                                type="text"
                                name="apellido_materno"
                                value="{{ old('is_edit') ? '' : old('apellido_materno') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>


                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Teléfono
                            </label>

                            <input
                                type="text"
                                name="telefono"
                                value="{{ old('is_edit') ? '' : old('telefono') }}"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>


                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Correo electrónico *
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="{{ old('is_edit') ? '' : old('email') }}"
                                required
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                        </div>


                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Rol *
                            </label>

                            <select
                                name="role"
                                required
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="">
                                    Seleccionar rol
                                </option>

                                @foreach($roles as $rol)
                                    <option
                                        value="{{ $rol->name }}"
                                        @selected(
                                            !old('is_edit')
                                            &&
                                            old('role') === $rol->name
                                        )
                                    >
                                        {{ $rol->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        @if(auth()->user()->hasRole('Super Administrador'))
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Empresa
                                </label>

                                <select
                                    name="empresa_id"
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >
                                    <option value="">
                                        SOTyTECH / Sin empresa
                                    </option>

                                    @foreach($empresas as $empresa)
                                        <option
                                            value="{{ $empresa->id }}"
                                            @selected(
                                                !old('is_edit')
                                                &&
                                                (string) old('empresa_id')
                                                ===
                                                (string) $empresa->id
                                            )
                                        >
                                            {{ $empresa->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif


                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Foto de perfil
                            </label>

                            <input
                                type="file"
                                name="avatar"
                                accept=".jpg,.jpeg,.png,.webp"
                                data-avatar-input
                                class="block w-full text-sm text-gray-600 rounded-xl border border-gray-300 p-2"
                            >
                                <input
                                    type="hidden"
                                    name="avatar_recortado"
                                    value=""
                                    data-avatar-cropped
                                >
                        </div>


                        <div class="flex items-center gap-2 md:self-end md:pb-3">
                            <input
                                type="checkbox"
                                name="activo"
                                value="1"
                                id="usuario-create-activo"
                                checked
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            >

                            <label
                                for="usuario-create-activo"
                                class="text-sm font-medium text-gray-700"
                            >
                                Cuenta activa
                            </label>
                        </div>

                    </div>


                    <div class="mt-6 pt-5 border-t border-gray-200">

                        <h4 class="font-semibold text-gray-900 mb-1">
                            Domicilio
                        </h4>

                        <p class="text-xs text-gray-500 mb-4">
                            Información opcional.
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <input
                                type="text"
                                name="direccion[calle]"
                                value="{{ old('is_edit') ? '' : old('direccion.calle') }}"
                                placeholder="Calle"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >

                            <input
                                type="text"
                                name="direccion[numero_exterior]"
                                value="{{ old('is_edit') ? '' : old('direccion.numero_exterior') }}"
                                placeholder="Número exterior"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >

                            <input
                                type="text"
                                name="direccion[numero_interior]"
                                value="{{ old('is_edit') ? '' : old('direccion.numero_interior') }}"
                                placeholder="Número interior"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >

                            <input
                                type="text"
                                name="direccion[colonia]"
                                value="{{ old('is_edit') ? '' : old('direccion.colonia') }}"
                                placeholder="Colonia"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >

                            <input
                                type="text"
                                name="direccion[ciudad]"
                                value="{{ old('is_edit') ? '' : old('direccion.ciudad') }}"
                                placeholder="Ciudad"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >

                            <input
                                type="text"
                                name="direccion[estado]"
                                value="{{ old('is_edit') ? '' : old('direccion.estado') }}"
                                placeholder="Estado"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >

                            <input
                                type="text"
                                name="direccion[codigo_postal]"
                                value="{{ old('is_edit') ? '' : old('direccion.codigo_postal') }}"
                                placeholder="Código postal"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >

                            <textarea
                                name="direccion[referencias]"
                                rows="2"
                                placeholder="Referencias"
                                class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >{{ old('is_edit') ? '' : old('direccion.referencias') }}</textarea>

                        </div>

                    </div>


                    <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-gray-200">

                        <button
                            type="button"
                            data-close-modal
                            class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-900 text-white hover:bg-gray-800"
                        >
                            <span class="material-symbols-rounded text-[18px]">
                                save
                            </span>

                            Crear usuario y enviar acceso
                        </button>

                    </div>

                </form>

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- MODALES POR USUARIO --}}
        {{-- ====================================================== --}}

        @foreach($users as $usuario)

            @php
                $rolActual =
                    $usuario->roles->first()->name
                    ?? '';

                $domicilio =
                    $usuario->domicilio;

                $nombreCompleto =
                    trim(
                        "{$usuario->nombre} {$usuario->apellido_paterno} {$usuario->apellido_materno}"
                    );
            @endphp


            {{-- ================= SHOW ================= --}}

            <div
                id="usuario-show-{{ $usuario->id }}"
                class="usuario-modal fixed inset-0 z-[80] items-center justify-center p-4"
                aria-hidden="true"
            >

                <div
                    class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                    data-close-modal
                ></div>

                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto z-10">

                    <div class="p-5 border-b border-gray-200 flex justify-between items-center">

                        <div>
                            <h3 class="font-heading text-xl font-semibold text-gray-900">
                                Detalles del usuario
                            </h3>

                            <p class="text-sm text-gray-500">
                                Información de la cuenta seleccionada.
                            </p>
                        </div>

                        <button
                            type="button"
                            data-close-modal
                            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100"
                        >
                            <span class="material-symbols-rounded">
                                close
                            </span>
                        </button>

                    </div>


                    <div class="p-6 space-y-6">

                        <div class="flex items-center gap-4">

                            <div class="relative w-16 h-16 rounded-full bg-blue-500 text-white flex items-center justify-center overflow-hidden font-bold">

                                <span>
                                    {{ mb_strtoupper(
                                        mb_substr($usuario->nombre, 0, 1)
                                        .
                                        mb_substr($usuario->apellido_paterno, 0, 1)
                                    ) }}
                                </span>

                                @if($usuario->avatar)
                                    <img
                                        src="{{ route('usuarios.avatar', $usuario) }}?v={{ $usuario->updated_at?->timestamp ?? time() }}"
                                        alt="{{ $nombreCompleto }}"
                                        class="absolute inset-0 w-full h-full object-cover"
                                        onerror="this.style.display='none';"
                                    >
                                @endif

                            </div>

                            <div>
                                <h4 class="text-lg font-bold text-gray-900">
                                    {{ $nombreCompleto }}
                                </h4>

                                <div class="mt-1">
                                    <x-role-badge :rol="$rolActual" />
                                </div>
                            </div>

                        </div>


                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                                <span class="text-xs text-gray-500">
                                    Correo
                                </span>

                                <div class="font-medium text-gray-900 mt-1">
                                    {{ $usuario->email }}
                                </div>
                            </div>


                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                                <span class="text-xs text-gray-500">
                                    Teléfono
                                </span>

                                <div class="font-medium text-gray-900 mt-1">
                                    {{ $usuario->telefono ?? 'Sin teléfono' }}
                                </div>
                            </div>


                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                                <span class="text-xs text-gray-500">
                                    Empresa
                                </span>

                                <div class="font-medium text-gray-900 mt-1">
                                    {{ $usuario->empresa?->nombre ?? 'SOTyTECH (Interno)' }}
                                </div>
                            </div>


                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                                <span class="text-xs text-gray-500">
                                    Estado
                                </span>

                                <div class="mt-1">
                                    <x-estado-badge :activo="$usuario->activo" />
                                </div>
                            </div>


                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                                <span class="text-xs text-gray-500">
                                    Último acceso
                                </span>

                                <div class="font-medium text-gray-900 mt-1">
                                    {{ $usuario->ultimo_login?->format('d/m/Y H:i') ?? 'Sin registro' }}
                                </div>
                            </div>


                            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                                <span class="text-xs text-gray-500">
                                    Fecha de registro
                                </span>

                                <div class="font-medium text-gray-900 mt-1">
                                    {{ $usuario->created_at?->format('d/m/Y H:i') ?? 'N/D' }}
                                </div>
                            </div>

                        </div>


                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">
                                Domicilio
                            </h4>

                            @if($domicilio)
                                <div class="p-4 rounded-xl bg-blue-50/40 border border-blue-100 text-sm text-gray-700">
                                    {{ $domicilio->calle }}

                                    @if($domicilio->numero_exterior)
                                        #{{ $domicilio->numero_exterior }}
                                    @endif

                                    @if($domicilio->numero_interior)
                                        Int. {{ $domicilio->numero_interior }}
                                    @endif

                                    <br>

                                    {{ $domicilio->colonia }}

                                    @if($domicilio->ciudad)
                                        , {{ $domicilio->ciudad }}
                                    @endif

                                    @if($domicilio->estado)
                                        , {{ $domicilio->estado }}
                                    @endif

                                    @if($domicilio->codigo_postal)
                                        C.P. {{ $domicilio->codigo_postal }}
                                    @endif

                                    @if($domicilio->referencias)
                                        <div class="mt-2 text-gray-500">
                                            Referencias:
                                            {{ $domicilio->referencias }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-gray-500">
                                    No hay domicilio registrado.
                                </p>
                            @endif
                        </div>

                    </div>


                    <div class="p-4 border-t border-gray-200 flex justify-end">

                        <button
                            type="button"
                            data-close-modal
                            class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50"
                        >
                            Cerrar
                        </button>

                    </div>

                </div>

            </div>


            {{-- ================= EDIT ================= --}}

            <div
                id="usuario-edit-{{ $usuario->id }}"
                class="usuario-modal fixed inset-0 z-[80] items-center justify-center p-4"
                aria-hidden="true"
            >

                <div
                    class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                    data-close-modal
                ></div>

                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto z-10">

                    <div class="sticky top-0 z-10 p-5 border-b border-gray-200 bg-white flex items-center justify-between">

                        <div>
                            <h3 class="font-heading text-xl font-semibold text-gray-900">
                                Editar usuario
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Modifica los datos, rol y acceso del usuario.
                            </p>
                        </div>

                        <button
                            type="button"
                            data-close-modal
                            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100"
                        >
                            <span class="material-symbols-rounded">
                                close
                            </span>
                        </button>

                    </div>


                    <form
                        action="{{ route('usuarios.update', $usuario) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="p-6"
                    >
                        @csrf
                        @method('PUT')

                        <input
                            type="hidden"
                            name="is_edit"
                            value="{{ $usuario->id }}"
                        >


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre *
                                </label>

                                <input
                                    type="text"
                                    name="nombre"
                                    value="{{ old('is_edit') == $usuario->id ? old('nombre') : $usuario->nombre }}"
                                    required
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >
                            </div>


                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Apellido paterno *
                                </label>

                                <input
                                    type="text"
                                    name="apellido_paterno"
                                    value="{{ old('is_edit') == $usuario->id ? old('apellido_paterno') : $usuario->apellido_paterno }}"
                                    required
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >
                            </div>


                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Apellido materno
                                </label>

                                <input
                                    type="text"
                                    name="apellido_materno"
                                    value="{{ old('is_edit') == $usuario->id ? old('apellido_materno') : $usuario->apellido_materno }}"
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >
                            </div>


                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Teléfono
                                </label>

                                <input
                                    type="text"
                                    name="telefono"
                                    value="{{ old('is_edit') == $usuario->id ? old('telefono') : $usuario->telefono }}"
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >
                            </div>


                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Correo electrónico *
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('is_edit') == $usuario->id ? old('email') : $usuario->email }}"
                                    required
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >
                            </div>


                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nueva contraseña
                                </label>

                                <input
                                    type="password"
                                    name="password"
                                    minlength="8"
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >

                                <p class="text-xs text-gray-400 mt-1">
                                    Déjala vacía para conservar la actual.
                                </p>
                            </div>


                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Rol *
                                </label>

                                <select
                                    name="role"
                                    required
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >
                                    @foreach($roles as $rol)
                                        <option
                                            value="{{ $rol->name }}"
                                            @selected(
                                                (
                                                    old('is_edit') == $usuario->id
                                                        ? old('role')
                                                        : $rolActual
                                                )
                                                ===
                                                $rol->name
                                            )
                                        >
                                            {{ $rol->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>


                            @if(auth()->user()->hasRole('Super Administrador'))
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Empresa
                                    </label>

                                    <select
                                        name="empresa_id"
                                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                    >
                                        <option value="">
                                            SOTyTECH / Sin empresa
                                        </option>

                                        @foreach($empresas as $empresa)
                                            <option
                                                value="{{ $empresa->id }}"
                                                @selected(
                                                    (
                                                        old('is_edit') == $usuario->id
                                                            ? (string) old('empresa_id')
                                                            : (string) $usuario->empresa_id
                                                    )
                                                    ===
                                                    (string) $empresa->id
                                                )
                                            >
                                                {{ $empresa->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif


                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nueva foto de perfil
                                </label>

                                <input
                                    type="file"
                                    name="avatar"
                                    accept=".jpg,.jpeg,.png,.webp"
                                data-avatar-input
                                    class="block w-full text-sm text-gray-600 rounded-xl border border-gray-300 p-2"
                                >
                                <input
                                    type="hidden"
                                    name="avatar_recortado"
                                    value=""
                                    data-avatar-cropped
                                >
                            </div>


                            <div class="flex items-center gap-2 md:self-end md:pb-3">

                                <input
                                    type="checkbox"
                                    name="activo"
                                    value="1"
                                    id="usuario-activo-{{ $usuario->id }}"
                                    @checked(
                                        old('is_edit') == $usuario->id
                                            ? old('activo', $usuario->activo)
                                            : $usuario->activo
                                    )
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >

                                <label
                                    for="usuario-activo-{{ $usuario->id }}"
                                    class="text-sm font-medium text-gray-700"
                                >
                                    Cuenta activa
                                </label>

                            </div>

                        </div>


                        <div class="mt-6 pt-5 border-t border-gray-200">

                            <h4 class="font-semibold text-gray-900 mb-1">
                                Domicilio
                            </h4>

                            <p class="text-xs text-gray-500 mb-4">
                                Puedes completar o actualizar la dirección del usuario.
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <input
                                    type="text"
                                    name="direccion[calle]"
                                    value="{{ old('is_edit') == $usuario->id ? old('direccion.calle') : $domicilio?->calle }}"
                                    placeholder="Calle"
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >

                                <input
                                    type="text"
                                    name="direccion[numero_exterior]"
                                    value="{{ old('is_edit') == $usuario->id ? old('direccion.numero_exterior') : $domicilio?->numero_exterior }}"
                                    placeholder="Número exterior"
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >

                                <input
                                    type="text"
                                    name="direccion[numero_interior]"
                                    value="{{ old('is_edit') == $usuario->id ? old('direccion.numero_interior') : $domicilio?->numero_interior }}"
                                    placeholder="Número interior"
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >

                                <input
                                    type="text"
                                    name="direccion[colonia]"
                                    value="{{ old('is_edit') == $usuario->id ? old('direccion.colonia') : $domicilio?->colonia }}"
                                    placeholder="Colonia"
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >

                                <input
                                    type="text"
                                    name="direccion[ciudad]"
                                    value="{{ old('is_edit') == $usuario->id ? old('direccion.ciudad') : $domicilio?->ciudad }}"
                                    placeholder="Ciudad"
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >

                                <input
                                    type="text"
                                    name="direccion[estado]"
                                    value="{{ old('is_edit') == $usuario->id ? old('direccion.estado') : $domicilio?->estado }}"
                                    placeholder="Estado"
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >

                                <input
                                    type="text"
                                    name="direccion[codigo_postal]"
                                    value="{{ old('is_edit') == $usuario->id ? old('direccion.codigo_postal') : $domicilio?->codigo_postal }}"
                                    placeholder="Código postal"
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >

                                <textarea
                                    name="direccion[referencias]"
                                    rows="2"
                                    placeholder="Referencias"
                                    class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                >{{ old('is_edit') == $usuario->id ? old('direccion.referencias') : $domicilio?->referencias }}</textarea>

                            </div>

                        </div>


                        <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-gray-200">

                            <button
                                type="button"
                                data-close-modal
                                class="px-4 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50"
                            >
                                Cancelar
                            </button>

                            <button
                                type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 text-white hover:bg-blue-700"
                            >
                                <span class="material-symbols-rounded text-[18px]">
                                    save
                                </span>

                                Guardar cambios
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        @endforeach

    </div>



    {{-- ====================================================== --}}
    {{-- RECORTADOR GLOBAL DE FOTO DE PERFIL --}}
    {{-- ====================================================== --}}

    <div
        id="avatar-crop-modal"
        class="avatar-crop-modal fixed inset-0 z-[120] items-center justify-center p-4"
        aria-hidden="true"
    >
        <div
            id="avatar-crop-backdrop"
            class="absolute inset-0 bg-black/60 backdrop-blur-sm"
        ></div>

        <div class="relative z-10 w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl">

            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <div>
                    <h3 class="font-heading text-lg font-semibold text-gray-900">
                        Ajustar foto de perfil
                    </h3>
                    <p class="mt-1 text-xs text-gray-500">
                        Arrastra la imagen y ajusta el zoom hasta dejar visible la parte que quieres usar.
                    </p>
                </div>

                <button
                    id="avatar-crop-cerrar"
                    type="button"
                    class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-800"
                >
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>

            <div class="flex flex-col items-center gap-5 p-5">
                <div
                    id="avatar-crop-viewport"
                    class="avatar-crop-viewport rounded-full shadow-inner"
                >
                    <img
                        id="avatar-crop-image"
                        class="avatar-crop-image"
                        alt="Vista previa de la foto"
                        draggable="false"
                    >
                    <div class="avatar-crop-mask"></div>
                </div>

                <div class="w-full">
                    <div class="mb-2 flex items-center justify-between">
                        <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                            <span class="material-symbols-rounded text-[16px]">zoom_out</span>
                            Zoom
                        </span>

                        <button
                            id="avatar-crop-restablecer"
                            type="button"
                            class="text-xs font-medium text-blue-600 hover:text-blue-800"
                        >
                            Restablecer
                        </button>
                    </div>

                    <input
                        id="avatar-crop-zoom"
                        type="range"
                        min="100"
                        max="400"
                        value="100"
                        step="1"
                        class="w-full accent-blue-600"
                    >

                    <p class="mt-3 text-center text-xs text-gray-400">
                        La imagen final se guardará como avatar cuadrado de 512 × 512 px.
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-200 bg-gray-50 px-5 py-4">
                <button
                    id="avatar-crop-cancelar"
                    type="button"
                    class="rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-white"
                >
                    Cancelar
                </button>

                <button
                    id="avatar-crop-aplicar"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-700"
                >
                    <span class="material-symbols-rounded text-[18px]">crop</span>
                    Usar esta foto
                </button>
            </div>
        </div>
    </div>

    {{-- ====================================================== --}}
    {{-- JAVASCRIPT SIN ALPINE PARA MODALES Y BÚSQUEDA --}}
    {{-- ====================================================== --}}

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            function abrirModal(id) {
                const modal =
                    document.getElementById(id);

                if (!modal) {
                    return;
                }

                document
                    .querySelectorAll('.usuario-modal.is-open')
                    .forEach(function (abierto) {
                        abierto.classList.remove('is-open');
                        abierto.setAttribute('aria-hidden', 'true');
                    });

                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');

                document.body.style.overflow = 'hidden';
            }


            function cerrarModal(modal) {
                if (!modal) {
                    return;
                }

                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');

                if (
                    !document.querySelector(
                        '.usuario-modal.is-open'
                    )
                ) {
                    document.body.style.overflow = '';
                }
            }


            document
                .querySelectorAll('[data-open-modal]')
                .forEach(function (boton) {

                    boton.addEventListener(
                        'click',
                        function () {
                            abrirModal(
                                boton.dataset.openModal
                            );
                        }
                    );

                });


            document
                .querySelectorAll('[data-close-modal]')
                .forEach(function (boton) {

                    boton.addEventListener(
                        'click',
                        function () {
                            cerrarModal(
                                boton.closest(
                                    '.usuario-modal'
                                )
                            );
                        }
                    );

                });


            document.addEventListener(
                'keydown',
                function (event) {

                    if (event.key !== 'Escape') {
                        return;
                    }

                    document
                        .querySelectorAll(
                            '.usuario-modal.is-open'
                        )
                        .forEach(
                            cerrarModal
                        );
                }
            );


            const buscador =
                document.getElementById(
                    'usuarios-busqueda'
                );

            if (buscador) {

                buscador.addEventListener(
                    'input',
                    function () {

                        const termino =
                            buscador
                                .value
                                .trim()
                                .toLocaleLowerCase(
                                    'es-MX'
                                );

                        document
                            .querySelectorAll(
                                '[data-usuario-row]'
                            )
                            .forEach(
                                function (fila) {

                                    const texto =
                                        (
                                            fila.dataset
                                                .search
                                            || ''
                                        )
                                            .toLocaleLowerCase(
                                                'es-MX'
                                            );

                                    fila.style.display =
                                        termino === ''
                                        ||
                                        texto.includes(
                                            termino
                                        )
                                            ?
                                            ''
                                            :
                                            'none';
                                }
                            );
                    }
                );
            }




            /* ====================================================== */
            /* RECORTADOR DE FOTO DE PERFIL */
            /* ====================================================== */

            const avatarCropModal = document.getElementById('avatar-crop-modal');
            const avatarCropViewport = document.getElementById('avatar-crop-viewport');
            const avatarCropImage = document.getElementById('avatar-crop-image');
            const avatarCropZoom = document.getElementById('avatar-crop-zoom');
            const avatarCropCerrar = document.getElementById('avatar-crop-cerrar');
            const avatarCropCancelar = document.getElementById('avatar-crop-cancelar');
            const avatarCropAplicar = document.getElementById('avatar-crop-aplicar');
            const avatarCropRestablecer = document.getElementById('avatar-crop-restablecer');
            const avatarCropBackdrop = document.getElementById('avatar-crop-backdrop');

            let avatarInputActivo = null;
            let avatarHiddenActivo = null;
            let avatarArchivoOriginal = null;
            let avatarUrlTemporal = null;
            let avatarNaturalWidth = 0;
            let avatarNaturalHeight = 0;
            let avatarEscalaMinima = 1;
            let avatarEscala = 1;
            let avatarX = 0;
            let avatarY = 0;
            let avatarArrastrando = false;
            let avatarInicioX = 0;
            let avatarInicioY = 0;
            let avatarXInicial = 0;
            let avatarYInicial = 0;

            function avatarTamanoViewport() {
                return avatarCropViewport ? avatarCropViewport.clientWidth : 0;
            }

            function avatarLimitarPosicion() {
                const tamano = avatarTamanoViewport();
                const ancho = avatarNaturalWidth * avatarEscala;
                const alto = avatarNaturalHeight * avatarEscala;
                const minX = Math.min(0, tamano - ancho);
                const minY = Math.min(0, tamano - alto);

                avatarX = Math.min(0, Math.max(minX, avatarX));
                avatarY = Math.min(0, Math.max(minY, avatarY));
            }

            function avatarRenderizar() {
                if (!avatarCropImage || !avatarNaturalWidth || !avatarNaturalHeight) {
                    return;
                }

                avatarLimitarPosicion();
                avatarCropImage.style.width = avatarNaturalWidth + 'px';
                avatarCropImage.style.height = avatarNaturalHeight + 'px';
                avatarCropImage.style.transform =
                    'translate(' + avatarX + 'px, ' + avatarY + 'px) scale(' + avatarEscala + ')';
            }

            function avatarRestablecer() {
                const tamano = avatarTamanoViewport();

                if (!tamano || !avatarNaturalWidth || !avatarNaturalHeight) {
                    return;
                }

                avatarEscalaMinima = Math.max(
                    tamano / avatarNaturalWidth,
                    tamano / avatarNaturalHeight
                );

                avatarEscala = avatarEscalaMinima;

                const ancho = avatarNaturalWidth * avatarEscala;
                const alto = avatarNaturalHeight * avatarEscala;

                avatarX = (tamano - ancho) / 2;
                avatarY = (tamano - alto) / 2;

                if (avatarCropZoom) {
                    avatarCropZoom.value = '100';
                }

                avatarRenderizar();
            }

            function avatarAbrirRecortador(input, archivo) {
                if (!avatarCropModal || !avatarCropImage || !archivo) {
                    return;
                }

                avatarInputActivo = input;

                avatarHiddenActivo =
                    input
                        .closest('form')
                        ?.querySelector(
                            '[data-avatar-cropped]'
                        )
                    || null;

                avatarArchivoOriginal = archivo;

                if (avatarUrlTemporal) {
                    URL.revokeObjectURL(avatarUrlTemporal);
                }

                avatarUrlTemporal = URL.createObjectURL(archivo);

                avatarCropImage.onload = function () {
                    avatarNaturalWidth = avatarCropImage.naturalWidth;
                    avatarNaturalHeight = avatarCropImage.naturalHeight;
                    requestAnimationFrame(avatarRestablecer);
                };

                avatarCropImage.src = avatarUrlTemporal;
                avatarCropModal.classList.add('is-open');
                avatarCropModal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }

            function avatarCerrarRecortador(limpiarInput) {
                if (!avatarCropModal) {
                    return;
                }

                avatarCropModal.classList.remove('is-open');
                avatarCropModal.setAttribute('aria-hidden', 'true');

                if (!document.querySelector('.usuario-modal.is-open')) {
                    document.body.style.overflow = '';
                }

                if (limpiarInput) {
                    if (avatarInputActivo) {
                        avatarInputActivo.value = '';
                    }

                    if (avatarHiddenActivo) {
                        avatarHiddenActivo.value = '';
                    }
                }

                if (avatarUrlTemporal) {
                    URL.revokeObjectURL(avatarUrlTemporal);
                    avatarUrlTemporal = null;
                }

                if (avatarCropImage) {
                    avatarCropImage.removeAttribute('src');
                }

                avatarInputActivo = null;
                avatarHiddenActivo = null;
                avatarArchivoOriginal = null;
                avatarArrastrando = false;
            }

            document.querySelectorAll('[data-avatar-input]').forEach(function (input) {
                input.addEventListener('change', function () {
                    const archivo = input.files && input.files[0];

                    if (!archivo) {
                        return;
                    }

                    if (!archivo.type.startsWith('image/')) {
                        alert('Selecciona una imagen válida.');
                        input.value = '';
                        return;
                    }

                    avatarAbrirRecortador(input, archivo);
                });
            });

            if (avatarCropViewport) {
                avatarCropViewport.addEventListener('pointerdown', function (event) {
                    avatarArrastrando = true;
                    avatarInicioX = event.clientX;
                    avatarInicioY = event.clientY;
                    avatarXInicial = avatarX;
                    avatarYInicial = avatarY;
                    avatarCropViewport.setPointerCapture(event.pointerId);
                    avatarCropViewport.classList.add('is-dragging');
                });

                avatarCropViewport.addEventListener('pointermove', function (event) {
                    if (!avatarArrastrando) {
                        return;
                    }

                    avatarX = avatarXInicial + (event.clientX - avatarInicioX);
                    avatarY = avatarYInicial + (event.clientY - avatarInicioY);
                    avatarRenderizar();
                });

                const avatarFinalizarArrastre = function () {
                    avatarArrastrando = false;
                    avatarCropViewport.classList.remove('is-dragging');
                };

                avatarCropViewport.addEventListener('pointerup', avatarFinalizarArrastre);
                avatarCropViewport.addEventListener('pointercancel', avatarFinalizarArrastre);
            }

            if (avatarCropZoom) {
                avatarCropZoom.addEventListener('input', function () {
                    const tamano = avatarTamanoViewport();

                    if (!tamano || !avatarNaturalWidth || !avatarNaturalHeight) {
                        return;
                    }

                    const escalaAnterior = avatarEscala;
                    const factor = Number(avatarCropZoom.value) / 100;
                    const nuevaEscala = avatarEscalaMinima * factor;

                    const centroXImagen = (tamano / 2 - avatarX) / escalaAnterior;
                    const centroYImagen = (tamano / 2 - avatarY) / escalaAnterior;

                    avatarEscala = nuevaEscala;
                    avatarX = tamano / 2 - centroXImagen * avatarEscala;
                    avatarY = tamano / 2 - centroYImagen * avatarEscala;
                    avatarRenderizar();
                });
            }

            if (avatarCropRestablecer) {
                avatarCropRestablecer.addEventListener('click', avatarRestablecer);
            }

            function avatarCancelarRecorte() {
                avatarCerrarRecortador(true);
            }

            if (avatarCropCerrar) {
                avatarCropCerrar.addEventListener('click', avatarCancelarRecorte);
            }

            if (avatarCropCancelar) {
                avatarCropCancelar.addEventListener('click', avatarCancelarRecorte);
            }

            if (avatarCropBackdrop) {
                avatarCropBackdrop.addEventListener('click', avatarCancelarRecorte);
            }

            if (avatarCropAplicar) {
                avatarCropAplicar.addEventListener('click', function () {
                    if (!avatarInputActivo || !avatarNaturalWidth || !avatarNaturalHeight) {
                        return;
                    }

                    const tamanoViewport = avatarTamanoViewport();
                    const salida = 512;
                    const canvas = document.createElement('canvas');
                    canvas.width = salida;
                    canvas.height = salida;

                    const contexto = canvas.getContext('2d');
                    contexto.fillStyle = '#ffffff';
                    contexto.fillRect(0, 0, salida, salida);

                    const proporcion = salida / tamanoViewport;

                    contexto.drawImage(
                        avatarCropImage,
                        avatarX * proporcion,
                        avatarY * proporcion,
                        avatarNaturalWidth * avatarEscala * proporcion,
                        avatarNaturalHeight * avatarEscala * proporcion
                    );

                    const avatarDataUrl =
                        canvas.toDataURL(
                            'image/jpeg',
                            0.92
                        );

                    if (!avatarHiddenActivo) {
                        alert(
                            'No fue posible preparar la foto recortada.'
                        );
                        return;
                    }

                    avatarHiddenActivo.value =
                        avatarDataUrl;

                    /*
                     * Limpiamos el archivo original. Laravel recibirá
                     * exclusivamente la versión ya recortada.
                     */
                    avatarInputActivo.value =
                        '';

                    avatarCerrarRecortador(false);

                    alert(
                        'Foto ajustada correctamente. Ahora guarda el usuario para aplicar el cambio.'
                    );
                });
            }

            window.addEventListener('resize', function () {
                if (avatarCropModal && avatarCropModal.classList.contains('is-open')) {
                    avatarRestablecer();
                }
            });

            @if($errors->any())
                @if(old('is_edit'))
                    abrirModal(
                        'usuario-edit-{{ (int) old('is_edit') }}'
                    );
                @else
                    abrirModal(
                        'usuario-create-modal'
                    );
                @endif
            @endif

        });
    </script>

</x-app-layout>
