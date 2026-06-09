@props([
    'label',
    'value',
    'icon' => null,
    'trend' => null,
    'trendUp' => true,
])

<div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
            <p class="mt-2 font-display text-3xl font-bold text-gray-900">{{ $value }}</p>
            @if($trend)
                <p class="mt-1 text-xs font-medium {{ $trendUp ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ $trendUp ? '↑' : '↓' }} {{ $trend }}
                </p>
            @endif
        </div>
        @if($icon)
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-cosmic-50">
                <svg class="h-5 w-5 text-cosmic-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
            </div>
        @endif
    </div>
</div>
