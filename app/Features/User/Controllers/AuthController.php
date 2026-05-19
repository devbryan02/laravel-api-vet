<?php

namespace App\Features\User\Controllers;

use App\Features\User\Requests\LoginRequest;
use App\Features\User\Resources\UserAuthResource;
use App\Features\User\Resources\UserResource;
use App\Features\User\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $service) {}

    #[OA\Post(
        path: '/api/auth/login',
        description: 'Autentica al usuario y retorna un JWT válido.',
        summary: 'Iniciar sesión',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/AuthLoginRequest'),
        ),
        tags: ['Autenticación'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Inicio de sesión exitoso',
                content: new OA\JsonContent(ref: '#/components/schemas/AuthSuccessResponse'),
            ),
            new OA\Response(
                response: 401,
                description: 'Credenciales inválidas',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'),
            ),
            new OA\Response(
                response: 422,
                description: 'Error de validación',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse'),
            ),
        ],
    )]
    public function login(LoginRequest $request): JsonResponse
    {
        return (new UserAuthResource($this->service->login($request->validated())))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    #[OA\Post(
        path: '/api/auth/logout',
        description: 'Invalida el token JWT actual.',
        summary: 'Cerrar sesión',
        security: [['bearerAuth' => []]],
        tags: ['Autenticación'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cierre de sesión exitoso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Cierre de sesión exitoso'),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'),
            ),
        ],
    )]
    public function logout(): JsonResponse
    {
        $this->service->logout();

        return response()->json([
            'message' => 'Cierre de sesión exitoso',
        ], Response::HTTP_OK);
    }

    #[OA\Get(
        path: '/api/user',
        description: 'Retorna los datos del usuario autenticado actual.',
        summary: 'Usuario autenticado',
        security: [['bearerAuth' => []]],
        tags: ['Autenticación'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Usuario autenticado',
                content: new OA\JsonContent(ref: '#/components/schemas/User'),
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado',
                content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse'),
            ),
        ],
    )]
    public function me(Request $request): UserResource
    {
        return new UserResource($request->user()->loadMissing('roles'));
    }
}
