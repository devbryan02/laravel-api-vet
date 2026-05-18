<?php

namespace App\Features\PetImage\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pet_id' => ['required', 'exists:pets,id'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
