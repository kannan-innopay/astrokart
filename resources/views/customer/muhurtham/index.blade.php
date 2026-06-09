<x-layouts.customer title="Muhurtham Finder">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
        <h1 class="font-display text-2xl font-bold text-gray-900">Muhurtham Finder</h1>
        <p class="mt-1 text-sm text-gray-500">Find auspicious dates for important events</p>

        @if(session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif
        @if(session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif

        {{-- Search form --}}
        <x-card class="mt-6">
            <h2 class="font-display text-lg font-bold text-gray-900">New Search</h2>

            <form method="POST" action="{{ route('muhurtham.search') }}" class="mt-4 space-y-4">
                @csrf

                <x-select name="purpose" label="Purpose" :options="$purposes" :selected="old('purpose')" placeholder="Select purpose..." />

                <div class="grid gap-4 sm:grid-cols-2">
                    <x-input name="date_start" label="From Date" type="date" :value="old('date_start')" />
                    <x-input name="date_end" label="To Date" type="date" :value="old('date_end')" />
                </div>

                {{-- Fee info --}}
                <div class="rounded-xl bg-cosmic-50 p-4">
                    <p class="text-sm text-cosmic-700">
                        Search fee:
                        @if($isPremium && $quotaRemaining > 0)
                            <span class="font-semibold text-green-600">Free</span>
                            <span class="text-xs text-cosmic-500">({{ $quotaRemaining }} free searches remaining)</span>
                        @else
                            <span class="font-semibold">₹5</span> per search
                        @endif
                    </p>
                </div>

                {{-- Wallet balance --}}
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <p>Wallet: <span class="font-semibold text-gray-900">₹{{ number_format($walletBalance / 100) }}</span></p>
                    @if($walletBalance < 500)
                        <a href="{{ route('wallet.index') }}" class="font-medium text-cosmic-600 hover:text-cosmic-700">Recharge &rarr;</a>
                    @endif
                </div>

                <x-button type="submit" variant="primary" class="w-full">Find Auspicious Dates</x-button>
            </form>
        </x-card>

        {{-- History --}}
        @if($history->isNotEmpty())
            <x-card class="mt-6">
                <h2 class="font-display text-lg font-bold text-gray-900">Search History</h2>

                <div class="mt-4 divide-y divide-gray-100">
                    @foreach($history as $item)
                        <a href="{{ route('muhurtham.show', $item) }}" class="flex items-center justify-between py-3 transition hover:bg-gray-50">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $purposes[$item->purpose] ?? $item->purpose }}</p>
                                <p class="mt-0.5 text-xs text-gray-500">
                                    {{ $item->date_range_start->format('d M Y') }} &mdash; {{ $item->date_range_end->format('d M Y') }}
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                @if($item->status === 'completed')
                                    <span class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Completed</span>
                                @elseif($item->status === 'failed')
                                    <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">Failed</span>
                                @elseif($item->status === 'refunded')
                                    <span class="rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-700">Refunded</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">{{ ucfirst($item->status) }}</span>
                                @endif
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </x-card>
        @endif
    </div>
</x-layouts.customer>
