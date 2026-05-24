<?php

namespace App\Features\User\Queries;

use App\Features\User\Models\RoleName;
use App\Features\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SearchOwnersQuery
{
    public function handle(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with('roles')
            ->withRole(RoleName::OWNER);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('dni', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('name')->paginate($perPage);
    }
}
