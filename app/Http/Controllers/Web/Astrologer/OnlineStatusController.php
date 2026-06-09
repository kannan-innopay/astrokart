<?php

namespace App\Http\Controllers\Web\Astrologer;

use App\Http\Controllers\Controller;
use App\Services\AstrologerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OnlineStatusController extends Controller
{
    public function __construct(
        private AstrologerService $astrologerService,
    ) {}

    public function goOnline(Request $request): RedirectResponse
    {
        $this->astrologerService->goOnline($request->user()->astrologerProfile);

        return back()->with('success', 'You are now online.');
    }

    public function goOffline(Request $request): RedirectResponse
    {
        $this->astrologerService->goOffline($request->user()->astrologerProfile);

        return back()->with('success', 'You are now offline.');
    }
}
