<?php

namespace App\Features\Pet\Controllers;

use App\Features\Pet\Models\Pet;
use App\Features\Pet\Requests\StorePetRequest;
use App\Features\Pet\Requests\UpdatePetRequest;
use App\Features\Pet\Resources\PetResource;
use App\Features\Pet\Services\PetService;
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
        responses: [
            new OA\Response(response: 200, description: 'Listado de mascotas', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Pet'))],
                type: 'object'
            )),
            new OA\Response(response: 401, description: 'No autenticado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function index(): AnonymousResourceCollection
    {
        $pets = $this->service->findAll();

        return PetResource::collection($pets);
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
        $pet->load('user');

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
}
