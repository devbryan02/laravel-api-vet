<?php

namespace App\Features\User\Services;

use App\Features\User\Models\User;
use Illuminate\Auth\AuthenticationException;

class AuthService
{
    /**
     * @throws AuthenticationException
     */
    public function login(array $credentials): array
    {
        $token = auth('api')->attempt($credentials);

        if (! $token) {
            throw new AuthenticationException('Credenciales inválidas.');
        }

        /** @var User $user */
        $user = auth('api')->user()->load('roles');

        return [
            'message' => 'Inicio de sesión exitoso',
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user,
        ];
    }

    public function logout(): void
    {
        auth('api')->logout();
    }
}
