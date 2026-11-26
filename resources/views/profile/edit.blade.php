<x-app-layout>

    <style>
        .font-heading {
            font-family: 'Josefin Sans', sans-serif;
        }

        .font-body {
            font-family: 'Ubuntu', sans-serif;
        }

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
            box-shadow:
                0 0 0 9999px
                rgba(15, 23, 42, .58);

            border:
                3px solid
                rgba(255, 255, 255, .95);
        }
    </style>


    @php
        $iniciales =
            mb_strtoupper(
                mb_substr(
                    $user->nombre ?? 'U',
                    0,
                    1
                )
                .
                mb_substr(
                    $user->apellido_paterno ?? '',
                    0,
                    1
                )
            );

        $nombreCompleto =
            trim(
                ($user->nombre ?? '')
                .
                ' '
                .
                ($user->apellido_paterno ?? '')
                .
                ' '
                .
                ($user->apellido_materno ?? '')
            );

        $rol =
            $user->roles->first()?->name
            ??
            'Usuario';

        $avatarPerfilUrl =
            $user->avatar
                ? route(
                    'profile.avatar.show'
                )
                    .
                    '?v='
                    .
                    (
                        $user
                            ->updated_at
                            ?->timestamp
                        ??
                        time()
                    )
                : null;
    @endphp


    <div class="font-body space-y-6">

        {{-- ====================================================== --}}
        {{-- CABECERA --}}
        {{-- ====================================================== --}}

        <div
            class="overflow-hidden rounded-2xl
                   border border-[#0056b3]/20
                   bg-white shadow-sm shadow-black/5"
        >

            <div
                class="bg-[#0056b3]
                       p-5 sm:p-6"
            >

                <h1
                    class="font-heading
                           text-xl sm:text-2xl
                           font-semibold tracking-tight
                           text-white"
                >
                    Mi perfil
                </h1>

                <p
                    class="mt-1 text-sm
                           text-blue-100"
                >
                    Administra tu foto y la información de tu cuenta de SotyGeo.
                </p>

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- MENSAJES --}}
        {{-- ====================================================== --}}

        @if(session('status') === 'avatar-updated')
            <div
                class="flex items-center gap-2
                       rounded-xl border
                       border-emerald-200
                       bg-emerald-50
                       px-4 py-3
                       text-sm font-medium
                       text-emerald-700"
            >
                <span class="material-symbols-rounded">
                    check_circle
                </span>

                Foto de perfil actualizada correctamente.
            </div>
        @endif


        @if(session('status') === 'avatar-deleted')
            <div
                class="flex items-center gap-2
                       rounded-xl border
                       border-blue-200
                       bg-blue-50
                       px-4 py-3
                       text-sm font-medium
                       text-[#0056b3]"
            >
                <span class="material-symbols-rounded">
                    account_circle
                </span>

                La foto de perfil fue eliminada.
            </div>
        @endif


        @error('avatar')
            <div
                class="rounded-xl border
                       border-red-200
                       bg-red-50
                       px-4 py-3
                       text-sm text-red-700"
            >
                {{ $message }}
            </div>
        @enderror


        {{-- ====================================================== --}}
        {{-- FOTO DE PERFIL --}}
        {{-- ====================================================== --}}

        <div
            class="overflow-hidden rounded-2xl
                   border border-[#0056b3]/20
                   bg-white shadow-sm"
        >

            <div
                class="border-b
                       border-[#0056b3]/10
                       px-5 py-4 sm:px-6"
            >
                <h2
                    class="text-lg font-semibold
                           text-gray-900"
                >
                    Foto de perfil
                </h2>

                <p
                    class="mt-1 text-sm
                           text-gray-500"
                >
                    Esta imagen aparecerá en tu cuenta y en el encabezado del sistema.
                </p>
            </div>


            <div
                class="grid grid-cols-1
                       gap-6 p-5 sm:p-6
                       md:grid-cols-[auto_1fr]"
            >

                {{-- FOTO ACTUAL --}}
                <div
                    class="flex flex-col
                           items-center gap-3"
                >

                    <div
                        id="profile-avatar-preview"
                        class="relative flex
                               h-32 w-32
                               items-center justify-center
                               overflow-hidden rounded-full
                               bg-[#0056b3]
                               text-3xl font-bold
                               text-white
                               ring-4 ring-blue-50
                               shadow-md"
                    >

                        <span
                            id="profile-avatar-initials"
                            @if($avatarPerfilUrl)
                                class="hidden"
                            @endif
                        >
                            {{ $iniciales ?: 'U' }}
                        </span>

                        @if($avatarPerfilUrl)
                            <img
                                id="profile-avatar-image"
                                src="{{ $avatarPerfilUrl }}"
                                alt="{{ $nombreCompleto }}"
                                class="h-full w-full object-cover"
                                onerror="
                                    this.classList.add('hidden');
                                    document.getElementById(
                                        'profile-avatar-initials'
                                    )?.classList.remove('hidden');
                                "
                            >
                        @else
                            <img
                                id="profile-avatar-image"
                                src=""
                                alt=""
                                class="hidden h-full w-full object-cover"
                            >
                        @endif

                    </div>


                    <div class="text-center">

                        <p
                            class="font-semibold
                                   text-gray-900"
                        >
                            {{ $nombreCompleto ?: 'Usuario' }}
                        </p>

                        <p
                            class="mt-0.5 text-xs
                                   text-gray-500"
                        >
                            {{ $rol }}
                        </p>

                    </div>

                </div>


                {{-- CONTROLES --}}
                <div
                    class="flex flex-col
                           justify-center"
                >

                    <form
                        id="profile-avatar-form"
                        action="{{ route('profile.avatar.update') }}"
                        method="POST"
                        enctype="multipart/form-data"
                    >
                        @csrf
                        @method('PATCH')


                        <input
                            id="profile-avatar-input"
                            type="file"
                            name="avatar"
                            accept=".jpg,.jpeg,.png,.webp"
                            class="hidden"
                        >

                        <input
                            id="profile-avatar-cropped"
                            type="hidden"
                            name="avatar_recortado"
                            value=""
                        >


                        <div
                            class="rounded-xl
                                   border border-[#0056b3]/15
                                   bg-blue-50/40
                                   p-4"
                        >

                            <div
                                class="flex items-start
                                       gap-3"
                            >

                                <div
                                    class="flex h-10 w-10
                                           shrink-0
                                           items-center justify-center
                                           rounded-xl
                                           bg-[#0056b3]/10
                                           text-[#0056b3]"
                                >
                                    <span class="material-symbols-rounded">
                                        add_a_photo
                                    </span>
                                </div>


                                <div>
                                    <p
                                        class="text-sm
                                               font-semibold
                                               text-gray-900"
                                    >
                                        Cambiar foto
                                    </p>

                                    <p
                                        class="mt-1 text-xs
                                               leading-relaxed
                                               text-gray-500"
                                    >
                                        Selecciona una imagen JPG, PNG o WEBP.
                                        Podrás moverla y ajustar el zoom antes de guardarla.
                                    </p>
                                </div>

                            </div>


                            <div
                                class="mt-4 flex
                                       flex-wrap gap-2"
                            >

                                <button
                                    id="profile-avatar-select"
                                    type="button"
                                    class="inline-flex
                                           items-center gap-2
                                           rounded-xl
                                           bg-[#0056b3]
                                           px-4 py-2.5
                                           text-sm font-medium
                                           text-white
                                           transition-colors
                                           hover:bg-[#003d82]"
                                >
                                    <span
                                        class="material-symbols-rounded
                                               text-[18px]"
                                    >
                                        photo_library
                                    </span>

                                    Seleccionar foto
                                </button>


                                <button
                                    id="profile-avatar-save"
                                    type="submit"
                                    disabled
                                    class="inline-flex
                                           items-center gap-2
                                           rounded-xl
                                           border border-gray-300
                                           bg-white
                                           px-4 py-2.5
                                           text-sm font-medium
                                           text-gray-700
                                           transition-colors
                                           hover:bg-gray-50
                                           disabled:cursor-not-allowed
                                           disabled:opacity-40"
                                >
                                    <span
                                        class="material-symbols-rounded
                                               text-[18px]"
                                    >
                                        save
                                    </span>

                                    Guardar foto
                                </button>

                            </div>

                        </div>

                    </form>


                    @if($user->avatar)
                        <form
                            action="{{ route('profile.avatar.destroy') }}"
                            method="POST"
                            class="mt-3"
                            onsubmit="
                                return confirm(
                                    '¿Quieres quitar tu foto de perfil?'
                                );
                            "
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="inline-flex
                                       items-center gap-2
                                       rounded-lg px-2 py-1
                                       text-sm font-medium
                                       text-red-600
                                       hover:bg-red-50
                                       hover:text-red-700"
                            >
                                <span
                                    class="material-symbols-rounded
                                           text-[18px]"
                                >
                                    delete
                                </span>

                                Quitar foto actual
                            </button>

                        </form>
                    @endif

                </div>

            </div>

        </div>


        {{-- ====================================================== --}}
        {{-- INFORMACIÓN DE PERFIL --}}
        {{-- ====================================================== --}}

        <div
            class="rounded-2xl
                   border border-gray-200
                   bg-white p-4
                   shadow-sm sm:p-8"
        >
            <div class="max-w-xl">
                <section>

    <header>
        <h2 class="text-lg font-semibold text-gray-900">
            Información del perfil
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Actualiza tus datos personales y el correo asociado a tu cuenta.
        </p>
    </header>


    <form
        method="POST"
        action="{{ route('profile.update') }}"
        class="mt-6 space-y-5"
    >
        @csrf
        @method('PATCH')


        <div>
            <label
                for="nombre"
                class="block text-sm font-medium text-gray-700 mb-1"
            >
                Nombre
            </label>

            <input
                id="nombre"
                name="nombre"
                type="text"
                value="{{ old('nombre', $user->nombre) }}"
                required
                autofocus
                autocomplete="given-name"
                class="w-full rounded-xl border-gray-300
                       focus:border-[#0056b3]
                       focus:ring-[#0056b3]"
            >

            <x-input-error
                class="mt-2"
                :messages="$errors->get('nombre')"
            />
        </div>


        <div>
            <label
                for="apellido_paterno"
                class="block text-sm font-medium text-gray-700 mb-1"
            >
                Apellido paterno
            </label>

            <input
                id="apellido_paterno"
                name="apellido_paterno"
                type="text"
                value="{{ old('apellido_paterno', $user->apellido_paterno) }}"
                required
                autocomplete="family-name"
                class="w-full rounded-xl border-gray-300
                       focus:border-[#0056b3]
                       focus:ring-[#0056b3]"
            >

            <x-input-error
                class="mt-2"
                :messages="$errors->get('apellido_paterno')"
            />
        </div>


        <div>
            <label
                for="apellido_materno"
                class="block text-sm font-medium text-gray-700 mb-1"
            >
                Apellido materno
            </label>

            <input
                id="apellido_materno"
                name="apellido_materno"
                type="text"
                value="{{ old('apellido_materno', $user->apellido_materno) }}"
                autocomplete="additional-name"
                class="w-full rounded-xl border-gray-300
                       focus:border-[#0056b3]
                       focus:ring-[#0056b3]"
            >

            <x-input-error
                class="mt-2"
                :messages="$errors->get('apellido_materno')"
            />
        </div>


        <div>
            <label
                for="telefono"
                class="block text-sm font-medium text-gray-700 mb-1"
            >
                Teléfono
            </label>

            <input
                id="telefono"
                name="telefono"
                type="text"
                value="{{ old('telefono', $user->telefono) }}"
                autocomplete="tel"
                class="w-full rounded-xl border-gray-300
                       focus:border-[#0056b3]
                       focus:ring-[#0056b3]"
            >

            <x-input-error
                class="mt-2"
                :messages="$errors->get('telefono')"
            />
        </div>


        <div>
            <label
                for="email"
                class="block text-sm font-medium text-gray-700 mb-1"
            >
                Correo electrónico
            </label>

            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
                class="w-full rounded-xl border-gray-300
                       focus:border-[#0056b3]
                       focus:ring-[#0056b3]"
            >

            <x-input-error
                class="mt-2"
                :messages="$errors->get('email')"
            />


            @if (
                $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail
                &&
                !$user->hasVerifiedEmail()
            )
                <div class="mt-3">

                    <p class="text-sm text-gray-600">
                        Tu correo electrónico aún no ha sido verificado.
                    </p>

                    <button
                        form="send-verification"
                        class="mt-2 text-sm font-medium
                               text-[#0056b3]
                               underline underline-offset-4
                               hover:text-[#003d82]"
                    >
                        Reenviar correo de verificación
                    </button>


                    @if (
                        session('status')
                        ===
                        'verification-link-sent'
                    )
                        <p
                            class="mt-2 text-sm
                                   font-medium
                                   text-emerald-600"
                        >
                            Se envió un nuevo enlace de verificación a tu correo.
                        </p>
                    @endif

                </div>
            @endif
        </div>


        <div class="flex items-center gap-4">

            <button
                type="submit"
                class="inline-flex items-center gap-2
                       rounded-xl bg-[#0056b3]
                       px-4 py-2.5
                       text-sm font-medium
                       text-white
                       transition-colors
                       hover:bg-[#003d82]"
            >
                <span
                    class="material-symbols-rounded
                           text-[18px]"
                >
                    save
                </span>

                Guardar cambios
            </button>


            @if(session('status') === 'profile-updated')
                <p
                    class="text-sm font-medium
                           text-emerald-600"
                >
                    Información actualizada correctamente.
                </p>
            @endif

        </div>

    </form>


    @if (
        $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail
        &&
        !$user->hasVerifiedEmail()
    )
        <form
            id="send-verification"
            method="POST"
            action="{{ route('verification.send') }}"
        >
            @csrf
        </form>
    @endif

</section>

            </div>
        </div>


        {{-- ====================================================== --}}
        {{-- CONTRASEÑA --}}
        {{-- ====================================================== --}}

        <div
            class="rounded-2xl
                   border border-gray-200
                   bg-white p-4
                   shadow-sm sm:p-8"
        >
            <div class="max-w-xl">
                <section>

    <header>
        <h2 class="text-lg font-semibold text-gray-900">
            Actualizar contraseña
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Usa una contraseña segura de al menos 8 caracteres para proteger tu cuenta.
        </p>
    </header>


    <form
        method="POST"
        action="{{ route('password.update') }}"
        class="mt-6 space-y-5"
    >
        @csrf
        @method('PUT')


        <div>
            <label
                for="update_password_current_password"
                class="block text-sm font-medium text-gray-700 mb-1"
            >
                Contraseña actual
            </label>

            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
                class="w-full rounded-xl border-gray-300
                       focus:border-[#0056b3]
                       focus:ring-[#0056b3]"
            >

            <x-input-error
                :messages="$errors->updatePassword->get('current_password')"
                class="mt-2"
            />
        </div>


        <div>
            <label
                for="update_password_password"
                class="block text-sm font-medium text-gray-700 mb-1"
            >
                Nueva contraseña
            </label>

            <input
                id="update_password_password"
                name="password"
                type="password"
                autocomplete="new-password"
                class="w-full rounded-xl border-gray-300
                       focus:border-[#0056b3]
                       focus:ring-[#0056b3]"
            >

            <x-input-error
                :messages="$errors->updatePassword->get('password')"
                class="mt-2"
            />
        </div>


        <div>
            <label
                for="update_password_password_confirmation"
                class="block text-sm font-medium text-gray-700 mb-1"
            >
                Confirmar nueva contraseña
            </label>

            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                class="w-full rounded-xl border-gray-300
                       focus:border-[#0056b3]
                       focus:ring-[#0056b3]"
            >

            <x-input-error
                :messages="$errors->updatePassword->get('password_confirmation')"
                class="mt-2"
            />
        </div>


        <div class="flex items-center gap-4">

            <button
                type="submit"
                class="inline-flex items-center gap-2
                       rounded-xl bg-[#0056b3]
                       px-4 py-2.5
                       text-sm font-medium
                       text-white
                       transition-colors
                       hover:bg-[#003d82]"
            >
                <span
                    class="material-symbols-rounded
                           text-[18px]"
                >
                    lock_reset
                </span>

                Actualizar contraseña
            </button>


            @if(session('status') === 'password-updated')
                <p
                    class="text-sm font-medium
                           text-emerald-600"
                >
                    Contraseña actualizada correctamente.
                </p>
            @endif

        </div>

    </form>

</section>

            </div>
        </div>


        {{-- ====================================================== --}}
        {{-- ELIMINAR CUENTA --}}
        {{-- ====================================================== --}}

        <div
            class="rounded-2xl
                   border border-gray-200
                   bg-white p-4
                   shadow-sm sm:p-8"
        >
            <div class="max-w-xl">
                <section>

    <header>
        <h2 class="text-lg font-semibold text-gray-900">
            Eliminar cuenta
        </h2>

        <p class="mt-1 text-sm leading-relaxed text-gray-500">
            Al eliminar tu cuenta, la información asociada se eliminará de forma permanente.
            Esta acción no se puede deshacer.
        </p>
    </header>


    <form
        method="POST"
        action="{{ route('profile.destroy') }}"
        class="mt-6"
        onsubmit="
            return confirm(
                '¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.'
            );
        "
    >
        @csrf
        @method('DELETE')


        <div class="max-w-md">

            <label
                for="delete_account_password"
                class="block text-sm font-medium text-gray-700 mb-1"
            >
                Confirma tu contraseña
            </label>

            <input
                id="delete_account_password"
                name="password"
                type="password"
                autocomplete="current-password"
                placeholder="Ingresa tu contraseña actual"
                class="w-full rounded-xl border-gray-300
                       focus:border-red-500
                       focus:ring-red-500"
            >

            <x-input-error
                :messages="$errors->userDeletion->get('password')"
                class="mt-2"
            />

        </div>


        <button
            type="submit"
            class="mt-4 inline-flex
                   items-center gap-2
                   rounded-xl bg-red-600
                   px-4 py-2.5
                   text-sm font-medium
                   text-white
                   transition-colors
                   hover:bg-red-700"
        >
            <span
                class="material-symbols-rounded
                       text-[18px]"
            >
                delete_forever
            </span>

            Eliminar cuenta
        </button>

    </form>

</section>

            </div>
        </div>

    </div>


    {{-- ====================================================== --}}
    {{-- MODAL RECORTADOR --}}
    {{-- ====================================================== --}}

    <div
        id="profile-avatar-crop-modal"
        class="avatar-crop-modal
               fixed inset-0 z-[150]
               items-center justify-center
               p-4"
        aria-hidden="true"
    >

        <div
            id="profile-avatar-crop-backdrop"
            class="absolute inset-0
                   bg-black/60 backdrop-blur-sm"
        ></div>


        <div
            class="relative z-10
                   w-full max-w-xl
                   overflow-hidden
                   rounded-2xl bg-white
                   shadow-2xl"
        >

            <div
                class="flex items-center
                       justify-between
                       bg-[#0056b3]
                       px-5 py-4"
            >

                <div>

                    <h3
                        class="font-heading
                               text-lg font-semibold
                               text-white"
                    >
                        Ajustar foto de perfil
                    </h3>

                    <p
                        class="mt-1 text-xs
                               text-blue-100"
                    >
                        Arrastra la imagen y usa el zoom para encuadrarla.
                    </p>

                </div>


                <button
                    id="profile-avatar-crop-close"
                    type="button"
                    class="rounded-lg p-2
                           text-white/80
                           hover:bg-white/10
                           hover:text-white"
                >
                    <span class="material-symbols-rounded">
                        close
                    </span>
                </button>

            </div>


            <div
                class="flex flex-col
                       items-center gap-5 p-5"
            >

                <div
                    id="profile-avatar-crop-viewport"
                    class="avatar-crop-viewport
                           rounded-full shadow-inner"
                >

                    <img
                        id="profile-avatar-crop-image"
                        class="avatar-crop-image"
                        alt="Vista previa"
                        draggable="false"
                    >

                    <div
                        class="avatar-crop-mask"
                    ></div>

                </div>


                <div class="w-full">

                    <div
                        class="mb-2 flex
                               items-center
                               justify-between"
                    >

                        <span
                            class="inline-flex
                                   items-center gap-1
                                   text-xs text-gray-500"
                        >
                            <span
                                class="material-symbols-rounded
                                       text-[16px]"
                            >
                                zoom_out
                            </span>

                            Zoom
                        </span>


                        <button
                            id="profile-avatar-crop-reset"
                            type="button"
                            class="text-xs
                                   font-medium
                                   text-[#0056b3]
                                   hover:text-[#003d82]"
                        >
                            Restablecer
                        </button>

                    </div>


                    <input
                        id="profile-avatar-crop-zoom"
                        type="range"
                        min="100"
                        max="400"
                        value="100"
                        step="1"
                        class="w-full
                               accent-[#0056b3]"
                    >


                    <p
                        class="mt-3 text-center
                               text-xs text-gray-400"
                    >
                        La imagen final se guardará en 512 × 512 px.
                    </p>

                </div>

            </div>


            <div
                class="flex justify-end
                       gap-3 border-t
                       border-gray-200
                       bg-gray-50
                       px-5 py-4"
            >

                <button
                    id="profile-avatar-crop-cancel"
                    type="button"
                    class="rounded-xl
                           border border-gray-300
                           px-4 py-2.5
                           text-sm font-medium
                           text-gray-700
                           hover:bg-white"
                >
                    Cancelar
                </button>


                <button
                    id="profile-avatar-crop-apply"
                    type="button"
                    class="inline-flex
                           items-center gap-2
                           rounded-xl
                           bg-[#0056b3]
                           px-4 py-2.5
                           text-sm font-medium
                           text-white
                           hover:bg-[#003d82]"
                >
                    <span
                        class="material-symbols-rounded
                               text-[18px]"
                    >
                        crop
                    </span>

                    Usar esta foto
                </button>

            </div>

        </div>

    </div>


    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function () {

                const input =
                    document.getElementById(
                        'profile-avatar-input'
                    );

                const hidden =
                    document.getElementById(
                        'profile-avatar-cropped'
                    );

                const selectButton =
                    document.getElementById(
                        'profile-avatar-select'
                    );

                const saveButton =
                    document.getElementById(
                        'profile-avatar-save'
                    );

                const previewImage =
                    document.getElementById(
                        'profile-avatar-image'
                    );

                const previewInitials =
                    document.getElementById(
                        'profile-avatar-initials'
                    );


                const modal =
                    document.getElementById(
                        'profile-avatar-crop-modal'
                    );

                const viewport =
                    document.getElementById(
                        'profile-avatar-crop-viewport'
                    );

                const cropImage =
                    document.getElementById(
                        'profile-avatar-crop-image'
                    );

                const zoom =
                    document.getElementById(
                        'profile-avatar-crop-zoom'
                    );

                const closeButton =
                    document.getElementById(
                        'profile-avatar-crop-close'
                    );

                const cancelButton =
                    document.getElementById(
                        'profile-avatar-crop-cancel'
                    );

                const applyButton =
                    document.getElementById(
                        'profile-avatar-crop-apply'
                    );

                const resetButton =
                    document.getElementById(
                        'profile-avatar-crop-reset'
                    );

                const backdrop =
                    document.getElementById(
                        'profile-avatar-crop-backdrop'
                    );


                let urlTemporal =
                    null;

                let naturalWidth =
                    0;

                let naturalHeight =
                    0;

                let escalaMinima =
                    1;

                let escala =
                    1;

                let posX =
                    0;

                let posY =
                    0;

                let arrastrando =
                    false;

                let inicioX =
                    0;

                let inicioY =
                    0;

                let posXInicial =
                    0;

                let posYInicial =
                    0;


                function tamanoViewport()
                {
                    return viewport
                        ? viewport.clientWidth
                        : 0;
                }


                function limitarPosicion()
                {
                    const tamano =
                        tamanoViewport();

                    const ancho =
                        naturalWidth
                        *
                        escala;

                    const alto =
                        naturalHeight
                        *
                        escala;

                    const minX =
                        Math.min(
                            0,
                            tamano - ancho
                        );

                    const minY =
                        Math.min(
                            0,
                            tamano - alto
                        );

                    posX =
                        Math.min(
                            0,
                            Math.max(
                                minX,
                                posX
                            )
                        );

                    posY =
                        Math.min(
                            0,
                            Math.max(
                                minY,
                                posY
                            )
                        );
                }


                function renderizar()
                {
                    if (
                        !cropImage
                        ||
                        !naturalWidth
                        ||
                        !naturalHeight
                    ) {
                        return;
                    }

                    limitarPosicion();

                    cropImage.style.width =
                        naturalWidth
                        +
                        'px';

                    cropImage.style.height =
                        naturalHeight
                        +
                        'px';

                    cropImage.style.transform =
                        'translate('
                        +
                        posX
                        +
                        'px, '
                        +
                        posY
                        +
                        'px) scale('
                        +
                        escala
                        +
                        ')';
                }


                function restablecer()
                {
                    const tamano =
                        tamanoViewport();

                    if (
                        !tamano
                        ||
                        !naturalWidth
                        ||
                        !naturalHeight
                    ) {
                        return;
                    }

                    escalaMinima =
                        Math.max(
                            tamano
                            /
                            naturalWidth,

                            tamano
                            /
                            naturalHeight
                        );

                    escala =
                        escalaMinima;

                    const ancho =
                        naturalWidth
                        *
                        escala;

                    const alto =
                        naturalHeight
                        *
                        escala;

                    posX =
                        (
                            tamano
                            -
                            ancho
                        )
                        /
                        2;

                    posY =
                        (
                            tamano
                            -
                            alto
                        )
                        /
                        2;

                    if (zoom) {
                        zoom.value =
                            100;
                    }

                    renderizar();
                }


                function abrirModal(
                    archivo
                ) {
                    if (
                        !modal
                        ||
                        !cropImage
                    ) {
                        return;
                    }

                    if (urlTemporal) {
                        URL.revokeObjectURL(
                            urlTemporal
                        );
                    }

                    urlTemporal =
                        URL.createObjectURL(
                            archivo
                        );

                    cropImage.onload =
                        function () {

                            naturalWidth =
                                cropImage
                                    .naturalWidth;

                            naturalHeight =
                                cropImage
                                    .naturalHeight;

                            modal.classList.add(
                                'is-open'
                            );

                            modal.setAttribute(
                                'aria-hidden',
                                'false'
                            );

                            document.body
                                .style
                                .overflow =
                                'hidden';

                            requestAnimationFrame(
                                restablecer
                            );
                        };

                    cropImage.src =
                        urlTemporal;
                }


                function cerrarModal(
                    limpiarInput
                ) {
                    if (!modal) {
                        return;
                    }

                    modal.classList.remove(
                        'is-open'
                    );

                    modal.setAttribute(
                        'aria-hidden',
                        'true'
                    );

                    document.body
                        .style
                        .overflow =
                        '';

                    if (
                        limpiarInput
                        &&
                        input
                    ) {
                        input.value =
                            '';
                    }
                }


                if (
                    selectButton
                    &&
                    input
                ) {
                    selectButton.addEventListener(
                        'click',
                        function () {
                            input.click();
                        }
                    );
                }


                if (input) {
                    input.addEventListener(
                        'change',
                        function () {

                            const archivo =
                                input.files
                                &&
                                input.files[0];

                            if (!archivo) {
                                return;
                            }

                            if (
                                !archivo.type
                                    .startsWith(
                                        'image/'
                                    )
                            ) {
                                alert(
                                    'Selecciona una imagen válida.'
                                );

                                input.value =
                                    '';

                                return;
                            }

                            if (
                                archivo.size
                                >
                                2
                                *
                                1024
                                *
                                1024
                            ) {
                                alert(
                                    'La imagen no debe superar los 2 MB.'
                                );

                                input.value =
                                    '';

                                return;
                            }

                            abrirModal(
                                archivo
                            );
                        }
                    );
                }


                if (viewport) {

                    viewport.addEventListener(
                        'pointerdown',
                        function (event) {

                            arrastrando =
                                true;

                            inicioX =
                                event.clientX;

                            inicioY =
                                event.clientY;

                            posXInicial =
                                posX;

                            posYInicial =
                                posY;

                            viewport
                                .setPointerCapture(
                                    event.pointerId
                                );

                            viewport
                                .classList
                                .add(
                                    'is-dragging'
                                );
                        }
                    );


                    viewport.addEventListener(
                        'pointermove',
                        function (event) {

                            if (
                                !arrastrando
                            ) {
                                return;
                            }

                            posX =
                                posXInicial
                                +
                                (
                                    event.clientX
                                    -
                                    inicioX
                                );

                            posY =
                                posYInicial
                                +
                                (
                                    event.clientY
                                    -
                                    inicioY
                                );

                            renderizar();
                        }
                    );


                    const finalizar =
                        function () {

                            arrastrando =
                                false;

                            viewport
                                .classList
                                .remove(
                                    'is-dragging'
                                );
                        };

                    viewport.addEventListener(
                        'pointerup',
                        finalizar
                    );

                    viewport.addEventListener(
                        'pointercancel',
                        finalizar
                    );
                }


                if (zoom) {
                    zoom.addEventListener(
                        'input',
                        function () {

                            const tamano =
                                tamanoViewport();

                            if (
                                !tamano
                                ||
                                !naturalWidth
                                ||
                                !naturalHeight
                            ) {
                                return;
                            }

                            const escalaAnterior =
                                escala;

                            const factor =
                                Number(
                                    zoom.value
                                )
                                /
                                100;

                            const nuevaEscala =
                                escalaMinima
                                *
                                factor;

                            const centroX =
                                (
                                    tamano / 2
                                    -
                                    posX
                                )
                                /
                                escalaAnterior;

                            const centroY =
                                (
                                    tamano / 2
                                    -
                                    posY
                                )
                                /
                                escalaAnterior;

                            escala =
                                nuevaEscala;

                            posX =
                                tamano / 2
                                -
                                centroX
                                *
                                escala;

                            posY =
                                tamano / 2
                                -
                                centroY
                                *
                                escala;

                            renderizar();
                        }
                    );
                }


                if (resetButton) {
                    resetButton.addEventListener(
                        'click',
                        restablecer
                    );
                }


                function cancelar()
                {
                    cerrarModal(
                        true
                    );
                }


                if (closeButton) {
                    closeButton.addEventListener(
                        'click',
                        cancelar
                    );
                }

                if (cancelButton) {
                    cancelButton.addEventListener(
                        'click',
                        cancelar
                    );
                }

                if (backdrop) {
                    backdrop.addEventListener(
                        'click',
                        cancelar
                    );
                }


                document.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key
                            ===
                            'Escape'
                            &&
                            modal
                            &&
                            modal.classList
                                .contains(
                                    'is-open'
                                )
                        ) {
                            cancelar();
                        }
                    }
                );


                if (applyButton) {
                    applyButton.addEventListener(
                        'click',
                        function () {

                            if (
                                !naturalWidth
                                ||
                                !naturalHeight
                            ) {
                                return;
                            }

                            const viewportSize =
                                tamanoViewport();

                            const salida =
                                512;

                            const canvas =
                                document.createElement(
                                    'canvas'
                                );

                            canvas.width =
                                salida;

                            canvas.height =
                                salida;

                            const contexto =
                                canvas.getContext(
                                    '2d'
                                );

                            contexto.fillStyle =
                                '#ffffff';

                            contexto.fillRect(
                                0,
                                0,
                                salida,
                                salida
                            );

                            const proporcion =
                                salida
                                /
                                viewportSize;

                            contexto.drawImage(
                                cropImage,
                                posX
                                *
                                proporcion,
                                posY
                                *
                                proporcion,
                                naturalWidth
                                *
                                escala
                                *
                                proporcion,
                                naturalHeight
                                *
                                escala
                                *
                                proporcion
                            );

                            const dataUrl =
                                canvas.toDataURL(
                                    'image/jpeg',
                                    0.92
                                );

                            if (hidden) {
                                hidden.value =
                                    dataUrl;
                            }

                            /*
                            | El backend recibirá solamente
                            | la imagen ya recortada.
                            */
                            if (input) {
                                input.value =
                                    '';
                            }

                            /*
                            | Actualizar vista previa del perfil.
                            */
                            if (previewImage) {
                                previewImage.src =
                                    dataUrl;

                                previewImage
                                    .classList
                                    .remove(
                                        'hidden'
                                    );
                            }

                            if (previewInitials) {
                                previewInitials
                                    .classList
                                    .add(
                                        'hidden'
                                    );
                            }

                            if (saveButton) {
                                saveButton.disabled =
                                    false;
                            }

                            cerrarModal(
                                false
                            );
                        }
                    );
                }


                window.addEventListener(
                    'resize',
                    function () {

                        if (
                            modal
                            &&
                            modal.classList
                                .contains(
                                    'is-open'
                                )
                        ) {
                            restablecer();
                        }
                    }
                );

            }
        );
    </script>

</x-app-layout>
