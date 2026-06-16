<?php

namespace App\Services\Contracts;

interface SmsSenderInterface
{
    /**
     * Send a transactional SMS using a DLT-approved template.
     *
     * @param  array<string, string>  $variables  Template variables (e.g. ['var1' => 'Ravi']).
     */
    public function send(string $mobile, string $templateId, array $variables): bool;
}
