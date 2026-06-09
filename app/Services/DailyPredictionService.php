<?php

namespace App\Services;

use App\Models\DailyPrediction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DailyPredictionService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.horoscope.url');
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getForDate(User $user, Carbon $date): ?array
    {
        if (! $user->birth_chart) {
            return null;
        }

        $existing = DailyPrediction::where('user_id', $user->id)
            ->whereDate('prediction_date', $date->toDateString())
            ->first();

        if ($existing) {
            return $existing->prediction_json;
        }

        return $this->generate($user, $date)?->prediction_json;
    }

    public function generate(User $user, Carbon $date): ?DailyPrediction
    {
        if (! $user->birth_chart || ! $user->date_of_birth) {
            return null;
        }

        try {
            $response = Http::timeout(30)
                ->connectTimeout(5)
                ->retry(2, 1000)
                ->post("{$this->baseUrl}/api/predictions/daily", [
                    'chart_data' => $user->birth_chart,
                    'date_of_birth' => $user->date_of_birth->format('Y-m-d'),
                    'target_date' => $date->format('Y-m-d'),
                ]);

            if (! $response->successful()) {
                Log::error('Daily prediction service error', ['status' => $response->status()]);

                return null;
            }

            $data = $response->json();

            return DailyPrediction::updateOrCreate(
                ['user_id' => $user->id, 'prediction_date' => $date->toDateString()],
                [
                    'prediction_json' => $data,
                    'engine_version' => $data['engine_version'] ?? 'unknown',
                    'generated_at' => now(),
                ],
            );
        } catch (\Throwable $e) {
            Log::error('Daily prediction exception', ['error' => $e->getMessage(), 'user_id' => $user->id]);

            return null;
        }
    }
}
