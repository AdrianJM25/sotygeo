<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a SotyGeo</title>
</head>

<body style="
    margin:0;
    padding:0;
    background-color:#f4f7f7;
    font-family:Arial, Helvetica, sans-serif;
    color:#1f2937;
">

<table
    role="presentation"
    width="100%"
    cellspacing="0"
    cellpadding="0"
    border="0"
    style="background-color:#f4f7f7; padding:40px 15px;"
>
    <tr>
        <td align="center">

            <table
                role="presentation"
                width="100%"
                cellspacing="0"
                cellpadding="0"
                border="0"
                style="
                    max-width:600px;
                    background:#ffffff;
                    border-radius:18px;
                    overflow:hidden;
                    box-shadow:0 10px 30px rgba(0,0,0,0.08);
                "
            >

                <!-- ENCABEZADO -->
                <tr>
                    <td
                        align="center"
                        style="
                            background-color:#56928C;
                            padding:35px 25px 32px 25px;
                        "
                    >

                        <div style="
                            width:58px;
                            height:58px;
                            line-height:58px;
                            border-radius:16px;
                            background:rgba(255,255,255,0.18);
                            color:#ffffff;
                            font-size:30px;
                            font-weight:bold;
                            margin:0 auto 15px auto;
                        ">
                            S
                        </div>

                        <div style="
                            font-size:28px;
                            font-weight:bold;
                            color:#ffffff;
                            margin-bottom:7px;
                        ">
                            SotyGeo
                        </div>

                        <div style="
                            font-size:14px;
                            color:#e8f3f2;
                        ">
                            Sistema Web de Rastreo GPS y Gestión de Flotas
                        </div>

                    </td>
                </tr>


                <!-- CONTENIDO -->
                <tr>
                    <td style="padding:38px 42px;">

                        <h1 style="
                            margin:0 0 15px 0;
                            text-align:center;
                            font-size:24px;
                            color:#263238;
                        ">
                            ¡Bienvenido a SotyGeo!
                        </h1>

                        <p style="
                            font-size:16px;
                            line-height:1.7;
                            margin:0 0 20px 0;
                            color:#4b5563;
                        ">
                            Hola <strong>{{ $nombre }}</strong>,
                        </p>

                        <p style="
                            font-size:16px;
                            line-height:1.7;
                            margin:0 0 25px 0;
                            color:#4b5563;
                        ">
                            Se ha creado una cuenta para ti en SotyGeo.
                            Para comenzar a utilizar el sistema solo necesitas
                            establecer tu contraseña.
                        </p>


                        <!-- USUARIO -->
                        <table
                            width="100%"
                            cellspacing="0"
                            cellpadding="0"
                            border="0"
                            style="
                                background:#f2f7f7;
                                border:1px solid #dbe8e7;
                                border-radius:12px;
                                margin-bottom:28px;
                            "
                        >
                            <tr>
                                <td style="padding:18px 20px;">

                                    <div style="
                                        font-size:12px;
                                        text-transform:uppercase;
                                        color:#6b7280;
                                        margin-bottom:7px;
                                        font-weight:bold;
                                        letter-spacing:0.5px;
                                    ">
                                        Tu usuario de acceso
                                    </div>

                                    <div style="
                                        font-size:16px;
                                        color:#39746f;
                                        font-weight:bold;
                                    ">
                                        {{ $email }}
                                    </div>

                                </td>
                            </tr>
                        </table>


                        <p style="
                            text-align:center;
                            font-size:15px;
                            color:#4b5563;
                            margin-bottom:22px;
                        ">
                            Haz clic en el siguiente botón para crear tu contraseña:
                        </p>


                        <!-- BOTÓN -->
                        <table
                            role="presentation"
                            cellspacing="0"
                            cellpadding="0"
                            border="0"
                            align="center"
                        >
                            <tr>
                                <td
                                    align="center"
                                    bgcolor="#56928C"
                                    style="
                                        border-radius:10px;
                                    "
                                >
                                    <a
                                        href="{{ $url }}"
                                        style="
                                            display:inline-block;
                                            padding:15px 32px;
                                            font-size:16px;
                                            font-weight:bold;
                                            color:#ffffff;
                                            text-decoration:none;
                                            border-radius:10px;
                                        "
                                    >
                                        Crear mi contraseña
                                    </a>
                                </td>
                            </tr>
                        </table>


                        <!-- SEGURIDAD -->
                        <table
                            width="100%"
                            cellspacing="0"
                            cellpadding="0"
                            border="0"
                            style="
                                margin-top:32px;
                                background:#fffaf0;
                                border-left:4px solid #e6b45e;
                                border-radius:6px;
                            "
                        >
                            <tr>
                                <td style="
                                    padding:15px 17px;
                                    font-size:13px;
                                    line-height:1.6;
                                    color:#6b5a36;
                                ">
                                    <strong>Importante:</strong>
                                    este enlace tiene una vigencia limitada por motivos
                                    de seguridad. Si expira, solicita al administrador
                                    que te envíe uno nuevo.
                                </td>
                            </tr>
                        </table>


                        <!-- LINK ALTERNATIVO -->
                        <p style="
                            margin-top:28px;
                            font-size:12px;
                            line-height:1.6;
                            color:#9ca3af;
                        ">
                            Si el botón no funciona, copia y pega este enlace en tu navegador:
                        </p>

                        <p style="
                            font-size:11px;
                            line-height:1.5;
                            word-break:break-all;
                            color:#56928C;
                        ">
                            {{ $url }}
                        </p>

                    </td>
                </tr>


                <!-- FOOTER -->
                <tr>
                    <td
                        align="center"
                        style="
                            background:#f7f9f9;
                            border-top:1px solid #edf1f1;
                            padding:24px 30px;
                        "
                    >

                        <div style="
                            font-size:13px;
                            font-weight:bold;
                            color:#56928C;
                            margin-bottom:6px;
                        ">
                            SotyGeo
                        </div>

                        <div style="
                            font-size:11px;
                            line-height:1.6;
                            color:#9ca3af;
                        ">
                            Rastreo GPS y Gestión de Flotas
                            <br>
                            © {{ date('Y') }} SotyGeo
                        </div>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>