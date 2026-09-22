<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'string', 'min:3', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required' => 'Debes escribir el contenido de la nota de seguimiento.',
            'note.min'      => 'La nota técnica debe tener al menos 3 caracteres.',
            'note.max'      => 'La nota técnica no puede superar los 1000 caracteres.',
        ];
    }
}
