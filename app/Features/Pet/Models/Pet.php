<?php

namespace App\Features\Pet\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasUlids;

    protected $fillable = [
        "identifier",
        "name",
        "species",
        "race",
        "sex",
        "temperament",
        "reproductive_condition",
        "color",
        "years",
        "months",
        "status",
        "user_id",
    ];

}
