<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\User;
use App\Services\DailyPredictionService;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('predictions:generate-daily')]
#[Description('Generate daily predictions for all premium users with birth charts')]
class GenerateDailyPredictions extends Command
{
    public function handle(DailyPredictionService $predictionService): void
    {
        $today = Carbon::today();

        $users = User::whereNotNull('birth_chart')
            ->whereHas('subscriptions', function ($q) {
                $q->where('status', SubscriptionStatus::Active)
                    ->where('expires_at', '>', now());
            })
            ->cursor();

        $generated = 0;
        $failed = 0;

        foreach ($users as $user) {
            $result = $predictionService->generate($user, $today);
            if ($result) {
                $generated++;
            } else {
                $failed++;
            }
        }

        $this->info("Generated: {$generated}, Failed: {$failed}");
    }
}
