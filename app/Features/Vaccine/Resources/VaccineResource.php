<?php

namespace App\Features\Vaccine\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VaccineResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"                =>$this->id,
            "type"              =>$this->type,
            "aplication_date"   =>$this->aplication_date,
            "months_validity"   =>$this->months_validity,
            "expiration_date"   =>$this->expiration_date,
            "pet"               =>$this->pet?->only("id","name"),

        ];
    }
}
