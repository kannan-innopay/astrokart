<?php

namespace App\Services;

use App\Models\MonthlyForecast;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonthlyForecastService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.horoscope.url');
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getForMonth(User $user, int $year, int $month): ?array
    {
        if (! $user->birth_chart) {
            return null;
        }

        $monthDate = sprintf('%04d-%02d-01', $year, $month);

        $existing = MonthlyForecast::where('user_id', $user->id)
            ->whereDate('forecast_month', $monthDate)
            ->first();

        if ($existing) {
            return $existing->forecast_json;
        }

        return $this->generate($user, $year, $month)?->forecast_json;
    }

    public function generate(User $user, int $year, int $month): ?MonthlyForecast
    {
        if (! $user->birth_chart || ! $user->date_of_birth) {
            return null;
        }

        try {
            $response = Http::timeout(60)
                ->connectTimeout(5)
                ->retry(2, 1000)
                ->post("{$this->baseUrl}/api/predictions/monthly", [
                    'chart_data' => $user->birth_chart,
                    'date_of_birth' => $user->date_of_birth->format('Y-m-d'),
                    'year' => $year,
                    'month' => $month,
                ]);

            if (! $response->successful()) {
                Log::error('Monthly forecast service error', ['status' => $response->status()]);

                return null;
            }

            $data = $response->json();
            $monthDate = sprintf('%04d-%02d-01', $year, $month);

            return MonthlyForecast::updateOrCreate(
                ['user_id' => $user->id, 'forecast_month' => $monthDate],
                [
                    'forecast_json' => $data,
                    'engine_version' => $data['engine_version'] ?? 'unknown',
                    'generated_at' => now(),
                ],
            );
        } catch (\Throwable $e) {
            Log::error('Monthly forecast exception', ['error' => $e->getMessage(), 'user_id' => $user->id]);

            return null;
        }
    }
}
