<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
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
            'items'                     => ['required', 'array', 'min:1'],
            'items.*.barcode'           => ['required', 'string', 'exists:products,bar_code'],
            'items.*.quantity'          => ['required', 'integer', 'min:1'],
            'payment_method'            => ['required', 'string', 'in:EFECTIVO,TARJETA,TRANSFERENCIA'],
            'amount_received'           => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Mensajes descriptivos de validación en español.
     */
    public function messages(): array
    {
        return [
            'items.required'            => 'Debes incluir al menos un artículo para registrar la venta.',
            'items.min'                 => 'La venta debe contener al menos un producto.',
            'items.*.barcode.required'  => 'El código de barras del producto es obligatorio.',
            'items.*.barcode.exists'    => 'Uno o más productos seleccionados no existen en el catálogo.',
            'items.*.quantity.required' => 'La cantidad es obligatoria.',
            'items.*.quantity.min'      => 'La cantidad vendida debe ser de al menos 1 unidad.',
            'payment_method.required'   => 'El método de pago es obligatorio.',
            'payment_method.in'         => 'El método de pago seleccionado no es válido.',
            'amount_received.numeric'   => 'El monto recibido debe ser un valor numérico.',
        ];
    }
}
