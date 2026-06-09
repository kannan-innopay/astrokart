<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Contracts\OtpServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private OtpServiceInterface $otpService,
    ) {}

    public function requestOtp(string $mobile): bool
    {
        return $this->otpService->sendOtp($mobile);
    }

    public function verifyOtpAndGetUser(string $mobile, string $otp): User
    {
        if (! $this->otpService->verifyOtp($mobile, $otp)) {
            throw ValidationException::withMessages([
                'otp' => ['The provided OTP is invalid or has expired.'],
            ]);
        }

        $user = User::firstOrCreate(
            ['mobile' => $mobile],
            [
                'name' => 'User',
                'role' => UserRole::Customer,
                'mobile_verified_at' => now(),
            ]
        );

        if (! $user->mobile_verified_at) {
            $user->update(['mobile_verified_at' => now()]);
        }

        if (! $user->wallet) {
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);
        }

        return $user->fresh(['wallet']);
    }

    /**
     * @return array{user: User, token: string}
     */
    public function verifyOtpAndLogin(string $mobile, string $otp): array
    {
        $user = $this->verifyOtpAndGetUser($mobile, $otp);
        $token = $user->createToken('mobile-app')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function authenticateWithEmail(string $email, string $password): User
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user;
    }

    /**
     * @return array{user: User, token: string}
     */
    public function loginWithEmail(string $email, string $password): array
    {
        $user = $this->authenticateWithEmail($email, $password);
        $token = $user->createToken('web-app')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if (method_exists($token, 'delete')) {
            $token->delete();
        }
    }
}
