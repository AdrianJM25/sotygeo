<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>SotyGeo · Recuperar contraseña</title>

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
                              d="M15.75 5.25a3 3 0 11-6 0 3 3 0 016 0zM4.5 21a7.5 7.5 0 0115 0M12 14.25v3m0 0l-1.5-1.5M12 17.25l1.5-1.5"/>
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-gray-900">
                    Recuperar contraseña
                </h1>

                <p class="mt-2 text-sm text-gray-500">
                    Ingresa el correo registrado en SotyGeo y te enviaremos un enlace para crear una nueva contraseña.
                </p>
            </div>


            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

                @if(session('status'))
                    <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif


                <form
                    method="POST"
                    action="{{ route('password.email') }}"
                    id="forgot-password-form"
                    class="space-y-5"
                >
                    @csrf

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
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            placeholder="correo@ejemplo.com"
                            class="w-full rounded-xl border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300' }} bg-gray-50 px-4 py-3 text-sm text-gray-900 focus:border-[#56928C] focus:ring-[#56928C]"
                        >

                        @error('email')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>


                    <button
                        id="forgot-submit"
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-4 py-3 text-sm font-semibold text-white hover:bg-gray-800 disabled:opacity-60 disabled:cursor-not-allowed"
                    >
                        <svg
                            id="forgot-spinner"
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

                        <span id="forgot-submit-text">
                            Enviar enlace de recuperación
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

            const form =
                document.getElementById('forgot-password-form');

            const button =
                document.getElementById('forgot-submit');

            const spinner =
                document.getElementById('forgot-spinner');

            const text =
                document.getElementById('forgot-submit-text');

            if (
                form
                &&
                button
                &&
                spinner
                &&
                text
            ) {
                form.addEventListener(
                    'submit',
                    function () {

                        button.disabled =
                            true;

                        spinner.classList.remove(
                            'hidden'
                        );

                        text.textContent =
                            'Enviando…';
                    }
                );
            }

        });
    </script>

</body>
</html>
