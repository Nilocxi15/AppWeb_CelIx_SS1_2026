<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
        $categoryId = $this->route('id') ?? $this->input('id');

        return [
            'name'        => [
                'required',
                'string',
                'max:100',
                Rule::unique('category_products', 'name')->ignore($categoryId),
            ],
            'description' => ['nullable', 'string'],
            'status'      => ['boolean'],
        ];
    }

    /**
     * Mensajes descriptivos de validación en español.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.unique'   => 'Ya existe otra categoría con este nombre.',
            'name.max'      => 'El nombre no debe superar los 100 caracteres.',
        ];
    }
}
