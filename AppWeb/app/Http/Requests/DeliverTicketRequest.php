<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliverTicketRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación aplicadas a la solicitud de entrega.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'return_date'    => ['required', 'date'],
            'amount_to_pay'  => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'in:EFECTIVO,TARJETA,TRANSFERENCIA'],
            'delivery_notes' => ['nullable', 'string', 'max:500'],
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
            'return_date.required'    => 'La fecha y hora de entrega es obligatoria.',
            'return_date.date'        => 'La fecha de entrega debe tener un formato válido.',
            'amount_to_pay.required'  => 'El monto liquidado es obligatorio.',
            'amount_to_pay.numeric'   => 'El monto liquidado debe ser un número válido.',
            'amount_to_pay.min'       => 'El monto liquidado no puede ser negativo.',
            'payment_method.required' => 'Debes seleccionar el método de pago utilizado.',
            'payment_method.in'       => 'El método de pago seleccionado no es válido.',
            'delivery_notes.max'      => 'Las notas de entrega no pueden superar los 500 caracteres.',
        ];
    }
}
