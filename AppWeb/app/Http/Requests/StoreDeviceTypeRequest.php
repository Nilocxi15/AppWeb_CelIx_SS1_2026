<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceTypeRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación aplicadas a la solicitud.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'   => ['required', 'string', 'max:50', 'unique:device_types,name'],
            'status' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Mensajes de error personalizados.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del tipo de dispositivo es obligatorio.',
            'name.string'   => 'El nombre debe ser una cadena de texto válida.',
            'name.max'      => 'El nombre no debe superar los 50 caracteres.',
            'name.unique'   => 'Ya existe un tipo de dispositivo registrado con este nombre.',
            'status.boolean'=> 'El estado debe ser verdadero o falso.',
        ];
    }
}
