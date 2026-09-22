<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketStateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'state'               => ['required', 'string', 'in:Diagnóstico,Reparación,Finalizado'],
            'technical_diagnosis' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'state.required' => 'Debes seleccionar el nuevo estado de trabajo.',
            'state.in'       => 'El estado seleccionado no es una etapa técnica válida.',
            'technical_diagnosis.max' => 'El diagnóstico técnico no debe superar los 1000 caracteres.',
        ];
    }
}
