<?php

namespace App\Features\Pet\Models;

use App\Features\PetImage\Models\PetImage;
use App\Features\User\Models\User;
use App\Features\Vaccine\Models\Vaccine;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pet extends Model
{
    use HasUlids;

    protected $fillable = [
        "identifier",
        "name",
        "species",
        "race",
        "gender",
        "temperament",
        "reproductive_condition",
        "color",
        "years",
        "months",
        "status",
        "user_id",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vaccines(): HasMany
    {
        return $this->hasMany(Vaccine::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(PetImage::class, 'pet_id');
    }

}
