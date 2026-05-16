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
}
