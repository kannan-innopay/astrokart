<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'role' => $this->role,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth?->toDateString(),
            'time_of_birth' => $this->time_of_birth,
            'place_of_birth' => $this->place_of_birth,
            'preferred_language' => $this->preferred_language,
            'account_status' => $this->account_status,
            'wallet_balance' => $this->whenLoaded('wallet', fn () => $this->wallet?->balance),
        ];
    }
}
