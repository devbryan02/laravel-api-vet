<?php

namespace App\Features\User\Requests;

use App\Features\User\Models\RoleName;
use Illuminate\Foundation\Http\FormRequest;

class StoreOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(RoleName::VETERINARIAN) === true
            || $this->user()?->hasRole(RoleName::ADMIN) === true;
    }

    public function rules(): array
    {
        return [
            "dni" => ["required", "string", "max:20", "unique:users,dni"],
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "email", "max:255", "unique:users,email"],
            "phone" => ["nullable", "string", "max:30"],
            "address" => ["nullable", "string", "max:255"],
            "latitude" => ["nullable", "numeric"],
            "longitude" => ["nullable", "numeric"],
            "active" => ["sometimes", "boolean"],
        ];
    }
}
