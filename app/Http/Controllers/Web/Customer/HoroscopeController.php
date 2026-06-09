<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Services\HoroscopeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HoroscopeController extends Controller
{
    public function __construct(
        private HoroscopeService $horoscopeService,
    ) {}

    public function show(Request $request): View
    {
        $user = $request->user();
        $chart = $this->horoscopeService->getChart($user);

        // Auto-generate if user has birth details but no chart yet
        if (! $chart && $user->date_of_birth && $user->birth_latitude && $user->birth_longitude) {
            $chart = $this->horoscopeService->generateChart($user);
        }

        $hasBirthCoordinates = $user->birth_latitude && $user->birth_longitude;
        $validLocales = ['en', 'hi', 'ta', 'te', 'ml', 'mr'];
        $locale = in_array($request->query('lang'), $validLocales)
            ? $request->query('lang')
            : ($user->preferred_language ?? 'en');

        return view('customer.horoscope.show', [
            'user' => $user,
            'chart' => $chart,
            'hasBirthCoordinates' => $hasBirthCoordinates,
            'locale' => $locale,
        ]);
    }

    public function regenerate(Request $request): RedirectResponse
    {
        set_time_limit(120);

        $user = $request->user();

        if (! $user->date_of_birth || ! $user->birth_latitude) {
            return back()->with('error', 'Please complete your birth details in your profile first.');
        }

        $chart = $this->horoscopeService->generateChart($user);

        if (! $chart) {
            return back()->with('error', 'Unable to generate birth chart. Please try again later.');
        }

        return back()->with('success', 'Birth chart generated successfully.');
    }
}
