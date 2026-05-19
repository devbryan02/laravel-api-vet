<?php

namespace App\Features\Stats\Services;

use App\Features\Stats\Queries\DashboardStatsQuery;
use App\Features\Stats\Queries\MonthlyActivityQuery;
use App\Features\Stats\Queries\SpeciesDistributionQuery;
use App\Features\Stats\Queries\UnvaccinatedPetsQuery;
use App\Features\Stats\Queries\VaccineAlertsQuery;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

readonly class StatsService
{
    public function __construct(
        private DashboardStatsQuery      $dashboardStatsQuery,
        private VaccineAlertsQuery       $vaccineAlertsQuery,
        private UnvaccinatedPetsQuery    $unvaccinatedPetsQuery,
        private MonthlyActivityQuery     $monthlyActivityQuery,
        private SpeciesDistributionQuery $speciesDistributionQuery,
    ) {
    }

    public function getDashboardStats(): array
    {
        return $this->dashboardStatsQuery->handle();
    }

    public function getVaccineAlerts(): Collection
    {
        return $this->vaccineAlertsQuery->handle();
    }

    public function getUnvaccinatedPets(): EloquentCollection
    {
        return $this->unvaccinatedPetsQuery->handle();
    }

    public function getMonthlyActivity(): Collection
    {
        return $this->monthlyActivityQuery->handle();
    }

    public function getSpeciesDistribution(): Collection
    {
        return $this->speciesDistributionQuery->handle();
    }
}
