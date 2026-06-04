<?php

namespace Tests\Unit\Services;

use App\Features\User\Models\Role;
use App\Features\User\Models\RoleName;
use App\Features\User\Models\User;
use App\Features\User\Queries\SearchOwnersQuery;
use App\Features\User\Services\UserService;
use App\Features\Pet\Models\Pet;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    private UserService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new UserService(
            $this->createMock(SearchOwnersQuery::class),
        );
    }

    public function test_create_owner_sets_password_as_dni(): void
    {
        $role = Role::create(['name' => RoleName::OWNER->value, 'description' => 'Owner']);

        $user = $this->service->createOwner([
            'dni' => '12345678',
            'name' => 'Juan Perez',
            'email' => 'juan@example.com',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('12345678', $user->dni);
        $this->assertNotEquals('12345678', $user->password);
        $this->assertTrue(password_verify('12345678', $user->password));
    }

    public function test_create_veterinarian_creates_with_role(): void
    {
        $role = Role::create(['name' => RoleName::VETERINARIAN->value, 'description' => 'Vet']);

        $user = $this->service->createVeterinarian([
            'dni' => '87654321',
            'name' => 'Dr. Lopez',
            'email' => 'lopez@example.com',
            'password' => 'securepass',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertTrue($user->relationLoaded('roles'));
        $this->assertTrue($user->hasRole(RoleName::VETERINARIAN));
    }

    public function test_delete_throws_when_user_has_pets(): void
    {
        $user = User::factory()->create();
        Pet::create([
            'name' => 'Firulais',
            'species' => 'Canino',
            'race' => 'Labrador',
            'gender' => 'MACHO',
            'temperament' => 'Tranquilo',
            'reproductive_condition' => 'ENTERO',
            'color' => 'Dorado',
            'years' => 3,
            'months' => 6,
            'status' => 'ADOPTADO',
            'user_id' => $user->id,
        ]);

        try {
            $this->service->delete($user);
            $this->fail('Expected HttpException was not thrown.');
        } catch (HttpException $e) {
            $this->assertSame(409, $e->getStatusCode());
            $this->assertStringContainsString('mascotas asociadas', $e->getMessage());
        }
    }

    public function test_delete_detaches_roles_and_deletes_user(): void
    {
        $role = Role::create(['name' => RoleName::OWNER->value, 'description' => 'Owner']);
        $user = User::factory()->create();
        $user->roles()->sync([$role->id]);

        $result = $this->service->delete($user);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('user_role', ['user_id' => $user->id]);
    }

    public function test_assert_can_manage_admin_allows_no_one(): void
    {
        $admin = $this->createUserWithRole(RoleName::ADMIN);
        $vet = $this->createUserWithRole(RoleName::VETERINARIAN);
        $owner = $this->createUserWithRole(RoleName::OWNER);

        $this->expectException(AuthorizationException::class);
        $this->service->assertCanManage($admin, RoleName::ADMIN);

        $this->expectException(AuthorizationException::class);
        $this->service->assertCanManage($vet, RoleName::ADMIN);

        $this->expectException(AuthorizationException::class);
        $this->service->assertCanManage($owner, RoleName::ADMIN);

        $this->expectException(AuthorizationException::class);
        $this->service->assertCanManage(null, RoleName::ADMIN);
    }

    public function test_assert_can_manage_veterinarian_allows_only_admin(): void
    {
        $admin = $this->createUserWithRole(RoleName::ADMIN);
        $vet = $this->createUserWithRole(RoleName::VETERINARIAN);
        $owner = $this->createUserWithRole(RoleName::OWNER);

        $this->service->assertCanManage($admin, RoleName::VETERINARIAN);
        $this->assertTrue(true);

        $this->expectException(AuthorizationException::class);
        $this->service->assertCanManage($vet, RoleName::VETERINARIAN);

        $this->expectException(AuthorizationException::class);
        $this->service->assertCanManage($owner, RoleName::VETERINARIAN);

        $this->expectException(AuthorizationException::class);
        $this->service->assertCanManage(null, RoleName::VETERINARIAN);
    }

    public function test_assert_can_manage_owner_allows_admin_and_veterinarian(): void
    {
        $admin = $this->createUserWithRole(RoleName::ADMIN);
        $vet = $this->createUserWithRole(RoleName::VETERINARIAN);
        $owner = $this->createUserWithRole(RoleName::OWNER);

        $this->service->assertCanManage($admin, RoleName::OWNER);
        $this->service->assertCanManage($vet, RoleName::OWNER);
        $this->assertTrue(true);

        $this->expectException(AuthorizationException::class);
        $this->service->assertCanManage($owner, RoleName::OWNER);

        $this->expectException(AuthorizationException::class);
        $this->service->assertCanManage(null, RoleName::OWNER);
    }

    public function test_managed_role_for_returns_role_name(): void
    {
        $role = Role::create(['name' => RoleName::VETERINARIAN->value, 'description' => 'Vet']);
        $user = User::factory()->create();
        $user->roles()->sync([$role->id]);

        $result = $this->service->managedRoleFor($user);

        $this->assertEquals(RoleName::VETERINARIAN, $result);
    }

    public function test_managed_role_for_throws_when_user_has_no_role(): void
    {
        $user = User::factory()->create();

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('El usuario no tiene un rol gestionable.');

        $this->service->managedRoleFor($user);
    }

    private function createUserWithRole(RoleName $roleName): User
    {
        $role = Role::firstOrCreate(
            ['name' => $roleName->value],
            ['description' => $roleName->value],
        );
        $user = User::factory()->create();
        $user->roles()->sync([$role->id]);

        return $user;
    }
}
