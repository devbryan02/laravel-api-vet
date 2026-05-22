<?php

namespace App\Features\Pet\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name"                   => ["sometimes", "required", "string"],
            "temperament"            => ["sometimes", "required", "string"],
            "reproductive_condition" => ["sometimes", "required", "string"],
            "color"                  => ["sometimes", "required", "string"],
            "years"                  => ["sometimes", "required", "integer"],
            "months"                 => ["sometimes", "required", "integer"],
            "status"                 => ["sometimes", "required", "string"]
        ];
    }

    public function messages(): array
    {
        return [
            "name.required" => "El nombre de la mascota es obligatorio.",
            "temperament.required" => "El temperamento de la mascota es obligatorio.",
            "reproductive_condition.required" => "La condición reproductiva es obligatoria.",
            "color.required" => "El color de la mascota es obligatorio.",
            "years.required" => "Los años de la mascota son obligatorios.",
            "years.integer" => "Los años deben ser un número entero.",
            "months.required" => "Los meses de la mascota son obligatorios.",
            "months.integer" => "Los meses deben ser un número entero.",
            "status.required" => "El estado de la mascota es obligatorio.",
        ];
    }
}
