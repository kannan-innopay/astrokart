<x-layouts.customer title="Monthly Forecast">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
        <x-predictions-nav />

        <h1 class="font-display text-2xl font-bold text-gray-900">Monthly Forecast</h1>

        {{-- Month navigation --}}
        <div class="mt-4 flex items-center justify-between">
            <a href="{{ route('predictions.monthly', ['year' => $month->copy()->subMonth()->year, 'month' => $month->copy()->subMonth()->month]) }}"
               class="inline-flex items-center gap-1 text-sm font-medium text-cosmic-600 hover:text-cosmic-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
                Previous
            </a>
            <p class="font-display text-lg font-semibold text-gray-900">{{ $month->format('F Y') }}</p>
            <a href="{{ route('predictions.monthly', ['year' => $month->copy()->addMonth()->year, 'month' => $month->copy()->addMonth()->month]) }}"
               class="inline-flex items-center gap-1 text-sm font-medium text-cosmic-600 hover:text-cosmic-700">
                Next
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>

        @if(!$isPremium)
            @include('customer.horoscope.partials.premium-lock', ['feature' => 'monthly forecast'])
        @elseif(!$hasChart)
            <x-card class="mt-6 py-12 text-center">
                <p class="font-display text-lg font-bold text-gray-900">Birth chart required</p>
                <p class="mt-2 text-sm text-gray-500">Generate your birth chart first to unlock monthly forecasts.</p>
                <a href="{{ route('horoscope.show') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-cosmic-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-cosmic-700">
                    Go to Horoscope
                </a>
            </x-card>
        @elseif($forecast)
            <div class="mt-6 space-y-4">
                {{-- Overall month score + overview --}}
                <x-card>
                    <div class="flex items-center justify-between">
                        <h2 class="font-display text-lg font-bold text-gray-900">Month Overview</h2>
                        <div class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= ($forecast['overall_score'] ?? 0))
                                    <svg class="h-5 w-5 text-gold-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" />
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                    </svg>
                                @endif
                            @endfor
                        </div>
                    </div>
                    @if(isset($forecast['overview']))
                        <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ $forecast['overview'] }}</p>
                    @endif
                </x-card>

                {{-- Current Dasha --}}
                @php $dasha = $forecast['dasha'] ?? []; @endphp
                @if(!empty($dasha))
                    <x-card>
                        <h2 class="font-display text-lg font-bold text-gray-900">Current Dasha Period</h2>
                        @if(isset($dasha['mahadasha']))
                            <div class="mt-3 rounded-xl bg-cosmic-50 px-4 py-3">
                                <p class="text-xs font-medium text-cosmic-500">Mahadasha: <span class="font-bold text-cosmic-800">{{ $dasha['mahadasha'] }}</span></p>
                                @if(isset($dasha['interpretation']))
                                    <p class="mt-1 text-sm text-cosmic-700">{{ $dasha['interpretation'] }}</p>
                                @endif
                            </div>
                        @endif
                    </x-card>
                @endif

                {{-- Weeks breakdown --}}
                @if(!empty($forecast['weeks']))
                    <h2 class="mt-2 font-display text-lg font-bold text-gray-900">Weekly Breakdown</h2>

                    @foreach($forecast['weeks'] as $week)
                        <x-card>
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-display text-base font-bold text-gray-900">Week {{ $week['week_number'] }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($week['start_date'])->format('d M') }} &mdash; {{ \Carbon\Carbon::parse($week['end_date'])->format('d M Y') }}
                                    </p>
                                </div>
                                @php $ws = $week['overall_score'] ?? 3; @endphp
                                <div class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="h-2 w-2 rounded-full {{ $i <= $ws ? ($ws >= 4 ? 'bg-green-500' : ($ws <= 2 ? 'bg-red-400' : 'bg-yellow-400')) : 'bg-gray-200' }}"></span>
                                    @endfor
                                </div>
                            </div>

                            @if(!empty($week['themes']))
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach($week['themes'] as $theme)
                                        <span class="rounded-full bg-cosmic-100 px-2.5 py-0.5 text-xs font-medium text-cosmic-700">{{ $theme }}</span>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($week['key_transits']))
                                <div class="mt-3">
                                    <p class="text-xs font-medium text-gray-500">Key Transits</p>
                                    <div class="mt-1.5 flex flex-wrap gap-2">
                                        @foreach($week['key_transits'] as $transit)
                                            <span class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-600">
                                                {{ $transit['planet'] }} in {{ $transit['rashi'] }}
                                                ({{ $transit['house_from_moon'] }}H from Moon)
                                                @if($transit['is_retrograde'] ?? false) <span class="text-red-400">R</span> @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </x-card>
                    @endforeach
                @endif

                {{-- CTA --}}
                <div class="mt-4 text-center">
                    <a href="{{ route('astrologers.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-6 py-3 text-sm font-semibold text-night-950 shadow transition hover:from-gold-600 hover:to-gold-700">
                        Consult an astrologer &rarr;
                    </a>
                </div>
            </div>
        @else
            <x-card class="mt-6 py-12 text-center">
                <p class="text-gray-500">No forecast available for this month. Please try again later.</p>
            </x-card>
        @endif
    </div>
</x-layouts.customer>
