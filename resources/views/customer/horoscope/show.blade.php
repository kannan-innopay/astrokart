<x-layouts.customer title="My Birth Chart">
    <div class="mx-auto max-w-5xl px-3 py-5 sm:px-6 sm:py-8 lg:px-8">
        <div class="mb-4 sm:mb-6">
            <div class="flex items-start justify-between gap-2">
                <h1 class="font-display text-xl font-bold text-gray-900 sm:text-2xl">My Birth Chart</h1>
                @if($chart)
                    <form method="POST" action="{{ route('horoscope.regenerate') }}" class="shrink-0">
                        @csrf
                        <x-button type="submit" variant="secondary" size="sm">Regenerate</x-button>
                    </form>
                @endif
            </div>
            <p class="mt-0.5 text-xs text-gray-500 sm:text-sm">Vedic astrology birth chart (Kundali)</p>
            @if($chart)
                <a href="{{ route('horoscope.analysis') }}" class="mt-2 inline-flex items-center gap-1 rounded-lg bg-gradient-to-r from-gold-500 to-gold-600 px-4 py-2 text-sm font-semibold text-night-950 shadow transition hover:from-gold-600 hover:to-gold-700">
                    View Detailed Analysis
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
            @endif
        </div>

        @if(!$chart)
            <x-card>
                @if(!$hasBirthCoordinates)
                    <x-empty-state
                        title="Birth coordinates needed"
                        description="Add your birth place latitude and longitude in your profile to generate your Vedic birth chart. You can find coordinates by searching your birth city on Google Maps."
                        icon="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"
                    >
                        <x-slot:action>
                            <x-button href="{{ route('profile.edit') }}" variant="primary">Add Birth Coordinates</x-button>
                        </x-slot:action>
                    </x-empty-state>
                @else
                    <x-empty-state
                        title="Chart generation failed"
                        description="Something went wrong generating your chart. Make sure the horoscope service is running and try again."
                        icon="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"
                    >
                        <x-slot:action>
                            <form method="POST" action="{{ route('horoscope.regenerate') }}">
                                @csrf
                                <x-button type="submit" variant="primary">Try Again</x-button>
                            </form>
                        </x-slot:action>
                    </x-empty-state>
                @endif
            </x-card>
        @else
            @php
                $availableLocales = [
                    'en' => 'English',
                    'hi' => 'हिन्दी',
                    'ta' => 'தமிழ்',
                    'te' => 'తెలుగు',
                    'ml' => 'മലയാളം',
                    'mr' => 'मराठी',
                ];

                // Build all locale label sets as JSON for Alpine
                $allLabels = [];
                foreach (array_keys($availableLocales) as $loc) {
                    $r = __('horoscope.rashis', [], $loc);
                    $g = __('horoscope.grahas', [], $loc);
                    $gs = __('horoscope.grahas_short', [], $loc);
                    $n = __('horoscope.nakshatras', [], $loc);
                    $pl = __('horoscope.panchanga_labels', [], $loc);
                    $ti = __('horoscope.tithis', [], $loc);
                    $yo = __('horoscope.yogas', [], $loc);
                    $ka = __('horoscope.karanas', [], $loc);
                    $va = __('horoscope.vaaras', [], $loc);
                    $ui = __('horoscope.ui', [], $loc);
                    $allLabels[$loc] = [
                        'rashis' => is_array($r) ? $r : __('horoscope.rashis', [], 'en'),
                        'grahas' => is_array($g) ? $g : __('horoscope.grahas', [], 'en'),
                        'grahas_short' => is_array($gs) ? $gs : __('horoscope.grahas_short', [], 'en'),
                        'nakshatras' => is_array($n) ? $n : __('horoscope.nakshatras', [], 'en'),
                        'panchanga_labels' => is_array($pl) ? $pl : __('horoscope.panchanga_labels', [], 'en'),
                        'tithis' => is_array($ti) ? $ti : __('horoscope.tithis', [], 'en'),
                        'yogas' => is_array($yo) ? $yo : __('horoscope.yogas', [], 'en'),
                        'karanas' => is_array($ka) ? $ka : __('horoscope.karanas', [], 'en'),
                        'vaaras' => is_array($va) ? $va : __('horoscope.vaaras', [], 'en'),
                        'ui' => is_array($ui) ? $ui : __('horoscope.ui', [], 'en'),
                    ];
                }

                // Build house-to-planets mapping with graha keys (not pre-abbreviated)
                $housePlanets = [];
                foreach ($chart['grahas'] as $graha) {
                    $houseNum = $graha['house'];
                    $housePlanets[$houseNum][] = [
                        'key' => $graha['name'],
                        'retrograde' => $graha['is_retrograde'],
                    ];
                }

                $lagnaSignIndex = $chart['lagna']['rashi']['index'];
                $signToHouse = [];
                for ($i = 0; $i < 12; $i++) {
                    $signToHouse[($lagnaSignIndex + $i) % 12] = $i + 1;
                }

                // South Indian: fixed sign positions as [col, row]
                $southCells = [
                    11 => [0, 0], 0 => [1, 0], 1 => [2, 0], 2 => [3, 0],
                    3 => [3, 1], 4 => [3, 2],
                    5 => [3, 3], 6 => [2, 3], 7 => [1, 3], 8 => [0, 3],
                    9 => [0, 2], 10 => [0, 1],
                ];

                $cellSize = 100;
            @endphp

            <div x-data="{
                style: 'south',
                locale: '{{ $locale }}',
                labels: {{ Js::from($allLabels) }},
                rashi(index) { return this.labels[this.locale]?.rashis[index] ?? ''; },
                graha(name) { return this.labels[this.locale]?.grahas[name] ?? name; },
                grahaShort(name, retro) {
                    const s = this.labels[this.locale]?.grahas_short[name] ?? name.substring(0, 2);
                    return retro ? s + '(R)' : s;
                },
                grahaLabel(name, retro) {
                    const s = this.labels[this.locale]?.grahas[name] ?? name;
                    return retro ? s + ' (R)' : s;
                },
                nakshatra(index) { return this.labels[this.locale]?.nakshatras[index] ?? ''; },
                pLabel(key) { return this.labels[this.locale]?.panchanga_labels[key] ?? key; },
                tithi(val) { return this.labels[this.locale]?.tithis[val] ?? val; },
                yoga(val) { return this.labels[this.locale]?.yogas[val] ?? val; },
                karana(val) { return this.labels[this.locale]?.karanas[val] ?? val; },
                vaara(val) { return this.labels[this.locale]?.vaaras[val] ?? val; },
                ui(key) { return this.labels[this.locale]?.ui[key] ?? key; },
                switchLang(code) {
                    this.locale = code;
                    const url = new URL(window.location);
                    url.searchParams.set('lang', code);
                    window.history.replaceState({}, '', url);
                },
            }" class="space-y-6">

                {{-- Language selector --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none">
                    <span class="shrink-0 text-[10px] font-medium text-gray-400 uppercase tracking-wider sm:text-xs" x-text="ui('language') + ':'"></span>
                    @foreach($availableLocales as $code => $name)
                        <button @click="switchLang('{{ $code }}')"
                                :class="locale === '{{ $code }}'
                                    ? 'bg-cosmic-600 text-white shadow-sm'
                                    : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-200'"
                                class="shrink-0 rounded-lg px-2.5 py-1 text-[11px] font-medium transition sm:px-3 sm:py-1.5 sm:text-xs">
                            {{ $name }}
                        </button>
                    @endforeach
                </div>

                {{-- Panchanga summary (horizontal scroll on mobile) --}}
                <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-none sm:grid sm:grid-cols-5 sm:gap-3 sm:overflow-visible">
                    @php
                        $moonNakIndex = 0;
                        foreach ($chart['grahas'] ?? [] as $g) {
                            if ($g['name'] === 'Moon') {
                                $moonNakIndex = $g['nakshatra']['index'] ?? 0;
                                break;
                            }
                        }
                        $panchangaItems = [
                            ['key' => 'tithi', 'value' => "tithi('" . ($chart['panchanga']['tithi'] ?? '') . "')"],
                            ['key' => 'nakshatra', 'value' => "nakshatra({$moonNakIndex})"],
                            ['key' => 'yoga', 'value' => "yoga('" . ($chart['panchanga']['yoga'] ?? '') . "')"],
                            ['key' => 'karana', 'value' => "karana('" . ($chart['panchanga']['karana'] ?? '') . "')"],
                            ['key' => 'vaara', 'value' => "vaara('" . ($chart['panchanga']['vaara'] ?? '') . "')"],
                        ];
                    @endphp
                    @foreach($panchangaItems as $item)
                        <div class="shrink-0 rounded-xl border border-cosmic-100 bg-white px-3 py-2 text-center sm:px-4 sm:py-3">
                            <p class="text-[9px] font-medium tracking-wider text-cosmic-400 uppercase whitespace-nowrap sm:text-[10px]" x-text="pLabel('{{ $item['key'] }}')"></p>
                            <p class="mt-0.5 text-xs font-semibold text-gray-900 whitespace-nowrap sm:text-sm" x-text="{{ $item['value'] }}"></p>
                        </div>
                    @endforeach
                </div>

                {{-- Lagna info --}}
                <x-card>
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-gold-400 to-gold-600 text-xs font-bold text-night-950 sm:h-14 sm:w-14 sm:text-sm" x-text="ui('asc')"></div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs text-gray-500 sm:text-sm" x-text="ui('ascendant')"></p>
                            <p class="font-display text-base font-bold text-gray-900 sm:text-lg" x-text="rashi({{ $chart['lagna']['rashi']['index'] }})"></p>
                            <p class="text-[11px] text-gray-500 sm:text-xs">
                                <span x-text="nakshatra({{ $chart['lagna']['nakshatra']['index'] }})"></span>
                                — <span x-text="ui('pada')"></span> {{ $chart['lagna']['nakshatra']['pada'] }}
                            </p>
                        </div>
                        <div class="shrink-0 text-right">
                            <p class="text-[10px] text-gray-400 sm:text-xs" x-text="ui('ayanamsa')"></p>
                            <p class="text-xs font-medium text-gray-700 sm:text-sm">{{ number_format($chart['ayanamsa'], 4) }}°</p>
                        </div>
                    </div>
                </x-card>

                {{-- Birth Chart --}}
                <x-card title="Birth Chart (Rasi)">
                    <x-slot:header>
                        <div class="flex items-center justify-between gap-2">
                            <h3 class="font-display text-base font-semibold text-gray-900 sm:text-lg" x-text="ui('birth_chart')"></h3>
                            <div class="flex shrink-0 gap-1 rounded-lg bg-gray-100 p-0.5 sm:p-1">
                                <button @click="style = 'south'"
                                        :class="style === 'south' ? 'bg-white shadow-sm text-cosmic-700' : 'text-gray-500'"
                                        class="rounded-md px-2 py-1 text-[11px] font-medium transition whitespace-nowrap sm:px-3 sm:text-xs">
                                    <span class="sm:hidden">South</span>
                                    <span class="hidden sm:inline" x-text="ui('south_indian')"></span>
                                </button>
                                <button @click="style = 'north'"
                                        :class="style === 'north' ? 'bg-white shadow-sm text-cosmic-700' : 'text-gray-500'"
                                        class="rounded-md px-2 py-1 text-[11px] font-medium transition whitespace-nowrap sm:px-3 sm:text-xs">
                                    <span class="sm:hidden">North</span>
                                    <span class="hidden sm:inline" x-text="ui('north_indian')"></span>
                                </button>
                            </div>
                        </div>
                    </x-slot:header>

                    {{-- South Indian Chart --}}
                    <div x-show="style === 'south'">
                        <div class="mx-auto max-w-lg">
                            @php $cs = 120; $svgSize = $cs * 4; @endphp
                            <svg viewBox="0 0 {{ $svgSize }} {{ $svgSize }}" class="w-full" xmlns="http://www.w3.org/2000/svg">
                                <rect x="0" y="0" width="{{ $svgSize }}" height="{{ $svgSize }}" fill="white" stroke="#d1d5db" stroke-width="1.5" rx="4"/>

                                {{-- Grid lines --}}
                                @for($i = 1; $i <= 3; $i++)
                                    <line x1="{{ $i * $cs }}" y1="0" x2="{{ $i * $cs }}" y2="{{ $svgSize }}" stroke="#e5e7eb" stroke-width="1"/>
                                    <line x1="0" y1="{{ $i * $cs }}" x2="{{ $svgSize }}" y2="{{ $i * $cs }}" stroke="#e5e7eb" stroke-width="1"/>
                                @endfor

                                {{-- Center box --}}
                                <rect x="{{ $cs }}" y="{{ $cs }}" width="{{ $cs * 2 }}" height="{{ $cs * 2 }}" fill="#faf9fc" stroke="#d1d5db" stroke-width="1.5"/>
                                <text x="{{ $svgSize / 2 }}" y="{{ $svgSize / 2 - 6 }}" text-anchor="middle" style="font-size: 13px; font-weight: 700; font-family: 'Playfair Display', serif;" fill="#6d28d9" x-text="ui('birth_chart')"></text>
                                <text x="{{ $svgSize / 2 }}" y="{{ $svgSize / 2 + 10 }}" text-anchor="middle" style="font-size: 9px; font-family: 'DM Sans', sans-serif;" fill="#9ca3af" x-text="ui('south_indian')"></text>

                                {{-- Sign cells --}}
                                @foreach($southCells as $signIdx => [$col, $row])
                                    @php
                                        $x = $col * $cs;
                                        $y = $row * $cs;
                                        $houseNum = $signToHouse[$signIdx] ?? 0;
                                        $planets = $housePlanets[$houseNum] ?? [];
                                        $isLagna = $signIdx === $lagnaSignIndex;
                                    @endphp

                                    @if($isLagna)
                                        <rect x="{{ $x + 1 }}" y="{{ $y + 1 }}" width="{{ $cs - 2 }}" height="{{ $cs - 2 }}" fill="#f5f0ff" rx="2"/>
                                    @endif

                                    {{-- Sign name --}}
                                    <text x="{{ $x + 5 }}" y="{{ $y + 13 }}" style="font-size: 9px; font-weight: 500; font-family: 'DM Sans', sans-serif;" fill="{{ $isLagna ? '#6d28d9' : '#9ca3af' }}" x-text="rashi({{ $signIdx }})"></text>

                                    @if($isLagna)
                                        <text x="{{ $x + $cs - 5 }}" y="{{ $y + 13 }}" text-anchor="end" style="font-size: 8px; font-weight: 700; font-family: 'DM Sans', sans-serif;" fill="#ca8a04" x-text="ui('asc')"></text>
                                    @endif

                                    {{-- Planet names (one per row, full names) --}}
                                    @foreach($planets as $i => $p)
                                        @php $py = $y + 30 + ($i * 15); @endphp
                                        <text x="{{ $x + 6 }}" y="{{ $py }}" style="font-size: 10px; font-weight: 600; font-family: 'DM Sans', sans-serif;" fill="#4c1d95" x-text="grahaLabel('{{ $p['key'] }}', {{ $p['retrograde'] ? 'true' : 'false' }})"></text>
                                    @endforeach
                                @endforeach
                            </svg>
                        </div>
                    </div>

                    {{-- North Indian Chart --}}
                    <div x-show="style === 'north'" x-cloak>
                        <div class="mx-auto max-w-lg">
                            <svg viewBox="0 0 480 480" class="w-full" xmlns="http://www.w3.org/2000/svg">
                                <rect x="0" y="0" width="480" height="480" fill="white" stroke="#d1d5db" stroke-width="1.5"/>
                                <line x1="0" y1="0" x2="480" y2="480" stroke="#d1d5db" stroke-width="1"/>
                                <line x1="480" y1="0" x2="0" y2="480" stroke="#d1d5db" stroke-width="1"/>
                                <polygon points="240,0 480,240 240,480 0,240" fill="none" stroke="#d1d5db" stroke-width="1.5"/>

                                @php
                                    $northHousePositions = [
                                        1 => [240, 108], 2 => [120, 55], 3 => [55, 120],
                                        4 => [120, 240], 5 => [55, 360], 6 => [120, 424],
                                        7 => [240, 372], 8 => [360, 424], 9 => [424, 360],
                                        10 => [360, 240], 11 => [424, 120], 12 => [360, 55],
                                    ];
                                @endphp

                                @for($h = 1; $h <= 12; $h++)
                                    @php
                                        [$cx, $cy] = $northHousePositions[$h];
                                        $planets = $housePlanets[$h] ?? [];
                                        $signIdx = ($lagnaSignIndex + $h - 1) % 12;
                                    @endphp
                                    <text x="{{ $cx }}" y="{{ $cy - 12 }}" text-anchor="middle" style="font-size: 9px; font-family: 'DM Sans';" fill="#9ca3af" x-text="rashi({{ $signIdx }})"></text>
                                    @foreach($planets as $i => $p)
                                        <text x="{{ $cx }}" y="{{ $cy + 3 + ($i * 14) }}" text-anchor="middle" style="font-size: 10px; font-weight: 600; font-family: 'DM Sans';" fill="#4c1d95" x-text="grahaLabel('{{ $p['key'] }}', {{ $p['retrograde'] ? 'true' : 'false' }})"></text>
                                    @endforeach
                                @endfor

                                <text x="240" y="234" text-anchor="middle" style="font-size: 13px; font-weight: 700; font-family: 'Playfair Display';" fill="#6d28d9" x-text="ui('birth_chart')"></text>
                                <text x="240" y="252" text-anchor="middle" style="font-size: 9px; font-family: 'DM Sans';" fill="#9ca3af" x-text="ui('north_indian')"></text>
                            </svg>
                        </div>
                    </div>
                </x-card>

                {{-- Planetary positions table --}}
                <x-card :padding="false">
                    <x-slot:header>
                        <h3 class="font-display text-base font-semibold text-gray-900 sm:text-lg" x-text="ui('planetary_positions')"></h3>
                    </x-slot:header>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs sm:text-sm">
                            <thead class="border-b border-gray-100 bg-gray-50/50 text-left">
                                <tr>
                                    <th class="px-3 py-2 font-medium text-gray-500 sm:px-6 sm:py-3" x-text="ui('graha')"></th>
                                    <th class="px-3 py-2 font-medium text-gray-500 sm:px-6 sm:py-3" x-text="ui('rashi')"></th>
                                    <th class="hidden px-3 py-2 font-medium text-gray-500 sm:table-cell sm:px-6 sm:py-3" x-text="ui('degrees')"></th>
                                    <th class="px-3 py-2 font-medium text-gray-500 sm:px-6 sm:py-3" x-text="ui('nakshatra')"></th>
                                    <th class="px-2 py-2 font-medium text-gray-500 sm:px-6 sm:py-3" x-text="ui('pada')"></th>
                                    <th class="px-2 py-2 font-medium text-gray-500 sm:px-6 sm:py-3" x-text="ui('house')"></th>
                                    <th class="px-2 py-2 font-medium text-gray-500 sm:px-6 sm:py-3" x-text="ui('motion')"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($chart['grahas'] as $graha)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-3 py-2 sm:px-6 sm:py-3">
                                            <div class="font-medium text-gray-900 whitespace-nowrap" x-text="graha('{{ $graha['name'] }}')"></div>
                                        </td>
                                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap sm:px-6 sm:py-3" x-text="rashi({{ $graha['rashi']['index'] }})"></td>
                                        <td class="hidden px-3 py-2 font-mono text-xs text-gray-600 sm:table-cell sm:px-6 sm:py-3">
                                            {{ number_format($graha['longitude'], 2) }}°
                                        </td>
                                        <td class="px-3 py-2 text-gray-700 whitespace-nowrap sm:px-6 sm:py-3" x-text="nakshatra({{ $graha['nakshatra']['index'] }})"></td>
                                        <td class="px-2 py-2 text-center text-gray-700 sm:px-6 sm:py-3">{{ $graha['nakshatra']['pada'] }}</td>
                                        <td class="px-2 py-2 text-center text-gray-700 sm:px-6 sm:py-3">{{ $graha['house'] }}</td>
                                        <td class="px-2 py-2 sm:px-6 sm:py-3">
                                            @if($graha['is_retrograde'])
                                                <span class="text-[10px] font-semibold text-red-600 sm:hidden">R</span>
                                                <span class="hidden sm:inline"><x-badge color="red"><span x-text="ui('retrograde')"></span></x-badge></span>
                                            @else
                                                <span class="text-[10px] font-semibold text-emerald-600 sm:hidden">D</span>
                                                <span class="hidden sm:inline"><x-badge color="green"><span x-text="ui('direct')"></span></x-badge></span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>
        @endif
    </div>
</x-layouts.customer>
