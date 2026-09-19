<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfilePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'new_password'     => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.required'         => 'La contraseña actual es obligatoria.',
            'current_password.string'           => 'La contraseña actual debe ser una cadena de texto.',
            'current_password.current_password' => 'La contraseña actual ingresada es incorrecta.',
            'new_password.required'             => 'La nueva contraseña es obligatoria.',
            'new_password.string'               => 'La nueva contraseña debe ser una cadena de texto.',
            'new_password.min'                  => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.confirmed'            => 'La confirmación de la nueva contraseña no coincide.',
            'new_password.different'            => 'La nueva contraseña debe ser distinta a la contraseña actual.',
        ];
    }
}
