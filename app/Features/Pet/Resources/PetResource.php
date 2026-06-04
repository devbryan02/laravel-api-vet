<?php
namespace App\Features\Pet\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"            =>$this->id,
            "identifier"    =>$this->identifier,
            "name"          =>$this->name,
            "species"       =>$this->species,
            "race"          =>$this->race,
            "gender"        =>$this->gender,
            "color"         =>$this->color,
            "temperament"   =>$this->temperament,
            "reproductive_condition"=>$this->reproductive_condition,
            "age"           =>$this->years." años y ".$this->months." meses",
            "status"        =>$this->status,
            "user"          =>$this->user?->only("id","name","phone"),
            "images"        =>$this->whenLoaded('images', fn() =>
                $this->images->map(fn($img) => [
                    "id"       => $img->id,
                    "path_url" => $img->path_url,
                    "pet_id"   => $img->pet_id,
                ])
            ),
        ];
    }
}
