<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class HoraService
{
    /**
     * Chaldean order of planets for hora sequence.
     */
    private const CHALDEAN_ORDER = ['Sun', 'Venus', 'Mercury', 'Moon', 'Saturn', 'Jupiter', 'Mars'];

    /**
     * Day lord for each weekday (0=Sunday through 6=Saturday).
     */
    private const DAY_LORDS = ['Sun', 'Moon', 'Mars', 'Mercury', 'Jupiter', 'Venus', 'Saturn'];

    /**
     * Hora qualities per planet.
     */
    private const HORA_DATA = [
        'Sun' => ['quality' => 'authority', 'mood' => 'Confident', 'color' => 'Red', 'element' => 'Fire', 'do' => 'Meet important people, seek authority, government work', 'avoid' => 'Starting journeys, risky ventures'],
        'Moon' => ['quality' => 'nurturing', 'mood' => 'Calm', 'color' => 'White', 'element' => 'Water', 'do' => 'Short travel, creative work, water-related tasks', 'avoid' => 'Legal matters, confrontations'],
        'Mars' => ['quality' => 'energy', 'mood' => 'Driven', 'color' => 'Red', 'element' => 'Fire', 'do' => 'Physical activity, property deals, courage-demanding tasks', 'avoid' => 'Signing contracts, starting new relationships'],
        'Mercury' => ['quality' => 'intellect', 'mood' => 'Sharp', 'color' => 'Green', 'element' => 'Earth', 'do' => 'Communication, writing, learning, business deals', 'avoid' => 'Emotional decisions, long-term commitments'],
        'Jupiter' => ['quality' => 'wisdom', 'mood' => 'Auspicious', 'color' => 'Yellow', 'element' => 'Ether', 'do' => 'Spiritual activities, teaching, charity, starting ventures', 'avoid' => 'Lending money, speculative trades'],
        'Venus' => ['quality' => 'harmony', 'mood' => 'Joyful', 'color' => 'White', 'element' => 'Water', 'do' => 'Romance, buying luxuries, artistic pursuits, entertainment', 'avoid' => 'Austerity, heavy labor'],
        'Saturn' => ['quality' => 'discipline', 'mood' => 'Cautious', 'color' => 'Blue', 'element' => 'Air', 'do' => 'Meditation, service, agriculture, dealing with elders', 'avoid' => 'Starting new projects, travel, important meetings'],
    ];

    /**
     * Sign lords: which planet rules each Rashi (0=Aries through 11=Pisces).
     */
    private const SIGN_LORDS = [
        0 => 'Mars', 1 => 'Venus', 2 => 'Mercury', 3 => 'Moon',
        4 => 'Sun', 5 => 'Mercury', 6 => 'Venus', 7 => 'Mars',
        8 => 'Jupiter', 9 => 'Saturn', 10 => 'Saturn', 11 => 'Jupiter',
    ];

    /**
     * Exaltation signs for planets (sign index where planet is exalted).
     */
    private const EXALTATION = [
        'Sun' => 0, 'Moon' => 1, 'Mars' => 9, 'Mercury' => 5,
        'Jupiter' => 3, 'Venus' => 11, 'Saturn' => 6,
    ];

    /**
     * Debilitation signs (sign index where planet is debilitated).
     */
    private const DEBILITATION = [
        'Sun' => 6, 'Moon' => 7, 'Mars' => 3, 'Mercury' => 11,
        'Jupiter' => 9, 'Venus' => 5, 'Saturn' => 0,
    ];

    /**
     * Houses considered benefic for their lords (Kendra = 1,4,7,10 + Trikona = 1,5,9).
     */
    private const BENEFIC_HOUSES = [1, 4, 5, 7, 9, 10];

    /**
     * Trik houses (challenging).
     */
    private const TRIK_HOUSES = [6, 8, 12];

    /**
     * Get today's hora table with optional personalization.
     *
     * @return array{day_lord: string, current_hora: array, horas: array, date: string}
     */
    public function getDailyHoras(?Carbon $date = null, float $sunriseHour = 6.0, ?User $user = null): array
    {
        $date = $date ?? Carbon::now('Asia/Kolkata');
        $dayOfWeek = (int) $date->dayOfWeek;
        $dayLord = self::DAY_LORDS[$dayOfWeek];
        $startIndex = array_search($dayLord, self::CHALDEAN_ORDER);

        $planetRelations = $user ? $this->analyzeBirthChart($user) : null;

        $horas = [];
        $currentHora = null;
        $currentHour = (int) $date->format('G');

        for ($i = 0; $i < 24; $i++) {
            $planetIndex = ($startIndex + $i) % 7;
            $planet = self::CHALDEAN_ORDER[$planetIndex];
            $horaStart = ((int) $sunriseHour + $i) % 24;
            $horaEnd = ((int) $sunriseHour + $i + 1) % 24;

            $hora = [
                'number' => $i + 1,
                'planet' => $planet,
                'start_hour' => $horaStart,
                'end_hour' => $horaEnd,
                'start_label' => sprintf('%02d:00', $horaStart),
                'end_label' => sprintf('%02d:00', $horaEnd),
                'is_day' => $i < 12,
                'data' => self::HORA_DATA[$planet],
                'personal' => $planetRelations[$planet] ?? null,
            ];

            if ($currentHour >= $horaStart && $currentHour < $horaEnd) {
                $hora['is_current'] = true;
                $currentHora = $hora;
            } else {
                $hora['is_current'] = false;
            }

            $horas[] = $hora;
        }

        // Fallback for edge cases
        if (! $currentHora && ! empty($horas)) {
            foreach ($horas as &$h) {
                if ($h['start_hour'] > $h['end_hour']) {
                    if ($currentHour >= $h['start_hour'] || $currentHour < $h['end_hour']) {
                        $h['is_current'] = true;
                        $currentHora = $h;
                        break;
                    }
                }
            }
        }

        if (! $currentHora) {
            $currentHora = $horas[0];
            $horas[0]['is_current'] = true;
        }

        return [
            'date' => $date->format('Y-m-d'),
            'day_lord' => $dayLord,
            'current_hora' => $currentHora,
            'horas' => $horas,
            'personalized' => $planetRelations !== null,
        ];
    }

    /**
     * Analyze a user's birth chart to determine each planet's relationship.
     *
     * @return array<string, array{favorability: string, reason: string, score: int}>
     */
    public function analyzeBirthChart(User $user): array
    {
        $chart = $user->birth_chart;
        if (! $chart) {
            return [];
        }

        $lagnaIndex = $chart['lagna']['rashi']['index'] ?? null;
        if ($lagnaIndex === null) {
            return [];
        }

        $lagnaLord = self::SIGN_LORDS[$lagnaIndex];

        // Map each graha to its natal rashi
        $natalPositions = [];
        foreach ($chart['grahas'] ?? [] as $graha) {
            $natalPositions[$graha['name']] = $graha['rashi']['index'];
        }

        $relations = [];

        foreach (array_keys(self::HORA_DATA) as $planet) {
            $score = 0;
            $reasons = [];

            // 1. Is this planet the Lagna lord? (+3, highly benefic)
            if ($planet === $lagnaLord) {
                $score += 3;
                $reasons[] = 'lagna_lord';
            }

            // 2. Which houses does this planet rule from Lagna?
            $ruledHouses = $this->getHousesRuledBy($planet, $lagnaIndex);
            foreach ($ruledHouses as $house) {
                if (in_array($house, self::BENEFIC_HOUSES)) {
                    $score += 2;
                    $reasons[] = 'rules_benefic_house';
                }
                if (in_array($house, self::TRIK_HOUSES)) {
                    $score -= 2;
                    $reasons[] = 'rules_trik_house';
                }
            }

            // 3. Is the planet exalted or debilitated in the birth chart?
            $natalSign = $natalPositions[$planet] ?? null;
            if ($natalSign !== null) {
                if (isset(self::EXALTATION[$planet]) && self::EXALTATION[$planet] === $natalSign) {
                    $score += 2;
                    $reasons[] = 'exalted';
                }
                if (isset(self::DEBILITATION[$planet]) && self::DEBILITATION[$planet] === $natalSign) {
                    $score -= 2;
                    $reasons[] = 'debilitated';
                }

                // 4. Is the planet in own sign?
                if (self::SIGN_LORDS[$natalSign] === $planet) {
                    $score += 1;
                    $reasons[] = 'own_sign';
                }
            }

            // 5. Natural benefics/malefics adjustment
            if (in_array($planet, ['Jupiter', 'Venus'])) {
                $score += 1; // natural benefics
            }
            if (in_array($planet, ['Saturn', 'Mars'])) {
                $score -= 1; // natural malefics (softened by functional role)
            }

            // Determine favorability
            if ($score >= 3) {
                $favorability = 'excellent';
            } elseif ($score >= 1) {
                $favorability = 'favorable';
            } elseif ($score >= -1) {
                $favorability = 'neutral';
            } else {
                $favorability = 'caution';
            }

            $relations[$planet] = [
                'favorability' => $favorability,
                'reasons' => array_values(array_unique($reasons)),
                'score' => $score,
            ];
        }

        return $relations;
    }

    /**
     * Get which houses (1-12) a planet rules from a given Lagna.
     *
     * @return array<int>
     */
    private function getHousesRuledBy(string $planet, int $lagnaIndex): array
    {
        $houses = [];
        for ($sign = 0; $sign < 12; $sign++) {
            if (self::SIGN_LORDS[$sign] === $planet) {
                // House number = (sign - lagnaIndex) % 12 + 1
                $house = ($sign - $lagnaIndex + 12) % 12 + 1;
                $houses[] = $house;
            }
        }

        return $houses;
    }
}
