<?php

namespace App\Services\Contracts;

interface OtpServiceInterface
{
    public function sendOtp(string $mobile, string $purpose = 'login'): bool;

    public function verifyOtp(string $mobile, string $otp, string $purpose = 'login'): bool;
}
