<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                'exists:users,email',
            ],
        ], [
            'email.required' =>
                'Ingresa tu correo electrónico.',

            'email.email' =>
                'Ingresa un correo electrónico válido.',

            'email.exists' =>
                'No existe ningún usuario registrado con ese correo.',
        ]);

        $user = User::query()
            ->where('email', $request->input('email'))
            ->firstOrFail();

        try {

            $token = Password::broker()
                ->createToken($user);

            $url = route(
                'password.reset',
                [
                    'token' => $token,
                    'email' => $user->email,
                ]
            );

            $nombre = trim(
                ($user->nombre ?? '')
                . ' '
                . ($user->apellido_paterno ?? '')
            );

            $nombreSeguro = e(
                $nombre !== ''
                    ? $nombre
                    : 'usuario'
            );

            $urlSegura = e($url);

            $anio = date('Y');

            $html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de contraseña - SotyGeo</title>
</head>

<body style="
    margin:0;
    padding:0;
    background:#f3f6f7;
    font-family:Arial, Helvetica, sans-serif;
    color:#1f2937;
">

<table
    role="presentation"
    width="100%"
    cellspacing="0"
    cellpadding="0"
    border="0"
    style="background:#f3f6f7; padding:40px 16px;"
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
                    box-shadow:0 8px 28px rgba(15,23,42,.08);
                "
            >

                <tr>
                    <td
                        align="center"
                        style="
                            background:#56928C;
                            padding:34px 24px;
                        "
                    >
                        <div style="
                            width:58px;
                            height:58px;
                            line-height:58px;
                            border-radius:16px;
                            background:rgba(255,255,255,.18);
                            color:#ffffff;
                            font-size:30px;
                            font-weight:700;
                            margin:0 auto 14px auto;
                        ">
                            S
                        </div>

                        <div style="
                            color:#ffffff;
                            font-size:26px;
                            font-weight:700;
                        ">
                            SotyGeo
                        </div>

                        <div style="
                            margin-top:6px;
                            color:#eaf5f3;
                            font-size:13px;
                        ">
                            Rastreo GPS y Gestión de Flotas
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:38px 34px 28px 34px;">

                        <h1 style="
                            margin:0 0 18px 0;
                            font-size:24px;
                            color:#111827;
                        ">
                            Restablece tu contraseña
                        </h1>

                        <p style="
                            margin:0 0 16px 0;
                            color:#4b5563;
                            font-size:15px;
                            line-height:1.7;
                        ">
                            Hola <strong>{$nombreSeguro}</strong>,
                        </p>

                        <p style="
                            margin:0 0 26px 0;
                            color:#4b5563;
                            font-size:15px;
                            line-height:1.7;
                        ">
                            Recibimos una solicitud para restablecer
                            la contraseña asociada a tu cuenta de SotyGeo.
                        </p>

                        <table
                            role="presentation"
                            cellspacing="0"
                            cellpadding="0"
                            border="0"
                            align="center"
                            style="margin:0 auto 28px auto;"
                        >
                            <tr>
                                <td
                                    align="center"
                                    bgcolor="#111827"
                                    style="border-radius:12px;"
                                >
                                    <a
                                        href="{$urlSegura}"
                                        style="
                                            display:inline-block;
                                            padding:14px 28px;
                                            font-size:15px;
                                            font-weight:700;
                                            color:#ffffff;
                                            text-decoration:none;
                                            border-radius:12px;
                                        "
                                    >
                                        Restablecer contraseña
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <div style="
                            background:#f0f7f6;
                            border:1px solid #d7e9e6;
                            border-radius:12px;
                            padding:16px 18px;
                            margin-bottom:24px;
                        ">
                            <div style="
                                color:#3f6f6b;
                                font-size:14px;
                                font-weight:700;
                                margin-bottom:6px;
                            ">
                                Aviso de seguridad
                            </div>

                            <div style="
                                color:#527a76;
                                font-size:13px;
                                line-height:1.6;
                            ">
                                Si tú no solicitaste este cambio,
                                puedes ignorar este correo.
                                Tu contraseña actual seguirá funcionando.
                            </div>
                        </div>

                        <p style="
                            margin:0 0 8px 0;
                            color:#6b7280;
                            font-size:12px;
                            line-height:1.6;
                        ">
                            Si el botón no funciona, copia y pega
                            este enlace en tu navegador:
                        </p>

                        <p style="
                            margin:0;
                            font-size:11px;
                            line-height:1.6;
                            word-break:break-all;
                        ">
                            <a
                                href="{$urlSegura}"
                                style="
                                    color:#56928C;
                                    text-decoration:none;
                                "
                            >
                                {$urlSegura}
                            </a>
                        </p>

                    </td>
                </tr>

                <tr>
                    <td style="
                        padding:20px 34px 30px 34px;
                        border-top:1px solid #eef1f3;
                        text-align:center;
                    ">
                        <p style="
                            margin:0;
                            color:#9ca3af;
                            font-size:11px;
                            line-height:1.6;
                        ">
                            Este mensaje fue enviado automáticamente
                            por SotyGeo. Por favor, no respondas a este correo.
                        </p>

                        <p style="
                            margin:8px 0 0 0;
                            color:#9ca3af;
                            font-size:11px;
                        ">
                            © {$anio} SotyGeo
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
HTML;

            Mail::html(
                $html,
                function ($mensaje) use ($user) {

                    $mensaje
                        ->to($user->email)
                        ->subject(
                            'Recuperación de contraseña - SotyGeo'
                        );
                }
            );

            Log::info(
                'SotyGeo: correo de recuperación enviado.',
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]
            );

        } catch (\Throwable $e) {

            Log::error(
                'SotyGeo: error al enviar recuperación de contraseña.',
                [
                    'email' => $request->input('email'),
                    'error' => $e->getMessage(),
                ]
            );

            report($e);

            return back()
                ->withInput(
                    $request->only('email')
                )
                ->withErrors([
                    'email' =>
                        'No fue posible enviar el correo de recuperación. Inténtalo nuevamente.',
                ]);
        }

        return back()
            ->with(
                'status',
                'Correo de recuperación enviado a '
                . $user->email
                . '. Revisa también Spam.'
            );
    }
}
