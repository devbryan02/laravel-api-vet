<?php

namespace App\Features\User\Controllers;

use App\Features\User\Models\RoleName;
use App\Features\User\Models\User;
use App\Features\User\Requests\StoreOwnerRequest;
use App\Features\User\Requests\StoreVeterinarianRequest;
use App\Features\User\Requests\UpdateUserRequest;
use App\Features\User\Resources\UserResource;
use App\Features\User\Services\UserService;
use App\Support\PaginatedResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function __construct(private readonly UserService $service) {}

    #[OA\Get(
        path: '/api/users/veterinarians',
        summary: 'Listar veterinarios',
        security: [['bearerAuth' => []]],
        tags: ['Usuarios'],
        responses: [
            new OA\Response(response: 200, description: 'Listado de veterinarios', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/User'))],
                type: 'object'
            )),
            new OA\Response(response: 401, description: 'No autenticado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'No autorizado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function veterinarians(Request $request): AnonymousResourceCollection
    {
        $this->service->assertCanManage($request->user(), RoleName::VETERINARIAN);

        return UserResource::collection($this->service->findByRole(RoleName::VETERINARIAN));
    }

    #[OA\Post(
        path: '/api/users/veterinarians',
        summary: 'Crear veterinario',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/StoreVeterinarianRequest')),
        tags: ['Usuarios'],
        responses: [
            new OA\Response(response: 201, description: 'Veterinario creado', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Veterinario creado correctamente.'),
                    new OA\Property(property: 'data', ref: '#/components/schemas/User'),
                ],
                type: 'object'
            )),
            new OA\Response(response: 401, description: 'No autenticado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'No autorizado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function storeVeterinarian(StoreVeterinarianRequest $request): JsonResponse
    {
        $user = $this->service->createVeterinarian($request->validated());

        return response()->json([
            'message' => 'Veterinario creado correctamente.',
            'data' => new UserResource($user),
        ], Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: '/api/users/owners',
        summary: 'Listar dueños',
        security: [['bearerAuth' => []]],
        tags: ['Usuarios'],
        parameters: [
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 10)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Listado paginado de dueños', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/User')),
                    new OA\Property(property: 'pagination', type: 'object'),
                ],
                type: 'object'
            )),
            new OA\Response(response: 401, description: 'No autenticado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'No autorizado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function owners(Request $request): JsonResponse
    {
        $this->service->assertCanManage($request->user(), RoleName::OWNER);

        $perPage = (int) $request->input('per_page', 10);
        $owners = $this->service->paginatedOwners($perPage);

        return PaginatedResponse::make(UserResource::collection($owners), $owners);
    }

    #[OA\Get(
        path: '/api/users/owners/search',
        summary: 'Buscar dueños por DNI o nombre',
        security: [['bearerAuth' => []]],
        tags: ['Usuarios'],
        parameters: [
            new OA\Parameter(name: 'q', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 10)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Listado paginado de dueños', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/User')),
                    new OA\Property(property: 'pagination', type: 'object'),
                ],
                type: 'object'
            )),
            new OA\Response(response: 401, description: 'No autenticado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'No autorizado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function searchOwners(Request $request): JsonResponse
    {
        $this->service->assertCanManage($request->user(), RoleName::OWNER);

        $search = $request->input('q');
        $perPage = (int) $request->input('per_page', 10);
        $owners = $this->service->searchOwners($search, $perPage);

        return PaginatedResponse::make(UserResource::collection($owners), $owners);
    }

    #[OA\Post(
        path: '/api/users/owners',
        summary: 'Crear dueño',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/StoreOwnerRequest')),
        tags: ['Usuarios'],
        responses: [
            new OA\Response(response: 201, description: 'Dueño creado', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Dueño creado correctamente.'),
                    new OA\Property(property: 'data', ref: '#/components/schemas/User'),
                ],
                type: 'object'
            )),
            new OA\Response(response: 401, description: 'No autenticado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'No autorizado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function storeOwner(StoreOwnerRequest $request): JsonResponse
    {
        $user = $this->service->createOwner($request->validated());

        return response()->json([
            'message' => 'Dueño creado correctamente.',
            'data' => new UserResource($user),
        ], Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: '/api/users/{user}',
        summary: 'Ver usuario',
        security: [['bearerAuth' => []]],
        tags: ['Usuarios'],
        parameters: [new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Detalle de usuario', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'data', ref: '#/components/schemas/User')],
                type: 'object'
            )),
            new OA\Response(response: 401, description: 'No autenticado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'No autorizado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Usuario no encontrado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function show(Request $request, User $user): JsonResponse
    {
        $managedRole = $this->service->managedRoleFor($user);
        $this->service->assertCanManage($request->user(), $managedRole);

        return response()->json([
            'data' => new UserResource($user->load('roles')),
        ], Response::HTTP_OK);
    }

    #[OA\Put(
        path: '/api/users/{user}',
        summary: 'Actualizar usuario',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/UpdateUserRequest')),
        tags: ['Usuarios'],
        parameters: [new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Usuario actualizado', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Usuario actualizado correctamente.'),
                    new OA\Property(property: 'data', ref: '#/components/schemas/User'),
                ],
                type: 'object'
            )),
            new OA\Response(response: 401, description: 'No autenticado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'No autorizado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Usuario no encontrado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 422, description: 'Error de validación', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $managedRole = $this->service->managedRoleFor($user);
        $this->service->assertCanManage($request->user(), $managedRole);

        $user = $this->service->update($user, $request->validated());

        return response()->json([
            'message' => 'Usuario actualizado correctamente.',
            'data' => new UserResource($user),
        ], Response::HTTP_OK);
    }

    #[OA\Delete(
        path: '/api/users/{user}',
        summary: 'Eliminar usuario',
        security: [['bearerAuth' => []]],
        tags: ['Usuarios'],
        parameters: [new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 204, description: 'Usuario eliminado'),
            new OA\Response(response: 401, description: 'No autenticado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 403, description: 'No autorizado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
            new OA\Response(response: 404, description: 'Usuario no encontrado', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function destroy(Request $request, User $user): Response
    {
        $managedRole = $this->service->managedRoleFor($user);
        $this->service->assertCanManage($request->user(), $managedRole);

        $this->service->delete($user);

        return response()->noContent();
    }
}
