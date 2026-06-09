<?php

namespace Database\Seeders;

use App\Models\PlanEntitlement;
use Illuminate\Database\Seeder;

class PlanEntitlementSeeder extends Seeder
{
    public function run(): void
    {
        $plans = ['daily', 'monthly', 'yearly'];

        $entitlements = [
            'full_chart_analysis',
            'daily_predictions',
            'monthly_forecast',
            'compatibility_matching',
            'muhurtham_finder',
            'pdf_report_download',
            'dasha_alerts',
            'remedies',
        ];

        foreach ($plans as $plan) {
            foreach ($entitlements as $entitlement) {
                PlanEntitlement::firstOrCreate([
                    'plan' => $plan,
                    'entitlement' => $entitlement,
                ]);
            }
        }

        $this->command->info('Seeded ' . count($plans) * count($entitlements) . ' plan entitlements.');
    }
}
