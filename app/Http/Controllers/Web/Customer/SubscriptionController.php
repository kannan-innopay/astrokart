<?php

namespace App\Http\Controllers\Web\Customer;

use App\Enums\SubscriptionPlan;
use App\Http\Controllers\Controller;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(
        private SubscriptionService $subscriptionService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('customer.subscription.index', [
            'plans' => SubscriptionPlan::cases(),
            'activeSubscription' => $this->subscriptionService->getActive($user),
            'walletBalance' => $user->wallet?->balance ?? 0,
        ]);
    }

    public function subscribe(Request $request): RedirectResponse
    {
        $request->validate([
            'plan' => ['required', new Enum(SubscriptionPlan::class)],
        ]);

        $plan = SubscriptionPlan::from($request->input('plan'));

        try {
            $this->subscriptionService->subscribe($request->user(), $plan);

            return redirect()->route('subscription.index')
                ->with('success', "You're now subscribed to {$plan->label()}!");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request): RedirectResponse
    {
        $subscription = $this->subscriptionService->getActive($request->user());

        if (! $subscription) {
            return back()->with('error', 'No active subscription found.');
        }

        $this->subscriptionService->cancel($subscription);

        return redirect()->route('subscription.index')
            ->with('success', 'Subscription cancelled. You will have access until ' . $subscription->expires_at->format('d M Y') . '.');
    }
}
