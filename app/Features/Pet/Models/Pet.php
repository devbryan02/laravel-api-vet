<?php

namespace App\Features\Pet\Models;

use App\Features\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

}
