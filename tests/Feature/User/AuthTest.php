<?php

namespace Tests\Feature\User;

use App\Features\User\Models\Role;
use App\Features\User\Models\RoleName;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_jwt_payload_for_valid_credentials(): void
    {
        $role = Role::create([
            'name' => RoleName::ADMIN->value,
            'description' => 'Administrador del sistema',
        ]);

        $user = User::factory()->create([
            'name' => 'Usuario Demo',
            'email' => 'demo@example.com',
            'password' => 'password123',
        ]);

        $user->roles()->sync([$role->id]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'demo@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Inicio de sesión exitoso')
            ->assertJsonPath('token_type', 'bearer')
            ->assertJsonPath('expires_in', 3600)
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.name', 'Usuario Demo')
            ->assertJsonPath('user.email', 'demo@example.com')
            ->assertJsonPath('user.role', RoleName::ADMIN->value);

        $this->assertIsString($response->json('token'));
        $this->assertNotEmpty($response->json('token'));
    }

    public function test_logout_invalidates_the_current_token(): void
    {
        $user = User::factory()->create();
        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/auth/logout');

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Cierre de sesión exitoso',
            ]);
    }

    public function test_logout_returns_consistent_error_when_token_is_missing(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response
            ->assertUnauthorized()
            ->assertJson([
                'success' => false,
                'message' => 'No autenticado.',
                'status' => 401,
            ]);
    }

    public function test_logout_returns_consistent_error_when_token_is_invalid(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer token-invalido')
            ->postJson('/api/auth/logout');

        $response
            ->assertUnauthorized()
            ->assertJson([
                'success' => false,
                'message' => 'No autenticado.',
                'status' => 401,
            ]);
    }

    public function test_login_validates_required_fields(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'password']);
    }
}
