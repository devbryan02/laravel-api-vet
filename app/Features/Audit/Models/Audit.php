<?php

namespace App\Features\Audit\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Audit extends Model
{
    use HasUlids;

    protected $fillable = [
        'action',
        'entity_type',
        'entity_id',
        'payload',
        'ip_address',
        'user_agent',
        'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

}
