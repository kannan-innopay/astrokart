<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthTokenResource extends JsonResource
{
    /**
     * @param  array{user: \App\Models\User, token: string}  $resource
     */
    public function __construct(private array $authData)
    {
        parent::__construct($authData['user']);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this->authData['user']),
            'token' => $this->authData['token'],
            'token_type' => 'Bearer',
        ];
    }
}
