<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SotyGeo · Restablecer contraseña</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-900">

<div class="min-h-screen flex items-center justify-center px-4 py-8">

    <div class="w-full max-w-md">

        <div class="text-center mb-6">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-[#56928C] text-white flex items-center justify-center shadow-sm mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M15 7a3 3 0 11-6 0 3 3 0 016 0zm-7.5 5.5h9A2.5 2.5 0 0119 15v4H5v-4a2.5 2.5 0 012.5-2.5zM12 16v2"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900">
                Restablecer contraseña
            </h1>

            <p class="mt-2 text-sm text-gray-500">
                Crea una nueva contraseña para volver a acceder a tu cuenta de SotyGeo.
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

            @if ($errors->any())
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                    @foreach ($errors->all() as $error)
                        <p class="text-sm text-red-600">
                            {{ $error }}
                        </p>
                    @endforeach
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('password.store') }}"
                id="reset-password-form"
                class="space-y-5"
            >
                @csrf

                <input
                    type="hidden"
                    name="token"
                    value="{{ $request->route('token') }}"
                >

                <div>
                    <label
                        for="email"
                        class="block mb-2 text-sm font-medium text-gray-800"
                    >
                        Correo electrónico
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email', $request->email) }}"
                        required
                        readonly
                        autocomplete="username"
                        class="w-full rounded-xl border border-gray-300 bg-gray-100 px-4 py-3 text-sm text-gray-700"
                    >
                </div>

                <div>
                    <label
                        for="password"
                        class="block mb-2 text-sm font-medium text-gray-800"
                    >
                        Nueva contraseña
                    </label>

                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            minlength="8"
                            placeholder="Mínimo 8 caracteres"
                            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 pr-12 text-sm text-gray-900 focus:border-[#56928C] focus:ring-[#56928C]"
                        >

                        <button
                            type="button"
                            data-toggle-password="password"
                            class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-gray-700"
                            aria-label="Mostrar u ocultar contraseña"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M2.25 12s3.75-6 9.75-6 9.75 6 9.75 6-3.75 6-9.75 6S2.25 12 2.25 12z"/>
                                <circle cx="12" cy="12" r="2.5" stroke-width="1.8"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label
                        for="password_confirmation"
                        class="block mb-2 text-sm font-medium text-gray-800"
                    >
                        Confirmar nueva contraseña
                    </label>

                    <div class="relative">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            minlength="8"
                            placeholder="Repite la contraseña"
                            class="w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 pr-12 text-sm text-gray-900 focus:border-[#56928C] focus:ring-[#56928C]"
                        >

                        <button
                            type="button"
                            data-toggle-password="password_confirmation"
                            class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-gray-700"
                            aria-label="Mostrar u ocultar contraseña"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M2.25 12s3.75-6 9.75-6 9.75 6 9.75 6-3.75 6-9.75 6S2.25 12 2.25 12z"/>
                                <circle cx="12" cy="12" r="2.5" stroke-width="1.8"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button
                    id="reset-submit"
                    type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white hover:bg-gray-800 disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <svg
                        id="reset-spinner"
                        class="hidden w-4 h-4 animate-spin"
                        viewBox="0 0 24 24"
                        fill="none"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>

                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                        ></path>
                    </svg>

                    <span id="reset-submit-text">
                        Guardar nueva contraseña
                    </span>
                </button>

            </form>

            <div class="mt-5 text-center">
                <a
                    href="{{ route('login') }}"
                    class="text-sm font-medium text-[#477e79] hover:underline"
                >
                    Volver a iniciar sesión
                </a>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        document
            .querySelectorAll('[data-toggle-password]')
            .forEach(function (button) {

                button.addEventListener('click', function () {

                    const id =
                        button.getAttribute('data-toggle-password');

                    const input =
                        document.getElementById(id);

                    if (!input) {
                        return;
                    }

                    input.type =
                        input.type === 'password'
                            ? 'text'
                            : 'password';
                });
            });

        const form =
            document.getElementById('reset-password-form');

        const button =
            document.getElementById('reset-submit');

        const spinner =
            document.getElementById('reset-spinner');

        const text =
            document.getElementById('reset-submit-text');

        if (
            form
            &&
            button
            &&
            spinner
            &&
            text
        ) {
            form.addEventListener('submit', function () {

                button.disabled =
                    true;

                spinner.classList.remove(
                    'hidden'
                );

                text.textContent =
                    'Guardando…';
            });
        }

    });
</script>

</body>
</html>
