<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\ChartAnalysis;
use App\Models\User;
use App\Notifications\DashaChangeNotification;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('dasha:send-alerts')]
#[Description('Send notifications for upcoming Dasha period changes')]
class SendDashaAlerts extends Command
{
    public function handle(): void
    {
        $today = Carbon::today();
        $alertWindow = $today->copy()->addDays(30);
        $sent = 0;

        $users = User::whereNotNull('birth_chart')
            ->whereHas('subscriptions', function ($q) {
                $q->where('status', SubscriptionStatus::Active)
                    ->where('expires_at', '>', now());
            })
            ->cursor();

        foreach ($users as $user) {
            $analysis = ChartAnalysis::where('user_id', $user->id)
                ->latest('generated_at')
                ->first();

            if (! $analysis || ! isset($analysis->analysis_json['dasha']['mahadashas'])) {
                continue;
            }

            $dasha = $analysis->analysis_json['dasha'];

            // Check Mahadasha changes
            foreach ($dasha['mahadashas'] ?? [] as $md) {
                $startDate = Carbon::parse($md['start_date']);

                if ($startDate->between($today, $alertWindow)) {
                    // Check if already notified
                    $alreadyNotified = $user->notifications()
                        ->where('type', DashaChangeNotification::class)
                        ->whereJsonContains('data->lord', $md['lord'])
                        ->whereJsonContains('data->start_date', $md['start_date'])
                        ->exists();

                    if (! $alreadyNotified) {
                        $user->notify(new DashaChangeNotification(
                            type: 'mahadasha',
                            lord: $md['lord'],
                            startDate: $md['start_date'],
                            interpretation: $md['interpretation'] ?? '',
                        ));
                        $sent++;
                    }
                }

                // Check Antardasha changes within current Mahadasha
                foreach ($md['sub_periods'] ?? [] as $ad) {
                    $adStart = Carbon::parse($ad['start_date']);
                    if ($adStart->between($today, $alertWindow)) {
                        $alreadyNotified = $user->notifications()
                            ->where('type', DashaChangeNotification::class)
                            ->whereJsonContains('data->lord', $ad['lord'])
                            ->whereJsonContains('data->start_date', $ad['start_date'])
                            ->whereJsonContains('data->type', 'antardasha')
                            ->exists();

                        if (! $alreadyNotified) {
                            $user->notify(new DashaChangeNotification(
                                type: 'antardasha',
                                lord: $ad['lord'],
                                startDate: $ad['start_date'],
                            ));
                            $sent++;
                        }
                    }
                }
            }
        }

        $this->info("Sent {$sent} dasha alert(s).");
    }
}
