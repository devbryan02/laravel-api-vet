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

    public function messages(): array
    {
        return [
            'pet_id.required' => 'La mascota es obligatoria.',
            'pet_id.exists' => 'La mascota seleccionada no existe.',
            'image.required' => 'La imagen es obligatoria.',
            'image.image' => 'El archivo debe ser una imagen válida.',
            'image.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg o webp.',
            'image.max' => 'La imagen no debe superar los 2MB.',
            'description.max' => 'La descripción no debe exceder los :max caracteres.',
        ];
    }
}
