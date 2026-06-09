<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Services\PariharamService;
use App\Services\TransitService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransitController extends Controller
{
    public function __construct(
        private TransitService $transitService,
        private PariharamService $pariharamService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $validLocales = ['en', 'hi', 'ta', 'te', 'ml', 'mr'];
        $locale = in_array($request->query('lang'), $validLocales)
            ? $request->query('lang')
            : ($user?->preferred_language ?? 'en');

        $currentTransits = $this->transitService->getCurrentTransits();
        $upcomingEvents = $this->transitService->getUpcomingEvents(12);

        $forecast = null;
        $remedies = [];

        if ($user?->birth_chart) {
            $forecast = $this->transitService->getPersonalForecast($user);

            if ($forecast) {
                // Add effect level and forecast key to each transit
                foreach ($forecast['transits'] as &$transit) {
                    $transit['effect_level'] = $this->transitService->getEffectLevel($transit['house_from_moon']);
                    $transit['forecast_key'] = strtolower($transit['planet']) . '_in_' . $transit['house_from_moon'];
                }

                $remedies = $this->pariharamService->getRemediesForUser($user, $forecast);
            }
        }

        return view('customer.horoscope.transits', [
            'currentTransits' => $currentTransits,
            'upcomingEvents' => $upcomingEvents,
            'forecast' => $forecast,
            'remedies' => $remedies,
            'locale' => $locale,
            'user' => $user,
        ]);
    }
}
