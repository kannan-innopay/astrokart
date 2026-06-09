<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Services\MonthlyForecastService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonthlyForecastController extends Controller
{
    public function __construct(
        private MonthlyForecastService $forecastService,
    ) {}

    public function show(Request $request): View
    {
        $user = $request->user();
        $isPremium = $user->hasEntitlement('monthly_forecast');

        $year = (int) ($request->query('year') ?: Carbon::now()->year);
        $month = (int) ($request->query('month') ?: Carbon::now()->month);

        $forecast = null;
        if ($isPremium && $user->birth_chart) {
            $forecast = $this->forecastService->getForMonth($user, $year, $month);
        }

        $monthDate = Carbon::create($year, $month, 1);

        return view('customer.predictions.monthly', [
            'forecast' => $forecast,
            'year' => $year,
            'month' => $monthDate,
            'isPremium' => $isPremium,
            'hasChart' => (bool) $user->birth_chart,
        ]);
    }
}
