<?php

namespace App\Features\Stats\Queries;

use App\Features\Pet\Models\Pet;
use Illuminate\Database\Eloquent\Collection;

class UnvaccinatedPetsQuery
{
    public function handle(): Collection
    {
        return Pet::with(['user' => fn ($query) => $query->select('id', 'name', 'phone')])
            ->doesntHave('vaccines')
            ->select('id', 'name', 'species', 'race', 'status', 'user_id', 'created_at')
            ->orderByDesc('created_at')
            ->get();
    }
}
