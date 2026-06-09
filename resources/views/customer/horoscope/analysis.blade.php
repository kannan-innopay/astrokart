<x-layouts.customer title="Chart Analysis">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6"
         x-data="{
            locale: '{{ $locale }}',
            labels: {{ Js::from($allLabels) }},
            analysis: {{ Js::from($analysis) }},
            isPremium: {{ Js::from($isPremium) }},

            t(key) { return this.labels[this.locale]?.ui?.[key] ?? this.labels['en']?.ui?.[key] ?? key; },
            personality(idx) { return this.labels[this.locale]?.personality?.[idx] ?? this.labels['en']?.personality?.[idx] ?? ''; },
            moonNature(idx) { return this.labels[this.locale]?.moon_nature?.[idx] ?? this.labels['en']?.moon_nature?.[idx] ?? ''; },
            element(key) { return this.labels[this.locale]?.elements?.[key] ?? this.labels['en']?.elements?.[key] ?? ''; },
            dashaInterp(planet) { return this.labels[this.locale]?.dasha_interpretations?.[planet] ?? this.labels['en']?.dasha_interpretations?.[planet] ?? ''; },
            careerField(planet) { return this.labels[this.locale]?.career_fields?.[planet] ?? this.labels['en']?.career_fields?.[planet] ?? ''; },
            manglik(key) { return this.labels[this.locale]?.manglik_text?.[key] ?? this.labels['en']?.manglik_text?.[key] ?? ''; },
            disclaimer(key) { return this.labels[this.locale]?.disclaimers?.[key] ?? this.labels['en']?.disclaimers?.[key] ?? ''; },
            rashi(idx) { return this.labels[this.locale]?.rashis?.[idx] ?? this.labels['en']?.rashis?.[idx] ?? ''; },
            graha(name) { return this.labels[this.locale]?.grahas?.[name] ?? name; },
            nakshatra(idx) { return this.labels[this.locale]?.nakshatras?.[idx] ?? this.labels['en']?.nakshatras?.[idx] ?? ''; },
            bp(key, idx) { return this.labels[this.locale]?.birth_profile?.[key]?.[idx] ?? this.labels['en']?.birth_profile?.[key]?.[idx] ?? ''; },
            bpLabel(key) { return this.labels[this.locale]?.birth_profile?.[key] ?? this.labels['en']?.birth_profile?.[key] ?? key; },

            openDecade: null,
            toggleDecade(i) { this.openDecade = this.openDecade === i ? null : i; },

            switchLang(code) {
                this.locale = code;
                const url = new URL(window.location);
                url.searchParams.set('lang', code);
                window.history.replaceState({}, '', url);
            },
         }">

        <x-horoscope-nav />

        {{-- Language selector + title --}}
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <h1 class="font-display text-2xl font-bold text-gray-900" x-text="t('title')"></h1>
                @if($isPremium)
                    <span class="rounded-full bg-gold-100 px-3 py-1 text-xs font-bold text-gold-700" x-text="t('premium')"></span>
                @endif
            </div>

            @php
                $availableLocales = ['en' => 'English', 'hi' => 'हिन्दी', 'ta' => 'தமிழ்', 'te' => 'తెలుగు', 'ml' => 'മലയാളം', 'mr' => 'मराठी'];
            @endphp
            <div class="flex flex-wrap gap-1.5">
                @foreach($availableLocales as $code => $label)
                    <button @click="switchLang('{{ $code }}')"
                            :class="locale === '{{ $code }}'
                                ? 'bg-cosmic-600 text-white shadow-sm'
                                : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'"
                            class="rounded-lg px-3 py-1.5 text-xs font-medium transition">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        @unless($hasChart)
            <x-card class="mt-8 py-12 text-center">
                <p class="font-display text-lg font-bold text-gray-900" x-text="t('no_chart')"></p>
                <p class="mt-2 text-sm text-gray-500" x-text="t('no_chart_desc')"></p>
                <a href="{{ route('horoscope.show') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-cosmic-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-cosmic-700" x-text="t('go_to_horoscope')"></a>
            </x-card>
        @else
            @if(!$analysis)
                <x-card class="mt-6">
                    <div class="py-8 text-center">
                        <p class="text-gray-500" x-text="t('generating')"></p>
                        <a href="{{ route('horoscope.analysis') }}" class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-cosmic-600 hover:text-cosmic-700" x-text="t('refresh')"></a>
                    </div>
                </x-card>
            @else
                <div class="space-y-6">

                    {{-- ===== BIRTH PROFILE (all free) ===== --}}
                    @if($birthProfile)
                        {{-- Birth Details Summary --}}
                        <x-card>
                            <h2 class="font-display text-lg font-bold text-gray-900" x-text="bpLabel('section_title')"></h2>
                            <div class="mt-4 grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
                                <div>
                                    <p class="text-xs font-medium text-gray-500" x-text="bpLabel('nakshatra_title')"></p>
                                    <p class="font-semibold text-gray-900"><span x-text="nakshatra({{ $birthProfile['moon_nak_index'] }})"></span> <span class="text-xs font-normal text-gray-500" x-text="bpLabel('pada') + ' {{ $birthProfile['moon_nak_pada'] }}'"></span></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500" x-text="bpLabel('rasi_title')"></p>
                                    <p class="font-semibold text-gray-900" x-text="rashi({{ $birthProfile['moon_rashi_index'] }})"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500" x-text="bpLabel('rasi_lord_label')"></p>
                                    <p class="font-semibold text-gray-900" x-text="graha('{{ $birthProfile['rasi_lord'] ?? '' }}')"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500" x-text="bpLabel('ascendant')"></p>
                                    <p class="font-semibold text-gray-900" x-text="rashi({{ $birthProfile['lagna_rashi_index'] }})"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500" x-text="bpLabel('deity')"></p>
                                    <p class="font-semibold text-gray-900" x-text="bp('nakshatra_deity', {{ $birthProfile['moon_nak_index'] }})"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500" x-text="bpLabel('animal_sign')"></p>
                                    <p class="font-semibold text-gray-900" x-text="bp('nakshatra_animal', {{ $birthProfile['moon_nak_index'] }})"></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500" x-text="bpLabel('ganam')"></p>
                                    <p class="font-semibold text-gray-900" x-text="bp('nakshatra_ganam', {{ $birthProfile['moon_nak_index'] }})"></p>
                                </div>
                            </div>
                        </x-card>

                        {{-- Weekday --}}
                        <x-card>
                            <h2 class="font-display text-lg font-bold text-gray-900">
                                <span x-text="bpLabel('weekday_title')"></span>: {{ $birthProfile['vaara_raw'] }}
                            </h2>
                            <p class="mt-3 text-sm leading-relaxed text-gray-600" x-text="bp('weekday', {{ $birthProfile['weekday_index'] }})"></p>
                        </x-card>

                        {{-- Nakshatra detailed --}}
                        <x-card>
                            <h2 class="font-display text-lg font-bold text-gray-900">
                                <span x-text="bpLabel('nakshatra_title')"></span>: <span x-text="nakshatra({{ $birthProfile['moon_nak_index'] }})"></span>
                            </h2>
                            <p class="mt-3 text-sm leading-relaxed text-gray-600" x-text="bp('nakshatra_detailed', {{ $birthProfile['moon_nak_index'] }})"></p>
                        </x-card>

                        {{-- Rasi detailed --}}
                        <x-card>
                            <h2 class="font-display text-lg font-bold text-gray-900">
                                <span x-text="bpLabel('rasi_title')"></span>: <span x-text="rashi({{ $birthProfile['moon_rashi_index'] }})"></span>
                            </h2>
                            <p class="mt-3 text-sm leading-relaxed text-gray-600" x-text="bp('rasi_detailed', {{ $birthProfile['moon_rashi_index'] }})"></p>
                        </x-card>

                        {{-- Tithi --}}
                        <x-card>
                            <h2 class="font-display text-lg font-bold text-gray-900">
                                <span x-text="bpLabel('tithi_title')"></span>: {{ $birthProfile['tithi_raw'] }}
                            </h2>
                            <p class="mt-3 text-sm leading-relaxed text-gray-600" x-text="bp('tithi_detailed', {{ $birthProfile['tithi_index'] }})"></p>
                        </x-card>

                        {{-- Yoga --}}
                        <x-card>
                            <h2 class="font-display text-lg font-bold text-gray-900">
                                <span x-text="bpLabel('yoga_title')"></span>: {{ $birthProfile['yoga_raw'] }}
                            </h2>
                            <p class="mt-3 text-sm leading-relaxed text-gray-600" x-text="bp('yoga_detailed', {{ $birthProfile['yoga_index'] }})"></p>
                        </x-card>

                        {{-- Karana --}}
                        <x-card>
                            <h2 class="font-display text-lg font-bold text-gray-900">
                                <span x-text="bpLabel('karana_title')"></span>: {{ $birthProfile['karana_raw'] }}
                            </h2>
                            <p class="mt-3 text-sm leading-relaxed text-gray-600" x-text="bp('karana_detailed', {{ $birthProfile['karana_index'] }})"></p>
                        </x-card>
                    @endif

                    {{-- ===== PERSONALITY ===== --}}
                    <x-card>
                        <h2 class="font-display text-lg font-bold text-gray-900" x-text="t('personality')"></h2>

                        @php $lagnaIdx = $analysis['personality']['lagna_rashi_index'] ?? null; @endphp

                        {{-- Translated summary --}}
                        @if($lagnaIdx !== null)
                            <p class="mt-3 text-sm leading-relaxed text-gray-600">
                                <span x-text="t('with_ascendant')"></span>
                                <span class="font-medium" x-text="rashi({{ $lagnaIdx }})"></span>
                                <span x-text="t('as_ascendant')"></span>
                                @php
                                    $moonIdx = null;
                                    if (isset($analysis['personality']['moon_sign'])) {
                                        $moonNames = ['Aries','Taurus','Gemini','Cancer','Leo','Virgo','Libra','Scorpio','Sagittarius','Capricorn','Aquarius','Pisces'];
                                        $moonIdx = array_search($analysis['personality']['moon_sign'], $moonNames);
                                        if ($moonIdx === false) $moonIdx = null;
                                    }
                                @endphp
                                @if($moonIdx !== null)
                                    <span x-text="t('moon_sign_is')"></span>
                                    <span class="font-medium" x-text="rashi({{ $moonIdx }})"></span>,
                                @endif
                                <span x-text="t('your_element')"></span>
                                <span class="font-medium">{{ $analysis['personality']['dominant_element'] ?? '' }}</span>.
                            </p>

                            <div class="mt-4 rounded-xl bg-cosmic-50 p-4">
                                <p class="text-xs font-semibold text-cosmic-700">
                                    <span x-text="t('ascendant')"></span> (<span x-text="rashi({{ $lagnaIdx }})"></span>)
                                </p>
                                <p class="mt-1 text-sm text-cosmic-800" x-text="personality({{ $lagnaIdx }})"></p>
                            </div>
                        @endif

                        @if($isPremium)
                            @if($moonIdx !== null)
                                <div class="mt-3 rounded-xl bg-blue-50 p-4">
                                    <p class="text-xs font-semibold text-blue-700">
                                        <span x-text="t('moon_in')"></span> <span x-text="rashi({{ $moonIdx }})"></span>
                                    </p>
                                    <p class="mt-1 text-sm text-blue-800" x-text="moonNature({{ $moonIdx }})"></p>
                                </div>
                            @endif

                            @if(isset($analysis['personality']['dominant_element']))
                                <div class="mt-3 rounded-xl bg-amber-50 p-4">
                                    <p class="text-xs font-semibold text-amber-700">
                                        <span x-text="t('dominant_element')"></span>: {{ $analysis['personality']['dominant_element'] }}
                                    </p>
                                    <p class="mt-1 text-sm text-amber-800" x-text="element('{{ $analysis['personality']['dominant_element'] }}')"></p>
                                </div>
                            @endif
                        @endif
                    </x-card>

                    {{-- ===== CAREER ===== --}}
                    <x-card>
                        <h2 class="font-display text-lg font-bold text-gray-900" x-text="t('career')"></h2>

                        @php $tenthLord = $analysis['career']['tenth_house_lord'] ?? null; @endphp
                        @php $tenthHouse = $analysis['career']['tenth_lord_house'] ?? null; @endphp

                        {{-- Translated career summary --}}
                        @if($tenthLord)
                            <p class="mt-3 text-sm text-gray-600">
                                <span x-text="t('tenth_lord_is')"></span>
                                <span class="font-medium" x-text="graha('{{ $tenthLord }}')"></span>
                                @if($tenthHouse)
                                    — <span x-text="t('placed_in')"></span>
                                    {{ $tenthHouse }}<span x-text="t('house')"></span>
                                @endif
                            </p>
                        @endif

                        @if($isPremium)
                            {{-- Career fields from translation, based on dominant career planets --}}
                            @php
                                $careerPlanets = array_unique(array_filter([
                                    $tenthLord,
                                    $analysis['career']['second_lord'] ?? null,
                                    ...($analysis['career']['planets_in_10th'] ?? []),
                                ]));
                            @endphp
                            @if(!empty($careerPlanets))
                                <div class="mt-4">
                                    <p class="text-xs font-semibold text-gray-500" x-text="t('chart_suggests_career')"></p>
                                    @foreach($careerPlanets as $planet)
                                        <div class="mt-2">
                                            <p class="text-xs font-medium text-cosmic-600" x-text="graha('{{ $planet }}')"></p>
                                            <p class="mt-0.5 text-sm text-gray-600" x-text="careerField('{{ $planet }}')"></p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="mt-4 text-center">
                                <a href="{{ route('astrologers.index') }}" class="text-sm font-medium text-cosmic-600 hover:text-cosmic-700">
                                    <span x-text="t('consult_career')"></span> &rarr;
                                </a>
                            </div>
                        @else
                            @include('customer.horoscope.partials.premium-lock', ['feature' => 'career analysis'])
                        @endif
                    </x-card>

                    {{-- ===== MARRIAGE ===== --}}
                    <x-card>
                        <h2 class="font-display text-lg font-bold text-gray-900" x-text="t('marriage')"></h2>

                        @php $manglikStatus = $analysis['marriage']['manglik_status'] ?? null; @endphp
                        @php $seventhLord = $analysis['marriage']['seventh_house_lord'] ?? ($analysis['marriage']['seventh_lord'] ?? null); @endphp

                        {{-- Translated marriage summary --}}
                        @if($seventhLord)
                            <p class="mt-3 text-sm text-gray-600">
                                <span x-text="t('seventh_lord_is')"></span>
                                <span class="font-medium" x-text="graha('{{ $seventhLord }}')"></span>
                            </p>
                        @endif

                        @if($manglikStatus)
                            <div class="mt-3 inline-flex items-center gap-2 rounded-lg {{ $manglikStatus === 'non_manglik' ? 'bg-green-50 text-green-700' : ($manglikStatus === 'partial' ? 'bg-yellow-50 text-yellow-700' : 'bg-red-50 text-red-700') }} px-3 py-1.5 text-xs font-medium">
                                <span x-text="t('manglik_status')"></span>:
                                <span x-text="t('{{ $manglikStatus === 'non_manglik' ? 'non_manglik' : ($manglikStatus === 'partial' ? 'partial_manglik' : 'manglik') }}')"></span>
                            </div>
                        @endif

                        @if($isPremium)
                            @if($manglikStatus)
                                <p class="mt-3 text-sm text-gray-600" x-text="manglik('{{ $manglikStatus }}')"></p>
                            @endif
                            @if(isset($analysis['marriage']['venus_analysis']))
                                @php $venusHouse = $analysis['marriage']['venus_analysis']['house'] ?? null; @endphp
                                <div class="mt-3 rounded-xl bg-pink-50 p-4">
                                    <p class="text-xs font-semibold text-pink-700">
                                        <span x-text="t('venus_in')"></span>
                                        @if($venusHouse) {{ $venusHouse }}<span x-text="t('house')"></span> @endif
                                    </p>
                                </div>
                            @endif
                            <p class="mt-3 text-xs italic text-gray-400" x-text="disclaimer('marriage')"></p>
                            <div class="mt-3 text-center">
                                <a href="{{ route('astrologers.index') }}" class="text-sm font-medium text-cosmic-600 hover:text-cosmic-700">
                                    <span x-text="t('consult_marriage')"></span> &rarr;
                                </a>
                            </div>
                        @else
                            @include('customer.horoscope.partials.premium-lock', ['feature' => 'marriage analysis'])
                        @endif
                    </x-card>

                    {{-- ===== FINANCE ===== --}}
                    <x-card>
                        <h2 class="font-display text-lg font-bold text-gray-900" x-text="t('finance')"></h2>

                        @php $wealthPotential = $analysis['finance']['wealth_potential'] ?? null; @endphp
                        @php $secondLord = $analysis['finance']['second_lord'] ?? null; @endphp
                        @php $eleventhLord = $analysis['finance']['eleventh_lord'] ?? null; @endphp

                        {{-- Translated finance summary --}}
                        <div class="mt-3 flex flex-wrap gap-3">
                            @if($wealthPotential)
                                <div class="inline-flex items-center gap-2 rounded-lg {{ $wealthPotential === 'strong' ? 'bg-green-50 text-green-700' : ($wealthPotential === 'moderate' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-50 text-gray-700') }} px-3 py-1.5 text-xs font-medium">
                                    <span x-text="t('wealth_potential')"></span>: <span x-text="t('{{ $wealthPotential }}')"></span>
                                </div>
                            @endif
                        </div>

                        @if($secondLord || $eleventhLord)
                            <p class="mt-2 text-sm text-gray-600">
                                @if($secondLord)
                                    <span x-text="t('second_lord')"></span>:
                                    <span class="font-medium" x-text="graha('{{ $secondLord }}')"></span>
                                @endif
                                @if($secondLord && $eleventhLord) &middot; @endif
                                @if($eleventhLord)
                                    <span x-text="t('eleventh_lord')"></span>:
                                    <span class="font-medium" x-text="graha('{{ $eleventhLord }}')"></span>
                                @endif
                            </p>
                        @endif

                        @if($isPremium)
                            @if(!empty($analysis['finance']['dhana_yogas']))
                                <div class="mt-4">
                                    <p class="text-xs font-semibold text-gray-500" x-text="t('dhana_yogas')"></p>
                                    <ul class="mt-2 space-y-2">
                                        @foreach($analysis['finance']['dhana_yogas'] as $yoga)
                                            <li class="flex items-start gap-2 text-sm text-gray-600">
                                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-gold-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
                                                {{ $yoga }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <p class="mt-3 text-xs italic text-gray-400" x-text="disclaimer('finance')"></p>
                            <div class="mt-3 text-center">
                                <a href="{{ route('astrologers.index') }}" class="text-sm font-medium text-cosmic-600 hover:text-cosmic-700">
                                    <span x-text="t('consult_finance')"></span> &rarr;
                                </a>
                            </div>
                        @else
                            @include('customer.horoscope.partials.premium-lock', ['feature' => 'financial analysis'])
                        @endif
                    </x-card>

                    {{-- ===== WELLNESS ===== --}}
                    <x-card>
                        <h2 class="font-display text-lg font-bold text-gray-900" x-text="t('wellness')"></h2>

                        @php $vitalityScore = $analysis['wellness']['vitality_score'] ?? null; @endphp
                        @if($vitalityScore)
                            <p class="mt-3 text-sm text-gray-600">
                                <span x-text="t('vitality_score')"></span>: <span class="font-semibold">{{ $vitalityScore }}/10</span>
                            </p>
                        @endif

                        @if($isPremium && isset($analysis['wellness']['constitution']))
                            <div class="mt-3 rounded-xl bg-teal-50 p-4">
                                <p class="text-xs font-semibold text-teal-700" x-text="t('constitution')"></p>
                                <p class="mt-1 text-sm text-teal-800">{{ $analysis['wellness']['constitution'] }}</p>
                            </div>
                            <p class="mt-3 text-xs italic text-gray-400" x-text="disclaimer('wellness')"></p>
                        @elseif(!$isPremium)
                            @include('customer.horoscope.partials.premium-lock', ['feature' => 'wellness analysis'])
                        @endif
                    </x-card>

                    {{-- ===== DASHA ===== --}}
                    <x-card>
                        <h2 class="font-display text-lg font-bold text-gray-900" x-text="t('dasha')"></h2>

                        @if(isset($analysis['dasha']['current_mahadasha']))
                            {{-- Always show current dasha lord (even for free) --}}
                            <p class="mt-3 text-sm text-gray-600">
                                <span x-text="t('current_dasha_is')"></span>:
                                <span class="font-semibold" x-text="graha('{{ $analysis['dasha']['current_mahadasha']['lord'] }}')"></span>
                                ({{ $analysis['dasha']['current_mahadasha']['start_date'] }} — {{ $analysis['dasha']['current_mahadasha']['end_date'] }})
                            </p>
                        @endif

                        @if($isPremium && isset($analysis['dasha']['current_mahadasha']))
                            <div class="mt-4 rounded-xl bg-cosmic-50 p-4">
                                <p class="text-xs font-semibold text-cosmic-700">
                                    <span x-text="t('current_mahadasha')"></span>: <span x-text="graha('{{ $analysis['dasha']['current_mahadasha']['lord'] }}')"></span>
                                </p>
                                <p class="mt-2 text-sm text-cosmic-800" x-text="dashaInterp('{{ $analysis['dasha']['current_mahadasha']['lord'] }}')"></p>
                            </div>

                            @if(isset($analysis['dasha']['current_antardasha']))
                                <div class="mt-3 rounded-xl bg-indigo-50 p-4">
                                    <p class="text-xs font-semibold text-indigo-700">
                                        <span x-text="t('current_antardasha')"></span>: <span x-text="graha('{{ $analysis['dasha']['current_antardasha']['lord'] }}')"></span>
                                    </p>
                                    <p class="mt-1 text-xs text-indigo-500">
                                        {{ $analysis['dasha']['current_antardasha']['start_date'] }} — {{ $analysis['dasha']['current_antardasha']['end_date'] }}
                                    </p>
                                </div>
                            @endif

                            @if(isset($analysis['dasha']['mahadashas']))
                                <div class="mt-5">
                                    <p class="text-xs font-semibold text-gray-500" x-text="t('dasha_timeline')"></p>
                                    <div class="mt-3 space-y-2">
                                        @foreach($analysis['dasha']['mahadashas'] as $md)
                                            @php
                                                $isCurrent = isset($analysis['dasha']['current_mahadasha'])
                                                    && $md['lord'] === $analysis['dasha']['current_mahadasha']['lord']
                                                    && $md['start_date'] === $analysis['dasha']['current_mahadasha']['start_date'];
                                            @endphp
                                            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm {{ $isCurrent ? 'bg-cosmic-100 font-medium text-cosmic-800' : 'text-gray-600' }}">
                                                <span class="w-16 shrink-0 font-mono text-xs">{{ \Carbon\Carbon::parse($md['start_date'])->format('Y') }}</span>
                                                <span class="w-20 shrink-0 font-semibold" x-text="graha('{{ $md['lord'] }}')"></span>
                                                <span class="text-xs text-gray-400">{{ $md['duration_years'] }} <span x-text="t('years')"></span></span>
                                                @if($isCurrent)
                                                    <span class="ml-auto rounded-full bg-cosmic-600 px-2 py-0.5 text-[10px] font-bold text-white" x-text="t('now')"></span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <div class="mt-4 text-center">
                                <a href="{{ route('astrologers.index') }}" class="text-sm font-medium text-cosmic-600 hover:text-cosmic-700">
                                    <span x-text="t('consult_dasha')"></span> &rarr;
                                </a>
                            </div>
                        @elseif(!$isPremium)
                            @include('customer.horoscope.partials.premium-lock', ['feature' => 'Dasha timeline'])
                        @endif
                    </x-card>

                    {{-- ===== DECADE PREDICTIONS ===== --}}
                    @php $decades = $analysis['decades'] ?? []; @endphp
                    @if(!empty($decades))
                        <h2 class="mt-2 font-display text-xl font-bold text-gray-900">Life Journey by Decade</h2>
                        <p class="text-sm text-gray-500">Predictions based on your Mahadasha periods and planetary transits</p>

                        @foreach($decades as $i => $decade)
                            @php
                                $isFree = $i < 3; // First 3 decades free
                                $canView = $isFree || $isPremium;
                            @endphp

                            @if($canView)
                                <x-card class="{{ ($decade['sade_sati'] ?? false) ? 'border-amber-200' : '' }}">
                                    {{-- Decade header (always visible, clickable to expand) --}}
                                    <button @click="toggleDecade({{ $i }})" class="flex w-full items-center justify-between text-left">
                                        <div>
                                            <p class="font-display text-base font-bold text-gray-900">
                                                Age {{ $decade['age_start'] }}-{{ $decade['age_end'] }}
                                                <span class="text-sm font-normal text-gray-400">({{ $decade['year_start'] }}-{{ $decade['year_end'] }})</span>
                                            </p>
                                            <div class="mt-1 flex flex-wrap items-center gap-2">
                                                <span class="rounded-full bg-cosmic-100 px-2.5 py-0.5 text-xs font-medium text-cosmic-700" x-text="graha('{{ $decade['primary_dasha'] }}') + ' Dasha'"></span>
                                                @if($decade['sade_sati'] ?? false)
                                                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">Sade Sati</span>
                                                @endif
                                                @if($decade['secondary_dasha'] ?? null)
                                                    <span class="rounded-full bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-700" x-text="'+ ' + graha('{{ $decade['secondary_dasha'] }}')"></span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="flex gap-0.5">
                                                @for($s = 1; $s <= 5; $s++)
                                                    <span class="h-2 w-2 rounded-full {{ $s <= ($decade['overall_score'] ?? 3) ? ($decade['overall_score'] >= 4 ? 'bg-green-500' : ($decade['overall_score'] <= 2 ? 'bg-red-400' : 'bg-yellow-400')) : 'bg-gray-200' }}"></span>
                                                @endfor
                                            </div>
                                            <svg class="h-5 w-5 text-gray-400 transition" :class="openDecade === {{ $i }} && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                                        </div>
                                    </button>

                                    {{-- Expanded content --}}
                                    <div x-show="openDecade === {{ $i }}" x-collapse x-cloak class="mt-4">
                                        @if($decade['dasha_transition'] ?? null)
                                            <div class="mb-3 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-medium text-indigo-700">
                                                {{ $decade['dasha_transition'] }}
                                            </div>
                                        @endif

                                        {{-- Life area scores --}}
                                        <div class="space-y-3">
                                            @foreach(['career' => 'Career', 'relationships' => 'Relationships', 'finance' => 'Finance', 'health' => 'Health', 'spiritual' => 'Spiritual Growth'] as $areaKey => $areaLabel)
                                                @if(isset($decade['areas'][$areaKey]))
                                                    @php $area = $decade['areas'][$areaKey]; @endphp
                                                    <div>
                                                        <div class="flex items-center justify-between">
                                                            <span class="text-xs font-semibold text-gray-500">{{ $areaLabel }}</span>
                                                            <div class="flex gap-0.5">
                                                                @for($s = 1; $s <= 5; $s++)
                                                                    <span class="h-1.5 w-1.5 rounded-full {{ $s <= ($area['score'] ?? 3) ? ($area['score'] >= 4 ? 'bg-green-500' : ($area['score'] <= 2 ? 'bg-red-400' : 'bg-yellow-400')) : 'bg-gray-200' }}"></span>
                                                                @endfor
                                                            </div>
                                                        </div>
                                                        <p class="mt-1 text-sm leading-relaxed text-gray-600">{{ $area['prediction'] ?? '' }}</p>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>

                                        {{-- Transit effects --}}
                                        @if(!empty($decade['transit_effects']))
                                            <div class="mt-4">
                                                <p class="text-xs font-semibold text-gray-500">Planetary Transit Effects</p>
                                                <ul class="mt-1.5 space-y-1">
                                                    @foreach($decade['transit_effects'] as $effect)
                                                        <li class="flex items-start gap-2 text-xs text-gray-500">
                                                            <svg class="mt-0.5 h-3 w-3 shrink-0 text-cosmic-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
                                                            {{ $effect }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        {{-- Summary --}}
                                        <p class="mt-4 rounded-xl bg-gray-50 p-3 text-sm leading-relaxed text-gray-600">{{ $decade['summary'] ?? '' }}</p>
                                    </div>
                                </x-card>
                            @endif
                        @endforeach

                        {{-- Premium lock for remaining decades --}}
                        @if(!$isPremium && count($decades) > 3)
                            @include('customer.horoscope.partials.premium-lock', ['feature' => 'life predictions (ages 31-100)'])
                        @endif
                    @endif
                </div>

                <p class="mt-8 text-center text-xs text-gray-400" x-text="disclaimer('general')"></p>
            @endif
        @endunless
    </div>
</x-layouts.customer>
