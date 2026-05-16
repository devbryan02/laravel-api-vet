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
}
