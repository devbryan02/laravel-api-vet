<?php

namespace App\Features\Stats\Queries;

use App\Features\Vaccine\Models\Vaccine;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class VaccineAlertsQuery
{
    public function handle(): Collection
    {
        $today = Carbon::today();
        $in30Days = Carbon::today()->addDays(30);

        return Vaccine::with(['pet' => function ($query) {
            $query->select('id', 'name', 'species', 'race', 'user_id')
                ->with(['user' => fn ($query) => $query->select('id', 'name', 'phone')]);
        }])
            ->whereIn('id', $this->latestVaccineIds())
            ->where(function ($query) use ($today, $in30Days) {
                $query->where('next_vaccine_date', '<', $today)
                    ->orWhereBetween('next_vaccine_date', [$today, $in30Days]);
            })
            ->orderBy('next_vaccine_date')
            ->get()
            ->map(fn (Vaccine $vaccine) => $this->toAlert($vaccine, $today));
    }

    private function latestVaccineIds(): Builder
    {
        return DB::table('vaccines')
            ->select(DB::raw('MAX(id)'))
            ->whereNotNull('next_vaccine_date')
            ->groupBy('pet_id');
    }

    private function toAlert(Vaccine $vaccine, Carbon $today): array
    {
        $nextDate = Carbon::parse($vaccine->next_vaccine_date);

        return [
            'vaccine_id' => $vaccine->id,
            'type' => $vaccine->type,
            'alert_type' => $nextDate->lt($today) ? 'atrasada' : 'próxima',
            'days_diff' => (int) $today->diffInDays($nextDate, false),
            'aplication_date' => $vaccine->aplication_date,
            'next_vaccine_date' => $vaccine->next_vaccine_date,
            'months_validity' => $vaccine->months_validity,
            'pet' => [
                'id' => $vaccine->pet?->id,
                'name' => $vaccine->pet?->name,
                'species' => $vaccine->pet?->species,
                'race' => $vaccine->pet?->race,
            ],
            'owner' => [
                'id' => $vaccine->pet?->user?->id,
                'name' => $vaccine->pet?->user?->name,
                'phone' => $vaccine->pet?->user?->phone,
            ],
        ];
    }
}
