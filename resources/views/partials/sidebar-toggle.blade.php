@auth
<style>
    /*
    |--------------------------------------------------------------------------
    | SIDEBAR COMPACTO - SotyGeo
    |--------------------------------------------------------------------------
    */

    @media (min-width: 1024px) {

        #sotygeo-sidebar {
            width: 16rem !important;
            transition:
                width .32s cubic-bezier(.4, 0, .2, 1),
                box-shadow .25s ease !important;
        }

        #sotygeo-sidebar.sotygeo-sidebar-mini {
            width: 5rem !important;
        }

        #sotygeo-sidebar.sotygeo-sidebar-mini
        .sotygeo-sidebar-label {
            display: none !important;
        }

        #sotygeo-sidebar.sotygeo-sidebar-mini
        .sotygeo-sidebar-menu-control {
            justify-content: center !important;
            padding-left: .5rem !important;
            padding-right: .5rem !important;
        }

        #sotygeo-sidebar.sotygeo-sidebar-mini
        .sotygeo-sidebar-header-inner {
            justify-content: center !important;
        }

        #sotygeo-sidebar.sotygeo-sidebar-mini
        .sotygeo-sidebar-header {
            padding-left: .75rem !important;
            padding-right: .75rem !important;
        }

        /*
        | Tooltips cuando el sidebar está contraído.
        */
        #sotygeo-sidebar.sotygeo-sidebar-mini
        .sotygeo-sidebar-menu-control[data-sidebar-tooltip] {
            position: relative;
        }

        #sotygeo-sidebar.sotygeo-sidebar-mini
        .sotygeo-sidebar-menu-control[data-sidebar-tooltip]::after {
            content: attr(data-sidebar-tooltip);

            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%) translateX(-4px);

            z-index: 9999;

            padding: 7px 10px;
            border-radius: 9px;

            background: #111827;
            color: #ffffff;

            font-size: 12px;
            font-weight: 600;
            line-height: 1;
            white-space: nowrap;

            box-shadow:
                0 8px 24px rgba(15, 23, 42, .18);

            opacity: 0;
            visibility: hidden;
            pointer-events: none;

            transition:
                opacity .16s ease,
                transform .16s ease,
                visibility .16s ease;
        }

        #sotygeo-sidebar.sotygeo-sidebar-mini
        .sotygeo-sidebar-menu-control[data-sidebar-tooltip]::before {
            content: "";

            position: absolute;
            left: calc(100% + 6px);
            top: 50%;
            transform: translateY(-50%);

            z-index: 9999;

            border-width: 5px;
            border-style: solid;
            border-color:
                transparent
                #111827
                transparent
                transparent;

            opacity: 0;
            visibility: hidden;

            transition:
                opacity .16s ease,
                visibility .16s ease;
        }

        #sotygeo-sidebar.sotygeo-sidebar-mini
        .sotygeo-sidebar-menu-control[data-sidebar-tooltip]:hover::after {
            opacity: 1;
            visibility: visible;
            transform: translateY(-50%) translateX(0);
        }

        #sotygeo-sidebar.sotygeo-sidebar-mini
        .sotygeo-sidebar-menu-control[data-sidebar-tooltip]:hover::before {
            opacity: 1;
            visibility: visible;
        }

        /*
        | Botón flotante.
        */
        #sotygeo-sidebar-toggle {
            width: 30px !important;
            height: 30px !important;
            right: -15px !important;

            border: 1px solid rgba(86, 146, 140, .25) !important;

            background: rgba(255, 255, 255, .98) !important;
            color: #56928C !important;

            box-shadow:
                0 5px 16px rgba(15, 23, 42, .13) !important;

            transition:
                transform .2s ease,
                background-color .2s ease,
                color .2s ease,
                box-shadow .2s ease !important;
        }

        #sotygeo-sidebar-toggle:hover {
            background: #56928C !important;
            color: #ffffff !important;

            transform: scale(1.08);

            box-shadow:
                0 7px 20px rgba(86, 146, 140, .28) !important;
        }

        #sotygeo-sidebar-toggle:active {
            transform: scale(.93);
        }

        #sotygeo-sidebar-toggle
        .sotygeo-sidebar-chevron {
            transition: transform .28s ease;
        }

        #sotygeo-sidebar.sotygeo-sidebar-mini
        #sotygeo-sidebar-toggle
        .sotygeo-sidebar-chevron {
            transform: rotate(180deg);
        }
    }
</style>


<script>
    document.addEventListener(
        'DOMContentLoaded',
        function () {

            /*
            |--------------------------------------------------------------------------
            | SIDEBAR CONTRAER / EXPANDIR SIN ALPINE
            |--------------------------------------------------------------------------
            */

            const sidebar =
                document.querySelector('aside');

            if (!sidebar) {
                return;
            }

            sidebar.id =
                'sotygeo-sidebar';


            /*
            | El primer botón directo del aside es la flecha actual.
            | Lo clonamos para quitar cualquier evento viejo de Alpine.
            */
            const botonViejo =
                Array
                    .from(sidebar.children)
                    .find(function (elemento) {
                        return elemento.tagName === 'BUTTON';
                    });

            if (!botonViejo) {
                return;
            }

            const boton =
                botonViejo.cloneNode(true);

            botonViejo.replaceWith(
                boton
            );

            boton.id =
                'sotygeo-sidebar-toggle';

            boton.removeAttribute(
                '@click'
            );

            boton.removeAttribute(
                'x-on:click'
            );

            boton.setAttribute(
                'type',
                'button'
            );

            boton.setAttribute(
                'aria-label',
                'Contraer menú lateral'
            );

            boton.setAttribute(
                'title',
                'Contraer menú'
            );

            /*
            | Dejamos un solo chevron.
            | Cuando el sidebar se contrae gira 180°.
            */
            boton.innerHTML =
                '<span class="material-symbols-rounded text-base sotygeo-sidebar-chevron">chevron_left</span>';


            /*
            |--------------------------------------------------------------------------
            | IDENTIFICAR TEXTOS DEL SIDEBAR
            |--------------------------------------------------------------------------
            |
            | Tu sidebar ya usa x-show="!sidebarMini" en los textos.
            | Los aprovechamos como referencia, pero el funcionamiento
            | ya no dependerá de Alpine.
            |
            */

            const etiquetas =
                Array
                    .from(
                        sidebar.querySelectorAll(
                            '[x-show]'
                        )
                    )
                    .filter(function (elemento) {

                        return (
                            elemento.getAttribute(
                                'x-show'
                            )
                            ===
                            '!sidebarMini'
                        );
                    });


            etiquetas.forEach(
                function (etiqueta) {

                    etiqueta.classList.add(
                        'sotygeo-sidebar-label'
                    );

                    const control =
                        etiqueta.closest(
                            'a, button'
                        );

                    if (
                        control
                        &&
                        control !== boton
                    ) {

                        control.classList.add(
                            'sotygeo-sidebar-menu-control'
                        );

                        const texto =
                            etiqueta.textContent
                                .replace(
                                    /\s+/g,
                                    ' '
                                )
                                .trim();

                        if (texto) {
                            control.setAttribute(
                                'data-sidebar-tooltip',
                                texto
                            );
                        }
                    }
                }
            );


            /*
            | Cabecera / logo.
            */
            const header =
                Array
                    .from(sidebar.children)
                    .find(function (elemento) {

                        return (
                            elemento.tagName === 'DIV'
                            &&
                            elemento.querySelector(
                                '.material-symbols-rounded'
                            )
                        );
                    });

            if (header) {

                header.classList.add(
                    'sotygeo-sidebar-header'
                );

                const interior =
                    header.firstElementChild;

                if (interior) {
                    interior.classList.add(
                        'sotygeo-sidebar-header-inner'
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | GUARDAR ESTADO
            |--------------------------------------------------------------------------
            */

            const CLAVE =
                'sotygeo_sidebar_mini_{{ auth()->id() }}';


            function estaMini()
            {
                return sidebar.classList.contains(
                    'sotygeo-sidebar-mini'
                );
            }


            function actualizarBoton()
            {
                const mini =
                    estaMini();

                boton.setAttribute(
                    'aria-expanded',
                    mini
                        ? 'false'
                        : 'true'
                );

                boton.setAttribute(
                    'aria-label',
                    mini
                        ? 'Expandir menú lateral'
                        : 'Contraer menú lateral'
                );

                boton.setAttribute(
                    'title',
                    mini
                        ? 'Expandir menú'
                        : 'Contraer menú'
                );
            }


            function aplicarEstado(
                mini,
                guardar
            ) {

                sidebar.classList.toggle(
                    'sotygeo-sidebar-mini',
                    mini
                );

                sidebar.classList.toggle(
                    'sotygeo-sidebar-expanded',
                    !mini
                );

                actualizarBoton();

                if (guardar) {

                    localStorage.setItem(
                        CLAVE,
                        mini
                            ? '1'
                            : '0'
                    );
                }
            }


            /*
            | Recuperar cómo lo dejó el usuario.
            */
            const guardado =
                localStorage.getItem(
                    CLAVE
                );

            aplicarEstado(
                guardado === '1',
                false
            );


            boton.addEventListener(
                'click',
                function (event) {

                    event.preventDefault();
                    event.stopPropagation();

                    aplicarEstado(
                        !estaMini(),
                        true
                    );
                }
            );

        }
    );
</script>
@endauth
