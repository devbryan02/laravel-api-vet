<?php

namespace App\Features\Vaccine\Controllers;

use App\Features\Pet\Models\Pet;
use App\Features\Vaccine\Models\Vaccine;
use App\Features\Vaccine\Requests\StoreVaccineRequest;
use App\Features\Vaccine\Requests\UpdateVaccineRequest;
use App\Features\Vaccine\Resources\VaccineResource;
use App\Features\Vaccine\Services\VaccineService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

class VaccineController extends Controller
{
    public function __construct(protected VaccineService $vaccineService) {}

    #[OA\Get(
        path: '/api/pets/{pet}/vaccines',
        description: 'Retorna todas las vacunas registradas para una mascota específica.',
        summary: 'Listar vacunas por mascota',
        security: [['bearerAuth' => []]],
        tags: ['Vacunas'],
        parameters: [
            new OA\Parameter(
                name: 'pet',
                description: 'ULID de la mascota',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', example: '01JVMR0M2T6FC7NG8R5GRM5AJV'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listado de vacunas de la mascota',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Vaccine'),
                        ),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'),
            ),
            new OA\Response(
                response: 403,
                description: 'No autorizado',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'),
            ),
            new OA\Response(
                response: 404,
                description: 'Mascota no encontrada',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'),
            ),
        ],
    )]
    public function byPet(Pet $pet): AnonymousResourceCollection
    {
        return VaccineResource::collection($this->vaccineService->findByPet($pet));
    }

    #[OA\Get(
        path: '/api/vaccines',
        summary: 'Listar vacunas',
        security: [['bearerAuth' => []]],
        tags: ['Vacunas'],
        responses: [
            new OA\Response(response: 200, description: 'Listado de vacunas', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Vaccine'))],
                type: 'object'
            )),
        ],
    )]
    public function index(): AnonymousResourceCollection
    {
        $vaccines = $this->vaccineService->findAll();

        return VaccineResource::collection($vaccines);
    }

    #[OA\Post(
        path: '/api/vaccines',
        summary: 'Crear vacuna',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/StoreVaccineRequest')),
        tags: ['Vacunas'],
        responses: [
            new OA\Response(response: 200, description: 'Vacuna creada', content: new OA\JsonContent(ref: '#/components/schemas/Vaccine')),
            new OA\Response(response: 422, description: 'Error de validación', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function store(StoreVaccineRequest $request): VaccineResource
    {
        $vaccine = $this->vaccineService->create($request->validated());
        $vaccine->load('pet');

        return new VaccineResource($vaccine);
    }

    #[OA\Get(
        path: '/api/vaccines/{vaccine}',
        summary: 'Ver vacuna',
        security: [['bearerAuth' => []]],
        tags: ['Vacunas'],
        parameters: [new OA\Parameter(name: 'vaccine', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detalle de vacuna', content: new OA\JsonContent(ref: '#/components/schemas/Vaccine')),
            new OA\Response(response: 404, description: 'Vacuna no encontrada', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function show(Vaccine $vaccine): VaccineResource
    {
        $vaccine->load('pet');

        return new VaccineResource($vaccine);
    }

    #[OA\Put(
        path: '/api/vaccines/{vaccine}',
        summary: 'Actualizar vacuna',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UpdateVaccineRequest')),
        tags: ['Vacunas'],
        parameters: [new OA\Parameter(name: 'vaccine', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Vacuna actualizada', content: new OA\JsonContent(ref: '#/components/schemas/Vaccine')),
            new OA\Response(response: 404, description: 'Vacuna no encontrada', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function update(UpdateVaccineRequest $request, Vaccine $vaccine): VaccineResource
    {
        $updatedVaccine = $this->vaccineService->update($vaccine, $request->validated());
        $vaccine->load('pet');

        return new VaccineResource($updatedVaccine);
    }

    #[OA\Delete(
        path: '/api/vaccines/{vaccine}',
        summary: 'Eliminar vacuna',
        security: [['bearerAuth' => []]],
        tags: ['Vacunas'],
        parameters: [new OA\Parameter(name: 'vaccine', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 204, description: 'Vacuna eliminada'),
            new OA\Response(response: 404, description: 'Vacuna no encontrada', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function destroy(Vaccine $vaccine): Response
    {
        $this->vaccineService->delete($vaccine);

        return response()->noContent();
    }
}
