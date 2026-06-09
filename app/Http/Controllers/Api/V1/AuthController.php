<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailLoginRequest;
use App\Http\Requests\Auth\RequestOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\V1\AuthTokenResource;
use App\Http\Resources\V1\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {}

    public function requestOtp(RequestOtpRequest $request): JsonResponse
    {
        $this->authService->requestOtp($request->validated('mobile'));

        return response()->json(['message' => 'OTP sent successfully.']);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $result = $this->authService->verifyOtpAndLogin(
            $request->validated('mobile'),
            $request->validated('otp'),
        );

        return (new AuthTokenResource($result))
            ->response()
            ->setStatusCode(200);
    }

    public function loginWithEmail(EmailLoginRequest $request): JsonResponse
    {
        $result = $this->authService->loginWithEmail(
            $request->validated('email'),
            $request->validated('password'),
        );

        return (new AuthTokenResource($result))
            ->response()
            ->setStatusCode(200);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user()->load('wallet'));
    }
}
