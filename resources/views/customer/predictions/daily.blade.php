<x-layouts.customer title="Daily Prediction">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
        <x-predictions-nav />

        <h1 class="font-display text-2xl font-bold text-gray-900">Daily Prediction</h1>

        {{-- Date navigation --}}
        <div class="mt-4 flex items-center justify-between">
            <a href="{{ route('predictions.daily', ['date' => $date->copy()->subDay()->toDateString()]) }}"
               class="inline-flex items-center gap-1 text-sm font-medium text-cosmic-600 hover:text-cosmic-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
                Previous
            </a>
            <p class="font-display text-lg font-semibold text-gray-900">{{ $date->format('D, d M Y') }}</p>
            <a href="{{ route('predictions.daily', ['date' => $date->copy()->addDay()->toDateString()]) }}"
               class="inline-flex items-center gap-1 text-sm font-medium text-cosmic-600 hover:text-cosmic-700">
                Next
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>

        @if(!$isPremium)
            @include('customer.horoscope.partials.premium-lock', ['feature' => 'daily predictions'])
        @elseif(!$hasChart)
            <x-card class="mt-6 py-12 text-center">
                <p class="font-display text-lg font-bold text-gray-900">Birth chart required</p>
                <p class="mt-2 text-sm text-gray-500">Generate your birth chart first to unlock daily predictions.</p>
                <a href="{{ route('horoscope.show') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-cosmic-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-cosmic-700">
                    Go to Horoscope
                </a>
            </x-card>
        @elseif($prediction)
            <div class="mt-6 space-y-4">
                {{-- Overall mood --}}
                <x-card>
                    <div class="flex items-center justify-between">
                        <h2 class="font-display text-lg font-bold text-gray-900">Overall Mood</h2>
                        <div class="flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= ($prediction['overall_score'] ?? 0))
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
                </x-card>

                {{-- Section cards --}}
                @php
                    $sectionMeta = [
                        'career' => ['icon' => 'M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0', 'label' => 'Career'],
                        'relationships' => ['icon' => 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z', 'label' => 'Relationships'],
                        'finance' => ['icon' => 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'label' => 'Finance'],
                        'health' => ['icon' => 'M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75', 'label' => 'Health'],
                    ];
                    $sections = $prediction['sections'] ?? [];
                @endphp

                @foreach($sectionMeta as $key => $meta)
                    @if(isset($sections[$key]))
                        @php $section = $sections[$key]; @endphp
                        <x-card>
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5 text-cosmic-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $meta['icon'] }}" />
                                </svg>
                                <h2 class="font-display text-lg font-bold text-gray-900">{{ $meta['label'] }}</h2>
                                @php
                                    $s = $section['score'] ?? 3;
                                    $sColor = $s >= 4 ? 'bg-green-100 text-green-700' : ($s <= 2 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700');
                                @endphp
                                <span class="ml-auto flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="h-2 w-2 rounded-full {{ $i <= $s ? ($s >= 4 ? 'bg-green-500' : ($s <= 2 ? 'bg-red-400' : 'bg-yellow-400')) : 'bg-gray-200' }}"></span>
                                    @endfor
                                </span>
                            </div>
                            <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ $section['prediction'] ?? '' }}</p>
                        </x-card>
                    @endif
                @endforeach

                {{-- Lucky info --}}
                @php $lucky = $prediction['lucky'] ?? []; @endphp
                @if(!empty($lucky))
                    <x-card>
                        <div class="flex flex-wrap items-center gap-6">
                            @if(isset($lucky['color']))
                                <div class="flex items-center gap-2">
                                    <span class="h-5 w-5 rounded-full border border-gray-200 shadow-sm" style="background-color: {{ strtolower($lucky['color']) }};"></span>
                                    <div>
                                        <p class="text-xs font-medium text-gray-500">Lucky Color</p>
                                        <p class="text-sm font-semibold text-gray-900">{{ $lucky['color'] }}</p>
                                    </div>
                                </div>
                            @endif
                            @if(isset($lucky['number']))
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Lucky Number</p>
                                    <p class="font-display text-xl font-bold text-cosmic-700">{{ $lucky['number'] }}</p>
                                </div>
                            @endif
                        </div>
                    </x-card>
                @endif

                {{-- Advice --}}
                @if(isset($prediction['advice']))
                    <x-card class="border-cosmic-200 bg-cosmic-50">
                        <h2 class="text-sm font-semibold text-cosmic-700">Today's Advice</h2>
                        <p class="mt-2 text-sm leading-relaxed text-cosmic-800">{{ $prediction['advice'] }}</p>
                    </x-card>
                @endif

                {{-- Current Dasha --}}
                @php $dasha = $prediction['dasha'] ?? []; @endphp
                @if(!empty($dasha))
                    <x-card>
                        <h2 class="font-display text-lg font-bold text-gray-900">Current Dasha Period</h2>
                        <div class="mt-3 flex flex-wrap gap-3">
                            @if(isset($dasha['mahadasha']))
                                <div class="rounded-xl bg-cosmic-50 px-4 py-2">
                                    <p class="text-xs font-medium text-cosmic-500">Mahadasha</p>
                                    <p class="text-sm font-bold text-cosmic-800">{{ $dasha['mahadasha'] }}</p>
                                </div>
                            @endif
                            @if(isset($dasha['antardasha']))
                                <div class="rounded-xl bg-indigo-50 px-4 py-2">
                                    <p class="text-xs font-medium text-indigo-500">Antardasha</p>
                                    <p class="text-sm font-bold text-indigo-800">{{ $dasha['antardasha'] }}</p>
                                </div>
                            @endif
                        </div>
                    </x-card>
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
                <p class="text-gray-500">No prediction available for this date. Please try again later.</p>
            </x-card>
        @endif
    </div>
</x-layouts.customer>
