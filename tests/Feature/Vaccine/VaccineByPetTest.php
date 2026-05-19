<?php

namespace Tests\Feature\Vaccine;

use App\Features\Pet\Models\Pet;
use App\Features\User\Models\Role;
use App\Features\User\Models\RoleName;
use App\Features\User\Models\User;
use App\Features\Vaccine\Models\Vaccine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VaccineByPetTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_admin_can_list_vaccines_by_pet(): void
    {
        $role = Role::create([
            'name' => RoleName::ADMIN->value,
            'description' => 'Administrador del sistema',
        ]);

        $user = User::factory()->create();
        $user->roles()->sync([$role->id]);

        $pet = Pet::create([
            'identifier' => 'PET-001',
            'name' => 'Firulais',
            'species' => 'Canino',
            'race' => 'Mestizo',
            'gender' => 'MACHO',
            'temperament' => 'Tranquilo',
            'reproductive_condition' => 'ENTERO',
            'color' => 'Marrón',
            'years' => 3,
            'months' => 4,
            'status' => 'ADOPTADO',
            'user_id' => $user->id,
        ]);

        Vaccine::create([
            'type' => 'Rabia',
            'aplication_date' => '2026-05-01',
            'months_validity' => 12,
            'expiration_date' => '2027-05-01',
            'next_vaccine_date' => '2027-05-01',
            'pet_id' => $pet->id,
        ]);

        Vaccine::create([
            'type' => 'Parvovirus',
            'aplication_date' => '2026-04-15',
            'months_validity' => 12,
            'expiration_date' => '2027-04-15',
            'next_vaccine_date' => '2027-04-15',
            'pet_id' => $pet->id,
        ]);

        $token = auth('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/pets/{$pet->id}/vaccines");

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.pet.id', $pet->id)
            ->assertJsonPath('data.0.pet.name', 'Firulais');
    }
}
