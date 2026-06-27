<?php

namespace App\Http\Controllers\Web\Sales;

use App\Enums\AstrologerStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $salesUser = $request->user();

        $astrologers = $salesUser->referredAstrologers()
            ->with(['user:id,name,mobile', 'expertises'])
            ->latest()
            ->paginate(15);

        return view('sales.dashboard', [
            'astrologers' => $astrologers,
            'stats' => [
                'total' => $salesUser->referredAstrologers()->count(),
                'approved' => $salesUser->referredAstrologers()->where('status', AstrologerStatus::Approved)->count(),
                'pending' => $salesUser->referredAstrologers()->whereIn('status', [AstrologerStatus::Applied, AstrologerStatus::PendingVerification])->count(),
            ],
        ]);
    }
}
