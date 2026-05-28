<?php
namespace App\Features\Pet\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetVerifyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "identifier" => $this->identifier,
            "name"       => $this->name,
            "species"    => $this->species,
            "race"       => $this->race,
            "gender"     => $this->gender,
            "color"      => $this->color,
            "temperament"=> $this->temperament,
            "reproductive_condition"=> $this->reproductive_condition,
            "age"        => $this->years . " años y " . $this->months . " meses",
            "status"     => $this->status,
            "owner"      => $this->user?->only("id", "name", "phone", "address", "dni"),
            "images"     => $this->images->map(fn($img) => [
                "id"       => $img->id,
                "path_url" => $img->path_url,
            ]),
            "vaccines"   => $this->vaccines->map(fn($v) => [
                "id"                => $v->id,
                "type"              => $v->type,
                "aplication_date"   => $v->aplication_date,
                "months_validity"   => $v->months_validity,
                "expiration_date"   => $v->expiration_date,
                "next_vaccine_date" => $v->next_vaccine_date,
            ]),
        ];
    }
}
