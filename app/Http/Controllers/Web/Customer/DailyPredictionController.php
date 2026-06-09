<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Services\DailyPredictionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyPredictionController extends Controller
{
    public function __construct(
        private DailyPredictionService $predictionService,
    ) {}

    public function show(Request $request): View
    {
        $user = $request->user();
        $isPremium = $user->hasEntitlement('daily_predictions');

        $date = $request->has('date')
            ? Carbon::parse($request->query('date'))
            : Carbon::today();

        $prediction = null;
        if ($isPremium && $user->birth_chart) {
            $prediction = $this->predictionService->getForDate($user, $date);
        }

        return view('customer.predictions.daily', [
            'prediction' => $prediction,
            'date' => $date,
            'isPremium' => $isPremium,
            'hasChart' => (bool) $user->birth_chart,
        ]);
    }
}
