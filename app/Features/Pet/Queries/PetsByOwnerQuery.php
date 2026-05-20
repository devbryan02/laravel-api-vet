<?php

namespace App\Features\Pet\Queries;

use App\Features\Pet\Models\Pet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PetsByOwnerQuery
{
    public function handle(string $ownerId, int $perPage = 15): LengthAwarePaginator
    {
        return Pet::with(['user' => fn ($query) => $query->select('id', 'name', 'phone')])
            ->where('user_id', $ownerId)
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }
}
