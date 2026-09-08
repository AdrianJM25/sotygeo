<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SotyGeo') }} · Iniciar sesión</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes drawRoute {
            from { stroke-dashoffset: 620; }
            to { stroke-dashoffset: 0; }
        }
        @keyframes panelIn {
            from { opacity: 0; transform: translateX(-16px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .route-path {
            stroke-dasharray: 620;
            stroke-dashoffset: 620;
            animation: drawRoute 2.1s ease-out 0.3s forwards;
        }
        .panel-in { animation: panelIn 0.7s ease-out 0.1s both; }
        .card-in { animation: cardIn 0.7s ease-out 0.3s both; }

        @media (prefers-reduced-motion: reduce) {
            .route-path { stroke-dashoffset: 0; animation: none; }
            .panel-in, .card-in { animation: none; opacity: 1; transform: none; }
            .animate-ping, .vehicle-dot { animation: none !important; }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">

    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-5xl grid lg:grid-cols-2 gap-4">

            <!-- ================= PANEL IZQUIERDO: IDENTIDAD + RASTREO EN VIVO ================= -->
            <div class="panel-in hidden lg:flex flex-col justify-between bg-gray-900 rounded-2xl p-10 relative overflow-hidden min-h-[560px]">

                <!-- Cuadrícula tipo mapa, muy sutil -->
                <div class="absolute inset-0 opacity-[0.07]"
                     style="background-image: linear-gradient(#fff 1px, transparent 1px), linear-gradient(90deg, #fff 1px, transparent 1px); background-size: 32px 32px;"></div>

                <!-- Logo / marca -->
                <div class="relative z-10 flex items-center gap-2">
                    <div class="w-9 h-9 rounded-lg bg-emerald-500 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </div>
                    <span class="text-white font-semibold text-lg tracking-tight">SotyGeo</span>
                </div>

                <!-- Ilustración de ruta animada -->
                <div class="relative z-10 flex-1 flex items-center justify-center py-8">
                    <svg viewBox="0 0 320 260" class="w-full max-w-sm" fill="none">
                        <path class="route-path" d="M20 220 C 70 220, 60 140, 120 130 S 190 60, 160 40 S 260 30, 300 60"
                              stroke="#4B5563" stroke-width="3" stroke-linecap="round" />

                        <!-- Punto de partida -->
                        <circle cx="20" cy="220" r="5" fill="#6B7280" />

                        <!-- Punto actual (en vivo) -->
                        <circle cx="300" cy="60" r="14" fill="#10B981" opacity="0.2" class="animate-ping" style="transform-origin: 300px 60px;" />
                        <circle cx="300" cy="60" r="6" fill="#10B981" />

                        <!-- Vehículo recorriendo la ruta -->
                        <circle r="5" fill="#F9FAFB" class="vehicle-dot">
                            <animateMotion dur="7s" repeatCount="indefinite"
                                path="M20 220 C 70 220, 60 140, 120 130 S 190 60, 160 40 S 260 30, 300 60" />
                        </circle>
                    </svg>
                </div>

                <!-- Tagline + coordenadas en vivo -->
                <div class="relative z-10 space-y-4">
                    <p class="text-gray-300 text-base leading-relaxed max-w-xs">
                        Visibilidad total de cada unidad en tiempo real.
                    </p>

                    <div class="flex items-center gap-4 pt-2 border-t border-gray-800">
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs text-gray-400">GPS activo</span>
                        </div>
                        <div x-data="{
                                lat: 18.8842, lng: -99.2216,
                                init() {
                                    setInterval(() => {
                                        this.lat = (18.8842 + (Math.random() - 0.5) * 0.01).toFixed(4);
                                        this.lng = (-99.2216 + (Math.random() - 0.5) * 0.01).toFixed(4);
                                    }, 2000);
                                }
                             }"
                             class="text-xs font-mono text-gray-500">
                            <span x-text="lat"></span>° N, <span x-text="Math.abs(lng)"></span>° O
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= PANEL DERECHO: FORMULARIO ================= -->
            <div class="card-in bg-white rounded-2xl shadow-sm border border-gray-200 p-8 sm:p-10 flex flex-col justify-center">

                <!-- Logo visible solo en móvil, sin el panel izquierdo -->
                <div class="flex lg:hidden items-center gap-2 mb-8">
                    <div class="w-8 h-8 rounded-lg bg-gray-900 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                        </svg>
                    </div>
                    <span class="text-gray-900 font-semibold text-lg">SotyGeo</span>
                </div>

                <h1 class="text-2xl font-bold text-gray-900">Inicia sesión</h1>
                <p class="text-sm text-gray-500 mt-1 mb-8">Ingresa tus credenciales para acceder al panel.</p>

                <!-- Estado de sesión (ej. contraseña restablecida) -->
                @if (session('status'))
                    <div class="mb-6 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" x-data="{ showPassword: false, loading: false }" @submit="loading = true" class="space-y-5">
                    @csrf

                    <!-- Correo Electrónico -->
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Correo Electrónico</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                   class="bg-gray-50 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5 pl-10 transition-colors">
                        </div>
                        @error('email') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Contraseña</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </span>
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                                   class="bg-gray-50 border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-gray-900 focus:border-gray-900 block w-full p-2.5 pl-10 pr-10 transition-colors">
                            <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-700 focus:outline-none">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="showPassword" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        @error('password') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Recordarme + Olvidé contraseña -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                            <span class="ms-2 text-sm text-gray-600">Recordarme</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a class="text-sm text-gray-600 hover:text-gray-900 underline underline-offset-2" href="{{ route('password.request') }}">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <!-- Botón de acceso -->
                    <button type="submit" :disabled="loading"
                            class="w-full flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 disabled:opacity-70 disabled:cursor-not-allowed transition-colors shadow-sm">
                        <svg x-show="loading" style="display: none;" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span x-text="loading ? 'Verificando…' : 'Iniciar Sesión'"></span>
                    </button>
                </form>
            </div>

        </div>
    </div>

</body>
</html>