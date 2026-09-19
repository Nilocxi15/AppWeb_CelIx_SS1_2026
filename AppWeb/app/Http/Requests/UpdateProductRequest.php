<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Preparar datos antes de validar.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->has('status') ? $this->boolean('status') : false,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:150'],
            'id_category'  => ['required', 'integer', 'exists:category_products,id'],
            'description'  => ['nullable', 'string'],
            'price'        => ['required', 'numeric', 'min:0.01'],
            'minium_stock' => ['required', 'integer', 'min:1'],
            'status'       => ['boolean'],
            'image'        => ['nullable', 'image', 'max:2048'],
        ];
    }

    /**
     * Mensajes descriptivos de validación en español.
     */
    public function messages(): array
    {
        return [
            'name.required'         => 'El nombre del producto es obligatorio.',
            'name.max'              => 'El nombre del producto no debe exceder 150 caracteres.',
            'id_category.required'  => 'Debes seleccionar una categoría.',
            'id_category.exists'    => 'La categoría seleccionada no es válida.',
            'price.required'        => 'El precio de venta es obligatorio.',
            'price.numeric'         => 'El precio debe ser un valor numérico.',
            'price.min'             => 'El precio debe ser mayor a 0.',
            'minium_stock.required' => 'El stock mínimo de alerta es obligatorio.',
            'minium_stock.integer'  => 'El stock mínimo debe ser un número entero.',
            'minium_stock.min'      => 'El stock mínimo debe ser al menos 1.',
            'image.image'           => 'El archivo de imagen debe ser un formato válido (jpg, png, webp).',
            'image.max'             => 'La imagen no debe superar los 2MB de tamaño.',
        ];
    }
}
