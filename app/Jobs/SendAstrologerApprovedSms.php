<?php

namespace App\Jobs;

use App\Models\Astrologer;
use App\Services\Contracts\SmsSenderInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class SendAstrologerApprovedSms implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Astrologer $astrologer,
    ) {}

    public function handle(SmsSenderInterface $sms): void
    {
        $user = $this->astrologer->user;

        if (! $user?->mobile) {
            return;
        }

        $sms->send(
            $user->mobile,
            (string) config('services.msg91.templates.astrologer_approved'),
            ['var1' => Str::limit(Str::before($user->name, ' '), 30, '')],
        );
    }
}
