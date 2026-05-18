<?php

namespace App\Features\Stats\Queries;

use App\Features\Pet\Models\Pet;
use App\Features\Vaccine\Models\Vaccine;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class MonthlyActivityQuery
{
    public function handle(): Collection
    {
        $months = collect();

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $key = $date->format('Y-m');

            $months->push([
                'month' => $date->translatedFormat('M Y'),
                'month_key' => $key,
                'new_pets' => Pet::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'vaccines_applied' => Vaccine::whereYear('aplication_date', $date->year)
                    ->whereMonth('aplication_date', $date->month)
                    ->count(),
            ]);
        }

        return $months;
    }
}
