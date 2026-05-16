<?php

namespace App\Features\PetImage\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PetImage extends Model
{
    use HasUlids;

    protected $table = "pet_images";

    protected $fillable = [
        "path_url",
        "description",
        "pet_id",
    ];

}
