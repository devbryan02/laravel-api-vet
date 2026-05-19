<?php

namespace App\Features\User\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasUlids;

    public $timestamps = false;
    protected $table = "roles";

    protected $fillable = [
        "name",
        "description"
    ];

    // Relación con UserRole
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "user_role", "role_id","user_id")
                    ->using(UserRole::class)
                    ->withPivot("asigned_at");
    }

}
