<?php

namespace App\Features\Stats\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnvaccinatedPetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'species'      => $this->species,
            'race'         => $this->race,
            'status'       => $this->status,
            'registered_at'=> $this->created_at?->format('Y-m-d'),
            'owner'        => [
                'id'    => $this->user?->id,
                'name'  => $this->user?->name,
                'phone' => $this->user?->phone,
            ],
        ];
    }
}
