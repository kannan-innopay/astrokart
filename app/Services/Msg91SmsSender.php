<?php

namespace App\Services;

use App\Services\Contracts\SmsSenderInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Msg91SmsSender implements SmsSenderInterface
{
    private string $flowUrl = 'https://control.msg91.com/api/v5/flow/';

    private string $authKey;

    private string $countryCode;

    public function __construct()
    {
        $this->authKey = config('services.msg91.auth_key');
        $this->countryCode = config('services.msg91.country_code', '91');
    }

    public function send(string $mobile, string $templateId, array $variables): bool
    {
        if (! $templateId) {
            Log::warning('MSG91 transactional SMS skipped: missing template id', ['mobile' => $mobile]);

            return false;
        }

        try {
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->retry(2, 500, fn ($exception) => $exception instanceof RequestException && $exception->response?->serverError())
                ->withHeaders([
                    'authkey' => $this->authKey,
                    'Content-Type' => 'application/json',
                    'accept' => 'application/json',
                ])
                ->post($this->flowUrl, [
                    'template_id' => $templateId,
                    'recipients' => [
                        array_merge(['mobiles' => $this->formatMobile($mobile)], $variables),
                    ],
                ]);

            $body = $response->json();

            if (($body['type'] ?? '') === 'success') {
                return true;
            }

            Log::error('MSG91 transactional SMS failed', [
                'mobile' => $mobile,
                'response' => $body,
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('MSG91 transactional SMS exception', [
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
}
