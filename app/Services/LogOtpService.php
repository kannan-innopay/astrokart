<?php

namespace App\Services;

use App\Models\OtpVerification;
use App\Services\Contracts\OtpServiceInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LogOtpService implements OtpServiceInterface
{
    public function sendOtp(string $mobile, string $purpose = 'login'): bool
    {
        // Invalidate previous OTPs for this mobile/purpose
        OtpVerification::forMobile($mobile)
            ->forPurpose($purpose)
            ->valid()
            ->update(['verified_at' => now()]);

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpVerification::create([
            'mobile' => $mobile,
            'otp_hash' => Hash::make($otp),
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(10),
        ]);

        Log::info("OTP for {$mobile}: {$otp}");

        return true;
    }

    public function verifyOtp(string $mobile, string $otp, string $purpose = 'login'): bool
    {
        $otpRecord = OtpVerification::forMobile($mobile)
            ->forPurpose($purpose)
            ->valid()
            ->latest()
            ->first();

        if (! $otpRecord) {
            return false;
        }

        $otpRecord->increment('attempts');

        if (! Hash::check($otp, $otpRecord->otp_hash)) {
            return false;
        }

        $otpRecord->update(['verified_at' => now()]);

        return true;
    }
}
