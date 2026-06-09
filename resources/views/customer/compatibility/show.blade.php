<x-layouts.customer title="Compatibility Report">
    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6">
        {{-- Back link --}}
        <a href="{{ route('compatibility.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-cosmic-600 hover:text-cosmic-700">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to Compatibility
        </a>

        {{-- Title --}}
        <div class="mt-4 text-center">
            <h1 class="font-display text-3xl font-bold text-gray-900">Compatibility Report</h1>
            @if($report->partner_name)
                <p class="mt-2 text-gray-500">Match with <span class="font-medium text-gray-700">{{ $report->partner_name }}</span></p>
            @endif
        </div>

        {{-- Score gauge --}}
        @php
            $score = $report->score;
            $scoreColor = $score >= 24 ? 'text-green-600' : ($score >= 18 ? 'text-yellow-600' : 'text-red-600');
            $scoreBg = $score >= 24 ? 'bg-green-50 border-green-200' : ($score >= 18 ? 'bg-yellow-50 border-yellow-200' : 'bg-red-50 border-red-200');
            $scoreBarColor = $score >= 24 ? 'bg-green-500' : ($score >= 18 ? 'bg-yellow-500' : 'bg-red-500');
        @endphp

        <x-card class="mt-6 {{ $scoreBg }} text-center">
            <div class="py-4">
                <p class="text-sm font-medium text-gray-500">Overall Score</p>
                <div class="mt-2">
                    <span class="font-display text-6xl font-bold {{ $scoreColor }}">{{ $score }}</span>
                    <span class="text-2xl text-gray-400">/36</span>
                </div>
                <div class="mx-auto mt-4 h-3 max-w-xs overflow-hidden rounded-full bg-gray-200">
                    <div class="{{ $scoreBarColor }} h-full rounded-full transition-all" style="width: {{ round(($score / 36) * 100) }}%"></div>
                </div>
            </div>
        </x-card>

        {{-- Interpretation --}}
        @if($report->result_json['interpretation'] ?? null)
            <x-card class="mt-6">
                <h2 class="font-display text-lg font-bold text-gray-900">Interpretation</h2>
                <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ $report->result_json['interpretation'] }}</p>
            </x-card>
        @endif

        {{-- 8 Factor cards --}}
        @if(!empty($report->result_json['factors']))
            <div class="mt-6">
                <h2 class="font-display text-lg font-bold text-gray-900">Ashtakoota Factors</h2>
                <div class="mt-4 space-y-4">
                    @foreach($report->result_json['factors'] as $factor)
                        @php
                            $factorScore = $factor['score'] ?? 0;
                            $factorMax = $factor['max'] ?? 1;
                            $factorPct = round(($factorScore / $factorMax) * 100);
                            $factorBarColor = $factorPct >= 67 ? 'bg-green-500' : ($factorPct >= 34 ? 'bg-yellow-500' : 'bg-red-500');
                        @endphp
                        <x-card>
                            <div class="flex items-center justify-between">
                                <h3 class="font-display text-sm font-bold text-gray-900">{{ $factor['name_local'] ?? $factor['name'] ?? '' }}</h3>
                                <span class="text-sm font-semibold {{ $factorPct >= 67 ? 'text-green-600' : ($factorPct >= 34 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $factorScore }}/{{ $factorMax }}
                                </span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-200">
                                <div class="{{ $factorBarColor }} h-full rounded-full transition-all" style="width: {{ $factorPct }}%"></div>
                            </div>
                            @if($factor['description'] ?? null)
                                <p class="mt-2 text-xs leading-relaxed text-gray-500">{{ $factor['description'] }}</p>
                            @endif
                        </x-card>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Disclaimer --}}
        @if($report->result_json['disclaimer'] ?? null)
            <p class="mt-8 text-center text-xs italic text-gray-400">{{ $report->result_json['disclaimer'] }}</p>
        @endif

        {{-- CTA --}}
        <div class="mt-6 text-center">
            <x-button :href="route('astrologers.index')" variant="secondary">
                Consult an astrologer for detailed analysis
            </x-button>
        </div>
    </div>
</x-layouts.customer>
