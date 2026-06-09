<x-layouts.customer title="Transits & Forecast">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

        @php
            $availableLocales = ['en' => 'English', 'hi' => 'हिन्दी', 'ta' => 'தமிழ்', 'te' => 'తెలుగు', 'ml' => 'മലയാളം', 'mr' => 'मराठी'];

            $allLabels = [];
            foreach (array_keys($availableLocales) as $loc) {
                $ui = __('transit.ui', [], $loc);
                $fc = __('transit.forecasts', [], $loc);
                $sp = __('transit.sade_sati_phases', [], $loc);
                $tr = __('transit.triggers', [], $loc);
                $dy = __('transit.days', [], $loc);
                $gm = __('transit.gemstones', [], $loc);
                $cl = __('transit.colors', [], $loc);
                $hr = __('horoscope.rashis', [], $loc);
                $hg = __('horoscope.grahas', [], $loc);
                $hn = __('horoscope.nakshatras', [], $loc);
                $allLabels[$loc] = [
                    'ui' => is_array($ui) ? $ui : __('transit.ui', [], 'en'),
                    'forecasts' => is_array($fc) ? $fc : __('transit.forecasts', [], 'en'),
                    'sade_sati_phases' => is_array($sp) ? $sp : __('transit.sade_sati_phases', [], 'en'),
                    'triggers' => is_array($tr) ? $tr : __('transit.triggers', [], 'en'),
                    'days' => is_array($dy) ? $dy : __('transit.days', [], 'en'),
                    'gemstones' => is_array($gm) ? $gm : __('transit.gemstones', [], 'en'),
                    'colors' => is_array($cl) ? $cl : __('transit.colors', [], 'en'),
                    'rashis' => is_array($hr) ? $hr : __('horoscope.rashis', [], 'en'),
                    'grahas' => is_array($hg) ? $hg : __('horoscope.grahas', [], 'en'),
                    'nakshatras' => is_array($hn) ? $hn : __('horoscope.nakshatras', [], 'en'),
                ];
            }

            $grahaColors = [
                'Sun' => 'text-amber-600 bg-amber-50', 'Moon' => 'text-slate-600 bg-slate-50',
                'Mars' => 'text-red-600 bg-red-50', 'Mercury' => 'text-emerald-600 bg-emerald-50',
                'Jupiter' => 'text-yellow-700 bg-yellow-50', 'Venus' => 'text-pink-600 bg-pink-50',
                'Saturn' => 'text-indigo-700 bg-indigo-50', 'Rahu' => 'text-stone-700 bg-stone-50',
                'Ketu' => 'text-gray-600 bg-gray-100',
            ];
        @endphp

        <div x-data="{
            locale: '{{ $locale }}',
            labels: {{ Js::from($allLabels) }},
            ui(key) { return this.labels[this.locale]?.ui[key] ?? key; },
            rashi(i) { return this.labels[this.locale]?.rashis[i] ?? ''; },
            graha(n) { return this.labels[this.locale]?.grahas[n] ?? n; },
            nak(i) { return this.labels[this.locale]?.nakshatras[i] ?? ''; },
            forecast(key) { return this.labels[this.locale]?.forecasts[key] ?? ''; },
            sadeSatiPhase(p) { return this.labels[this.locale]?.sade_sati_phases[p] ?? ''; },
            day(d) { return this.labels[this.locale]?.days[d] ?? d; },
            gem(g) { return this.labels[this.locale]?.gemstones[g] ?? g; },
            color(c) { return this.labels[this.locale]?.colors[c] ?? c; },
            switchLang(code) {
                this.locale = code;
                const url = new URL(window.location);
                url.searchParams.set('lang', code);
                window.history.replaceState({}, '', url);
            },
        }" class="space-y-6">

            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="font-display text-2xl font-bold text-gray-900" x-text="ui('your_forecast')"></h1>
                    <p class="mt-1 text-sm text-gray-500" x-text="ui('current_transits')"></p>
                </div>
            </div>

            {{-- Language selector --}}
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">Language:</span>
                @foreach($availableLocales as $code => $name)
                    <button @click="switchLang('{{ $code }}')"
                            :class="locale === '{{ $code }}' ? 'bg-cosmic-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'"
                            class="rounded-lg px-3 py-1.5 text-xs font-medium transition">{{ $name }}</button>
                @endforeach
            </div>

            {{-- 1. Current Planetary Positions --}}
            @if($currentTransits)
                <x-card>
                    <x-slot:header>
                        <h3 class="font-display text-lg font-semibold text-gray-900" x-text="ui('current_transits')"></h3>
                    </x-slot:header>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($currentTransits['grahas'] as $g)
                            <div class="flex items-center gap-3 rounded-xl border border-gray-100 px-4 py-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg text-xs font-bold {{ $grahaColors[$g['name']] ?? 'text-gray-600 bg-gray-50' }}">
                                    {{ substr($g['name'], 0, 2) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900" x-text="graha('{{ $g['name'] }}')"></p>
                                    <p class="text-xs text-gray-500">
                                        <span x-text="rashi({{ $g['rashi_index'] }})"></span>
                                        &middot;
                                        <span x-text="nak({{ $g['nakshatra_index'] }})"></span>
                                    </p>
                                </div>
                                @if($g['is_retrograde'])
                                    <span class="rounded bg-red-50 px-1.5 py-0.5 text-[10px] font-medium text-red-600" x-text="ui('retrograde')"></span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif

            {{-- 2. Upcoming Major Transits --}}
            @if($upcomingEvents && !empty($upcomingEvents['events']))
                <x-card>
                    <x-slot:header>
                        <h3 class="font-display text-lg font-semibold text-gray-900" x-text="ui('upcoming_events')"></h3>
                    </x-slot:header>
                    <div class="space-y-3">
                        @foreach($upcomingEvents['events'] as $event)
                            <div class="flex items-center gap-4 rounded-xl border border-gray-100 px-4 py-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $grahaColors[$event['planet']] ?? 'bg-gray-50 text-gray-600' }} text-xs font-bold">
                                    {{ substr($event['planet'], 0, 2) }}
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-gray-900" x-text="graha('{{ $event['planet'] }}')"></p>
                                    <p class="text-xs text-gray-500">
                                        <span x-text="rashi({{ $event['from_rashi_index'] }})"></span>
                                        &rarr;
                                        <span x-text="rashi({{ $event['to_rashi_index'] }})"></span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($event['date'])->format('M d, Y') }}</p>
                                    @if($event['significance'])
                                        <p class="text-[10px] font-medium text-gold-600" x-text="ui('{{ $event['significance'] }}')"></p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif

            {{-- 3. Personal Transit Forecast --}}
            @auth
                @if($forecast)
                    {{-- Sade Sati Alert --}}
                    @if($forecast['sade_sati']['active'] ?? false)
                        <div class="rounded-2xl border border-indigo-200 bg-gradient-to-r from-indigo-50 to-cosmic-50 p-5">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                                </div>
                                <div>
                                    <h4 class="font-display text-base font-semibold text-indigo-900" x-text="ui('sade_sati') + ' — ' + ui('phase_{{ $forecast['sade_sati']['phase'] }}')"></h4>
                                    <p class="mt-1 text-sm leading-relaxed text-indigo-800" x-text="sadeSatiPhase('{{ $forecast['sade_sati']['phase'] }}')"></p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Ashtama Shani Alert --}}
                    @if($forecast['ashtama_shani'] ?? false)
                        <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-700">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-red-800" x-text="ui('ashtama_shani')"></p>
                            </div>
                        </div>
                    @endif

                    {{-- Transit Forecasts per planet --}}
                    <x-card>
                        <x-slot:header>
                            <h3 class="font-display text-lg font-semibold text-gray-900" x-text="ui('your_forecast')"></h3>
                        </x-slot:header>
                        <div class="space-y-4">
                            @foreach($forecast['transits'] as $transit)
                                <div class="rounded-xl border border-gray-100 p-4">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $grahaColors[$transit['planet']] ?? 'bg-gray-50 text-gray-600' }} text-xs font-bold">
                                            {{ substr($transit['planet'], 0, 2) }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-display text-sm font-semibold text-gray-900" x-text="graha('{{ $transit['planet'] }}')"></h4>
                                                @php
                                                    $effectColors = ['benefic' => 'green', 'malefic' => 'red', 'mixed' => 'yellow'];
                                                @endphp
                                                <x-badge :color="$effectColors[$transit['effect_level']] ?? 'gray'">
                                                    <span x-text="ui('{{ $transit['effect_level'] }}')"></span>
                                                </x-badge>
                                            </div>
                                            <p class="mt-0.5 text-xs text-gray-500">
                                                <span x-text="ui('house_from_moon')"></span>: {{ $transit['house_from_moon'] }}
                                                &middot;
                                                <span x-text="ui('house_from_lagna')"></span>: {{ $transit['house_from_lagna'] }}
                                                @if($transit['is_retrograde'])
                                                    &middot; <span class="text-red-500" x-text="ui('retrograde')"></span>
                                                @endif
                                            </p>
                                            <p class="mt-2 text-sm leading-relaxed text-gray-600" x-text="forecast('{{ $transit['forecast_key'] }}')"></p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-card>

                    {{-- 5. Pariharam & Remedies --}}
                    @if(!empty($remedies))
                        <x-card>
                            <x-slot:header>
                                <h3 class="font-display text-lg font-semibold text-gray-900" x-text="ui('remedies')"></h3>
                            </x-slot:header>
                            <div class="space-y-5">
                                @foreach($remedies as $remedy)
                                    <div class="rounded-xl border border-gray-100 bg-surface p-5">
                                        {{-- Header --}}
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $grahaColors[$remedy['planet']] ?? 'bg-gray-50 text-gray-600' }} text-xs font-bold">
                                                {{ substr($remedy['planet'], 0, 2) }}
                                            </div>
                                            <div>
                                                <h4 class="font-display text-sm font-semibold text-gray-900" x-text="graha('{{ $remedy['planet'] }}')"></h4>
                                                <p class="text-xs text-gray-500">
                                                    @if($remedy['trigger'] === 'sade_sati')
                                                        <span x-text="ui('sade_sati')"></span>
                                                    @elseif($remedy['trigger'] === 'ashtama_shani')
                                                        <span x-text="ui('ashtama_shani')"></span>
                                                    @else
                                                        {{ ucfirst(str_replace('_', ' ', $remedy['trigger'])) }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Remedy grid --}}
                                        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                            {{-- Temple --}}
                                            @if($remedy['temple'])
                                                <div class="rounded-lg border border-gray-100 bg-white p-3">
                                                    <p class="text-[10px] font-medium tracking-wider text-cosmic-400 uppercase" x-text="ui('temple')"></p>
                                                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $remedy['temple']['name'] }}</p>
                                                    <p class="text-xs text-gray-500">{{ $remedy['temple']['location'] }}</p>
                                                    <a href="https://www.google.com/maps?q={{ $remedy['temple']['lat'] }},{{ $remedy['temple']['lng'] }}" target="_blank" class="mt-1 inline-flex items-center gap-1 text-xs font-medium text-cosmic-600 hover:text-cosmic-800">
                                                        <span x-text="ui('view_on_map')"></span> &rarr;
                                                    </a>
                                                </div>
                                            @endif

                                            {{-- Mantra --}}
                                            <div class="rounded-lg border border-gray-100 bg-white p-3">
                                                <p class="text-[10px] font-medium tracking-wider text-cosmic-400 uppercase" x-text="ui('mantra')"></p>
                                                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $remedy['mantra'] }}</p>
                                                <p class="text-xs text-gray-500">{{ $remedy['mantra_count'] }} <span x-text="ui('times')"></span></p>
                                            </div>

                                            {{-- Gemstone + Color --}}
                                            <div class="rounded-lg border border-gray-100 bg-white p-3">
                                                <p class="text-[10px] font-medium tracking-wider text-cosmic-400 uppercase" x-text="ui('gemstone')"></p>
                                                <p class="mt-1 text-sm font-semibold text-gray-900" x-text="gem('{{ $remedy['gemstone'] }}')"></p>
                                                <p class="mt-2 text-[10px] font-medium tracking-wider text-cosmic-400 uppercase" x-text="ui('color_to_wear')"></p>
                                                <p class="text-sm text-gray-700" x-text="color('{{ $remedy['color'] }}')"></p>
                                            </div>

                                            {{-- Fasting + Deity --}}
                                            <div class="rounded-lg border border-gray-100 bg-white p-3">
                                                <p class="text-[10px] font-medium tracking-wider text-cosmic-400 uppercase" x-text="ui('fasting')"></p>
                                                <p class="mt-1 text-sm font-semibold text-gray-900" x-text="day('{{ $remedy['fasting'] }}')"></p>
                                                <p class="mt-2 text-[10px] font-medium tracking-wider text-cosmic-400 uppercase" x-text="ui('deity')"></p>
                                                <p class="text-sm text-gray-700">{{ $remedy['deity'] }}</p>
                                            </div>
                                        </div>

                                        {{-- Offerings & Donations --}}
                                        <div class="mt-3 grid grid-cols-2 gap-3">
                                            <div>
                                                <p class="text-[10px] font-medium tracking-wider text-cosmic-400 uppercase" x-text="ui('offerings')"></p>
                                                <ul class="mt-1 space-y-0.5 text-xs text-gray-600">
                                                    @foreach($remedy['offerings'] as $offering)
                                                        <li>{{ $offering }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-medium tracking-wider text-cosmic-400 uppercase" x-text="ui('donations')"></p>
                                                <ul class="mt-1 space-y-0.5 text-xs text-gray-600">
                                                    @foreach($remedy['donations'] as $donation)
                                                        <li>{{ $donation }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>

                                        {{-- Other temples --}}
                                        @if(!empty($remedy['other_temples']))
                                            <div class="mt-3 border-t border-gray-100 pt-3">
                                                <p class="text-[10px] font-medium tracking-wider text-cosmic-400 uppercase" x-text="ui('other_temples')"></p>
                                                @foreach($remedy['other_temples'] as $temple)
                                                    <div class="mt-1 flex items-center justify-between text-xs">
                                                        <span class="text-gray-700">{{ $temple['name'] }} — {{ $temple['location'] }}</span>
                                                        <a href="https://www.google.com/maps?q={{ $temple['lat'] }},{{ $temple['lng'] }}" target="_blank" class="text-cosmic-600 hover:text-cosmic-800">
                                                            <span x-text="ui('view_on_map')"></span>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </x-card>
                    @endif

                @else
                    {{-- No birth chart --}}
                    <x-card>
                        <x-empty-state
                            title=""
                            icon="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"
                        >
                            <x-slot:action>
                                <p class="mb-4 text-sm text-gray-500" x-text="ui('no_chart')"></p>
                                <x-button href="{{ route('horoscope.show') }}" variant="primary">View My Chart</x-button>
                            </x-slot:action>
                        </x-empty-state>
                    </x-card>
                @endif
            @else
                <x-card>
                    <x-empty-state title="Login Required" description="Login to see your personalized transit forecast and remedies.">
                        <x-slot:action>
                            <x-button href="{{ route('login') }}" variant="primary">Login</x-button>
                        </x-slot:action>
                    </x-empty-state>
                </x-card>
            @endauth
        </div>
    </div>
</x-layouts.customer>
