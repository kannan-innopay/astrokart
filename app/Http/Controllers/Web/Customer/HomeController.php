<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Services\AstrologerService;
use App\Services\DailyPredictionService;
use App\Services\HoraService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private AstrologerService $astrologerService,
        private HoraService $horaService,
        private DailyPredictionService $predictionService,
    ) {}

    public function index(Request $request): View
    {
        // Show native-style landing page for unauthenticated mobile users
        if (! $request->user() && $this->isMobileDevice($request)) {
            return view('customer.mobile-landing');
        }

        $featuredAstrologers = $this->astrologerService->listApproved(
            ['is_online' => true],
            6,
        );

        $user = $request->user();
        $horaData = null;
        $profileComplete = false;
        $dailyPrediction = null;

        if ($user) {
            $horaData = $this->horaService->getDailyHoras(user: $user);
            $profileComplete = $user->date_of_birth && $user->place_of_birth && $user->name !== 'User';

            if ($user->hasEntitlement('daily_predictions') && $user->birth_chart) {
                $dailyPrediction = $this->predictionService->getForDate($user, now());
            }
        }

        return view('customer.home', [
            'featuredAstrologers' => $featuredAstrologers,
            'user' => $user,
            'horaData' => $horaData,
            'profileComplete' => $profileComplete,
            'dailyPrediction' => $dailyPrediction,
        ]);
    }

    private function isMobileDevice(Request $request): bool
    {
        $ua = $request->userAgent() ?? '';

        return (bool) preg_match('/Mobile|Android|iPhone|iPad|iPod|webOS|BlackBerry|Opera Mini|IEMobile/i', $ua);
    }
}
