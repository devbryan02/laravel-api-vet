<?php

namespace App\Features\Pet\Controllers;

use App\Features\Pet\Models\Pet;
use App\Features\Pet\Requests\StorePetRequest;
use App\Features\Pet\Requests\UpdatePetRequest;
use App\Features\Pet\Resources\PetResource;
use App\Features\Pet\Services\PetService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class PetController extends Controller
{
    protected PetService $service;

    public function __construct(PetService $service)
    {
        $this->service = $service;
    }

    public function index(): AnonymousResourceCollection
    {
        $pets = $this->service->findAll();

        return PetResource::collection($pets);
    }

    public function store(StorePetRequest $request): PetResource
    {
        $pet = $this->service->create($request->validated());
        $pet->load('user');

        return new PetResource($pet);
    }

    public function show(Pet $pet): PetResource
    {
        $pet->load("user");

        return new PetResource($pet);
    }

    public function update(UpdatePetRequest $request, Pet $pet): PetResource
    {
        $updatedPet = $this->service->update($pet, $request->validated());
        $updatedPet->load('user');

        return new PetResource($updatedPet);
    }

    public function destroy(Pet $pet): Response
    {
        $this->service->delete($pet);

        return response()->noContent();
    }


}
