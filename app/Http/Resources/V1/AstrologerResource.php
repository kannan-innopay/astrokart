<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AstrologerResource extends JsonResource
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
            'bio' => $this->bio,
            'years_of_experience' => $this->years_of_experience,
            'price_per_minute' => $this->price_per_minute,
            'consultation_modes' => $this->consultation_modes,
            'is_online' => $this->is_online,
            'rating' => $this->rating,
            'total_reviews' => $this->total_reviews,
            'status' => $this->status,
            'verified_at' => $this->verified_at?->toIso8601String(),
            'expertises' => ExpertiseResource::collection($this->whenLoaded('expertises')),
            'languages' => LanguageResource::collection($this->whenLoaded('languages')),
            'user' => new UserResource($this->whenLoaded('user')),
            'bank_account_name' => $this->when($this->shouldShowPayoutDetails($request), $this->bank_account_name),
            'bank_account_number' => $this->when($this->shouldShowPayoutDetails($request), $this->bank_account_number),
            'bank_ifsc_code' => $this->when($this->shouldShowPayoutDetails($request), $this->bank_ifsc_code),
            'upi_id' => $this->when($this->shouldShowPayoutDetails($request), $this->upi_id),
            'verification_notes' => $this->when($this->shouldShowAdminDetails($request), $this->verification_notes),
        ];
    }

    private function shouldShowPayoutDetails(Request $request): bool
    {
        $user = $request->user();

        return $user && ($user->isAdmin() || $user->id === $this->user_id);
    }

    private function shouldShowAdminDetails(Request $request): bool
    {
        return $request->user()?->isAdmin() ?? false;
    }
}
