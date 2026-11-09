<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Enlace expirado | SotyGeo
    </title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@400;500;600;700&family=Ubuntu:wght@300;400;500;700&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
        }

        body {
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 24px;

            font-family: 'Ubuntu', sans-serif;

            background:
                linear-gradient(
                    135deg,
                    #f8fafc 0%,
                    #eaf3fc 100%
                );

            color: #1e293b;
        }

        .contenedor {
            width: 100%;
            max-width: 560px;

            overflow: hidden;

            border: 1px solid #dbe4f0;
            border-radius: 24px;

            background: #ffffff;

            box-shadow:
                0 18px 45px rgba(15, 23, 42, 0.10);
        }

        .encabezado {
            padding: 26px;

            text-align: center;

            background: #0056b3;
            color: #ffffff;
        }

        .logo {
            width: 62px;
            height: 62px;

            margin: 0 auto 14px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 18px;

            background: rgba(255,255,255,0.15);
        }

        .marca {
            margin: 0;

            font-family: 'Josefin Sans', sans-serif;
            font-size: 27px;
            font-weight: 700;
        }

        .subtitulo {
            margin-top: 5px;

            font-size: 13px;
            color: #dbeafe;
        }

        .contenido {
            padding: 34px 30px;
            text-align: center;
        }

        .icono {
            width: 78px;
            height: 78px;

            margin: 0 auto 20px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 9999px;

            background: #fef2f2;
            color: #dc2626;
        }

        h1 {
            margin: 0;

            font-family: 'Josefin Sans', sans-serif;
            font-size: 25px;
            font-weight: 600;

            color: #1e293b;
        }

        .descripcion {
            margin: 12px auto 0;

            max-width: 420px;

            font-size: 14px;
            line-height: 1.65;

            color: #64748b;
        }

        .detalle {
            margin-top: 26px;

            padding: 16px;

            border: 1px solid #e2e8f0;
            border-radius: 15px;

            background: #f8fafc;

            text-align: left;
        }

        .fila {
            display: flex;
            justify-content: space-between;
            gap: 18px;

            padding: 7px 0;

            font-size: 13px;
        }

        .etiqueta {
            color: #64748b;
        }

        .valor {
            font-weight: 500;
            color: #334155;
            text-align: right;
        }

        .aviso {
            margin-top: 24px;

            padding-top: 20px;

            border-top: 1px solid #e2e8f0;

            font-size: 12px;
            line-height: 1.55;

            color: #94a3b8;
        }

        @media (max-width: 500px) {

            .contenido {
                padding: 28px 20px;
            }

            .fila {
                flex-direction: column;
                gap: 3px;
            }

            .valor {
                text-align: left;
            }
        }

    </style>

</head>


<body>

    <div class="contenedor">

        <div class="encabezado">

            <div class="logo">

                <svg
                    width="32"
                    height="32"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M10 13a5 5 0 007.07 0l2.12-2.12a5 5 0 00-7.07-7.07L11 4.93"
                    ></path>

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M14 11a5 5 0 00-7.07 0l-2.12 2.12a5 5 0 007.07 7.07L13 19.07"
                    ></path>

                </svg>

            </div>

            <h2 class="marca">
                SotyGeo
            </h2>

            <div class="subtitulo">
                Seguimiento temporal de vehículos
            </div>

        </div>


        <div class="contenido">

            <div class="icono">

                <svg
                    width="38"
                    height="38"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >

                    <circle
                        cx="12"
                        cy="12"
                        r="9"
                    ></circle>

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 7v5l3 2"
                    ></path>

                </svg>

            </div>


            <h1>
                Enlace expirado
            </h1>


            <p class="descripcion">
                Este enlace de seguimiento ya no se encuentra disponible porque alcanzó su fecha de expiración.
                Por seguridad, la ubicación del vehículo ya no puede consultarse mediante este acceso.
            </p>


            @if(isset($enlace))

                <div class="detalle">

                    <div class="fila">

                        <span class="etiqueta">
                            Vehículo
                        </span>

                        <span class="valor">
                            {{ $enlace->vehiculo?->nombre ?? 'No disponible' }}
                        </span>

                    </div>


                    <div class="fila">

                        <span class="etiqueta">
                            Fecha de expiración
                        </span>

                        <span class="valor">
                            {{ $enlace->fecha_expiracion?->format('d/m/Y H:i') ?? '—' }}
                        </span>

                    </div>

                </div>

            @endif


            <div class="aviso">
                Si necesitas continuar consultando la ubicación del vehículo,
                solicita al responsable de SotyGeo que genere un nuevo enlace de seguimiento.
            </div>

        </div>

    </div>

</body>

</html>
