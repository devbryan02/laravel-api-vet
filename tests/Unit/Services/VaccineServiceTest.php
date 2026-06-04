<?php

namespace Tests\Unit\Services;

use App\Features\Pet\Models\Pet;
use App\Features\User\Models\User;
use App\Features\Vaccine\Models\Vaccine;
use App\Features\Vaccine\Services\VaccineService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VaccineServiceTest extends TestCase
{
    use RefreshDatabase;

    private VaccineService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new VaccineService();
    }

    private function makePet(): Pet
    {
        return Pet::create([
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
        ]);
    }

    public function test_create_calculates_expiration_and_next_vaccine_date(): void
    {
        $pet = $this->makePet();

        $vaccine = $this->service->create([
            'type' => 'Rabia',
            'aplication_date' => '2024-01-15',
            'months_validity' => 12,
            'pet_id' => $pet->id,
        ]);

        $this->assertEquals('2024-01-15', Carbon::parse($vaccine->aplication_date)->format('Y-m-d'));
        $this->assertEquals('2025-01-15', Carbon::parse($vaccine->expiration_date)->format('Y-m-d'));
        $this->assertEquals('2025-01-15', Carbon::parse($vaccine->next_vaccine_date)->format('Y-m-d'));
    }

    public function test_update_recalculates_when_aplication_date_changes(): void
    {
        $pet = $this->makePet();
        $vaccine = Vaccine::create([
            'type' => 'Rabia',
            'aplication_date' => '2024-01-15',
            'months_validity' => 12,
            'expiration_date' => '2025-01-15',
            'next_vaccine_date' => '2025-01-15',
            'pet_id' => $pet->id,
        ]);

        $updated = $this->service->update($vaccine, [
            'aplication_date' => '2024-06-01',
        ]);

        $this->assertEquals('2024-06-01', Carbon::parse($updated->aplication_date)->format('Y-m-d'));
        $this->assertEquals('2025-06-01', Carbon::parse($updated->expiration_date)->format('Y-m-d'));
        $this->assertEquals('2025-06-01', Carbon::parse($updated->next_vaccine_date)->format('Y-m-d'));
    }

    public function test_update_recalculates_when_months_validity_changes(): void
    {
        $pet = $this->makePet();
        $vaccine = Vaccine::create([
            'type' => 'Rabia',
            'aplication_date' => '2024-01-15',
            'months_validity' => 12,
            'expiration_date' => '2025-01-15',
            'next_vaccine_date' => '2025-01-15',
            'pet_id' => $pet->id,
        ]);

        $updated = $this->service->update($vaccine, [
            'months_validity' => 6,
        ]);

        $this->assertEquals('2024-01-15', Carbon::parse($updated->aplication_date)->format('Y-m-d'));
        $this->assertEquals('2024-07-15', Carbon::parse($updated->expiration_date)->format('Y-m-d'));
        $this->assertEquals('2024-07-15', Carbon::parse($updated->next_vaccine_date)->format('Y-m-d'));
    }

    public function test_update_preserves_dates_when_neither_field_changes(): void
    {
        $pet = $this->makePet();
        $vaccine = Vaccine::create([
            'type' => 'Rabia',
            'aplication_date' => '2024-01-15',
            'months_validity' => 12,
            'expiration_date' => '2025-01-15',
            'next_vaccine_date' => '2025-01-15',
            'pet_id' => $pet->id,
        ]);

        $updated = $this->service->update($vaccine, [
            'type' => 'Antirrábica',
        ]);

        $this->assertEquals('Antirrábica', $updated->type);
        $this->assertEquals('2024-01-15', Carbon::parse($updated->aplication_date)->format('Y-m-d'));
        $this->assertEquals('2025-01-15', Carbon::parse($updated->expiration_date)->format('Y-m-d'));
        $this->assertEquals('2025-01-15', Carbon::parse($updated->next_vaccine_date)->format('Y-m-d'));
    }

    public function test_delete_removes_vaccine(): void
    {
        $pet = $this->makePet();
        $vaccine = Vaccine::create([
            'type' => 'Rabia',
            'aplication_date' => '2024-01-15',
            'months_validity' => 12,
            'expiration_date' => '2025-01-15',
            'next_vaccine_date' => '2025-01-15',
            'pet_id' => $pet->id,
        ]);

        $result = $this->service->delete($vaccine);

        $this->assertTrue($result);
        $this->assertModelMissing($vaccine);
    }
}
