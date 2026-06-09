@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-xl font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:pointer-events-none disabled:opacity-50';

    $variants = [
        'primary' => 'bg-cosmic-600 text-white hover:bg-cosmic-700 focus:ring-cosmic-500 shadow-lg shadow-cosmic-600/25',
        'secondary' => 'bg-cosmic-50 text-cosmic-700 hover:bg-cosmic-100 focus:ring-cosmic-300',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
        'ghost' => 'text-gray-600 hover:bg-gray-100 focus:ring-gray-300',
        'gold' => 'bg-gradient-to-r from-gold-500 to-gold-600 text-night-950 hover:from-gold-600 hover:to-gold-700 focus:ring-gold-400 shadow-lg shadow-gold-500/25 font-semibold',
    ];

    $sizes = [
        'sm' => 'px-3.5 py-1.5 text-xs',
        'md' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-7 py-3 text-base',
    ];

    $classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
