@props([
    'type' => 'info',
    'message' => null,
])

@php
    $styles = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'error' => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'info' => 'border-blue-200 bg-blue-50 text-blue-800',
    ];
@endphp

<div x-data="{ show: true }"
     x-show="show"
     x-transition.opacity
     class="fixed top-4 right-4 z-50 max-w-sm rounded-xl border p-4 shadow-lg {{ $styles[$type] ?? $styles['info'] }}">
    <div class="flex items-start gap-3">
        <p class="text-sm font-medium">{{ $message ?? $slot }}</p>
        <button @click="show = false" class="shrink-0 opacity-60 transition hover:opacity-100">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>
