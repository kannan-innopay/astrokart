<?php

namespace App\Services;

use App\Services\Contracts\SmsSenderInterface;
use Illuminate\Support\Facades\Log;

class LogSmsSender implements SmsSenderInterface
{
    public function send(string $mobile, string $templateId, array $variables): bool
    {
        Log::info("SMS to {$mobile} via template {$templateId}", $variables);

        return true;
    }
}
