@props([
    'title' => null,
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-gray-100 bg-white shadow-sm']) }}>
    @if($title || isset($header))
        <div class="border-b border-gray-100 px-6 py-4">
            @isset($header)
                {{ $header }}
            @else
                <h3 class="font-display text-lg font-semibold text-gray-900">{{ $title }}</h3>
            @endisset
        </div>
    @endif

    <div @class([$padding ? 'p-6' : ''])>
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t border-gray-100 px-6 py-4">
            {{ $footer }}
        </div>
    @endisset
</div>
