@props([
    'color' => 'gray',
    'dot' => false,
])

@php
    $colors = [
        'green' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'red' => 'bg-red-50 text-red-700 ring-red-600/20',
        'yellow' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'blue' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'gray' => 'bg-gray-50 text-gray-600 ring-gray-500/20',
        'cosmic' => 'bg-cosmic-50 text-cosmic-700 ring-cosmic-600/20',
        'gold' => 'bg-gold-50 text-gold-700 ring-gold-600/20',
    ];

    $dotColors = [
        'green' => 'bg-emerald-500',
        'red' => 'bg-red-500',
        'yellow' => 'bg-amber-500',
        'blue' => 'bg-blue-500',
        'gray' => 'bg-gray-400',
        'cosmic' => 'bg-cosmic-500',
        'gold' => 'bg-gold-500',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset ' . ($colors[$color] ?? $colors['gray'])]) }}>
    @if($dot)
        <span class="h-1.5 w-1.5 rounded-full {{ $dotColors[$color] ?? $dotColors['gray'] }}"></span>
    @endif
    {{ $slot }}
</span>
