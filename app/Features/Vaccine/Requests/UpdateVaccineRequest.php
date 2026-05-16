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
}
