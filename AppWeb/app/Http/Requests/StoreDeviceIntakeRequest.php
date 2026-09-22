<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceIntakeRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_mode'        => ['required', 'in:new,existing'],
            'existing_client_id' => ['required_if:client_mode,existing', 'nullable', 'exists:clients,id'],
            'client_name'        => ['required_if:client_mode,new', 'nullable', 'string', 'max:100'],
            'client_lastname'    => ['required_if:client_mode,new', 'nullable', 'string', 'max:100'],
            'client_phone'       => ['required_if:client_mode,new', 'nullable', 'string', 'max:20'],
            'client_dpi'         => ['nullable', 'string', 'max:14'],
            'device_type'        => ['required', 'exists:device_types,id'],
            'device_brand'       => ['required', 'string', 'max:50'],
            'device_model'       => ['required', 'string', 'max:50'],
            'device_serial'      => ['nullable', 'string', 'max:100'],
            'device_password'    => ['nullable', 'string', 'max:100'],
            'reported_issue'     => ['required', 'string', 'max:1000'],
            'reception_notes'    => ['nullable', 'string', 'max:1000'],
            'total_charged'      => ['required', 'numeric', 'min:0'],
            'deposit'            => ['required', 'numeric', 'min:0', 'lte:total_charged'],
            'id_user_technician' => ['nullable', 'exists:users,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client_mode.required'             => 'El modo de cliente es obligatorio.',
            'client_mode.in'                   => 'El modo de cliente seleccionado no es válido.',
            'existing_client_id.required_if'   => 'Debes seleccionar un cliente del directorio.',
            'existing_client_id.exists'        => 'El cliente seleccionado no existe en el sistema.',
            'client_name.required_if'          => 'El nombre del cliente es obligatorio.',
            'client_lastname.required_if'      => 'El apellido del cliente es obligatorio.',
            'client_phone.required_if'         => 'El teléfono o WhatsApp del cliente es obligatorio.',
            'device_type.required'             => 'Debes seleccionar el tipo de equipo.',
            'device_type.exists'               => 'El tipo de equipo seleccionado no es válido.',
            'device_brand.required'            => 'La marca del dispositivo es obligatoria.',
            'device_model.required'            => 'El modelo del dispositivo es obligatorio.',
            'reported_issue.required'          => 'La descripción de la falla reportada es obligatoria.',
            'total_charged.required'           => 'El costo del trabajo es obligatorio.',
            'total_charged.numeric'            => 'El costo del trabajo debe ser un valor numérico.',
            'total_charged.min'                => 'El costo del trabajo no puede ser menor a 0.',
            'deposit.required'                 => 'El anticipo es obligatorio (ingresa 0 si no hay abono).',
            'deposit.numeric'                  => 'El anticipo debe ser un valor numérico.',
            'deposit.min'                      => 'El anticipo no puede ser negativo.',
            'deposit.lte'                      => 'El anticipo no puede ser mayor al costo total del trabajo.',
            'id_user_technician.exists'        => 'El técnico seleccionado no existe.',
        ];
    }

    /**
     * Validación adicional después de las reglas básicas.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('id_user_technician')) {
                $tech = User::with('role')->find($this->input('id_user_technician'));
                $roleName = strtoupper(trim($tech?->role?->name ?? ''));

                if (!$tech || !$tech->state || !in_array($roleName, ['TECNICO', 'TÉCNICO'])) {
                    $validator->errors()->add('id_user_technician', 'El técnico asignado debe tener una cuenta activa con rol de Técnico.');
                }
            }
        });
    }
}
