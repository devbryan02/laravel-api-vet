<?php

namespace App\Features\User\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRole extends Pivot
{
    use HasUlids;

    protected $table = "user_role";

    protected $fillable = [
        "user_id",
        "role_id",
        "asigned_at"
    ];

    protected static function booted(): void
    {
        static::creating(function (UserRole $userRole) {
            $userRole->asigned_at = now();
        });
    }
}
