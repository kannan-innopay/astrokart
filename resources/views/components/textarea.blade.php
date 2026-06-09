@props([
    'label' => null,
    'name',
    'rows' => 4,
    'error' => null,
])

<div>
    @if($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        {{ $attributes->merge([
            'class' => 'block w-full rounded-xl border px-4 py-2.5 text-sm transition-colors placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0 ' .
            ($error ?? $errors->first($name)
                ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-200'
                : 'border-gray-200 text-gray-900 focus:border-cosmic-400 focus:ring-cosmic-200')
        ]) }}
    >{{ $slot }}</textarea>

    @if($error ?? $errors->first($name))
        <p class="mt-1.5 text-xs text-red-600">{{ $error ?? $errors->first($name) }}</p>
    @endif
</div>
