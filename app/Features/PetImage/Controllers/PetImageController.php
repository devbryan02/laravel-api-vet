<?php

namespace App\Features\PetImage\Controllers;

use App\Features\PetImage\Models\PetImage;
use App\Features\PetImage\Requests\StoreImageRequest;
use App\Features\PetImage\Services\PetImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use OpenApi\Attributes as OA;

class PetImageController extends Controller
{
    protected PetImageService $petImageService;

    public function __construct(PetImageService $petImageService)
    {
        $this->petImageService = $petImageService;
    }

    #[OA\Post(
        path: '/api/pets/images',
        summary: 'Subir imagen de mascota',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(ref: '#/components/schemas/StorePetImageRequest')
            )
        ),
        tags: ['Imágenes'],
        responses: [
            new OA\Response(response: 201, description: 'Imagen guardada', content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Imagen de la mascota guardada exitosamente.'),
                    new OA\Property(property: 'data', ref: '#/components/schemas/PetImage'),
                ],
                type: 'object'
            )),
            new OA\Response(response: 422, description: 'Error de validación', content: new OA\JsonContent(ref: '#/components/schemas/ValidationErrorResponse')),
        ],
    )]
    public function store(StoreImageRequest $request): JsonResponse
    {
        $file = $request->file('image');
        $petImage = $this->petImageService->storeImage($file, $request->validated());

        return response()->json([
            'message' => 'Imagen de la mascota guardada exitosamente.',
            'data' => $petImage,
        ], 201);
    }

    #[OA\Delete(
        path: '/api/pets/images/{image}',
        summary: 'Eliminar imagen de mascota',
        security: [['bearerAuth' => []]],
        tags: ['Imágenes'],
        parameters: [new OA\Parameter(name: 'image', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Imagen eliminada', content: new OA\JsonContent(
                properties: [new OA\Property(property: 'message', type: 'string', example: 'La imagen y el archivo físico fueron eliminados correctamente.')],
                type: 'object'
            )),
            new OA\Response(response: 404, description: 'Imagen no encontrada', content: new OA\JsonContent(ref: '#/components/schemas/ErrorResponse')),
        ],
    )]
    public function destroy(PetImage $image): JsonResponse
    {
        $this->petImageService->deleteImage($image);

        return response()->json([
            'message' => 'La imagen y el archivo físico fueron eliminados correctamente.',
        ], 200);
    }
}
