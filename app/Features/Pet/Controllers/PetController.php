<?php

namespace App\Features\Pet\Controllers;

use App\Features\Pet\Requests\StorePetRequest;
use App\Features\Pet\Resources\PetResource;
use App\Features\Pet\Services\CreatePetService;
use Illuminate\Routing\Controller;

class PetController extends Controller
{
    public function store(StorePetRequest $request, CreatePetService $service): PetResource
    {
        $pet = $service->execute($request->validated());

        $pet->load('user');

        return new PetResource($pet);
    }
}
