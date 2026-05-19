<?php

namespace App\Features\PetImage\Services;

use App\Features\PetImage\Models\PetImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Exceptions\InvalidArgumentException;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;

class PetImageService
{
    /**
     * @throws InvalidArgumentException
     */
    public function storeImage(UploadedFile $file, array $data): PetImage
    {
        $manager = ImageManager::usingDriver(Driver::class);
        $image = $manager->decode($file);
        $image->scale(width: 1200);
        $encoded = $image->encodeUsingFormat(Format::WEBP, quality: 75);

        $fileName = 'pets/' . Str::uuid() . '.webp';
        Storage::disk('public')->put($fileName, (string) $encoded);

        $url = asset('storage/' . $fileName);

        return PetImage::create([
            'pet_id'      => $data['pet_id'],
            'path_url'    => $url,
            'description' => $data['description'] ?? null,
        ]);
    }

    public function deleteImage(PetImage $petImage): bool
    {
        $relativeStoragePath = str_replace(asset('storage/'), '', $petImage->path_url);

        if (Storage::disk('public')->exists($relativeStoragePath)) {
            Storage::disk('public')->delete($relativeStoragePath);
        }

        return $petImage->delete();
    }
}
