<?php

namespace App\Features\Usuario\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasUlids;

    protected $fillable = [
        "name",
        "description"
    ];

    // create new rol
    public static function createNew(string $name, string $description ): self
    {
        // create new rol
        $rol = new self();

        // set values
        $rol->name=$name;
        $rol->description=$description;

        // save rol
        $rol->save();

        // return rol
        return $rol;
    }

    // relationship with user
    public function users()
    {
        return $this->belongsToMany(User::class, "user_rol")
                    ->withPivot("assigned_at")
                    ->using(UserRol::class);
    }

}
