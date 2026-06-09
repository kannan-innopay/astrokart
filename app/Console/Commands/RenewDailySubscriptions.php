<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('subscription:renew-daily')]
#[Description('Renew daily premium passes by debiting wallet')]
class RenewDailySubscriptions extends Command
{
    public function handle(SubscriptionService $subscriptionService): void
    {
        $subscriptions = Subscription::with('user.wallet')
            ->where('plan', SubscriptionPlan::Daily)
            ->where('auto_renew', true)
            ->whereIn('status', [SubscriptionStatus::Active, SubscriptionStatus::PastDue])
            ->where('next_billing_at', '<=', now())
            ->get();

        $renewed = 0;
        $failed = 0;

        foreach ($subscriptions as $subscription) {
            if ($subscriptionService->renewDaily($subscription)) {
                $renewed++;
            } else {
                $failed++;
            }
        }

        $this->info("Renewed: {$renewed}, Failed: {$failed}");
    }
}
