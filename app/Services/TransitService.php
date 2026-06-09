<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransitService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.horoscope.url');
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getCurrentTransits(): ?array
    {
        return Cache::remember('transits:current', 86400, function () {
            try {
                $response = Http::timeout(30)->get("{$this->baseUrl}/api/transit/current");

                return $response->successful() ? $response->json() : null;
            } catch (\Throwable $e) {
                Log::error('Transit service error', ['error' => $e->getMessage()]);

                return null;
            }
        });
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getUpcomingEvents(int $months = 12): ?array
    {
        return Cache::remember("transits:upcoming:{$months}", 86400, function () use ($months) {
            try {
                $response = Http::timeout(120)->get("{$this->baseUrl}/api/transit/upcoming", [
                    'months' => $months,
                ]);

                return $response->successful() ? $response->json() : null;
            } catch (\Throwable $e) {
                Log::error('Transit upcoming error', ['error' => $e->getMessage()]);

                return null;
            }
        });
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getPersonalForecast(User $user): ?array
    {
        $chart = $user->birth_chart;
        if (! $chart) {
            return null;
        }

        $moonRashiIndex = $chart['grahas'][1]['rashi']['index'] ?? null;
        $lagnaRashiIndex = $chart['lagna']['rashi']['index'] ?? null;

        if ($moonRashiIndex === null || $lagnaRashiIndex === null) {
            return null;
        }

        try {
            $response = Http::timeout(30)->post("{$this->baseUrl}/api/transit/forecast", [
                'moon_rashi_index' => $moonRashiIndex,
                'lagna_rashi_index' => $lagnaRashiIndex,
            ]);

            return $response->successful() ? $response->json() : null;
        } catch (\Throwable $e) {
            Log::error('Transit forecast error', ['error' => $e->getMessage()]);

            return null;
        }
    }

    public function getEffectLevel(int $houseFromMoon): string
    {
        $levels = config('navagraha.effect_levels');

        if (in_array($houseFromMoon, $levels['benefic_houses'])) {
            return 'benefic';
        }
        if (in_array($houseFromMoon, $levels['malefic_houses'])) {
            return 'malefic';
        }

        return 'mixed';
    }
}
