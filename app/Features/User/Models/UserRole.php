<?php

namespace App\Features\Usuario\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserRole extends Pivot
{
    use HasUlids;

    protected $fillable = [
        "user_id",
        "role_id",
        "asigned_at"
    ];

    // create new assignment
    public static function createAssignment(string $userRole, string $roleId): self
    {
        $assignment = new self();

        $assignment->user_role = $userRole;
        $assignment->role_id = $roleId;
        $assignment->asigned_at = now();

        $assignment->save();

        return $assignment;
    }

}
