@props(['class' => ''])

@php
    // Constellation node stars (gold) — these are the points joined by the lines below.
    $nodes = [
        [90, 160], [170, 120], [250, 150], [330, 120], [360, 210], [280, 250],
        [760, 140], [840, 180], [900, 120], [870, 250], [790, 260],
        [120, 780], [210, 740], [300, 800], [380, 760],
        [680, 820], [760, 860], [840, 800], [900, 860],
    ];
    // Scattered faint stars (white) for depth.
    $scatter = [
        [500, 300], [450, 520], [600, 620], [520, 150], [150, 420],
        [820, 500], [300, 560], [700, 400], [430, 880], [560, 760], [640, 220], [200, 940],
    ];
@endphp

<div aria-hidden="true" {{ $attributes->merge(['class' => 'pointer-events-none absolute inset-0 overflow-hidden '.$class]) }}>
    <svg class="constellations h-full w-full" viewBox="0 0 1000 1000" preserveAspectRatio="xMidYMid slice" fill="none">
        {{-- Constellation lines --}}
        <g stroke="rgb(196 181 253)" stroke-width="0.7" stroke-opacity="0.22" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="90,160 170,120 250,150 330,120 360,210 280,250" />
            <polyline points="760,140 840,180 900,120 870,250 790,260 760,140" />
            <polyline points="120,780 210,740 300,800 380,760" />
            <polyline points="680,820 760,860 840,800 900,860" />
        </g>

        {{-- Node stars (gold, twinkling) --}}
        <g fill="rgb(253 224 71)">
            @foreach($nodes as $i => [$x, $y])
                <circle cx="{{ $x }}" cy="{{ $y }}" r="{{ $i % 3 === 0 ? '3' : '2.2' }}" class="star" style="animation-delay: {{ ($i % 6) * 0.6 }}s" />
            @endforeach
        </g>

        {{-- Scattered stars (white, twinkling) --}}
        <g fill="rgb(255 255 255)">
            @foreach($scatter as $i => [$x, $y])
                <circle cx="{{ $x }}" cy="{{ $y }}" r="1.6" class="star star--faint" style="animation-delay: {{ ($i % 5) * 0.8 }}s" />
            @endforeach
        </g>
    </svg>
</div>

@once
    @push('styles')
        <style>
            @keyframes constellation-twinkle {
                0%, 100% { opacity: 0.25; }
                50% { opacity: 1; }
            }
            @keyframes constellation-drift {
                0%, 100% { transform: translate3d(0, 0, 0); }
                50% { transform: translate3d(-1.5%, 1.2%, 0); }
            }
            .constellations { animation: constellation-drift 45s ease-in-out infinite; will-change: transform; }
            .constellations .star { animation: constellation-twinkle 5s ease-in-out infinite; }
            .constellations .star--faint { animation-duration: 7s; opacity: 0.4; }

            @media (prefers-reduced-motion: reduce) {
                .constellations,
                .constellations .star { animation: none; }
            }
        </style>
    @endpush
@endonce
