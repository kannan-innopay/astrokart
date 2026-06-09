<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Models\MuhurthamRequest;
use App\Services\MuhurthamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MuhurthamController extends Controller
{
    private const PURPOSES = [
        'marriage' => 'Marriage',
        'engagement' => 'Engagement',
        'business_opening' => 'Business Opening',
        'shop_opening' => 'Shop Opening',
        'housewarming' => 'Housewarming (Gruha Pravesham)',
        'vehicle_purchase' => 'Vehicle Purchase',
        'travel' => 'Travel',
        'naming_ceremony' => 'Naming Ceremony',
        'agreement_signing' => 'Agreement Signing',
        'job_joining' => 'Job Joining',
        'education' => 'Education',
    ];

    public function __construct(
        private MuhurthamService $muhurthamService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $subscription = $user->activeSubscription;
        $quotaRemaining = 0;

        if ($subscription) {
            $quota = match ($subscription->plan) {
                \App\Enums\SubscriptionPlan::Monthly => 3,
                \App\Enums\SubscriptionPlan::Yearly => 50,
                default => 0,
            };

            $used = \App\Models\MuhurthamRequest::where('user_id', $user->id)
                ->where('amount_charged', 0)
                ->where('created_at', '>=', $subscription->starts_at)
                ->count();

            $quotaRemaining = max(0, $quota - $used);
        }

        return view('customer.muhurtham.index', [
            'purposes' => self::PURPOSES,
            'walletBalance' => $user->wallet?->balance ?? 0,
            'history' => $this->muhurthamService->getUserRequests($user),
            'isPremium' => $user->isPremium(),
            'quotaRemaining' => $quotaRemaining,
        ]);
    }

    public function search(Request $request): RedirectResponse
    {
        $request->validate([
            'purpose' => ['required', 'string', 'in:' . implode(',', array_keys(self::PURPOSES))],
            'date_start' => ['required', 'date', 'after_or_equal:today'],
            'date_end' => ['required', 'date', 'after:date_start'],
        ]);

        try {
            $result = $this->muhurthamService->search(
                $request->user(),
                $request->input('purpose'),
                $request->date('date_start'),
                $request->date('date_end'),
            );

            return redirect()->route('muhurtham.show', $result)
                ->with('success', 'Muhurtham search completed.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, MuhurthamRequest $muhurthamRequest): View
    {
        abort_unless($muhurthamRequest->user_id === $request->user()->id, 403);

        return view('customer.muhurtham.show', [
            'request' => $muhurthamRequest,
            'purposes' => self::PURPOSES,
        ]);
    }
}
