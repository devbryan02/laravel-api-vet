<?php

namespace App\Features\PetImage\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PetImage extends Model
{
    use HasUlids;

    protected $fillable = [
        "path_url",
        "description",
        "pet_id",
    ];

    public static function createNewImage(string $pathUrl, string $description, string $petId): self
    {
        // create new pet image
        $petImage = new self();

        // set pet image data
        $petImage->path_url = $pathUrl;
        $petImage->description = $description;
        $petImage->pet_id = $petId;

        // save pet image
        $petImage->save();

        // return pet image
        return $petImage;

    }
}
