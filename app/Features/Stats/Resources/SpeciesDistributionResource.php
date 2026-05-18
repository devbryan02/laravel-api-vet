<?php

namespace App\Features\Stats\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpeciesDistributionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'species'    => $this->resource['species'],
            'total'      => $this->resource['total'],
            'percentage' => $this->resource['percentage'],
        ];
    }
}
