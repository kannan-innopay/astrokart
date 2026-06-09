<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HoroscopeService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.horoscope.url');
    }

    /**
     * @return array<string, mixed>|null
     */
    public function generateChart(User $user): ?array
    {
        if (! $user->date_of_birth || ! $user->birth_latitude || ! $user->birth_longitude) {
            return null;
        }

        try {
            $response = Http::timeout(60)
                ->connectTimeout(10)
                ->post("{$this->baseUrl}/api/chart/generate", [
                    'date_of_birth' => $user->date_of_birth->format('Y-m-d'),
                    'time_of_birth' => $user->time_of_birth ?? '12:00',
                    'latitude' => (float) $user->birth_latitude,
                    'longitude' => (float) $user->birth_longitude,
                    'timezone_offset' => 5.5,
                    'name' => $user->name,
                    'place_of_birth' => $user->place_of_birth ?? '',
                ]);

            if ($response->successful()) {
                $chartData = $response->json();
                $user->update(['birth_chart' => $chartData]);

                return $chartData;
            }

            Log::error('Horoscope service error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('Horoscope service exception', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return null;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getChart(User $user): ?array
    {
        return $user->birth_chart;
    }

    public function isServiceAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/api/chart/health");

            return $response->successful();
        } catch (\Throwable) {
            return false;
        }
    }
}
