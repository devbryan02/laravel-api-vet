<?php

namespace App\Features\Pet\Services;

use App\Features\Pet\Models\Pet;
use App\Features\Pet\Queries\PetsByOwnerQuery;
use App\Features\Pet\Queries\SearchPetsQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

readonly class PetService
{
    public function __construct(
        private PetsByOwnerQuery $petsByOwnerQuery,
        private SearchPetsQuery  $searchPetsQuery,
    ) {
    }

    public function create(array $data): Pet
    {
        return Pet::create($data);
    }

    public function findAll(int $perPage = 10): LengthAwarePaginator
    {
        return Pet::with("user")
            ->orderBy("created_at", "desc")
            ->paginate($perPage);
    }

    public function findByOwner(string $ownerId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->petsByOwnerQuery->handle($ownerId, $perPage);
    }

    public function search(?string $search = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->searchPetsQuery->handle($search, $perPage);
    }

    public function update(Pet $pet, array $data): Pet
    {
        $pet->update($data);
        return $pet;
    }

    public function delete(Pet $pet): bool
    {
        return $pet->delete();
    }

}
