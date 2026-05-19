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
}
