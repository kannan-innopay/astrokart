<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('subscription:expire')]
#[Description('Mark expired subscriptions')]
class ExpireSubscriptions extends Command
{
    public function handle(): void
    {
        $count = Subscription::whereIn('status', [
            SubscriptionStatus::Active,
            SubscriptionStatus::PastDue,
        ])
            ->where('expires_at', '<=', now())
            ->update(['status' => SubscriptionStatus::Expired]);

        $this->info("Expired {$count} subscription(s).");
    }
}
