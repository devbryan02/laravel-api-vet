<?php

namespace App\Features\Stats\Controllers;

use App\Features\Stats\Resources\DashboardStatsResource;
use App\Features\Stats\Resources\MonthlyActivityResource;
use App\Features\Stats\Resources\SpeciesDistributionResource;
use App\Features\Stats\Resources\UnvaccinatedPetResource;
use App\Features\Stats\Resources\VaccineAlertResource;
use App\Features\Stats\Services\StatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

class StatsController
{
    protected StatsService $statsService;

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * GET /stats/dashboard
     * Números rápidos para el panel principal
     */
    #[OA\Get(
        path: '/api/stats/dashboard',
        summary: 'Dashboard principal',
        security: [['bearerAuth' => []]],
        tags: ['Estadísticas'],
        responses: [
            new OA\Response(response: 200, description: 'Resumen del dashboard', content: new OA\JsonContent(ref: '#/components/schemas/DashboardStats')),
        ],
    )]
    public function dashboard(): JsonResponse
    {
        $stats = $this->statsService->getDashboardStats();

        return response()->json(new DashboardStatsResource($stats));
    }

    /**
     * GET /stats/vaccine-alerts
     * Mascotas con vacuna vencida o próxima a vencer
     */
    #[OA\Get(
        path: '/api/stats/vaccine-alerts',
        summary: 'Alertas de vacunas',
        security: [['bearerAuth' => []]],
        tags: ['Estadísticas'],
        responses: [
            new OA\Response(response: 200, description: 'Alertas de vacunas', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/VaccineAlert'))],
                type: 'object'
            )),
        ],
    )]
    public function vaccineAlerts(): AnonymousResourceCollection
    {
        $alerts = $this->statsService->getVaccineAlerts();

        return VaccineAlertResource::collection($alerts);
    }

    /**
     * GET /stats/unvaccinated
     * Mascotas sin ninguna vacuna registrada
     */
    #[OA\Get(
        path: '/api/stats/unvaccinated',
        summary: 'Mascotas sin vacunas',
        security: [['bearerAuth' => []]],
        tags: ['Estadísticas'],
        responses: [
            new OA\Response(response: 200, description: 'Mascotas sin vacunas', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/UnvaccinatedPet'))],
                type: 'object'
            )),
        ],
    )]
    public function unvaccinated(): AnonymousResourceCollection
    {
        $pets = $this->statsService->getUnvaccinatedPets();

        return UnvaccinatedPetResource::collection($pets);
    }

    /**
     * GET /stats/monthly-activity
     * Mascotas nuevas y vacunas aplicadas por mes (últimos 12 meses)
     */
    #[OA\Get(
        path: '/api/stats/monthly-activity',
        tags: ['Estadísticas'],
        summary: 'Actividad mensual',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Actividad mensual', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/MonthlyActivity'))],
                type: 'object'
            )),
        ],
    )]
    public function monthlyActivity(): JsonResponse
    {
        $activity = $this->statsService->getMonthlyActivity();

        return response()->json(MonthlyActivityResource::collection($activity));
    }

    /**
     * GET /stats/species-distribution
     * Distribución por especie
     */
    #[OA\Get(
        path: '/api/stats/species-distribution',
        tags: ['Estadísticas'],
        summary: 'Distribución por especie',
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Distribución por especie', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/SpeciesDistribution'))],
                type: 'object'
            )),
        ],
    )]
    public function speciesDistribution(): AnonymousResourceCollection
    {
        $distribution = $this->statsService->getSpeciesDistribution();

        return SpeciesDistributionResource::collection($distribution);
    }
}
