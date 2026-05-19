<?php

namespace App\Features\Pet\Services;

use App\Features\Pet\Models\Pet;
use Illuminate\Support\Collection;

class PetService
{
    public function create(array $data): Pet
    {
        return Pet::create($data);
    }

    public function findAll(): Collection
    {
        return Pet::with("user")->get();
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
