<?php

namespace App\Features\Pet\Services;

use App\Features\Pet\Models\Pet;

class CreatePetService
{
    public function execute(array $data): Pet
    {
        return Pet::create($data);
    }
}
