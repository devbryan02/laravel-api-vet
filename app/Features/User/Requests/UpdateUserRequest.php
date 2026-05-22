<?php

namespace App\Features\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route("user")?->id;

        return [
            "dni" => ["sometimes", "required", "string", "max:20", Rule::unique("users", "dni")->ignore($userId)],
            "name" => ["sometimes", "required", "string", "max:255"],
            "email" => ["sometimes", "required", "email", "max:255", Rule::unique("users", "email")->ignore($userId)],
            "password" => ["sometimes", "required", "string", "min:8"],
            "phone" => ["nullable", "string", "max:30"],
            "address" => ["nullable", "string", "max:255"],
            "latitude" => ["sometimes","nullable", "numeric"],
            "longitude" => ["sometimes","nullable", "numeric"],
            "active" => ["sometimes", "boolean"],
        ];
    }

    public function messages(): array
    {
        return [
            "dni.required" => "El DNI es obligatorio.",
            "dni.unique" => "El DNI ingresado ya está registrado en el sistema.",
            "dni.max" => "El DNI no debe exceder los :max caracteres.",
            "name.required" => "El nombre es obligatorio.",
            "name.max" => "El nombre no debe exceder los :max caracteres.",
            "email.required" => "El correo electrónico es obligatorio.",
            "email.email" => "Ingrese un correo electrónico válido.",
            "email.unique" => "El correo electrónico ingresado ya está registrado en el sistema.",
            "email.max" => "El correo electrónico no debe exceder los :max caracteres.",
            "password.required" => "La contraseña es obligatoria.",
            "password.min" => "La contraseña debe tener al menos :min caracteres.",
            "phone.max" => "El teléfono no debe exceder los :max caracteres.",
            "address.max" => "La dirección no debe exceder los :max caracteres.",
            "latitude.numeric" => "La latitud debe ser un valor numérico.",
            "longitude.numeric" => "La longitud debe ser un valor numérico.",
            "active.boolean" => "El estado activo debe ser verdadero o falso.",
        ];
    }
}
