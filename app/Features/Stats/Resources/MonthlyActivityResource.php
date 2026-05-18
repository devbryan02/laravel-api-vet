<?php

namespace App\Features\Stats\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'month'            => $this->resource['month'],
            'month_key'        => $this->resource['month_key'],
            'new_pets'         => $this->resource['new_pets'],
            'vaccines_applied' => $this->resource['vaccines_applied'],
        ];
    }
}
