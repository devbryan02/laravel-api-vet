<?php

namespace App\Features\Pet\Controllers;

use App\Features\Pet\Models\Pet;
use App\Features\Pet\Requests\StorePetRequest;
use App\Features\Pet\Requests\UpdatePetRequest;
use App\Features\Pet\Resources\PetResource;
use App\Features\Pet\Resources\PetVerifyResource;
use App\Features\Pet\Services\PetService;
use App\Support\PaginatedResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

class PetController extends Controller
{
    protected PetService $service;

    public function __construct(PetService $service)
    {
        $this->service = $service;
    }

    #[OA\Get(
        path: '/api/pets',
        summary: 'Listar mascotas',
        security: [['bearerAuth' => []]],
        tags: ['Mascotas'],
        parameters: [
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 10)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Listado paginado de mascotas', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Pet')),
                    new OA\Property(property: 'pagination', type: 'object'),
                ],
                type: 'object'
            )),
            new OA\Response(response: 401, description: 'No autenticado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 10);
        $pets = $this->service->findAll($perPage);

        return PaginatedResponse::make(PetResource::collection($pets), $pets);
    }

    #[OA\Post(
        path: '/api/pets',
        summary: 'Crear mascota',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/StorePetRequest')),
        tags: ['Mascotas'],
        responses: [
            new OA\Response(response: 200, description: 'Mascota creada', content: new OA\JsonContent(ref: '#/components/schemas/Pet')),
            new OA\Response(response: 422, description: 'Error de validación', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function store(StorePetRequest $request): PetResource
    {
        $pet = $this->service->create($request->validated());
        $pet->load('user');

        return new PetResource($pet);
    }

    #[OA\Get(
        path: '/api/pets/{pet}',
        summary: 'Ver mascota',
        security: [['bearerAuth' => []]],
        tags: ['Mascotas'],
        parameters: [new OA\Parameter(name: 'pet', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detalle de mascota', content: new OA\JsonContent(ref: '#/components/schemas/Pet')),
            new OA\Response(response: 404, description: 'Mascota no encontrada', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function show(Pet $pet): PetResource
    {
        $pet->load('user', 'images');

        return new PetResource($pet);
    }

    #[OA\Put(
        path: '/api/pets/{pet}',
        summary: 'Actualizar mascota',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UpdatePetRequest')),
        tags: ['Mascotas'],
        parameters: [new OA\Parameter(name: 'pet', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Mascota actualizada', content: new OA\JsonContent(ref: '#/components/schemas/Pet')),
            new OA\Response(response: 404, description: 'Mascota no encontrada', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function update(UpdatePetRequest $request, Pet $pet): PetResource
    {
        $updatedPet = $this->service->update($pet, $request->validated());
        $updatedPet->load('user');

        return new PetResource($updatedPet);
    }

    #[OA\Delete(
        path: '/api/pets/{pet}',
        summary: 'Eliminar mascota',
        security: [['bearerAuth' => []]],
        tags: ['Mascotas'],
        parameters: [new OA\Parameter(name: 'pet', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 204, description: 'Mascota eliminada'),
            new OA\Response(response: 404, description: 'Mascota no encontrada', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function destroy(Pet $pet): Response
    {
        $this->service->delete($pet);

        return response()->noContent();
    }

    #[OA\Get(
        path: '/api/pets/owner/{ownerId}',
        summary: 'Listar mascotas de un dueño',
        security: [['bearerAuth' => []]],
        tags: ['Mascotas'],
        parameters: [
            new OA\Parameter(name: 'ownerId', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 10)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Listado paginado de mascotas', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Pet')),
                    new OA\Property(property: 'pagination', type: 'object'),
                ],
                type: 'object'
            )),
        ],
    )]
    public function byOwner(string $ownerId, Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 10);
        $pets = $this->service->findByOwner($ownerId, $perPage);

        return PaginatedResponse::make(PetResource::collection($pets), $pets);
    }

    #[OA\Get(
        path: '/api/pets/search',
        summary: 'Buscar mascotas por nombre o DNI/nombre del dueño',
        security: [['bearerAuth' => []]],
        tags: ['Mascotas'],
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 10)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Listado paginado de mascotas', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Pet')),
                    new OA\Property(property: 'pagination', type: 'object'),
                ],
                type: 'object'
            )),
        ],
    )]
    public function search(Request $request): JsonResponse
    {
        $search = $request->input('q');
        $perPage = (int) $request->input('per_page', 10);
        $pets = $this->service->search($search, $perPage);

        return PaginatedResponse::make(PetResource::collection($pets), $pets);
    }

    public function verify(string $identifier): PetVerifyResource|JsonResponse
    {
        $pet = $this->service->findByIdentifier($identifier);

        if (!$pet) {
            return response()->json(['message' => 'Mascota no encontrada'], 404);
        }

        return new PetVerifyResource($pet);
    }
}
