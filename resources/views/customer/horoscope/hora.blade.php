<x-layouts.customer title="Daily Hora">
    <div class="mx-auto max-w-5xl px-3 py-5 sm:px-6 sm:py-8 lg:px-8">

        @php
            $availableLocales = ['en' => 'English', 'hi' => 'हिन्दी', 'ta' => 'தமிழ்', 'te' => 'తెలుగు', 'ml' => 'മലയാളം', 'mr' => 'मराठी'];

            $allLabels = [];
            foreach (array_keys($availableLocales) as $loc) {
                $hu = __('hora.ui', [], $loc);
                $hq = __('hora.qualities', [], $loc);
                $hm = __('hora.moods', [], $loc);
                $ht = __('hora.tips', [], $loc);
                $ha = __('hora.avoids', [], $loc);
                $hg = __('horoscope.grahas', [], $loc);
                $hf = __('hora.favorability', [], $loc);
                $hr = __('hora.reasons', [], $loc);
                $allLabels[$loc] = [
                    'ui' => is_array($hu) ? $hu : __('hora.ui', [], 'en'),
                    'qualities' => is_array($hq) ? $hq : __('hora.qualities', [], 'en'),
                    'moods' => is_array($hm) ? $hm : __('hora.moods', [], 'en'),
                    'tips' => is_array($ht) ? $ht : __('hora.tips', [], 'en'),
                    'avoids' => is_array($ha) ? $ha : __('hora.avoids', [], 'en'),
                    'grahas' => is_array($hg) ? $hg : __('horoscope.grahas', [], 'en'),
                    'favorability' => is_array($hf) ? $hf : __('hora.favorability', [], 'en'),
                    'reasons' => is_array($hr) ? $hr : __('hora.reasons', [], 'en'),
                ];
            }

            $grahaColors = [
                'Sun' => ['text-amber-700', 'bg-amber-50', 'border-amber-200'],
                'Moon' => ['text-slate-600', 'bg-slate-50', 'border-slate-200'],
                'Mars' => ['text-red-700', 'bg-red-50', 'border-red-200'],
                'Mercury' => ['text-emerald-700', 'bg-emerald-50', 'border-emerald-200'],
                'Jupiter' => ['text-yellow-700', 'bg-yellow-50', 'border-yellow-200'],
                'Venus' => ['text-pink-700', 'bg-pink-50', 'border-pink-200'],
                'Saturn' => ['text-indigo-700', 'bg-indigo-50', 'border-indigo-200'],
            ];

            $colorSwatches = [
                'Red' => 'bg-red-500', 'White' => 'bg-white border border-gray-300',
                'Green' => 'bg-emerald-500', 'Yellow' => 'bg-yellow-400',
                'Blue' => 'bg-blue-600',
            ];

            $favColors = [
                'excellent' => ['text-emerald-700', 'bg-emerald-50', 'border-emerald-300'],
                'favorable' => ['text-blue-700', 'bg-blue-50', 'border-blue-200'],
                'neutral' => ['text-gray-600', 'bg-gray-50', 'border-gray-200'],
                'caution' => ['text-amber-700', 'bg-amber-50', 'border-amber-200'],
            ];

            $current = $horaData['current_hora'];
            $currentColors = $grahaColors[$current['planet']] ?? ['text-gray-700', 'bg-gray-50', 'border-gray-200'];
        @endphp

        <div x-data="{
            locale: '{{ $locale }}',
            labels: {{ Js::from($allLabels) }},
            ui(k) { return this.labels[this.locale]?.ui[k] ?? k; },
            graha(n) { return this.labels[this.locale]?.grahas[n] ?? n; },
            quality(q) { return this.labels[this.locale]?.qualities[q] ?? q; },
            mood(m) { return this.labels[this.locale]?.moods[m] ?? m; },
            tip(p) { return this.labels[this.locale]?.tips[p] ?? ''; },
            avoid(p) { return this.labels[this.locale]?.avoids[p] ?? ''; },
            fav(f) { return this.labels[this.locale]?.favorability[f] ?? f; },
            reason(r) { return this.labels[this.locale]?.reasons[r] ?? r; },
            switchLang(code) {
                this.locale = code;
                const url = new URL(window.location);
                url.searchParams.set('lang', code);
                window.history.replaceState({}, '', url);
            },
        }" class="space-y-4 sm:space-y-6">

            {{-- Header --}}
            <div>
                <h1 class="font-display text-xl font-bold text-gray-900 sm:text-2xl" x-text="ui('title')"></h1>
                <p class="mt-0.5 text-xs text-gray-500 sm:text-sm" x-text="ui('subtitle')"></p>
            </div>

            {{-- Language selector --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
                <span class="shrink-0 text-[10px] font-medium text-gray-400 uppercase tracking-wider sm:text-xs">Language:</span>
                @foreach($availableLocales as $code => $name)
                    <button @click="switchLang('{{ $code }}')"
                            :class="locale === '{{ $code }}' ? 'bg-cosmic-600 text-white shadow-sm' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'"
                            class="shrink-0 rounded-lg px-2.5 py-1 text-[11px] font-medium transition sm:px-3 sm:py-1.5 sm:text-xs">{{ $name }}</button>
                @endforeach
            </div>

            {{-- Current Hora --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
                {{-- Top row: planet + time + badge --}}
                <div class="flex items-center gap-3 px-4 py-3.5 sm:px-5 sm:py-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-cosmic-50 sm:h-14 sm:w-14">
                        <div class="h-5 w-5 rounded-full sm:h-6 sm:w-6 {{ $colorSwatches[$current['data']['color']] ?? 'bg-gray-400' }}"></div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-2 w-2 shrink-0">
                                <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                            </span>
                            <h3 class="font-display text-base font-bold text-gray-900 sm:text-lg" x-text="graha('{{ $current['planet'] }}') + ' Hora'"></h3>
                        </div>
                        <div class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs text-gray-500">
                            <span>{{ $current['start_label'] }}–{{ $current['end_label'] }}</span>
                            <span class="text-gray-300">&middot;</span>
                            <span x-text="quality('{{ $current['data']['quality'] }}')"></span>
                            <span class="text-gray-300">&middot;</span>
                            <span class="font-medium text-gray-700" x-text="mood('{{ $current['data']['mood'] }}')"></span>
                        </div>
                    </div>
                    @if($current['personal'] ?? false)
                        @php
                            $cf = $current['personal']['favorability'];
                            $favBadge = [
                                'excellent' => 'bg-emerald-100 text-emerald-700',
                                'favorable' => 'bg-blue-100 text-blue-700',
                                'neutral' => 'bg-gray-100 text-gray-600',
                                'caution' => 'bg-amber-100 text-amber-700',
                            ];
                        @endphp
                        <div class="shrink-0 rounded-xl px-3 py-1.5 text-center {{ $favBadge[$cf] ?? 'bg-gray-100 text-gray-600' }}">
                            <p class="text-[8px] font-medium uppercase tracking-wider sm:text-[9px]" x-text="ui('for_you')"></p>
                            <p class="text-xs font-bold sm:text-sm" x-text="fav('{{ $cf }}')"></p>
                        </div>
                    @endif
                </div>

                {{-- Info pills --}}
                <div class="flex items-center gap-2 overflow-x-auto border-t border-gray-100 px-4 py-3 scrollbar-none sm:px-5">
                    <div class="flex shrink-0 items-center gap-1.5 rounded-lg bg-gray-50 px-2.5 py-1.5">
                        <div class="h-3.5 w-3.5 rounded-full {{ $colorSwatches[$current['data']['color']] ?? 'bg-gray-400' }}"></div>
                        <span class="text-xs text-gray-700">{{ $current['data']['color'] }}</span>
                    </div>
                    <div class="shrink-0 rounded-lg bg-gray-50 px-2.5 py-1.5 text-xs text-gray-700">
                        <span class="text-gray-400" x-text="ui('day_lord') + ': '"></span><span class="font-medium" x-text="graha('{{ $horaData['day_lord'] }}')"></span>
                    </div>
                    @if($current['personal'] ?? false)
                        @foreach($current['personal']['reasons'] as $reason)
                            <span class="shrink-0 rounded-lg bg-cosmic-50 px-2.5 py-1.5 text-[11px] text-cosmic-700" x-text="reason('{{ $reason }}')"></span>
                        @endforeach
                    @endif
                </div>

                {{-- Do / Avoid --}}
                <div class="grid grid-cols-2 gap-px border-t border-gray-100 bg-gray-100">
                    <div class="bg-white p-3 sm:p-4">
                        <div class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 shrink-0 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            <span class="text-[10px] font-semibold text-emerald-600 uppercase sm:text-xs" x-text="ui('what_to_do')"></span>
                        </div>
                        <p class="mt-1 text-[11px] leading-snug text-gray-600 sm:text-xs" x-text="tip('{{ $current['planet'] }}')"></p>
                    </div>
                    <div class="bg-white p-3 sm:p-4">
                        <div class="flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 shrink-0 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                            <span class="text-[10px] font-semibold text-red-500 uppercase sm:text-xs" x-text="ui('what_to_avoid')"></span>
                        </div>
                        <p class="mt-1 text-[11px] leading-snug text-gray-600 sm:text-xs" x-text="avoid('{{ $current['planet'] }}')"></p>
                    </div>
                </div>
            </div>

            {{-- Personalization note --}}
            @if($horaData['personalized'])
                <div class="flex items-center gap-2 rounded-lg bg-cosmic-50 px-3 py-2 text-[10px] font-medium text-cosmic-700 sm:px-4 sm:py-2.5 sm:text-xs">
                    <svg class="h-3.5 w-3.5 shrink-0 sm:h-4 sm:w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z"/></svg>
                    <span x-text="ui('personalized')"></span>
                </div>
            @else
                <div class="rounded-lg bg-gray-50 px-3 py-2 text-[10px] text-gray-500 sm:px-4 sm:py-2.5 sm:text-xs" x-text="ui('generic')"></div>
            @endif

            {{-- Hora table --}}
            <x-card :padding="false">
                <x-slot:header>
                    <h3 class="font-display text-base font-semibold text-gray-900 sm:text-lg" x-text="ui('hora_table')"></h3>
                </x-slot:header>

                {{-- Day horas --}}
                <div class="px-3 pt-3 pb-1 sm:px-6 sm:pt-4 sm:pb-2">
                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wider sm:text-xs" x-text="ui('day_horas')"></p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm">
                        <thead class="border-b border-gray-100 bg-gray-50/50 text-left">
                            <tr>
                                <th class="px-3 py-1.5 font-medium text-gray-500 sm:px-6 sm:py-2" x-text="ui('time')"></th>
                                <th class="px-3 py-1.5 font-medium text-gray-500 sm:px-6 sm:py-2" x-text="ui('planet')"></th>
                                <th class="hidden px-3 py-1.5 font-medium text-gray-500 sm:table-cell sm:px-6 sm:py-2" x-text="ui('quality')"></th>
                                <th class="px-3 py-1.5 font-medium text-gray-500 sm:px-6 sm:py-2" x-text="ui('lucky_color')"></th>
                                @if($horaData['personalized'])
                                    <th class="px-2 py-1.5 font-medium text-gray-500 sm:px-6 sm:py-2" x-text="ui('for_you')"></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($horaData['horas'] as $hora)
                                @if(!$hora['is_day'])
                                    @break
                                @endif
                                @include('customer.horoscope._hora-row', ['hora' => $hora, 'grahaColors' => $grahaColors, 'colorSwatches' => $colorSwatches, 'favColors' => $favColors, 'personalized' => $horaData['personalized']])
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Night horas --}}
                <div class="px-3 pt-3 pb-1 border-t border-gray-100 sm:px-6 sm:pt-4 sm:pb-2">
                    <p class="text-[10px] font-medium text-gray-400 uppercase tracking-wider sm:text-xs" x-text="ui('night_horas')"></p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs sm:text-sm">
                        <tbody class="divide-y divide-gray-50">
                            @foreach($horaData['horas'] as $hora)
                                @if($hora['is_day'])
                                    @continue
                                @endif
                                @include('customer.horoscope._hora-row', ['hora' => $hora, 'grahaColors' => $grahaColors, 'colorSwatches' => $colorSwatches, 'favColors' => $favColors, 'personalized' => $horaData['personalized']])
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.customer>
