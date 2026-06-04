<?php

namespace Tests\Unit\Services;

use App\Features\Pet\Models\Pet;
use App\Features\PetImage\Models\PetImage;
use App\Features\PetImage\Services\PetImageService;
use App\Features\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PetImageServiceTest extends TestCase
{
    use RefreshDatabase;

    private PetImageService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new PetImageService();
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

    public function test_store_image_stores_file_and_creates_record(): void
    {
        Storage::fake('public');

        $pet = $this->makePet();

        $file = UploadedFile::fake()->create('foto.txt', 10);

        $image = $this->service->storeImage($file, [
            'pet_id' => $pet->id,
            'description' => 'Foto de perfil',
        ]);

        $this->assertInstanceOf(PetImage::class, $image);
        $this->assertEquals($pet->id, $image->pet_id);
        $this->assertEquals('Foto de perfil', $image->description);
        $this->assertStringStartsWith('/api/pets/storage/pets/', $image->path_url);
        $this->assertStringEndsWith('.txt', $image->path_url);

        Storage::disk('public')->assertExists(
            str_replace('/api/pets/storage/', '', $image->path_url)
        );
    }

    public function test_delete_image_removes_file_and_record(): void
    {
        Storage::fake('public');

        $pet = $this->makePet();

        $file = UploadedFile::fake()->create('foto.txt', 10);
        $image = $this->service->storeImage($file, ['pet_id' => $pet->id]);

        $result = $this->service->deleteImage($image);

        $this->assertTrue($result);
        $this->assertModelMissing($image);
        Storage::disk('public')->assertMissing(
            str_replace('/api/pets/storage/', '', $image->path_url)
        );
    }

    public function test_delete_image_handles_missing_file_gracefully(): void
    {
        $pet = $this->makePet();

        $image = PetImage::create([
            'pet_id' => $pet->id,
            'path_url' => '/api/pets/storage/pets/nonexistent.jpg',
        ]);

        $result = $this->service->deleteImage($image);

        $this->assertTrue($result);
        $this->assertModelMissing($image);
    }
}
