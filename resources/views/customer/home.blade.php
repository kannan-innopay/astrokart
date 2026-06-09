<x-layouts.customer title="Home">
    @auth
        {{-- ============ AUTHENTICATED HOME ============ --}}
        <div class="mx-auto max-w-5xl px-3 py-5 sm:px-6 sm:py-8 lg:px-8 space-y-5 sm:space-y-6">

            {{-- Greeting --}}
            <div>
                <h1 class="font-display text-xl font-bold text-gray-900 sm:text-2xl">
                    Namaste, {{ Str::before($user->name, ' ') }}
                </h1>
                <p class="mt-0.5 text-xs text-gray-500 sm:text-sm">{{ now()->format('l, d M Y') }}</p>
            </div>

            {{-- Profile incomplete prompt --}}
            @unless($profileComplete)
                <div class="rounded-2xl border-2 border-gold-300 bg-gradient-to-r from-gold-50 to-amber-50 p-4 sm:p-5">
                    <div class="flex items-start gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-100 text-gold-600">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456Z"/></svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-display text-sm font-semibold text-gray-900 sm:text-base">Complete Your Birth Profile</h3>
                            <p class="mt-0.5 text-xs text-gray-600 sm:text-sm">Add your date, time, and place of birth to generate your personalized Vedic birth chart and daily predictions.</p>
                            <div class="mt-3">
                                <x-button href="{{ route('onboarding') }}" variant="gold" size="sm">Add Birth Details</x-button>
                            </div>
                        </div>
                    </div>
                </div>
            @endunless

            {{-- Current Hora card --}}
            @if($horaData)
                @php
                    $current = $horaData['current_hora'];
                    $colorSwatches = ['Red' => 'bg-red-500', 'White' => 'bg-white border border-gray-200', 'Green' => 'bg-emerald-500', 'Yellow' => 'bg-yellow-400', 'Blue' => 'bg-indigo-500'];
                    $personal = $current['personal'] ?? null;
                    $fav = $personal['favorability'] ?? null;
                    $favBadge = ['excellent' => ['Excellent', 'bg-emerald-100 text-emerald-700'], 'favorable' => ['Favorable', 'bg-blue-100 text-blue-700'], 'neutral' => ['Neutral', 'bg-gray-100 text-gray-600'], 'caution' => ['Caution', 'bg-amber-100 text-amber-700']];
                @endphp

                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                    {{-- Top row: planet name + time + personalization --}}
                    <div class="flex items-center gap-3 px-4 py-3.5 sm:px-5 sm:py-4">
                        {{-- Planet icon --}}
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-cosmic-50 sm:h-14 sm:w-14">
                            <div class="h-5 w-5 rounded-full {{ $colorSwatches[$current['data']['color']] ?? 'bg-gray-400' }} sm:h-6 sm:w-6"></div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="relative flex h-2 w-2 shrink-0">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                                </span>
                                <h3 class="font-display text-base font-bold text-gray-900 sm:text-lg">{{ $current['planet'] }} Hora</h3>
                            </div>
                            <div class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-gray-500">
                                <span>{{ $current['start_label'] }}–{{ $current['end_label'] }}</span>
                                <span class="text-gray-300">&middot;</span>
                                <span>{{ ucfirst($current['data']['quality']) }}</span>
                                <span class="text-gray-300">&middot;</span>
                                <span class="font-medium text-gray-700">{{ $current['data']['mood'] }}</span>
                            </div>
                        </div>
                        @if($profileComplete && $fav)
                            <div class="shrink-0 rounded-xl px-3 py-1.5 text-center {{ $favBadge[$fav][1] ?? 'bg-gray-100 text-gray-600' }}">
                                <p class="text-[8px] font-medium uppercase tracking-wider sm:text-[9px]">For You</p>
                                <p class="text-xs font-bold sm:text-sm">{{ $favBadge[$fav][0] ?? ucfirst($fav) }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Tips row --}}
                    <div class="grid grid-cols-2 gap-px border-t border-gray-100 bg-gray-100">
                        <div class="bg-white p-3 sm:p-4">
                            <div class="flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 shrink-0 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                <span class="text-[10px] font-semibold text-emerald-600 uppercase sm:text-xs">Do</span>
                            </div>
                            <p class="mt-1 text-[11px] leading-snug text-gray-600 sm:text-xs">{{ $current['data']['do'] }}</p>
                        </div>
                        <div class="bg-white p-3 sm:p-4">
                            <div class="flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 shrink-0 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                <span class="text-[10px] font-semibold text-red-500 uppercase sm:text-xs">Avoid</span>
                            </div>
                            <p class="mt-1 text-[11px] leading-snug text-gray-600 sm:text-xs">{{ $current['data']['avoid'] }}</p>
                        </div>
                    </div>

                    {{-- Footer link --}}
                    <a href="{{ route('horoscope.hora') }}" class="flex items-center justify-center gap-1.5 border-t border-gray-100 px-4 py-2.5 text-xs font-medium text-cosmic-600 transition hover:bg-cosmic-50 sm:text-sm">
                        View Full Hora Table
                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    </a>
                </div>
            @endif

            {{-- Daily Prediction card --}}
            @if($dailyPrediction)
                @php
                    $overallScore = $dailyPrediction['overall_score'] ?? 3;
                    $predSections = $dailyPrediction['sections'] ?? [];
                    $lucky = $dailyPrediction['lucky'] ?? [];
                @endphp
                <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="flex items-center gap-3 px-4 py-3.5 sm:px-5 sm:py-4">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gold-50 sm:h-14 sm:w-14">
                            <svg class="h-6 w-6 text-gold-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" />
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-display text-base font-bold text-gray-900 sm:text-lg">Today's Prediction</h3>
                            <div class="mt-0.5 flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="h-2 w-2 rounded-full {{ $i <= $overallScore ? 'bg-gold-400' : 'bg-gray-200' }}"></span>
                                @endfor
                                <span class="ml-1 text-xs text-gray-500">{{ $overallScore }}/5</span>
                                @if(isset($lucky['color']))
                                    <span class="ml-2 text-gray-300">&middot;</span>
                                    <span class="ml-1 text-xs text-gray-500">Lucky: {{ $lucky['color'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-px border-t border-gray-100 bg-gray-100 sm:grid-cols-4">
                        @foreach(['career' => 'Career', 'relationships' => 'Love', 'finance' => 'Finance', 'health' => 'Health'] as $key => $label)
                            @if(isset($predSections[$key]))
                                @php $s = $predSections[$key]['score'] ?? 3; @endphp
                                <div class="bg-white p-3 sm:p-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-semibold uppercase text-gray-500 sm:text-xs">{{ $label }}</span>
                                        <div class="flex gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="h-1.5 w-1.5 rounded-full {{ $i <= $s ? ($s >= 4 ? 'bg-green-500' : ($s <= 2 ? 'bg-red-400' : 'bg-yellow-400')) : 'bg-gray-200' }}"></span>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="mt-1 text-[11px] leading-snug text-gray-600 line-clamp-2 sm:text-xs">{{ $predSections[$key]['prediction'] ?? '' }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <a href="{{ route('predictions.daily') }}" class="flex items-center justify-center gap-1.5 border-t border-gray-100 px-4 py-2.5 text-xs font-medium text-cosmic-600 transition hover:bg-cosmic-50 sm:text-sm">
                        View Full Prediction
                        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
                    </a>
                </div>
            @endif

            {{-- Quick actions --}}
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
                @php
                    $actions = [
                        ['route' => 'horoscope.show', 'label' => 'My Chart', 'icon' => 'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456Z', 'color' => 'bg-cosmic-50 text-cosmic-600'],
                        ['route' => 'horoscope.transits', 'label' => 'Transits', 'icon' => 'M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z', 'color' => 'bg-amber-50 text-amber-600'],
                        ['route' => 'horoscope.hora', 'label' => 'Hora', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z', 'color' => 'bg-emerald-50 text-emerald-600'],
                        ['route' => 'predictions.daily', 'label' => 'Predictions', 'icon' => 'M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z', 'color' => 'bg-gold-50 text-gold-600'],
                        ['route' => 'muhurtham.index', 'label' => 'Muhurtham', 'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z', 'color' => 'bg-pink-50 text-pink-600'],
                    ];
                    if ($featureAstrologers ?? false) {
                        $actions[] = ['route' => 'astrologers.index', 'label' => 'Astrologers', 'icon' => 'M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155', 'color' => 'bg-indigo-50 text-indigo-600'];
                    }
                @endphp
                @foreach($actions as $action)
                    <a href="{{ route($action['route']) }}" class="group flex flex-col items-center gap-2 rounded-2xl border border-gray-100 bg-white p-4 text-center shadow-sm transition hover:border-cosmic-200 hover:shadow-md sm:p-5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl {{ $action['color'] }} transition group-hover:scale-110 sm:h-12 sm:w-12">
                            <svg class="h-5 w-5 sm:h-6 sm:w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $action['icon'] }}"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-700 sm:text-sm">{{ $action['label'] }}</span>
                    </a>
                @endforeach
            </div>

            {{-- Online astrologers --}}
            @if(($featureAstrologers ?? false) && $featuredAstrologers->isNotEmpty())
                <div>
                    <div class="mb-3 flex items-end justify-between sm:mb-4">
                        <div>
                            <h2 class="font-display text-base font-bold text-gray-900 sm:text-lg">Online Now</h2>
                            <p class="text-xs text-gray-500">Consult with astrologers available right now</p>
                        </div>
                        <a href="{{ route('astrologers.index') }}" class="text-xs font-medium text-cosmic-600 hover:text-cosmic-800 sm:text-sm">View All &rarr;</a>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-3">
                        @foreach($featuredAstrologers->take(3) as $astrologer)
                            <x-astrologer-card :astrologer="$astrologer" />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    @else
        {{-- ============ GUEST HOME ============ --}}

        {{-- Hero --}}
        <section class="relative overflow-hidden bg-gradient-to-br from-night-950 via-cosmic-950 to-night-900 px-4 py-20 text-center sm:py-28">
            <div class="pointer-events-none absolute inset-0 opacity-20">
                <div class="absolute top-[15%] left-[10%] h-1 w-1 rounded-full bg-gold-300 animate-pulse"></div>
                <div class="absolute top-[30%] right-[15%] h-1.5 w-1.5 rounded-full bg-cosmic-300 animate-pulse" style="animation-delay: 0.5s"></div>
                <div class="absolute top-[70%] left-[70%] h-1 w-1 rounded-full bg-gold-200 animate-pulse" style="animation-delay: 1s"></div>
                <div class="absolute top-[50%] left-[25%] h-0.5 w-0.5 rounded-full bg-white animate-pulse" style="animation-delay: 1.5s"></div>
            </div>
            <div class="relative mx-auto max-w-3xl">
                <h1 class="font-display text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                    Your Stars, <br>
                    <span class="bg-gradient-to-r from-gold-400 to-gold-200 bg-clip-text text-transparent">Your Guidance</span>
                </h1>
                <p class="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-cosmic-200">
                    Your personal Vedic astrology companion. Get personalized birth charts, daily predictions, and cosmic guidance.
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                    @if($featureAstrologers ?? false)
                        <x-button href="{{ route('astrologers.index') }}" variant="gold" size="lg">Browse Astrologers</x-button>
                    @endif
                    <x-button href="{{ route('login') }}" variant="gold" size="lg">Get Started</x-button>
                </div>
            </div>
        </section>

        {{-- Featured Astrologers --}}
        @if(($featureAstrologers ?? false) && $featuredAstrologers->isNotEmpty())
            <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="mb-8 flex items-end justify-between">
                    <div>
                        <h2 class="font-display text-2xl font-bold text-gray-900">Online Now</h2>
                        <p class="mt-1 text-sm text-gray-500">Consult with astrologers who are available right now</p>
                    </div>
                    <a href="{{ route('astrologers.index') }}" class="text-sm font-medium text-cosmic-600 hover:text-cosmic-800">View All &rarr;</a>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($featuredAstrologers as $astrologer)
                        <x-astrologer-card :astrologer="$astrologer" />
                    @endforeach
                </div>
            </section>
        @endif

        {{-- How it works --}}
        @if($featureAstrologers ?? false)
        <section class="border-t border-gray-100 bg-surface-alt px-4 py-16 sm:px-6">
            <div class="mx-auto max-w-4xl text-center">
                <h2 class="font-display text-2xl font-bold text-gray-900">How It Works</h2>
                <div class="mt-10 grid grid-cols-1 gap-8 md:grid-cols-3">
                    @php
                        $steps = [
                            ['step' => '1', 'title' => 'Choose Your Astrologer', 'desc' => 'Browse verified experts by specialty, language, rating, and availability.'],
                            ['step' => '2', 'title' => 'Start a Consultation', 'desc' => 'Recharge your wallet and begin a live chat session with your chosen astrologer.'],
                            ['step' => '3', 'title' => 'Get Personalized Guidance', 'desc' => 'Receive insights based on your birth chart, planetary positions, and Vedic wisdom.'],
                        ];
                    @endphp
                    @foreach($steps as $s)
                        <div class="flex flex-col items-center">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cosmic-100 font-display text-xl font-bold text-cosmic-600">{{ $s['step'] }}</div>
                            <h3 class="mt-4 font-display text-lg font-semibold text-gray-900">{{ $s['title'] }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-gray-500">{{ $s['desc'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif
    @endauth
</x-layouts.customer>
