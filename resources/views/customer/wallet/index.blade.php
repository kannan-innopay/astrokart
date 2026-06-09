<x-layouts.customer title="Wallet">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
        <h1 class="font-display text-2xl font-bold text-gray-900">My Wallet</h1>

        {{-- Balance card --}}
        <div class="mt-6 rounded-2xl bg-gradient-to-br from-cosmic-600 to-cosmic-800 p-8 text-white shadow-xl shadow-cosmic-600/20">
            <p class="text-sm font-medium text-cosmic-200">Available Balance</p>
            <p class="mt-2 font-display text-4xl font-bold">
                ₹{{ number_format(($wallet?->balance ?? 0) / 100, 2) }}
            </p>
        </div>

        {{-- Recharge section --}}
        <x-card class="mt-6" title="Recharge Wallet">
            <form method="POST" action="{{ route('wallet.recharge') }}" x-data="{ amount: 50000, custom: '' }">
                @csrf

                {{-- Preset amounts --}}
                <div class="grid grid-cols-4 gap-2">
                    @foreach([10000, 20000, 50000, 100000] as $preset)
                        <button type="button"
                                @click="amount = {{ $preset }}; custom = ''"
                                :class="amount === {{ $preset }} && !custom ? 'border-cosmic-500 bg-cosmic-50 text-cosmic-700 ring-2 ring-cosmic-200' : 'border-gray-200 text-gray-700 hover:border-gray-300'"
                                class="rounded-xl border-2 px-3 py-3 text-center text-sm font-semibold transition">
                            ₹{{ number_format($preset / 100) }}
                        </button>
                    @endforeach
                </div>

                {{-- Custom amount --}}
                <div class="mt-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700">Or enter custom amount (₹)</label>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-500">₹</span>
                        <input type="number"
                               x-model="custom"
                               @input="if(custom) amount = custom * 100"
                               min="10"
                               max="100000"
                               placeholder="Enter amount"
                               class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-cosmic-400 focus:outline-none focus:ring-2 focus:ring-cosmic-200">
                    </div>
                    @error('amount')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <input type="hidden" name="amount" :value="amount">

                <div class="mt-4 flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        You'll pay: <span class="font-semibold text-gray-900">₹<span x-text="(amount / 100).toFixed(2)"></span></span>
                    </p>
                    <x-button type="submit" variant="gold">
                        Pay Now
                    </x-button>
                </div>
            </form>
        </x-card>

        {{-- Transaction history --}}
        <x-card class="mt-6" :padding="false">
            <x-slot:header>
                <h3 class="font-display text-lg font-semibold text-gray-900">Transaction History</h3>
            </x-slot:header>

            @if($transactions->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-100 bg-gray-50/50 text-left">
                            <tr>
                                <th class="px-6 py-3 font-medium text-gray-500">Date</th>
                                <th class="px-6 py-3 font-medium text-gray-500">Type</th>
                                <th class="px-6 py-3 font-medium text-gray-500">Amount</th>
                                <th class="px-6 py-3 font-medium text-gray-500">Balance</th>
                                <th class="px-6 py-3 font-medium text-gray-500">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($transactions as $txn)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-6 py-3 text-gray-700">{{ $txn->created_at->format('M d, h:i A') }}</td>
                                    <td class="px-6 py-3">
                                        @php
                                            $typeColors = ['credit' => 'green', 'debit' => 'red', 'refund' => 'blue', 'hold' => 'yellow'];
                                        @endphp
                                        <x-badge :color="$typeColors[$txn->type->value] ?? 'gray'">
                                            {{ ucfirst($txn->type->value) }}
                                        </x-badge>
                                    </td>
                                    <td class="px-6 py-3 font-medium {{ in_array($txn->type->value, ['credit', 'refund']) ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ in_array($txn->type->value, ['credit', 'refund']) ? '+' : '-' }}₹{{ number_format($txn->amount / 100, 2) }}
                                    </td>
                                    <td class="px-6 py-3 text-gray-500">₹{{ number_format($txn->balance_after / 100, 2) }}</td>
                                    <td class="px-6 py-3 text-xs text-gray-400">{{ $txn->remarks ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6">
                    <x-empty-state
                        title="No transactions yet"
                        description="Your wallet transactions will appear here."
                    />
                </div>
            @endif
        </x-card>
    </div>
</x-layouts.customer>
