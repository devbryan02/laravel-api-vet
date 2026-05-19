<?php

namespace App\Features\Stats\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardStatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total_pets'            => $this->resource['totalPets'],
            'total_owners'          => $this->resource['totalOwners'],
            'vaccinated_this_month' => $this->resource['vaccinatedThisMonth'],
            'overdue_vaccines'      => $this->resource['overdueVaccines'],
            'upcoming_in_30_days'   => $this->resource['upcomingIn30Days'],
            'unvaccinated_count'    => $this->resource['unvaccinatedCount'],
        ];
    }
}
