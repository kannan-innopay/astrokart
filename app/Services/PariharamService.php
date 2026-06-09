<?php

namespace App\Services;

use App\Models\User;

class PariharamService
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getRemediesForUser(User $user, array $forecastData): array
    {
        $remedies = [];
        $allRemedyData = config('navagraha.remedies');
        $allTempleData = config('navagraha.temples');
        $maleficHouses = config('navagraha.effect_levels.malefic_houses');

        // Check Sade Sati
        if ($forecastData['sade_sati']['active'] ?? false) {
            $remedies[] = $this->buildRemedy('Saturn', $allRemedyData, $allTempleData, 'sade_sati', $forecastData['sade_sati']['phase']);
        }

        // Check Ashtama Shani
        if ($forecastData['ashtama_shani'] ?? false) {
            if (! isset($remedies[0]) || $remedies[0]['trigger'] !== 'sade_sati') {
                $remedies[] = $this->buildRemedy('Saturn', $allRemedyData, $allTempleData, 'ashtama_shani');
            }
        }

        // Check each transit for challenging houses (6, 8, 12)
        foreach ($forecastData['transits'] ?? [] as $transit) {
            $planet = $transit['planet'];
            $house = $transit['house_from_moon'];

            if (in_array($house, $maleficHouses)) {
                // Skip Saturn if already added via Sade Sati/Ashtama Shani
                if ($planet === 'Saturn' && ! empty($remedies) && $remedies[0]['planet'] === 'Saturn') {
                    continue;
                }

                $remedies[] = $this->buildRemedy($planet, $allRemedyData, $allTempleData, 'malefic_transit', null, $house);
            }
        }

        // Check Rahu in 1st house from Moon (Rahu over Moon sign)
        foreach ($forecastData['transits'] ?? [] as $transit) {
            if ($transit['planet'] === 'Rahu' && $transit['house_from_moon'] === 1) {
                $alreadyAdded = collect($remedies)->contains(fn ($r) => $r['planet'] === 'Rahu');
                if (! $alreadyAdded) {
                    $remedies[] = $this->buildRemedy('Rahu', $allRemedyData, $allTempleData, 'rahu_on_moon');
                }
            }
        }

        return $remedies;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildRemedy(
        string $planet,
        array $allRemedyData,
        array $allTempleData,
        string $trigger,
        ?string $phase = null,
        ?int $house = null,
    ): array {
        $remedy = $allRemedyData[$planet] ?? [];
        $temple = $allTempleData[$planet] ?? [];

        return [
            'planet' => $planet,
            'trigger' => $trigger,
            'phase' => $phase,
            'house_from_moon' => $house,
            'temple' => $temple['primary'] ?? null,
            'other_temples' => $temple['others'] ?? [],
            'day' => $remedy['day'] ?? '',
            'gemstone' => $remedy['gemstone'] ?? '',
            'color' => $remedy['color'] ?? '',
            'metal' => $remedy['metal'] ?? '',
            'mantra' => $remedy['mantra'] ?? '',
            'mantra_count' => $remedy['mantra_count'] ?? 108,
            'offerings' => $remedy['offerings'] ?? [],
            'fasting' => $remedy['fasting'] ?? '',
            'donations' => $remedy['donations'] ?? [],
            'deity' => $remedy['deity'] ?? '',
        ];
    }
}
