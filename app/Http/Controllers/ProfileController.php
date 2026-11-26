<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Mostrar el perfil del usuario autenticado.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }


    /**
     * Actualizar información general del perfil.
     *
     * Se conserva el flujo original de Breeze.
     */
    public function update(
        ProfileUpdateRequest $request
    ): RedirectResponse {
        $request->user()->fill(
            $request->validated()
        );

        if (
            $request->user()->isDirty(
                'email'
            )
        ) {
            $request->user()
                ->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route(
            'profile.edit'
        )->with(
            'status',
            'profile-updated'
        );
    }


    /**
     * Mostrar la foto del usuario autenticado directamente
     * desde el disco public de Laravel.
     *
     * Esto evita depender del enlace public/storage.
     */
    public function showAvatar(
        Request $request
    ) {
        $user =
            $request->user();

        if (
            !$user->avatar
            ||
            !Storage::disk(
                'public'
            )->exists(
                $user->avatar
            )
        ) {
            abort(404);
        }

        return Storage::disk(
            'public'
        )->response(
            $user->avatar,
            null,
            [
                'Cache-Control' =>
                    'no-store, no-cache, must-revalidate, max-age=0',

                'Pragma' =>
                    'no-cache',
            ]
        );
    }


    /**
     * Actualizar únicamente la foto de perfil.
     */
    public function updateAvatar(
        Request $request
    ): RedirectResponse {
        $request->validate(
            [
                'avatar' => [
                    'nullable',
                    'image',
                    'mimes:jpeg,jpg,png,webp',
                    'max:2048',
                ],

                'avatar_recortado' => [
                    'nullable',
                    'string',
                ],
            ],
            [
                'avatar.image' =>
                    'Selecciona una imagen válida.',

                'avatar.mimes' =>
                    'La foto debe ser JPG, JPEG, PNG o WEBP.',

                'avatar.max' =>
                    'La foto no debe superar los 2 MB.',
            ]
        );

        if (
            !$request->filled(
                'avatar_recortado'
            )
            &&
            !$request->hasFile(
                'avatar'
            )
        ) {
            return Redirect::route(
                'profile.edit'
            )->withErrors([
                'avatar' =>
                    'Selecciona y ajusta una foto antes de guardarla.',
            ]);
        }

        $user =
            $request->user();

        /*
        |--------------------------------------------------------------------------
        | PREPARAR NUEVO AVATAR
        |--------------------------------------------------------------------------
        */
        if (
            $request->filled(
                'avatar_recortado'
            )
        ) {
            $nuevaRuta =
                $this->guardarAvatarRecortado(
                    $request->input(
                        'avatar_recortado'
                    )
                );
        } else {
            $nuevaRuta =
                $request
                    ->file('avatar')
                    ->store(
                        'avatars',
                        'public'
                    );
        }


        /*
        |--------------------------------------------------------------------------
        | ELIMINAR FOTO ANTERIOR
        |--------------------------------------------------------------------------
        */
        $avatarAnterior =
            $user->avatar;

        $user->avatar =
            $nuevaRuta;

        $user->save();

        if (
            $avatarAnterior
            &&
            $avatarAnterior
            !==
            $nuevaRuta
        ) {
            Storage::disk(
                'public'
            )->delete(
                $avatarAnterior
            );
        }


        return Redirect::route(
            'profile.edit'
        )->with(
            'status',
            'avatar-updated'
        );
    }


    /**
     * Quitar la foto actual del usuario.
     */
    public function destroyAvatar(
        Request $request
    ): RedirectResponse {
        $user =
            $request->user();

        if ($user->avatar) {
            Storage::disk(
                'public'
            )->delete(
                $user->avatar
            );

            $user->avatar =
                null;

            $user->save();
        }

        return Redirect::route(
            'profile.edit'
        )->with(
            'status',
            'avatar-deleted'
        );
    }


    /**
     * Eliminar la cuenta.
     */
    public function destroy(
        Request $request
    ): RedirectResponse {
        $request->validateWithBag(
            'userDeletion',
            [
                'password' => [
                    'required',
                    'current_password',
                ],
            ]
        );

        $user =
            $request->user();

        Auth::logout();

        if ($user->avatar) {
            Storage::disk(
                'public'
            )->delete(
                $user->avatar
            );
        }

        $user->delete();

        $request
            ->session()
            ->invalidate();

        $request
            ->session()
            ->regenerateToken();

        return Redirect::to('/');
    }


    /**
     * Guardar la imagen recortada enviada por JavaScript.
     */
    private function guardarAvatarRecortado(
        string $dataUrl
    ): string {
        if (
            !preg_match(
                '/^data:image\/(jpeg|jpg|png|webp);base64,(.+)$/',
                $dataUrl,
                $coincidencias
            )
        ) {
            abort(
                422,
                'La imagen recortada no tiene un formato válido.'
            );
        }

        $contenido =
            base64_decode(
                $coincidencias[2],
                true
            );

        if (
            $contenido
            ===
            false
        ) {
            abort(
                422,
                'No fue posible procesar la imagen recortada.'
            );
        }

        /*
        | 3 MB como protección adicional.
        */
        if (
            strlen($contenido)
            >
            3 * 1024 * 1024
        ) {
            abort(
                422,
                'La imagen recortada es demasiado grande.'
            );
        }

        $ruta =
            'avatars/'
            .
            Str::uuid()
            .
            '.jpg';

        Storage::disk(
            'public'
        )->put(
            $ruta,
            $contenido
        );

        return $ruta;
    }
}
