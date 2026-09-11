<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SotyGeo | Rastreo GPS y Gestión Logística Avanzada</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />
    
    <!-- Tailwind CSS (Asegúrate de que Vite esté configurado o usa este CDN para probar) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js (Si no lo cargas desde app.js) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-panel { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        
        /* Animaciones personalizadas de entrada */
        .fade-in-up { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
        .fade-in-up.visible { opacity: 1; transform: translateY(0); }
        
        /* Patrón de fondo SVG */
        .bg-grid-pattern { background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px); background-size: 32px 32px; }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-900 selection:bg-indigo-500 selection:text-white"
      x-data="{ scrolled: false }" 
      @scroll.window="scrolled = (window.pageYOffset > 20)">

    <!-- Navegación Sticky -->
    <nav :class="{ 'bg-white/80 backdrop-blur-md shadow-sm': scrolled, 'bg-transparent': !scrolled }" 
         class="fixed w-full z-[100] transition-all duration-300 border-b border-transparent"
         :class="{ 'border-slate-200': scrolled }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-600/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <span class="text-2xl font-bold tracking-tight" :class="{ 'text-slate-900': scrolled, 'text-white': !scrolled }">SotyGeo</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm font-semibold transition-colors" :class="{ 'text-slate-700 hover:text-indigo-600': scrolled, 'text-slate-200 hover:text-white': !scrolled }">Panel de Control</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold transition-colors" :class="{ 'text-slate-700 hover:text-indigo-600': scrolled, 'text-slate-200 hover:text-white': !scrolled }">Iniciar Sesión</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-md hover:shadow-lg hover:shadow-indigo-600/30">
                                    Crear Cuenta
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section con Fondo Dinámico -->
    <div class="relative bg-slate-950 min-h-[90vh] flex items-center overflow-hidden">
        <!-- Elementos decorativos de fondo -->
        <div class="absolute inset-0 bg-grid-pattern opacity-20"></div>
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-indigo-600 rounded-full mix-blend-multiply filter blur-[128px] opacity-70 animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-emerald-600 rounded-full mix-blend-multiply filter blur-[128px] opacity-50"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 w-full text-center lg:text-left flex flex-col lg:flex-row items-center gap-16">
            <div class="lg:w-1/2" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full glass-panel text-indigo-300 text-sm font-medium mb-6 transition-all duration-700 transform translate-y-4 opacity-0" :class="{ 'translate-y-0 opacity-100': show }">
                    <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span></span>
                    Plataforma SaaS v2.0 Disponible
                </div>
                <h1 class="text-5xl lg:text-7xl font-extrabold text-white tracking-tight mb-8 leading-tight transition-all duration-700 delay-100 transform translate-y-4 opacity-0" :class="{ 'translate-y-0 opacity-100': show }">
                    Logística y control <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">en tiempo real.</span>
                </h1>
                <p class="text-lg text-slate-300 mb-10 max-w-2xl mx-auto lg:mx-0 transition-all duration-700 delay-200 transform translate-y-4 opacity-0" :class="{ 'translate-y-0 opacity-100': show }">
                    Monitorea tu flotilla, establece geocercas precisas y audita históricos de rutas con la infraestructura tecnológica más robusta y una interfaz limpia diseñada para la eficiencia.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start transition-all duration-700 delay-300 transform translate-y-4 opacity-0" :class="{ 'translate-y-0 opacity-100': show }">
                    <a href="{{ route('login') }}" class="px-8 py-4 rounded-full text-white bg-indigo-600 hover:bg-indigo-500 font-semibold text-lg transition-all shadow-[0_0_20px_rgba(79,70,229,0.4)] hover:shadow-[0_0_30px_rgba(79,70,229,0.6)] flex items-center justify-center gap-2">
                        Explorar Dashboard
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Dashboard Mockup Animado -->
            <div class="lg:w-1/2 relative perspective-1000 w-full max-w-lg lg:max-w-none mx-auto" x-data="{ show: false }" x-init="setTimeout(() => show = true, 400)">
                <div class="transition-all duration-1000 transform translate-x-8 opacity-0 rotate-y-[-10deg] rotate-x-[5deg]" :class="{ 'translate-x-0 opacity-100': show }" style="transform-style: preserve-3d;">
                    <div class="glass-panel p-4 rounded-2xl shadow-2xl relative z-20 bg-slate-900/60 border border-slate-700/50">
                        <div class="flex items-center gap-2 mb-4 border-b border-slate-700/50 pb-4">
                            <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex gap-4 items-center p-3 rounded-xl bg-slate-800/50">
                                <div class="w-10 h-10 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="h-2 w-24 bg-slate-600 rounded mb-2"></div>
                                    <div class="h-2 w-32 bg-slate-700 rounded"></div>
                                </div>
                                <div class="text-emerald-400 text-xs font-bold px-2 py-1 bg-emerald-400/10 rounded-full">En Ruta</div>
                            </div>
                            <!-- Repetición para simular lista de vehículos -->
                            <div class="flex gap-4 items-center p-3 rounded-xl bg-slate-800/50">
                                <div class="w-10 h-10 rounded-lg bg-cyan-500/20 flex items-center justify-center text-cyan-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="h-2 w-20 bg-slate-600 rounded mb-2"></div>
                                    <div class="h-2 w-28 bg-slate-700 rounded"></div>
                                </div>
                                <div class="text-rose-400 text-xs font-bold px-2 py-1 bg-rose-400/10 rounded-full">Alerta</div>
                            </div>
                        </div>
                    </div>
                    <!-- Elemento flotante trasero -->
                    <div class="absolute -right-12 -bottom-12 glass-panel p-4 rounded-xl shadow-2xl z-10 w-48 bg-slate-900/80 border border-slate-700 animate-[bounce_4s_infinite]">
                        <div class="text-slate-400 text-xs mb-1">Geocerca Activa</div>
                        <div class="text-white font-bold text-lg">Zona Norte</div>
                        <div class="mt-2 h-1 w-full bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500 w-3/4"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Cifras (Estadísticas) -->
    <div class="bg-indigo-600 relative overflow-hidden">
        <div class="absolute inset-0 bg-grid-pattern opacity-10"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10"
             x-data="{ shown: false }" 
             x-intersect.once="shown = true">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 divide-x divide-indigo-500/50">
                
                <div class="text-center px-4 fade-in-up" :class="shown ? 'visible' : ''" style="transition-delay: 0ms;">
                    <div class="text-4xl lg:text-5xl font-black text-white mb-2" 
                         x-data="{ count: 0 }" 
                         x-effect="if(shown) { let i = setInterval(() => { count += 150; if(count >= 15000) { count = 15000; clearInterval(i); } }, 20); }">
                        <span x-text="count + '+'">0</span>
                    </div>
                    <div class="text-indigo-200 font-medium">Vehículos Rastreables</div>
                </div>

                <div class="text-center px-4 fade-in-up" :class="shown ? 'visible' : ''" style="transition-delay: 100ms;">
                    <div class="text-4xl lg:text-5xl font-black text-white mb-2" 
                         x-data="{ count: 0 }" 
                         x-effect="if(shown) { let i = setInterval(() => { count += 1; if(count >= 99) { count = 99; clearInterval(i); } }, 20); }">
                        <span x-text="count + '.9%'">0</span>
                    </div>
                    <div class="text-indigo-200 font-medium">Uptime del Sistema</div>
                </div>

                <div class="text-center px-4 fade-in-up" :class="shown ? 'visible' : ''" style="transition-delay: 200ms;">
                    <div class="text-4xl lg:text-5xl font-black text-white mb-2" 
                         x-data="{ count: 0 }" 
                         x-effect="if(shown) { let i = setInterval(() => { count += 5; if(count >= 500) { count = 500; clearInterval(i); } }, 20); }">
                        <span x-text="count + '+'">0</span>
                    </div>
                    <div class="text-indigo-200 font-medium">Empresas Logísticas</div>
                </div>

                <div class="text-center px-4 fade-in-up" :class="shown ? 'visible' : ''" style="transition-delay: 300ms;">
                    <div class="text-4xl lg:text-5xl font-black text-white mb-2" 
                         x-data="{ count: 0 }" 
                         x-effect="if(shown) { let i = setInterval(() => { count += 10; if(count >= 150) { count = 150; clearInterval(i); } }, 20); }">
                        <span x-text="count + 'M'">0</span>
                    </div>
                    <div class="text-indigo-200 font-medium">Eventos Procesados/Mes</div>
                </div>

            </div>
        </div>
    </div>

    <!-- Sección de Módulos (Bloques de colores) -->
    <div class="py-24 bg-slate-50" x-data="{ shown: false }" x-intersect.margin.-100px="shown = true">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 fade-in-up" :class="shown ? 'visible' : ''">
                <h2 class="text-indigo-600 font-semibold tracking-wide uppercase text-sm mb-2">Herramientas Profesionales</h2>
                <h3 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Todo lo que tu operación necesita en un solo lugar</h3>
                <p class="text-slate-600 text-lg">Olvídate de sistemas fragmentados. SotyGeo unifica el hardware y el software con una interfaz hiperreactiva basada en Alpine.js y Tailwind CSS.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                <!-- Bloque 1: Índigo -->
                <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100 hover:-translate-y-2 transition-transform duration-300 fade-in-up relative overflow-hidden group" :class="shown ? 'visible' : ''" style="transition-delay: 100ms;">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg shadow-indigo-600/30 relative z-10">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3 relative z-10">Rastreo en Tiempo Real</h4>
                    <p class="text-slate-600 leading-relaxed relative z-10">Conexión directa con dispositivos físicos (IMEI). Visualiza posiciones, define colores de íconos y agrupa por flotillas al instante.</p>
                </div>

                <!-- Bloque 2: Esmeralda -->
                <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100 hover:-translate-y-2 transition-transform duration-300 fade-in-up relative overflow-hidden group" :class="shown ? 'visible' : ''" style="transition-delay: 200ms;">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div class="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg shadow-emerald-500/30 relative z-10">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3 relative z-10">Auditoría de Geocercas</h4>
                    <p class="text-slate-600 leading-relaxed relative z-10">Módulo dedicado para consultar historiales de entrada, salida y alertas. Filtra por vehículo, zona y rango de fechas sin recargas.</p>
                </div>

                <!-- Bloque 3: Ámbar -->
                <div class="bg-white rounded-3xl p-8 shadow-xl shadow-slate-200/50 border border-slate-100 hover:-translate-y-2 transition-transform duration-300 fade-in-up relative overflow-hidden group" :class="shown ? 'visible' : ''" style="transition-delay: 300ms;">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-amber-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110"></div>
                    <div class="w-14 h-14 bg-amber-500 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg shadow-amber-500/30 relative z-10">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3 relative z-10">Gestión de Tiempos</h4>
                    <p class="text-slate-600 leading-relaxed relative z-10">Actualiza las horas de corte de ruta (6h, 12h, 24h, 168h) con un solo clic desde el directorio principal mediante modales teletransportados.</p>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                </div>
                <span class="text-xl font-bold text-white tracking-tight">SotyGeo</span>
            </div>
            <div class="text-sm">
                &copy; {{ date('Y') }} Todos los derechos reservados. Plataforma de tecnología IoT.
            </div>
        </div>
    </footer>
</body>
</html>