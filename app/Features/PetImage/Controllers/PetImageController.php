<?php

namespace App\Features\PetImage\Controllers;

use App\Features\PetImage\Models\PetImage;
use App\Features\PetImage\Requests\StoreImageRequest;
use App\Features\PetImage\Services\PetImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class PetImageController extends Controller
{
    protected PetImageService $petImageService;

    public function __construct(PetImageService $petImageService)
    {
        $this->petImageService = $petImageService;
    }

    public function store(StoreImageRequest $request): JsonResponse
    {
        $file = $request->file('image');
        $petImage = $this->petImageService->storeImage($file, $request->validated());

        return response()->json([
            'message' => 'Imagen de la mascota guardada exitosamente.',
            'data' => $petImage
        ], 201);
    }

    public function destroy(PetImage $image): JsonResponse
    {
        $this->petImageService->deleteImage($image);

        return response()->json([
            'message' => 'La imagen y el archivo físico fueron eliminados correctamente.'
        ], 200);
    }
}
