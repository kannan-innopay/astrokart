<x-layouts.customer title="Muhurtham Results">
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('muhurtham.index') }}" class="text-sm font-medium text-cosmic-600 hover:text-cosmic-700">&larr; Back</a>
        </div>

        <h1 class="mt-4 font-display text-2xl font-bold text-gray-900">Muhurtham Results</h1>

        <x-card class="mt-6">
            <div class="flex flex-wrap items-center gap-6">
                <div>
                    <p class="text-xs font-medium text-gray-500">Purpose</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $purposes[$request->purpose] ?? $request->purpose }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500">Date Range</p>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ $request->date_range_start->format('d M Y') }} &mdash; {{ $request->date_range_end->format('d M Y') }}
                    </p>
                </div>
                @if(!empty($request->result_json['dates']) && ($request->result_json['dates'][0]['personalized'] ?? false))
                    <div>
                        <span class="rounded-full bg-cosmic-100 px-3 py-1 text-xs font-bold text-cosmic-700">Personalized to your chart</span>
                    </div>
                @endif
            </div>
        </x-card>

        @if($request->status === 'completed' && $request->result_json)
            @if(empty($request->result_json['dates']))
                <x-card class="mt-6">
                    <div class="py-8 text-center">
                        <p class="text-sm text-gray-500">No auspicious dates found for this date range and purpose. Try expanding the date range or selecting a different purpose.</p>
                        <a href="{{ route('muhurtham.index') }}" class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-cosmic-600 hover:text-cosmic-700">&larr; Try another search</a>
                    </div>
                </x-card>
            @else
                <div class="mt-6 space-y-4">
                    @foreach($request->result_json['dates'] as $date)
                        <x-card>
                            {{-- Header: Date + Score --}}
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-display text-lg font-bold text-gray-900">
                                        {{ \Carbon\Carbon::parse($date['date'])->format('d M Y') }}
                                    </p>
                                    <p class="text-sm text-gray-500">{{ $date['weekday'] }}</p>
                                </div>
                                @php
                                    $score = $date['score'] ?? 0;
                                    $scoreColor = $score >= 75 ? 'bg-green-100 text-green-700' : ($score >= 55 ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700');
                                @endphp
                                <span class="rounded-full {{ $scoreColor }} px-3 py-1 text-sm font-bold">
                                    {{ $score }}/100
                                </span>
                            </div>

                            {{-- Panchanga details --}}
                            <div class="mt-4 grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Tithi</p>
                                    <p class="font-medium text-gray-900">{{ $date['tithi'] }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Nakshatra</p>
                                    <p class="font-medium text-gray-900">{{ $date['nakshatra'] }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Rashi</p>
                                    <p class="font-medium text-gray-900">{{ $date['rashi'] ?? '' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Yoga</p>
                                    <p class="font-medium text-gray-900">{{ $date['yoga'] }}</p>
                                </div>
                            </div>

                            {{-- Transit highlights (personalized) --}}
                            @if(!empty($date['transit_highlights']))
                                <div class="mt-4">
                                    <p class="text-xs font-medium text-gray-500">Transit Positions (from your Lagna)</p>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach($date['transit_highlights'] as $transit)
                                            @php
                                                $effectColor = match($transit['effect'] ?? 'neutral') {
                                                    'favorable' => 'bg-green-50 text-green-700 border-green-200',
                                                    'challenging' => 'bg-red-50 text-red-700 border-red-200',
                                                    default => 'bg-gray-50 text-gray-600 border-gray-200',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center gap-1 rounded-lg border px-2.5 py-1 text-xs font-medium {{ $effectColor }}">
                                                {{ $transit['planet'] }} in {{ $transit['rashi'] }}
                                                ({{ $transit['house_from_lagna'] }}H)
                                                @if($transit['is_retrograde']) <span class="text-[10px]">R</span> @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Reasons --}}
                            @if(!empty($date['reasons']))
                                <div class="mt-4">
                                    <p class="text-xs font-medium text-gray-500">Scoring Factors</p>
                                    <ul class="mt-1.5 space-y-1">
                                        @foreach($date['reasons'] as $reason)
                                            @php
                                                $isPersonal = str_contains($reason, '(for you)') || str_contains($reason, 'your');
                                                $isPositive = !str_contains($reason, 'Unfavorable') && !str_contains($reason, 'Rikta') && !str_contains($reason, 'Malefic') && !str_contains($reason, 'caution') && !str_contains($reason, 'challenging') && !str_contains($reason, 'Amavasya');
                                            @endphp
                                            <li class="flex items-start gap-2 text-sm {{ $isPersonal ? 'text-cosmic-700' : 'text-gray-600' }}">
                                                @if($isPositive)
                                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                                @else
                                                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126Z"/></svg>
                                                @endif
                                                {{ $reason }}
                                                @if($isPersonal)
                                                    <span class="rounded bg-cosmic-100 px-1 py-0.5 text-[10px] font-bold text-cosmic-600">YOU</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </x-card>
                    @endforeach
                </div>

                {{-- Consult CTA --}}
                <div class="mt-8 text-center">
                    <p class="mb-3 text-sm text-gray-500">For final confirmation of the muhurtham, consult an experienced astrologer</p>
                    <a href="{{ route('astrologers.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-6 py-3 text-sm font-semibold text-night-950 shadow transition hover:from-gold-600 hover:to-gold-700">
                        Verify with an astrologer &rarr;
                    </a>
                </div>
            @endif
        @elseif($request->status === 'pending' || $request->status === 'processing')
            <x-card class="mt-6">
                <div class="py-8 text-center">
                    <p class="text-gray-500">Your request is being processed...</p>
                    <a href="{{ route('muhurtham.show', $request) }}" class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-cosmic-600 hover:text-cosmic-700">Refresh</a>
                </div>
            </x-card>
        @else
            <x-card class="mt-6">
                <div class="py-8 text-center">
                    <svg class="mx-auto h-10 w-10 text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                    <p class="mt-3 text-sm font-medium text-gray-700">This request could not be completed.</p>
                    @if($request->status === 'refunded')
                        <p class="mt-1 text-xs text-gray-500">The fee has been refunded to your wallet.</p>
                    @endif
                    <a href="{{ route('muhurtham.index') }}" class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-cosmic-600 hover:text-cosmic-700">&larr; Try again</a>
                </div>
            </x-card>
        @endif
    </div>
</x-layouts.customer>
