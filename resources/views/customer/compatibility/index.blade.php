<x-layouts.customer title="Compatibility Matching">
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6">
        <div class="text-center">
            <h1 class="font-display text-3xl font-bold text-gray-900">Compatibility Matching</h1>
            <p class="mt-2 text-gray-500">Ashtakoota compatibility analysis based on Moon nakshatra</p>
        </div>

        @if(session('success'))
            <x-alert type="success" :message="session('success')" />
        @endif
        @if(session('error'))
            <x-alert type="error" :message="session('error')" />
        @endif

        @unless($hasChart)
            <x-card class="mt-8 py-12 text-center">
                <p class="font-display text-lg font-bold text-gray-900">Generate your birth chart first</p>
                <p class="mt-2 text-sm text-gray-500">You need a birth chart before running compatibility analysis.</p>
                <x-button :href="route('horoscope.show')" class="mt-4">Go to Horoscope</x-button>
            </x-card>
        @else
            {{-- Compatibility form --}}
            <x-card class="mt-8">
                <form method="POST" action="{{ route('compatibility.match') }}" class="space-y-5">
                    @csrf

                    <x-input
                        label="Partner Name"
                        name="partner_name"
                        placeholder="Optional"
                        :value="old('partner_name')"
                    />

                    @php
                        $nakshatras = [
                            0 => 'Ashwini', 1 => 'Bharani', 2 => 'Krittika', 3 => 'Rohini',
                            4 => 'Mrigashira', 5 => 'Ardra', 6 => 'Punarvasu', 7 => 'Pushya',
                            8 => 'Ashlesha', 9 => 'Magha', 10 => 'Purva Phalguni', 11 => 'Uttara Phalguni',
                            12 => 'Hasta', 13 => 'Chitra', 14 => 'Swati', 15 => 'Vishakha',
                            16 => 'Anuradha', 17 => 'Jyeshtha', 18 => 'Mula', 19 => 'Purva Ashadha',
                            20 => 'Uttara Ashadha', 21 => 'Shravana', 22 => 'Dhanishta', 23 => 'Shatabhisha',
                            24 => 'Purva Bhadrapada', 25 => 'Uttara Bhadrapada', 26 => 'Revati',
                        ];

                        $rashis = [
                            0 => 'Aries (Mesha)', 1 => 'Taurus (Vrishabha)', 2 => 'Gemini (Mithuna)',
                            3 => 'Cancer (Karka)', 4 => 'Leo (Simha)', 5 => 'Virgo (Kanya)',
                            6 => 'Libra (Tula)', 7 => 'Scorpio (Vrishchika)', 8 => 'Sagittarius (Dhanu)',
                            9 => 'Capricorn (Makara)', 10 => 'Aquarius (Kumbha)', 11 => 'Pisces (Meena)',
                        ];
                    @endphp

                    <x-select
                        label="Partner's Moon Nakshatra"
                        name="moon_nakshatra"
                        :options="$nakshatras"
                        :selected="old('moon_nakshatra')"
                        placeholder="Select nakshatra..."
                    />

                    <x-select
                        label="Partner's Moon Rashi"
                        name="moon_rashi"
                        :options="$rashis"
                        :selected="old('moon_rashi')"
                        placeholder="Select rashi..."
                    />

                    {{-- Fee information --}}
                    <div class="rounded-xl bg-cosmic-50 px-4 py-3">
                        @if($isPremium)
                            <p class="text-sm font-medium text-cosmic-700">Free for premium users</p>
                        @else
                            <p class="text-sm font-medium text-cosmic-700">₹9 per match</p>
                            <p class="mt-1 text-xs text-cosmic-600">
                                Wallet balance: <span class="font-semibold">₹{{ number_format($walletBalance / 100) }}</span>
                                &middot; <a href="{{ route('wallet.index') }}" class="underline hover:text-cosmic-800">Recharge</a>
                            </p>
                        @endif
                    </div>

                    <x-button type="submit" class="w-full">Check Compatibility</x-button>
                </form>
            </x-card>

            {{-- History --}}
            @if($history->isNotEmpty())
                <div class="mt-8">
                    <h2 class="font-display text-lg font-bold text-gray-900">Previous Matches</h2>
                    <div class="mt-4 space-y-3">
                        @foreach($history as $match)
                            <a href="{{ route('compatibility.show', $match) }}" class="block">
                                <x-card class="transition hover:border-cosmic-200 hover:shadow-md">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $match->partner_name ?: 'Unnamed Partner' }}</p>
                                            <p class="mt-0.5 text-xs text-gray-500">{{ $match->created_at->format('d M Y') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-display text-2xl font-bold {{ $match->score >= 24 ? 'text-green-600' : ($match->score >= 18 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ $match->score }}
                                            </span>
                                            <span class="text-sm text-gray-400">/36</span>
                                        </div>
                                    </div>
                                </x-card>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endunless
    </div>
</x-layouts.customer>
