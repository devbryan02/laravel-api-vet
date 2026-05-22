<?php

namespace App\Features\Pet\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePetRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            "name"          => "required|string|max:255",
            "species"       => "required|string",
            "race"          => "required|string",
            "gender"        => "required|string",
            "temperament"   => "required|string",
            "reproductive_condition" => "required|string",
            "color"         => "required|string",
            "years"         => "required|integer",
            "months"        => "required|integer",
            "status"        => "required|string",
            "user_id"       => "required|exists:users,id",
        ];
    }

    public function messages(): array
    {
        return [
            "name.required" => "El nombre de la mascota es obligatorio.",
            "name.max" => "El nombre no debe exceder los :max caracteres.",
            "species.required" => "La especie de la mascota es obligatoria.",
            "race.required" => "La raza de la mascota es obligatoria.",
            "gender.required" => "El género de la mascota es obligatorio.",
            "temperament.required" => "El temperamento de la mascota es obligatorio.",
            "reproductive_condition.required" => "La condición reproductiva es obligatoria.",
            "color.required" => "El color de la mascota es obligatorio.",
            "years.required" => "Los años de la mascota son obligatorios.",
            "years.integer" => "Los años deben ser un número entero.",
            "months.required" => "Los meses de la mascota son obligatorios.",
            "months.integer" => "Los meses deben ser un número entero.",
            "status.required" => "El estado de la mascota es obligatorio.",
            "user_id.required" => "El dueño de la mascota es obligatorio.",
            "user_id.exists" => "El dueño seleccionado no existe.",
        ];
    }
}
