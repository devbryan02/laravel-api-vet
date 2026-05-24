<?php

namespace App\Features\Vaccine\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVaccineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "type"              => "sometimes|required|string",
            "aplication_date"   => "sometimes|required|date",
            "months_validity"   => "sometimes|required|integer",
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
        ];
    }
}
