<x-layouts.customer title="Premium Plans">
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6">
        <div class="text-center">
            <h1 class="font-display text-3xl font-bold text-gray-900">Unlock Premium Insights</h1>
            <p class="mt-2 text-gray-500">Detailed analysis, daily predictions, and personalized guidance</p>
        </div>

        @if(session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif
        @if(session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif

        {{-- Active subscription card --}}
        @if($activeSubscription)
            <x-card class="mt-6 border-cosmic-200 bg-cosmic-50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-cosmic-700">Active Plan</p>
                        <p class="font-display text-lg font-bold text-cosmic-900">{{ $activeSubscription->plan->label() }}</p>
                        <p class="mt-1 text-sm text-cosmic-600">
                            @if($activeSubscription->plan === \App\Enums\SubscriptionPlan::Daily)
                                Renews daily from wallet &middot; Active since {{ $activeSubscription->starts_at->format('d M Y') }}
                            @else
                                Valid until {{ $activeSubscription->expires_at->format('d M Y') }}
                                ({{ $activeSubscription->daysRemaining() }} days remaining)
                            @endif
                        </p>
                    </div>
                    <form method="POST" action="{{ route('subscription.cancel') }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50"
                                onclick="return confirm('{{ $activeSubscription->plan === \App\Enums\SubscriptionPlan::Daily ? 'Stop your Daily Pass? Access ends at the end of today.' : 'Cancel your subscription? Access continues until ' . $activeSubscription->expires_at->format('d M Y') . '.' }}')">
                            {{ $activeSubscription->plan === \App\Enums\SubscriptionPlan::Daily ? 'Stop Daily Pass' : 'Cancel' }}
                        </button>
                    </form>
                </div>
            </x-card>
        @endif

        {{-- Pricing cards --}}
        <div class="mt-8 grid gap-4 sm:grid-cols-3">
            {{-- Daily --}}
            <x-card class="relative text-center">
                <h3 class="font-display text-lg font-bold text-gray-900">Daily Pass</h3>
                <div class="mt-3">
                    <span class="font-display text-4xl font-bold text-cosmic-700">₹3</span>
                    <span class="text-gray-500">/day</span>
                </div>
                <p class="mt-2 text-sm text-gray-500">Try premium for a day</p>
                <ul class="mt-5 space-y-2 text-left text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Full chart analysis
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Dasha timeline
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Auto-renews from wallet
                    </li>
                </ul>
                @unless($activeSubscription)
                    <form method="POST" action="{{ route('subscription.subscribe') }}" class="mt-6">
                        @csrf
                        <input type="hidden" name="plan" value="daily">
                        <button type="submit" class="w-full rounded-xl border-2 border-cosmic-600 px-4 py-2.5 text-sm font-semibold text-cosmic-700 transition hover:bg-cosmic-50">
                            Start Daily Pass
                        </button>
                    </form>
                @endunless
            </x-card>

            {{-- Monthly (highlighted) --}}
            <x-card class="relative border-2 border-cosmic-500 text-center shadow-lg">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                    <span class="rounded-full bg-cosmic-600 px-3 py-1 text-xs font-bold text-white">Popular</span>
                </div>
                <h3 class="font-display text-lg font-bold text-gray-900">Monthly</h3>
                <div class="mt-3">
                    <span class="font-display text-4xl font-bold text-cosmic-700">₹99</span>
                    <span class="text-gray-500">/month</span>
                </div>
                <p class="mt-2 text-sm text-gray-500">Best for regular users</p>
                <ul class="mt-5 space-y-2 text-left text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Everything in Daily
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Daily predictions
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Monthly forecast
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        3 free Muhurtham requests
                    </li>
                </ul>
                @unless($activeSubscription)
                    <form method="POST" action="{{ route('subscription.subscribe') }}" class="mt-6">
                        @csrf
                        <input type="hidden" name="plan" value="monthly">
                        <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-cosmic-600 to-cosmic-700 px-4 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:from-cosmic-700 hover:to-cosmic-800">
                            Subscribe Monthly
                        </button>
                    </form>
                @endunless
            </x-card>

            {{-- Yearly --}}
            <x-card class="relative text-center">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2">
                    <span class="rounded-full bg-gold-500 px-3 py-1 text-xs font-bold text-night-950">Save 33%</span>
                </div>
                <h3 class="font-display text-lg font-bold text-gray-900">Yearly</h3>
                <div class="mt-3">
                    <span class="font-display text-4xl font-bold text-cosmic-700">₹799</span>
                    <span class="text-gray-500">/year</span>
                </div>
                <p class="mt-2 text-sm text-gray-500">Best value</p>
                <ul class="mt-5 space-y-2 text-left text-sm text-gray-600">
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Everything in Monthly
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        50 free Muhurtham requests
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                        Priority astrologer access
                    </li>
                </ul>
                @unless($activeSubscription)
                    <form method="POST" action="{{ route('subscription.subscribe') }}" class="mt-6">
                        @csrf
                        <input type="hidden" name="plan" value="yearly">
                        <button type="submit" class="w-full rounded-xl border-2 border-gold-500 px-4 py-2.5 text-sm font-semibold text-gold-700 transition hover:bg-gold-50">
                            Subscribe Yearly
                        </button>
                    </form>
                @endunless
            </x-card>
        </div>

        {{-- Wallet balance --}}
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                Wallet balance: <span class="font-semibold text-gray-900">₹{{ number_format($walletBalance / 100) }}</span>
                @if($walletBalance < 300)
                    &middot; <a href="{{ route('wallet.index') }}" class="text-cosmic-600 hover:text-cosmic-700">Recharge wallet</a>
                @endif
            </p>
        </div>
    </div>
</x-layouts.customer>
