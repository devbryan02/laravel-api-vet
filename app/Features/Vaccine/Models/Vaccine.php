<?php

namespace App\Features\Vaccine\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
    use HasUlids;

    
    protected $fillable = [
        "type",
        "aplication_date",
        "months_validity",
        "expiration_date",
        "next_vaccine_date",
        "pet_id"
    ];

}
