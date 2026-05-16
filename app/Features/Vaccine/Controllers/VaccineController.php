<?php

namespace App\Features\Vaccine\Controllers;

use App\Features\Vaccine\Models\Vaccine;
use App\Features\Vaccine\Requests\StoreVaccineRequest;
use App\Features\Vaccine\Requests\UpdateVaccineRequest;
use App\Features\Vaccine\Resources\VaccineResource;
use App\Features\Vaccine\Services\VaccineService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class VaccineController extends Controller
{
    protected VaccineService $vaccineService;

    public function __construct(VaccineService $vaccineService)
    {
        $this->vaccineService = $vaccineService;
    }

    public function index(): AnonymousResourceCollection
    {
        $vaccines = $this->vaccineService->findAll();
        return VaccineResource::collection($vaccines);
    }

    public function store(StoreVaccineRequest $request): VaccineResource
    {
        $vaccine = $this->vaccineService->create($request->validated());
        $vaccine->load("pet");

        return new VaccineResource($vaccine);
    }

    public function show(Vaccine $vaccine): VaccineResource
    {
        $vaccine->load("pet");
        return new VaccineResource($vaccine);
    }

    public function update(UpdateVaccineRequest $request, Vaccine $vaccine): VaccineResource
    {
        $updatedVaccine = $this->vaccineService->update($vaccine, $request->validated());
        $vaccine->load("pet");

        return new VaccineResource($updatedVaccine);
    }

    public function destroy(Vaccine $vaccine): Response
    {
        $this->vaccineService->delete($vaccine);
        return response()->noContent();
    }

}
