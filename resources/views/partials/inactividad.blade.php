
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
            | FN.24 - CIERRE AUTOMÁTICO POR INACTIVIDAD
            |--------------------------------------------------------------------------
            |
            | Tiempo máximo sin actividad humana: 5 minutos.
            | Las solicitudes automáticas del mapa y GPS
            | no reinician el contador.
            |
            */

            const LIMITE_INACTIVIDAD_MS =
                5 * 60 * 1000;

            const CLAVE_ULTIMA_ACTIVIDAD =
                'sotygeo_ultima_actividad_usuario_{{ auth()->id() }}';

            const CLAVE_CIERRE_INACTIVIDAD =
                'sotygeo_cierre_por_inactividad';

            const URL_LOGIN =
                @json(route('login'));

            const formularioLogout =
                document.getElementById(
                    'sotygeo-logout-inactividad'
                );

            let cerrandoSesion = false;

            let ultimoRegistroMovimiento = 0;

            let cierrePendiente = false;


            function ahora()
            {
                return Date.now();
            }


            function guardarActividad()
            {
                if (cerrandoSesion || cierrePendiente) {
                    return;
                }

                localStorage.setItem(
                    CLAVE_ULTIMA_ACTIVIDAD,
                    ahora().toString()
                );
            }


            function guardarMovimientoConLimite()
            {
                const tiempoActual = ahora();

                if (
                    tiempoActual -
                    ultimoRegistroMovimiento >= 5000
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

                const convertido = Number(valor);

                return Number.isFinite(convertido)
                    ? convertido
                    : ahora();
            }


            function redirigirAlLogin()
            {
                localStorage.removeItem(
                    CLAVE_ULTIMA_ACTIVIDAD
                );

                sessionStorage.setItem(
                    CLAVE_CIERRE_INACTIVIDAD,
                    '1'
                );

                /*
                | Usar replace evita regresar fácilmente
                | a la página anterior mediante el historial.
                */

                window.location.replace(URL_LOGIN);
            }


            async function cerrarPorInactividad()
            {
                if (
                    cerrandoSesion ||
                    !formularioLogout
                ) {
                    return;
                }

                cerrandoSesion = true;
                cierrePendiente = true;

                try {

                    const datosLogout =
                        new FormData(formularioLogout);

                    const respuesta = await fetch(
                        formularioLogout.action,
                        {
                            method: 'POST',
                            body: datosLogout,
                            credentials: 'same-origin',
                            cache: 'no-store',

                            headers: {
                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'Accept':
                                    'application/json'
                            }
                        }
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | SESIÓN EXPIRADA O TOKEN CSRF INVÁLIDO
                    |--------------------------------------------------------------------------
                    */

                    if (
                        respuesta.status === 419 ||
                        respuesta.status === 401
                    ) {

                        /*
                        | No reutilizar el formulario vencido.
                        | Solicitar una nueva página de acceso.
                        */

                        redirigirAlLogin();

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | COMPROBAR RESPUESTA DEL SERVIDOR
                    |--------------------------------------------------------------------------
                    */

                    if (!respuesta.ok) {
                        throw new Error(
                            'Error al cerrar sesión: ' +
                            respuesta.status
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CIERRE EXITOSO
                    |--------------------------------------------------------------------------
                    */

                    redirigirAlLogin();

                } catch (error) {

                    console.error(
                        'SotyGeo: Error durante el cierre por inactividad.',
                        error
                    );

                    /*
                    | No reenviar un formulario con un
                    | token CSRF posiblemente vencido.
                    |
                    | Permitir que el siguiente ciclo
                    | vuelva a intentar el cierre.
                    */

                    cerrandoSesion = false;
                }
            }


            function verificarInactividad()
            {
                if (cerrandoSesion) {
                    return;
                }

                if (cierrePendiente) {
                    cerrarPorInactividad();
                    return;
                }

                const tiempoInactivo =
                    ahora() -
                    obtenerUltimaActividad();

                if (
                    tiempoInactivo >=
                    LIMITE_INACTIVIDAD_MS
                ) {
                    cerrarPorInactividad();
                }
            }


            /*
            |--------------------------------------------------------------------------
            | EVENTOS DE ACTIVIDAD HUMANA
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | SINCRONIZACIÓN ENTRE PESTAÑAS
            |--------------------------------------------------------------------------
            */

            window.addEventListener(
                'storage',
                function (event) {

                    if (
                        event.key ===
                        CLAVE_ULTIMA_ACTIVIDAD
                    ) {
                        verificarInactividad();
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | VERIFICACIÓN AL REGRESAR A LA PESTAÑA
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'visibilitychange',
                function () {

                    if (
                        document.visibilityState ===
                        'visible'
                    ) {
                        verificarInactividad();
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | INICIAR CONTROL DE INACTIVIDAD
            |--------------------------------------------------------------------------
            */

            guardarActividad();

            setInterval(
                verificarInactividad,
                5000
            );

        });
    </script>
@endauth
