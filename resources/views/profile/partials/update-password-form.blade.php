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
