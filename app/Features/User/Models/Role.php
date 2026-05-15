<?php

namespace App\Features\Usuario\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasUlids;

    protected $fillable = [
        "name",
        "description"
    ];

    // create new role
    public static function createNew(string $name, string $description ): self
    {
        // create new role
        $role = new self();

        // set values
        $role->name=$name;
        $role->description=$description;

        // save role
        $role->save();

        // return rol
        return $role;
    }

    // relationship with user
    public function users()
    {
        return $this->belongsToMany(User::class, "user_rol")
                    ->withPivot("assigned_at")
                    ->using(UserRole::class);
    }

}
