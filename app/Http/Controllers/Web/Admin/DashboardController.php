<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\AstrologerStatus;
use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'totalAstrologers' => Astrologer::count(),
            'pendingApprovals' => Astrologer::where('status', AstrologerStatus::Applied)->count(),
            'approvedAstrologers' => Astrologer::where('status', AstrologerStatus::Approved)->count(),
            'onlineNow' => Astrologer::where('is_online', true)->count(),
            'recentUsers' => User::latest()->take(5)->get(),
        ]);
    }
}
