<?php

namespace App\Features\Usuario\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasUlids;

    protected $fillable = [
        "dni",
        "name",
        "email",
        "password_hash",
        "telephone",
        "direction",
        "latitude",
        "longitude",
        "active",
    ];

    // create new owner user
    public static function createNewOwner(string $dni, string $name, string $email, string $passwordHash, string $telephone, string $direction, float $latitude, float $longitude): self
    {
        $user = new self();

        $user->dni = $dni;
        $user->name = $name;
        $user->email = $email;
        $user->password_hash = $passwordHash;
        $user->telephone = $telephone;
        $user->direction = $direction;
        $user->latitude = $latitude;
        $user->longitude = $longitude;
        $user->active = true;

        $user->save();

        return $user;
    }

    // relation with rol
    public function roles()
    {
        return $this->belongsToMany(Rol::class, "user_rol")
                    ->using(UserRol::class);
    }
}
