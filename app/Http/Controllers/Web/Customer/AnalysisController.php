<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Services\AnalysisService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnalysisController extends Controller
{
    private const VALID_LOCALES = ['en', 'hi', 'ta', 'te', 'ml', 'mr'];

    public function __construct(
        private AnalysisService $analysisService,
    ) {}

    public function show(Request $request): View
    {
        $user = $request->user();
        $locale = in_array($request->query('lang'), self::VALID_LOCALES)
            ? $request->query('lang')
            : ($user->preferred_language ?? 'en');

        $chart = $user->birth_chart;

        if (! $chart) {
            return view('customer.horoscope.analysis', [
                'hasChart' => false,
                'analysis' => null,
                'isPremium' => false,
                'locale' => $locale,
                'allLabels' => $this->loadAllLabels(),
                'birthProfile' => null,
            ]);
        }

        $analysisData = $this->analysisService->getAnalysis($user);

        if (! $analysisData) {
            return view('customer.horoscope.analysis', [
                'hasChart' => true,
                'analysis' => null,
                'isPremium' => false,
                'locale' => $locale,
                'allLabels' => $this->loadAllLabels(),
                'birthProfile' => $this->extractBirthProfile($chart),
            ]);
        }

        $isPremium = $user->hasEntitlement('full_chart_analysis');

        return view('customer.horoscope.analysis', [
            'hasChart' => true,
            'analysis' => $isPremium
                ? $this->analysisService->getFullAnalysis($analysisData)
                : $this->analysisService->getFreeSummary($analysisData),
            'isPremium' => $isPremium,
            'activeSubscription' => $user->activeSubscription,
            'locale' => $locale,
            'allLabels' => $this->loadAllLabels(),
            'birthProfile' => $this->extractBirthProfile($chart),
        ]);
    }

    /**
     * Extract birth profile indices from chart data for translation lookups.
     *
     * @param  array<string, mixed>  $chart
     * @return array<string, mixed>
     */
    private function extractBirthProfile(array $chart): array
    {
        $lagna = $chart['lagna'] ?? [];
        $grahas = $chart['grahas'] ?? [];
        $panchanga = $chart['panchanga'] ?? [];

        // Find Moon data
        $moonRashiIndex = 0;
        $moonNakIndex = 0;
        $moonNakPada = 1;
        foreach ($grahas as $g) {
            if ($g['name'] === 'Moon') {
                $moonRashiIndex = $g['rashi']['index'] ?? 0;
                $moonNakIndex = $g['nakshatra']['index'] ?? 0;
                $moonNakPada = $g['nakshatra']['pada'] ?? 1;
                break;
            }
        }

        // Weekday index from vaara
        $vaaraMap = ['Sunday' => 0, 'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6];
        $weekdayIndex = $vaaraMap[$panchanga['vaara'] ?? ''] ?? 0;

        // Tithi index (map name to 1-15 cycle)
        $tithiNames = ['Pratipada', 'Dwitiya', 'Tritiya', 'Chaturthi', 'Panchami', 'Shashthi', 'Saptami', 'Ashtami', 'Navami', 'Dashami', 'Ekadashi', 'Dwadashi', 'Trayodashi', 'Chaturdashi', 'Purnima', 'Amavasya'];
        $tithiRaw = $panchanga['tithi'] ?? '';
        $tithiIndex = 0;
        foreach ($tithiNames as $i => $name) {
            if (str_contains($tithiRaw, $name)) {
                $tithiIndex = $i;
                break;
            }
        }

        // Yoga index
        $yogaNames = ['Vishkambha', 'Priti', 'Ayushman', 'Saubhagya', 'Shobhana', 'Atiganda', 'Sukarma', 'Dhriti', 'Shoola', 'Ganda', 'Vriddhi', 'Dhruva', 'Vyaghata', 'Harshana', 'Vajra', 'Siddhi', 'Vyatipata', 'Variyan', 'Parigha', 'Shiva', 'Siddha', 'Sadhya', 'Shubha', 'Shukla', 'Brahma', 'Indra', 'Vaidhriti'];
        $yogaIndex = array_search($panchanga['yoga'] ?? '', $yogaNames);
        if ($yogaIndex === false) {
            $yogaIndex = 0;
        }

        // Karana index
        $karanaNames = ['Bava', 'Balava', 'Kaulava', 'Taitila', 'Garija', 'Vanija', 'Vishti', 'Shakuni', 'Chatushpada', 'Naga', 'Kimstughna'];
        $karanaIndex = array_search($panchanga['karana'] ?? '', $karanaNames);
        if ($karanaIndex === false) {
            $karanaIndex = 0;
        }

        $rasiLords = ['Mars', 'Venus', 'Mercury', 'Moon', 'Sun', 'Mercury', 'Venus', 'Mars', 'Jupiter', 'Saturn', 'Saturn', 'Jupiter'];
        $lagnaLords = $rasiLords;

        return [
            'lagna_rashi_index' => $lagna['rashi']['index'] ?? 0,
            'lagna_nak_index' => $lagna['nakshatra']['index'] ?? 0,
            'moon_rashi_index' => $moonRashiIndex,
            'moon_nak_index' => $moonNakIndex,
            'moon_nak_pada' => $moonNakPada,
            'weekday_index' => $weekdayIndex,
            'tithi_index' => $tithiIndex,
            'yoga_index' => $yogaIndex,
            'karana_index' => $karanaIndex,
            'tithi_raw' => $tithiRaw,
            'yoga_raw' => $panchanga['yoga'] ?? '',
            'karana_raw' => $panchanga['karana'] ?? '',
            'vaara_raw' => $panchanga['vaara'] ?? '',
            'rasi_lord' => $rasiLords[$moonRashiIndex] ?? 'Saturn',
            'lagna_lord' => $lagnaLords[$lagna['rashi']['index'] ?? 0] ?? 'Venus',
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function loadAllLabels(): array
    {
        $labels = [];

        foreach (self::VALID_LOCALES as $loc) {
            $ui = __('analysis.ui', [], $loc);
            $personality = __('analysis.personality', [], $loc);
            $moonNature = __('analysis.moon_nature', [], $loc);
            $elements = __('analysis.elements', [], $loc);
            $dashaInterp = __('analysis.dasha_interpretations', [], $loc);
            $careerFields = __('analysis.career_fields', [], $loc);
            $manglikText = __('analysis.manglik_text', [], $loc);
            $disclaimers = __('analysis.disclaimers', [], $loc);
            $rashis = __('horoscope.rashis', [], $loc);
            $grahas = __('horoscope.grahas', [], $loc);
            $nakshatras = __('horoscope.nakshatras', [], $loc);
            $birthProfile = __('analysis.birth_profile', [], $loc);

            $labels[$loc] = [
                'ui' => is_array($ui) ? $ui : __('analysis.ui', [], 'en'),
                'personality' => is_array($personality) ? $personality : __('analysis.personality', [], 'en'),
                'moon_nature' => is_array($moonNature) ? $moonNature : __('analysis.moon_nature', [], 'en'),
                'elements' => is_array($elements) ? $elements : __('analysis.elements', [], 'en'),
                'dasha_interpretations' => is_array($dashaInterp) ? $dashaInterp : __('analysis.dasha_interpretations', [], 'en'),
                'career_fields' => is_array($careerFields) ? $careerFields : __('analysis.career_fields', [], 'en'),
                'manglik_text' => is_array($manglikText) ? $manglikText : __('analysis.manglik_text', [], 'en'),
                'disclaimers' => is_array($disclaimers) ? $disclaimers : __('analysis.disclaimers', [], 'en'),
                'rashis' => is_array($rashis) ? $rashis : __('horoscope.rashis', [], 'en'),
                'grahas' => is_array($grahas) ? $grahas : __('horoscope.grahas', [], 'en'),
                'nakshatras' => is_array($nakshatras) ? $nakshatras : __('horoscope.nakshatras', [], 'en'),
                'birth_profile' => is_array($birthProfile) ? $birthProfile : __('analysis.birth_profile', [], 'en'),
            ];
        }

        return $labels;
    }
}
