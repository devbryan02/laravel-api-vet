<?php

namespace Tests\Unit\Services;

use App\Features\User\Models\Role;
use App\Features\User\Models\RoleName;
use App\Features\User\Models\User;
use App\Features\User\Services\AuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AuthService();
    }

    public function test_login_throws_for_invalid_credentials(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Credenciales inválidas.');

        $this->service->login([
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);
    }
}
