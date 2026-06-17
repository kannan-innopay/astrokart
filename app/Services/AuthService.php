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

    public function verifyOtpAndGetUser(string $mobile, string $otp, UserRole $intendedRole = UserRole::Customer): User
    {
        if (! $this->otpService->verifyOtp($mobile, $otp)) {
            throw ValidationException::withMessages([
                'otp' => ['The provided OTP is invalid or has expired.'],
            ]);
        }

        $user = User::where('mobile', $mobile)->first();

        if ($user) {
            $this->guardRoleMatches($user, $intendedRole);

            if (! $user->mobile_verified_at) {
                $user->update(['mobile_verified_at' => now()]);
            }
        } else {
            $user = User::create([
                'name' => 'User',
                'mobile' => $mobile,
                'role' => $intendedRole,
                'mobile_verified_at' => now(),
            ]);
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
     * A mobile number is locked to a single role at first signup: it cannot be
     * reused to sign up as the other role (customer vs astrologer). Admins are
     * exempt as they authenticate via email.
     */
    private function guardRoleMatches(User $user, UserRole $intendedRole): void
    {
        if ($user->isAdmin()) {
            return;
        }

        $existingIsAstrologer = $user->role === UserRole::Astrologer;
        $intendedIsAstrologer = $intendedRole === UserRole::Astrologer;

        if ($existingIsAstrologer !== $intendedIsAstrologer) {
            throw ValidationException::withMessages([
                'mobile' => [$intendedIsAstrologer
                    ? __('astrologer.number_is_customer')
                    : __('astrologer.number_is_astrologer')],
            ]);
        }
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
