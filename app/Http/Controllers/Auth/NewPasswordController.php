<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class NewPasswordController extends Controller
{
    /**
     * Mostrar formulario para restablecer contraseña.
     */
    public function create(Request $request)
    {
        return view(
            'auth.reset-password',
            [
                'request' => $request,
            ]
        );
    }


    /**
     * Guardar la nueva contraseña.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'token' => [
                    'required',
                ],

                'email' => [
                    'required',
                    'email',
                ],

                'password' => [
                    'required',
                    'confirmed',
                    Rules\Password::defaults(),
                ],
            ],
            [
                'token.required' =>
                    'El enlace de recuperación no es válido.',

                'email.required' =>
                    'El correo electrónico es obligatorio.',

                'email.email' =>
                    'Ingresa un correo electrónico válido.',

                'password.required' =>
                    'La nueva contraseña es obligatoria.',

                'password.confirmed' =>
                    'Las contraseñas no coinciden.',
            ]
        );


        $status =
            Password::reset(
                $request->only(
                    'email',
                    'password',
                    'password_confirmation',
                    'token'
                ),
                function (User $user) use ($request) {

                    $user->forceFill([
                        'password' =>
                            Hash::make(
                                $request->password
                            ),

                        'remember_token' =>
                            Str::random(60),
                    ])->save();


                    event(
                        new PasswordReset(
                            $user
                        )
                    );
                }
            );


        if (
            $status ===
            Password::PASSWORD_RESET
        ) {

            return redirect()
                ->route('login')
                ->with(
                    'status',
                    'Tu contraseña se actualizó correctamente. Ya puedes iniciar sesión.'
                );
        }


        $mensaje =
            match ($status) {

                Password::INVALID_TOKEN =>
                    'El enlace de recuperación no es válido o ya expiró. Solicita uno nuevo.',

                Password::INVALID_USER =>
                    'No encontramos una cuenta asociada a ese correo.',

                default =>
                    'No fue posible restablecer la contraseña. Solicita un nuevo enlace e inténtalo otra vez.',
            };


        return back()
            ->withInput(
                $request->only(
                    'email'
                )
            )
            ->withErrors([
                'email' =>
                    $mensaje,
            ]);
    }
}
