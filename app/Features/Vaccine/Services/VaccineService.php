<?php

namespace App\Features\Vaccine\Services;

use App\Features\Pet\Models\Pet;
use App\Features\Vaccine\Models\Vaccine;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class VaccineService
{
    public function create(array $data): Vaccine
    {
        $aplicationDate = Carbon::parse($data['aplication_date']);

        $expirationDate = $aplicationDate
            ->copy()
            ->addMonths($data['months_validity']);

        $nextVaccinedate = $expirationDate->copy();

        return Vaccine::create([
            ...$data,
            'expiration_date' => $expirationDate,
            'next_vaccine_date' => $nextVaccinedate,
        ]);
    }

    public function findAll(int $perPage = 10): LengthAwarePaginator
    {
        return Vaccine::with('pet')
            ->orderBy("created_at", "desc")
            ->paginate($perPage);
    }

    public function findByPet(Pet $pet, int $perPage = 10): LengthAwarePaginator
    {
        return $pet->vaccines()
            ->with('pet')
            ->orderBy('aplication_date', 'desc')
            ->paginate($perPage);
    }

    public function update(Vaccine $vaccine, array $data): Vaccine
    {
        if (isset($data['aplication_date']) || isset($data['months_validity'])) {

            $aplicationDate = Carbon::parse($data['aplication_date'] ?? $vaccine->aplication_date);
            $monthsValidity = $data['months_validity'] ?? $vaccine->months_validity;

            $expirationDate = $aplicationDate
                ->copy()
                ->addMonths($monthsValidity);

            $data['expiration_date'] = $expirationDate;
            $data['next_vaccine_date'] = $expirationDate->copy();
        }

        $vaccine->update($data);

        return $vaccine;
    }

    public function delete(Vaccine $vaccine): bool
    {
        return $vaccine->delete();
    }
}
