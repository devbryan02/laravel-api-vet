<?php

namespace App\Features\User\Requests;

use App\Features\User\Models\RoleName;
use Illuminate\Foundation\Http\FormRequest;

class StoreOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(RoleName::VETERINARIAN) === true
            || $this->user()?->hasRole(RoleName::ADMIN) === true;
    }

    public function rules(): array
    {
        return [
            "dni" => ["required", "string", "regex:/^\d{8}$/", "unique:users,dni"],
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "email", "max:255", "unique:users,email"],
            "phone" => ["nullable", "string", "max:30"],
            "address" => ["nullable", "string", "max:255"],
            "latitude" => ["nullable", "numeric"],
            "longitude" => ["nullable", "numeric"],
            "active" => ["sometimes", "boolean"],
        ];
    }

    public function messages(): array
    {
        return [
            "dni.required" => "El DNI del dueño es obligatorio.",
            "dni.regex" => "El DNI debe tener exactamente 8 dígitos numéricos.",
            "dni.unique" => "El DNI ingresado ya está registrado en el sistema.",
            "name.required" => "El nombre del dueño es obligatorio.",
            "name.max" => "El nombre no debe exceder los :max caracteres.",
            "email.required" => "El correo electrónico es obligatorio.",
            "email.email" => "Ingrese un correo electrónico válido.",
            "email.unique" => "El correo electrónico ingresado ya está registrado en el sistema.",
            "email.max" => "El correo electrónico no debe exceder los :max caracteres.",
            "phone.max" => "El teléfono no debe exceder los :max caracteres.",
            "address.max" => "La dirección no debe exceder los :max caracteres.",
            "latitude.numeric" => "La latitud debe ser un valor numérico.",
            "longitude.numeric" => "La longitud debe ser un valor numérico.",
            "active.boolean" => "El estado activo debe ser verdadero o falso.",
        ];
    }
}
