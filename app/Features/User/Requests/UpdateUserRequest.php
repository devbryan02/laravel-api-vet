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
}
