@props([
    'name',
    'maxWidth' => 'md',
])

@php
    $widths = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
    ];
@endphp

<div x-data="{ open: false }"
     x-on:open-modal-{{ $name }}.window="open = true"
     x-on:close-modal-{{ $name }}.window="open = false"
     x-on:keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50">
    {{-- Backdrop --}}
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-night-950/60 backdrop-blur-sm" @click="open = false"></div>

    {{-- Panel --}}
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div x-show="open"
             x-transition:enter="duration-200 ease-out"
             x-transition:enter-start="scale-95 opacity-0"
             x-transition:enter-end="scale-100 opacity-100"
             x-transition:leave="duration-150 ease-in"
             x-transition:leave-start="scale-100 opacity-100"
             x-transition:leave-end="scale-95 opacity-0"
             class="w-full rounded-2xl border border-gray-100 bg-white shadow-2xl {{ $widths[$maxWidth] ?? $widths['md'] }}">
            {{ $slot }}
        </div>
    </div>
</div>
