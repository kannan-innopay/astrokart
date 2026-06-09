<x-layouts.customer title="Birth Chart — {{ $chartData['user_name'] ?? 'User' }}">
    @php
        $allLabels = [];
        $locales = ['en' => 'English', 'hi' => 'हिन्दी', 'ta' => 'தமிழ்', 'te' => 'తెలుగు', 'ml' => 'മലയാളം', 'mr' => 'मराठी'];
        foreach (array_keys($locales) as $loc) {
            $r = __('horoscope.rashis', [], $loc);
            $g = __('horoscope.grahas', [], $loc);
            $allLabels[$loc] = [
                'rashis' => is_array($r) ? $r : __('horoscope.rashis', [], 'en'),
                'grahas' => is_array($g) ? $g : __('horoscope.grahas', [], 'en'),
            ];
        }

        $lagnaIndex = $chartData['lagna']['rashi']['index'] ?? 0;
        $signToHouse = [];
        for ($i = 0; $i < 12; $i++) {
            $signToHouse[($lagnaIndex + $i) % 12] = $i + 1;
        }
        $housePlanets = [];
        foreach ($chartData['grahas'] ?? [] as $graha) {
            $housePlanets[$graha['house']][] = [
                'key' => $graha['name'],
                'retrograde' => $graha['is_retrograde'] ?? false,
            ];
        }
        $southCells = [
            11 => [0, 0], 0 => [1, 0], 1 => [2, 0], 2 => [3, 0],
            3 => [3, 1], 4 => [3, 2],
            5 => [3, 3], 6 => [2, 3], 7 => [1, 3], 8 => [0, 3],
            9 => [0, 2], 10 => [0, 1],
        ];
        $cs = 120;
        $svgSize = $cs * 4;
    @endphp

    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8" x-data="{
        style: 'south',
        locale: 'en',
        labels: {{ Js::from($allLabels) }},
        rashi(i) { return this.labels[this.locale]?.rashis[i] ?? ''; },
        graha(n) { return this.labels[this.locale]?.grahas[n] ?? n; },
        grahaLabel(n, retro) { const s = this.graha(n); return retro ? s + ' (R)' : s; },
    }">
        {{-- Header --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-bold text-gray-900">Birth Chart — {{ $chartData['user_name'] ?? 'User' }}</h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $chartData['date_of_birth'] ?? '' }} &middot; {{ $chartData['time_of_birth'] ?? '' }} &middot; {{ $chartData['place_of_birth'] ?? '' }}
                </p>
                <p class="mt-0.5 text-xs text-amber-600">This link is valid only during the active consultation.</p>
            </div>
            <a href="{{ route('consultation.chat', $consultation) }}" class="text-sm font-medium text-cosmic-600 hover:text-cosmic-800">&larr; Back to Chat</a>
        </div>

        {{-- Controls --}}
        <div class="mb-6 flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-xs font-medium text-gray-400 uppercase">Language:</span>
                @foreach($locales as $code => $name)
                    <button @click="locale = '{{ $code }}'"
                            :class="locale === '{{ $code }}' ? 'bg-cosmic-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'"
                            class="rounded-lg px-3 py-1.5 text-xs font-medium transition">{{ $name }}</button>
                @endforeach
            </div>
            <div class="flex gap-1 rounded-lg bg-gray-100 p-1">
                <button @click="style = 'south'" :class="style === 'south' ? 'bg-white shadow-sm text-cosmic-700' : 'text-gray-500'" class="rounded-md px-3 py-1 text-xs font-medium transition">South Indian</button>
                <button @click="style = 'north'" :class="style === 'north' ? 'bg-white shadow-sm text-cosmic-700' : 'text-gray-500'" class="rounded-md px-3 py-1 text-xs font-medium transition">North Indian</button>
            </div>
        </div>

        {{-- Lagna + Panchanga --}}
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-card>
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-gold-400 to-gold-600 text-sm font-bold text-night-950">Asc</div>
                    <div>
                        <p class="text-xs text-gray-500">Ascendant (Lagna)</p>
                        <p class="font-display text-lg font-bold text-gray-900" x-text="rashi({{ $lagnaIndex }})"></p>
                    </div>
                </div>
            </x-card>
            @if($chartData['panchanga'] ?? false)
                <x-card>
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div><p class="text-gray-400">Nakshatra</p><p class="font-semibold text-gray-900">{{ $chartData['panchanga']['nakshatra'] }}</p></div>
                        <div><p class="text-gray-400">Tithi</p><p class="font-semibold text-gray-900">{{ $chartData['panchanga']['tithi'] }}</p></div>
                        <div><p class="text-gray-400">Yoga</p><p class="font-semibold text-gray-900">{{ $chartData['panchanga']['yoga'] ?? '—' }}</p></div>
                    </div>
                </x-card>
            @endif
        </div>

        {{-- Chart Grid --}}
        <x-card>
            {{-- SOUTH INDIAN --}}
            <div x-show="style === 'south'" class="mx-auto max-w-lg">
                <svg viewBox="0 0 {{ $svgSize }} {{ $svgSize }}" class="w-full aspect-square" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0" y="0" width="{{ $svgSize }}" height="{{ $svgSize }}" fill="white" stroke="#d1d5db" stroke-width="1.5" rx="4"/>
                    @for($i = 1; $i <= 3; $i++)
                        <line x1="{{ $i * $cs }}" y1="0" x2="{{ $i * $cs }}" y2="{{ $svgSize }}" stroke="#e5e7eb" stroke-width="1"/>
                        <line x1="0" y1="{{ $i * $cs }}" x2="{{ $svgSize }}" y2="{{ $i * $cs }}" stroke="#e5e7eb" stroke-width="1"/>
                    @endfor
                    <rect x="{{ $cs }}" y="{{ $cs }}" width="{{ $cs * 2 }}" height="{{ $cs * 2 }}" fill="#faf9fc" stroke="#d1d5db" stroke-width="1.5"/>
                    <text x="{{ $svgSize / 2 }}" y="{{ $svgSize / 2 - 4 }}" text-anchor="middle" style="font-size: 13px; font-weight: 700; font-family: 'Cormorant Garamond', serif;" fill="#6d28d9">Rasi Chart</text>
                    <text x="{{ $svgSize / 2 }}" y="{{ $svgSize / 2 + 12 }}" text-anchor="middle" style="font-size: 9px; font-family: 'Plus Jakarta Sans', sans-serif;" fill="#9ca3af">South Indian</text>

                    @foreach($southCells as $signIdx => [$col, $row])
                        @php
                            $x = $col * $cs; $y = $row * $cs;
                            $houseNum = $signToHouse[$signIdx] ?? 0;
                            $planets = $housePlanets[$houseNum] ?? [];
                            $isLagna = $signIdx === $lagnaIndex;
                        @endphp
                        @if($isLagna)
                            <rect x="{{ $x + 1 }}" y="{{ $y + 1 }}" width="{{ $cs - 2 }}" height="{{ $cs - 2 }}" fill="#f5f0ff" rx="2"/>
                        @endif
                        <text x="{{ $x + 5 }}" y="{{ $y + 13 }}" style="font-size: 9px; font-weight: 500; font-family: 'Plus Jakarta Sans';" fill="{{ $isLagna ? '#6d28d9' : '#9ca3af' }}" x-text="rashi({{ $signIdx }})"></text>
                        @if($isLagna)
                            <text x="{{ $x + $cs - 5 }}" y="{{ $y + 13 }}" text-anchor="end" style="font-size: 8px; font-weight: 700;" fill="#ca8a04">Asc</text>
                        @endif
                        @foreach($planets as $i => $p)
                            @php $py = $y + 30 + ($i * 15); @endphp
                            <text x="{{ $x + 6 }}" y="{{ $py }}" style="font-size: 10px; font-weight: 600; font-family: 'Plus Jakarta Sans';" fill="#4c1d95" x-text="grahaLabel('{{ $p['key'] }}', {{ $p['retrograde'] ? 'true' : 'false' }})"></text>
                        @endforeach
                    @endforeach
                </svg>
            </div>

            {{-- NORTH INDIAN --}}
            <div x-show="style === 'north'" x-cloak class="mx-auto max-w-lg">
                <svg viewBox="0 0 480 480" class="w-full aspect-square" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0" y="0" width="480" height="480" fill="white" stroke="#d1d5db" stroke-width="1.5"/>
                    <line x1="0" y1="0" x2="480" y2="480" stroke="#d1d5db" stroke-width="1"/>
                    <line x1="480" y1="0" x2="0" y2="480" stroke="#d1d5db" stroke-width="1"/>
                    <polygon points="240,0 480,240 240,480 0,240" fill="none" stroke="#d1d5db" stroke-width="1.5"/>
                    @php
                        $nhp = [1=>[240,108],2=>[120,55],3=>[55,120],4=>[120,240],5=>[55,360],6=>[120,424],7=>[240,372],8=>[360,424],9=>[424,360],10=>[360,240],11=>[424,120],12=>[360,55]];
                    @endphp
                    @for($h = 1; $h <= 12; $h++)
                        @php [$cx,$cy] = $nhp[$h]; $planets = $housePlanets[$h] ?? []; $signIdx = ($lagnaIndex + $h - 1) % 12; @endphp
                        <text x="{{ $cx }}" y="{{ $cy - 12 }}" text-anchor="middle" style="font-size: 9px; font-family: 'Plus Jakarta Sans';" fill="#9ca3af" x-text="rashi({{ $signIdx }})"></text>
                        @foreach($planets as $i => $p)
                            <text x="{{ $cx }}" y="{{ $cy + 3 + ($i * 14) }}" text-anchor="middle" style="font-size: 10px; font-weight: 600; font-family: 'Plus Jakarta Sans';" fill="#4c1d95" x-text="grahaLabel('{{ $p['key'] }}', {{ $p['retrograde'] ? 'true' : 'false' }})"></text>
                        @endforeach
                    @endfor
                    <text x="240" y="234" text-anchor="middle" style="font-size: 13px; font-weight: 700; font-family: 'Cormorant Garamond', serif;" fill="#6d28d9">Rasi Chart</text>
                    <text x="240" y="252" text-anchor="middle" style="font-size: 9px; font-family: 'Plus Jakarta Sans';" fill="#9ca3af">North Indian</text>
                </svg>
            </div>
        </x-card>

        {{-- Planetary Positions Table --}}
        <x-card class="mt-6" :padding="false">
            <x-slot:header><h3 class="font-display text-lg font-semibold text-gray-900">Planetary Positions</h3></x-slot:header>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-gray-100 bg-gray-50/50 text-left">
                        <tr>
                            <th class="px-6 py-3 font-medium text-gray-500">Graha</th>
                            <th class="px-6 py-3 font-medium text-gray-500">Rashi</th>
                            <th class="px-6 py-3 font-medium text-gray-500">Nakshatra</th>
                            <th class="px-6 py-3 font-medium text-gray-500">Pada</th>
                            <th class="px-6 py-3 font-medium text-gray-500">House</th>
                            <th class="px-6 py-3 font-medium text-gray-500">Motion</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($chartData['grahas'] ?? [] as $graha)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-3">
                                    <span class="font-medium text-gray-900" x-text="graha('{{ $graha['name'] }}')"></span>
                                    <span class="ml-1 text-xs text-gray-400">{{ $graha['sanskrit'] }}</span>
                                </td>
                                <td class="px-6 py-3" x-text="rashi({{ $graha['rashi']['index'] }})"></td>
                                <td class="px-6 py-3 text-gray-600">{{ $graha['nakshatra']['name'] }}</td>
                                <td class="px-6 py-3 text-center text-gray-600">{{ $graha['nakshatra']['pada'] }}</td>
                                <td class="px-6 py-3 text-center text-gray-600">{{ $graha['house'] }}</td>
                                <td class="px-6 py-3">
                                    <x-badge :color="($graha['is_retrograde'] ?? false) ? 'red' : 'green'">
                                        {{ ($graha['is_retrograde'] ?? false) ? 'Retrograde' : 'Direct' }}
                                    </x-badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.customer>
