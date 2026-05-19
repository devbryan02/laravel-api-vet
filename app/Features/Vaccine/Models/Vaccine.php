<?php

namespace App\Features\Vaccine\Models;

use App\Features\Pet\Models\Pet;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vaccine extends Model
{
    use HasUlids;

    protected $table = 'vaccines';

    protected $fillable = [
        "type",
        "aplication_date",
        "months_validity",
        "expiration_date",
        "next_vaccine_date",
        "pet_id"
    ];

    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }

}
