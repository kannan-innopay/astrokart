<?php

namespace App\Services;

use App\Models\ChartAnalysis;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnalysisService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.horoscope.url');
    }

    /**
     * Get or generate analysis for a user's birth chart.
     *
     * @return array{analysis: array<string, mixed>, free_summary: array<string, mixed>}|null
     */
    public function getAnalysis(User $user): ?array
    {
        if (! $user->birth_chart) {
            return null;
        }

        $chartHash = hash('sha256', json_encode($user->birth_chart));

        // Check for existing analysis with same chart hash
        $existing = ChartAnalysis::where('user_id', $user->id)
            ->forChart($chartHash)
            ->latest('generated_at')
            ->first();

        if ($existing) {
            return [
                'analysis' => $existing->analysis_json,
                'free_summary' => $existing->free_summary_json,
            ];
        }

        // Generate new analysis
        return $this->generateAnalysis($user, $chartHash);
    }

    /**
     * Extract only the free-tier summary from analysis data.
     *
     * @param  array<string, mixed>  $analysisData
     * @return array<string, mixed>
     */
    public function getFreeSummary(array $analysisData): array
    {
        return $analysisData['free_summary'] ?? [];
    }

    /**
     * Return the full analysis (for premium users).
     *
     * @param  array<string, mixed>  $analysisData
     * @return array<string, mixed>
     */
    public function getFullAnalysis(array $analysisData): array
    {
        return $analysisData['analysis'] ?? [];
    }

    /**
     * @return array{analysis: array<string, mixed>, free_summary: array<string, mixed>}|null
     */
    private function generateAnalysis(User $user, string $chartHash): ?array
    {
        try {
            $response = Http::timeout(60)
                ->connectTimeout(5)
                ->retry(2, 1000)
                ->post("{$this->baseUrl}/api/chart/analyze", [
                    'chart_data' => $user->birth_chart,
                    'date_of_birth' => $user->date_of_birth->format('Y-m-d'),
                ]);

            if (! $response->successful()) {
                Log::error('Analysis service error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $data = $response->json();

            // Store in database
            $fullAnalysis = [
                'personality' => $data['personality'] ?? [],
                'career' => $data['career'] ?? [],
                'marriage' => $data['marriage'] ?? [],
                'finance' => $data['finance'] ?? [],
                'wellness' => $data['wellness'] ?? [],
                'dasha' => $data['dasha'] ?? [],
                'decades' => $data['decades'] ?? [],
            ];

            $freeSummary = $data['free_summary'] ?? [];

            ChartAnalysis::create([
                'user_id' => $user->id,
                'birth_chart_hash' => $chartHash,
                'engine_version' => $data['engine_version'] ?? 'unknown',
                'analysis_json' => $fullAnalysis,
                'free_summary_json' => $freeSummary,
                'generated_at' => now(),
            ]);

            return [
                'analysis' => $fullAnalysis,
                'free_summary' => $freeSummary,
            ];
        } catch (\Throwable $e) {
            Log::error('Analysis service exception', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return null;
        }
    }
}
