<?php

namespace App\Features\Stats\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VaccineAlertResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'vaccine_id'       => $this->resource['vaccine_id'],
            'type'             => $this->resource['type'],
            'alert_type'       => $this->resource['alert_type'],
            'days_diff'        => $this->resource['days_diff'],
            'application_date' => $this->resource['aplication_date'],
            'next_vaccine_date'=> $this->resource['next_vaccine_date'],
            'months_validity'  => $this->resource['months_validity'],
            'pet'              => $this->resource['pet'],
            'owner'            => $this->resource['owner'],
        ];
    }
}
