@auth
    <form
        id="sotygeo-logout-inactividad"
        method="POST"
        action="{{ route('logout') }}"
        class="hidden"
    >
        @csrf
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /*
            |--------------------------------------------------------------------------
            | CIERRE AUTOMÁTICO POR INACTIVIDAD - SotyGeo
            |--------------------------------------------------------------------------
            |
            | Tiempo máximo sin actividad humana: 15 minutos.
            | Las peticiones automáticas del mapa/GPS NO reinician el contador.
            |
            */

            const LIMITE_INACTIVIDAD_MS =
                5 * 60 * 1000;

            const CLAVE_ULTIMA_ACTIVIDAD =
                'sotygeo_ultima_actividad_usuario_{{ auth()->id() }}';

            const CLAVE_CIERRE_INACTIVIDAD =
                'sotygeo_cierre_por_inactividad';

            const formularioLogout =
                document.getElementById(
                    'sotygeo-logout-inactividad'
                );

            let cerrandoSesion =
                false;

            let ultimoRegistroMovimiento =
                0;


            function ahora()
            {
                return Date.now();
            }


            function guardarActividad()
            {
                if (cerrandoSesion) {
                    return;
                }

                localStorage.setItem(
                    CLAVE_ULTIMA_ACTIVIDAD,
                    ahora().toString()
                );
            }


            function guardarMovimientoConLimite()
            {
                const tiempoActual =
                    ahora();

                if (
                    tiempoActual
                    -
                    ultimoRegistroMovimiento
                    >=
                    5000
                ) {
                    ultimoRegistroMovimiento =
                        tiempoActual;

                    guardarActividad();
                }
            }


            function obtenerUltimaActividad()
            {
                const valor =
                    localStorage.getItem(
                        CLAVE_ULTIMA_ACTIVIDAD
                    );

                if (!valor) {
                    return ahora();
                }

                const convertido =
                    Number(valor);

                return Number.isFinite(convertido)
                    ? convertido
                    : ahora();
            }


            function cerrarPorInactividad()
            {
                if (
                    cerrandoSesion
                    ||
                    !formularioLogout
                ) {
                    return;
                }

                cerrandoSesion =
                    true;

                localStorage.removeItem(
                    CLAVE_ULTIMA_ACTIVIDAD
                );

                /*
                | Esta marca vive solo en esta pestaña y sirve
                | para mostrar el aviso al llegar al login.
                */
                sessionStorage.setItem(
                    CLAVE_CIERRE_INACTIVIDAD,
                    '1'
                );

                const datosLogout =
                    new FormData(
                        formularioLogout
                    );

                fetch(
                    formularioLogout.action,
                    {
                        method: 'POST',
                        body: datosLogout,
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With':
                                'XMLHttpRequest'
                        }
                    }
                )
                .then(function () {
                    window.location.href =
                        '{{ route('login') }}';
                })
                .catch(function () {

                    /*
                    | Si por alguna razón falla fetch,
                    | usamos el cierre normal como respaldo.
                    */
                    formularioLogout.submit();
                });
            }


            function verificarInactividad()
            {
                if (cerrandoSesion) {
                    return;
                }

                const tiempoInactivo =
                    ahora()
                    -
                    obtenerUltimaActividad();

                if (
                    tiempoInactivo
                    >=
                    LIMITE_INACTIVIDAD_MS
                ) {
                    cerrarPorInactividad();
                }
            }


            window.addEventListener(
                'mousemove',
                guardarMovimientoConLimite,
                { passive: true }
            );

            window.addEventListener(
                'mousedown',
                guardarActividad,
                { passive: true }
            );

            window.addEventListener(
                'keydown',
                guardarActividad
            );

            window.addEventListener(
                'scroll',
                guardarActividad,
                { passive: true }
            );

            window.addEventListener(
                'touchstart',
                guardarActividad,
                { passive: true }
            );

            window.addEventListener(
                'pointerdown',
                guardarActividad,
                { passive: true }
            );


            window.addEventListener(
                'storage',
                function (event) {

                    if (
                        event.key
                        ===
                        CLAVE_ULTIMA_ACTIVIDAD
                    ) {
                        verificarInactividad();
                    }
                }
            );


            document.addEventListener(
                'visibilitychange',
                function () {

                    if (
                        document.visibilityState
                        ===
                        'visible'
                    ) {
                        verificarInactividad();
                    }
                }
            );


            guardarActividad();

            setInterval(
                verificarInactividad,
                30000
            );

        });
    </script>
@endauth
