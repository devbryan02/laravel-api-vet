<?php

namespace App\Features\User\Models;

use App\Features\Pet\Models\Pet;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    use HasUlids;

    protected $table = "users";

    protected $fillable = [
        "dni",
        "name",
        "email",
        "password",
        "phone",
        "address",
        "latitude",
        "longitude",
        "active",
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, "user_role", "user_id", "role_id")
                    ->using(UserRole::class)
                    ->withPivot("asigned_at");
    }

    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

}
