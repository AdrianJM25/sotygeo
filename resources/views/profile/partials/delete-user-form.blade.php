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
