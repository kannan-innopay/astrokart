<?php

namespace App\Services;

use App\Services\Contracts\OtpServiceInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Msg91OtpService implements OtpServiceInterface
{
    private string $baseUrl = 'https://control.msg91.com/api/v5/otp';

    private string $authKey;

    private string $countryCode;

    private int $otpLength;

    private int $otpExpiry;

    private ?string $testPhone;

    private ?string $testOtp;

    /** @var array<string, string> */
    private array $templates;

    public function __construct()
    {
        $this->authKey = config('services.msg91.auth_key');
        $this->countryCode = config('services.msg91.country_code', '91');
        $this->otpLength = (int) config('services.msg91.otp_length', 6);
        $this->otpExpiry = (int) config('services.msg91.otp_expiry', 10);
        $this->testPhone = config('services.msg91.test_phone');
        $this->testOtp = config('services.msg91.test_otp');
        $this->templates = config('services.msg91.templates', []);
    }

    public function sendOtp(string $mobile, string $purpose = 'login'): bool
    {
        if ($this->isTestPhone($mobile)) {
            Log::info("MSG91 test phone detected, skipping API call for {$mobile}");

            return true;
        }

        $internationalMobile = $this->formatMobile($mobile);
        $templateId = $this->resolveTemplate($purpose);

        try {
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->retry(2, 500, fn ($exception) => $exception instanceof RequestException && $exception->response?->serverError())
                ->withHeaders([
                    'authkey' => $this->authKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl, [
                    'template_id' => $templateId,
                    'mobile' => $internationalMobile,
                    'otp_length' => $this->otpLength,
                    'otp_expiry' => $this->otpExpiry,
                ]);

            $body = $response->json();

            if (($body['type'] ?? '') === 'success') {
                return true;
            }

            Log::error('MSG91 send OTP failed', [
                'mobile' => $mobile,
                'response' => $body,
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('MSG91 send OTP exception', [
                'mobile' => $mobile,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function verifyOtp(string $mobile, string $otp, string $purpose = 'login'): bool
    {
        if ($this->isTestPhone($mobile)) {
            return $otp === $this->testOtp;
        }

        $internationalMobile = $this->formatMobile($mobile);

        try {
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->retry(2, 500, fn ($exception) => $exception instanceof RequestException && $exception->response?->serverError())
                ->withHeaders([
                    'authkey' => $this->authKey,
                ])
                ->get($this->baseUrl.'/verify', [
                    'otp' => $otp,
                    'mobile' => $internationalMobile,
                ]);

            $body = $response->json();

            if (($body['type'] ?? '') === 'success') {
                return true;
            }

            Log::warning('MSG91 verify OTP failed', [
                'mobile' => $mobile,
                'response' => $body,
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('MSG91 verify OTP exception', [
                'mobile' => $mobile,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function formatMobile(string $mobile): string
    {
        $mobile = preg_replace('/\D/', '', $mobile);

        if (! str_starts_with($mobile, $this->countryCode)) {
            $mobile = $this->countryCode.$mobile;
        }

        return $mobile;
    }

    private function isTestPhone(string $mobile): bool
    {
        if (! $this->testPhone) {
            return false;
        }

        $cleaned = preg_replace('/\D/', '', $mobile);
        $testPhones = array_map('trim', explode(',', $this->testPhone));

        return in_array($cleaned, $testPhones);
    }

    private function resolveTemplate(string $purpose): string
    {
        return $this->templates[$purpose] ?? $this->templates['login'] ?? '';
    }
}
