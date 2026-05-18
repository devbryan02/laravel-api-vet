<?php

namespace App\Features\Stats\Queries;

use App\Features\Pet\Models\Pet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SpeciesDistributionQuery
{
    public function handle(): Collection
    {
        $total = Pet::count();

        return Pet::select('species', DB::raw('COUNT(*) as total'))
            ->groupBy('species')
            ->orderByDesc('total')
            ->get()
            ->map(function (Pet $row) use ($total) {
                return [
                    'species' => $row->species,
                    'total' => (int) $row->total,
                    'percentage' => $total > 0
                        ? round(((int) $row->total / $total) * 100, 1)
                        : 0,
                ];
            });
    }
}
