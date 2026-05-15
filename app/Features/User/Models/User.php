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
        "password",
        "phone",
        "address",
        "latitude",
        "longitude",
        "active",
    ];

    // create new owner user
    public static function createNewOwner(string $dni, string $name, string $email, string $password, string $phone, string $address, float $latitude, float $longitude): self
    {
        $user = new self();

        $user->dni = $dni;
        $user->name = $name;
        $user->email = $email;
        $user->password = $password;
        $user->phone = $phone;
        $user->address = $address;
        $user->latitude = $latitude;
        $user->longitude = $longitude;
        $user->active = true;

        $user->save();

        return $user;
    }

    // relation with role
    public function roles()
    {
        return $this->belongsToMany(Role::class, "user_rol")
                    ->using(UserRole::class);
    }
}
