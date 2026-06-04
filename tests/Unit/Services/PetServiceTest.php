<?php

namespace Tests\Unit\Services;

use App\Features\Pet\Models\Pet;
use App\Features\Pet\Services\PetService;
use App\Features\User\Models\User;
use App\Features\Vaccine\Models\Vaccine;
use App\Features\PetImage\Models\PetImage;
use App\Features\Pet\Queries\PetsByOwnerQuery;
use App\Features\Pet\Queries\SearchPetsQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class PetServiceTest extends TestCase
{
    use RefreshDatabase;

    private PetService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new PetService(
            $this->createMock(PetsByOwnerQuery::class),
            $this->createMock(SearchPetsQuery::class),
        );
    }

    private function createPet(array $overrides = []): Pet
    {
        return Pet::create(array_merge([
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
            'user_id' => User::factory()->create()->id,
        ], $overrides));
    }

    public function test_create_returns_pet(): void
    {
        $user = User::factory()->create();

        $data = [
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
        ];

        $pet = $this->service->create($data);

        $this->assertInstanceOf(Pet::class, $pet);
        $this->assertStringStartsWith('MAACD', $pet->identifier);
        $this->assertDatabaseHas('pets', ['name' => 'Firulais']);
    }

    public function test_find_by_identifier_returns_pet_with_relations(): void
    {
        $pet = $this->createPet();

        $found = $this->service->findByIdentifier($pet->identifier);

        $this->assertInstanceOf(Pet::class, $found);
        $this->assertTrue($found->relationLoaded('user'));
        $this->assertTrue($found->relationLoaded('vaccines'));
        $this->assertTrue($found->relationLoaded('images'));
        $this->assertEquals('Firulais', $found->name);
    }

    public function test_find_by_identifier_returns_null_when_not_found(): void
    {
        $found = $this->service->findByIdentifier('NONEXISTENT');

        $this->assertNull($found);
    }

    public function test_delete_throws_when_pet_has_vaccines(): void
    {
        $pet = $this->createPet();
        Vaccine::create([
            'type' => 'Rabia',
            'aplication_date' => '2024-01-01',
            'months_validity' => 12,
            'expiration_date' => '2025-01-01',
            'next_vaccine_date' => '2025-01-01',
            'pet_id' => $pet->id,
        ]);

        try {
            $this->service->delete($pet);
            $this->fail('Expected HttpException was not thrown.');
        } catch (HttpException $e) {
            $this->assertSame(409, $e->getStatusCode());
            $this->assertStringContainsString('vacunas asociadas', $e->getMessage());
        }
    }

    public function test_delete_throws_when_pet_has_images(): void
    {
        $pet = $this->createPet();
        PetImage::create([
            'pet_id' => $pet->id,
            'path_url' => '/api/pets/storage/pets/test.jpg',
        ]);

        try {
            $this->service->delete($pet);
            $this->fail('Expected HttpException was not thrown.');
        } catch (HttpException $e) {
            $this->assertSame(409, $e->getStatusCode());
            $this->assertStringContainsString('imágenes asociadas', $e->getMessage());
        }
    }

    public function test_delete_succeeds_when_pet_has_no_relations(): void
    {
        $pet = $this->createPet();

        $result = $this->service->delete($pet);

        $this->assertTrue($result);
        $this->assertModelMissing($pet);
    }
}
