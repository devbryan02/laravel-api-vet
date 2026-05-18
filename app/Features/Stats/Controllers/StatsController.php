<?php

namespace App\Features\Stats\Controllers;

use App\Features\Stats\Services\StatsService;
use App\Features\Stats\Resources\DashboardStatsResource;
use App\Features\Stats\Resources\VaccineAlertResource;
use App\Features\Stats\Resources\UnvaccinatedPetResource;
use App\Features\Stats\Resources\MonthlyActivityResource;
use App\Features\Stats\Resources\SpeciesDistributionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StatsController
{
    protected StatsService $statsService;

    public function __construct(StatsService $statsService) {
        $this->statsService = $statsService;
    }

    /**
     * GET /stats/dashboard
     * Números rápidos para el panel principal
     */
    public function dashboard(): JsonResponse
    {
        $stats = $this->statsService->getDashboardStats();
        return response()->json(new DashboardStatsResource($stats));
    }

    /**
     * GET /stats/vaccine-alerts
     * Mascotas con vacuna vencida o próxima a vencer
     */
    public function vaccineAlerts(): AnonymousResourceCollection
    {
        $alerts = $this->statsService->getVaccineAlerts();
        return VaccineAlertResource::collection($alerts);
    }

    /**
     * GET /stats/unvaccinated
     * Mascotas sin ninguna vacuna registrada
     */
    public function unvaccinated(): AnonymousResourceCollection
    {
        $pets = $this->statsService->getUnvaccinatedPets();
        return UnvaccinatedPetResource::collection($pets);
    }

    /**
     * GET /stats/monthly-activity
     * Mascotas nuevas y vacunas aplicadas por mes (últimos 12 meses)
     */
    public function monthlyActivity(): JsonResponse
    {
        $activity = $this->statsService->getMonthlyActivity();
        return response()->json(MonthlyActivityResource::collection($activity));
    }

    /**
     * GET /stats/species-distribution
     * Distribución por especie
     */
    public function speciesDistribution(): AnonymousResourceCollection
    {
        $distribution = $this->statsService->getSpeciesDistribution();
        return SpeciesDistributionResource::collection($distribution);
    }
}
