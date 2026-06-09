<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Resources\V1\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function __construct(
        private UserService $userService,
    ) {}

    public function show(Request $request): UserResource
    {
        return new UserResource($request->user()->load('wallet'));
    }

    public function update(UpdateProfileRequest $request): UserResource
    {
        $user = $this->userService->updateProfile(
            $request->user(),
            $request->validated(),
        );

        return new UserResource($user->load('wallet'));
    }
}
