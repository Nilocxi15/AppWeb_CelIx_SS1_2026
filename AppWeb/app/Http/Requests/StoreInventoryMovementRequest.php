<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryMovementRequest extends FormRequest
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
        $type = $this->input('movement_type');
        $minQuantity = ($type === 'AJUSTE') ? 0 : 1;

        return [
            'product_bar_code' => ['required', 'string', 'exists:products,bar_code'],
            'movement_type'    => ['required', 'string', 'in:ENTRADA,SALIDA,AJUSTE'],
            'quantity'         => ['required', 'integer', "min:{$minQuantity}"],
            'reason'           => ['required', 'string', 'max:255'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Mensajes descriptivos de validación en español.
     */
    public function messages(): array
    {
        return [
            'product_bar_code.required' => 'Debes seleccionar un producto válido.',
            'product_bar_code.exists'   => 'El producto seleccionado no existe en el inventario.',
            'movement_type.required'    => 'El tipo de movimiento es obligatorio.',
            'movement_type.in'          => 'El tipo de movimiento debe ser ENTRADA, SALIDA o AJUSTE.',
            'quantity.required'         => 'La cantidad a afectar es obligatoria.',
            'quantity.integer'          => 'La cantidad debe ser un número entero.',
            'quantity.min'              => 'La cantidad no puede ser menor a :min.',
            'reason.required'           => 'Debes indicar el motivo del movimiento.',
            'reason.max'                => 'El motivo no debe exceder los 255 caracteres.',
            'notes.max'                 => 'Las observaciones no deben exceder los 500 caracteres.',
        ];
    }
}
