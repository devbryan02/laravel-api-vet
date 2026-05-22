<?php

namespace App\Features\Pet\Services;

use App\Features\Pet\Models\Pet;
use App\Features\Pet\Queries\PetsByOwnerQuery;
use App\Features\Pet\Queries\SearchPetsQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        if ($pet->vaccines()->exists()) {
            throw new HttpException(409, 'No se puede eliminar la mascota porque tiene vacunas asociadas. Elimine primero las vacunas.');
        }

        if ($pet->images()->exists()) {
            throw new HttpException(409, 'No se puede eliminar la mascota porque tiene imágenes asociadas. Elimine primero las imágenes.');
        }

        return $pet->delete();
    }

}
