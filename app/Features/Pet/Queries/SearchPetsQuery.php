<?php

namespace App\Features\Pet\Queries;

use App\Features\Pet\Models\Pet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SearchPetsQuery
{
    public function handle(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = Pet::with(['user' => fn ($query) => $query->select('id', 'name', 'phone', 'dni')]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pets.name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('dni', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }
}
