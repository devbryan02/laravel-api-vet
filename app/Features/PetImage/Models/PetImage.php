<?php

namespace App\Features\PetImage\Models;

use App\Features\Pet\Models\Pet;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetImage extends Model
{
    use HasUlids;

    protected $table = "images";

    protected $fillable = [
        "path_url",
        "description",
        "pet_id",
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

}
