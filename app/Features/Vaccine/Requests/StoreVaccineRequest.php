<?php

namespace App\Features\Vaccine\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVaccineRequest extends  FormRequest
{
    public function rules(): array
    {
        return [
            "type"              => "required|string",
            "aplication_date"   => "required|date",
            "months_validity"   => "required|integer",
            "pet_id"            => "required|exists:pets,id",
        ];
    }

    public function messages(): array
    {
        return [
            "type.required" => "El tipo de vacuna es obligatorio.",
            "aplication_date.required" => "La fecha de aplicación es obligatoria.",
            "aplication_date.date" => "La fecha de aplicación no es una fecha válida.",
            "months_validity.required" => "Los meses de validez son obligatorios.",
            "months_validity.integer" => "Los meses de validez deben ser un número entero.",
            "pet_id.required" => "La mascota es obligatoria.",
            "pet_id.exists" => "La mascota seleccionada no existe.",
        ];
    }
}
