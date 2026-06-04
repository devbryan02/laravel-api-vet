<?php

namespace App\Features\PetImage\Services;

use App\Features\PetImage\Models\PetImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PetImageService
{
    public function storeImage(UploadedFile $file, array $data): PetImage
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = 'pets/' . Str::uuid() . '.' . $extension;

        Storage::disk('public')->put($fileName, file_get_contents($file->getRealPath()));

        return PetImage::create([
            'pet_id'      => $data['pet_id'],
            'path_url'    => '/api/pets/storage/' . $fileName,
            'description' => $data['description'] ?? '',
        ]);
    }

    public function deleteImage(PetImage $petImage): bool
    {
        $relativeStoragePath = str_replace('/api/pets/storage/', '', $petImage->path_url);

        if (Storage::disk('public')->exists($relativeStoragePath)) {
            Storage::disk('public')->delete($relativeStoragePath);
        }

        return $petImage->delete();
    }
}
