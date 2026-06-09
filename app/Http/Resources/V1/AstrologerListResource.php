<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AstrologerListResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->user->name,
            'photo' => $this->photo,
            'years_of_experience' => $this->years_of_experience,
            'price_per_minute' => $this->price_per_minute,
            'is_online' => $this->is_online,
            'rating' => $this->rating,
            'total_reviews' => $this->total_reviews,
            'expertises' => ExpertiseResource::collection($this->whenLoaded('expertises')),
            'languages' => LanguageResource::collection($this->whenLoaded('languages')),
        ];
    }
}
