<?php

namespace App\Features\Audit\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasUlids;

    protected $fillable = [
        "action",
        "entity",
        "ip_address",
        "user_id"
    ];

    public static function saveAudit(string $action, string $entity, string $ip_address, ?int $user_id): self
    {
        $audit = new self();

        $audit->action = $action;
        $audit->entity = $entity;
        $audit->ip_address = $ip_address;
        $audit->user_id = $user_id;

        $audit->save();

        return $audit;
    }

}
