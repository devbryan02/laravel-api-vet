<?php

namespace App\Observers;

use App\Features\Pet\Models\Pet;

class PetObserver
{
    public function creating(Pet $pet): void
    {
        $lastPet = Pet::latest()->first();

        $nextNumber = 1;

        if ($lastPet && $lastPet->identifier) {

            $lastNumber = (int) substr(
                $lastPet->identifier,
                5
            );

            $nextNumber = $lastNumber + 1;
        }

        $pet->identifier =
            'MAACD' .
            str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
