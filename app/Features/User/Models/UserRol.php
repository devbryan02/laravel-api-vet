<?php

namespace App\Features\Usuario\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRol extends Pivot
{
    use HasUlids;

    protected $fillable = [
        "user_id",
        "rol_id",
        "asigned_at"
    ];

    // create new assignment
    public static function createAssignment(string $userRol, string $rolId): self
    {
        $assignment = new self();

        $assignment->user_rol = $userRol;
        $assignment->rol_id = $rolId;
        $assignment->asigned_at = now();

        $assignment->save();

        return $assignment;
    }

}
