<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Reglas para actualizar el perfil del usuario autenticado.
     */
    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],

            'apellido_paterno' => [
                'required',
                'string',
                'max:255',
            ],

            'apellido_materno' => [
                'nullable',
                'string',
                'max:255',
            ],

            'telefono' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',

                Rule::unique(
                    User::class,
                    'email'
                )->ignore(
                    $this->user()->id
                ),
            ],
        ];
    }


    /**
     * Mensajes de validación en español.
     */
    public function messages(): array
    {
        return [
            'nombre.required' =>
                'Ingresa tu nombre.',

            'apellido_paterno.required' =>
                'Ingresa tu apellido paterno.',

            'email.required' =>
                'Ingresa tu correo electrónico.',

            'email.email' =>
                'Ingresa un correo electrónico válido.',

            'email.unique' =>
                'Ese correo electrónico ya está registrado.',

            'telefono.max' =>
                'El teléfono no debe superar los 20 caracteres.',
        ];
    }
}
