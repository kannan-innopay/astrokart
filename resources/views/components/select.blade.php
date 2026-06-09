@props([
    'label' => null,
    'name',
    'options' => [],
    'selected' => null,
    'placeholder' => 'Select...',
    'error' => null,
])

<div>
    @if($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        style="min-height: 44px; -webkit-appearance: menulist; appearance: menulist;"
        {{ $attributes->merge([
            'class' => 'block w-full rounded-xl border bg-white px-4 py-2.5 text-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-0 ' .
            ($error ?? $errors->first($name)
                ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-200'
                : 'border-gray-200 text-gray-900 focus:border-cosmic-400 focus:ring-cosmic-200')
        ]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $value => $label)
            <option value="{{ $value }}" @selected(old($name, $selected) == $value)>{{ $label }}</option>
        @endforeach
    </select>

    @if($error ?? $errors->first($name))
        <p class="mt-1.5 text-xs text-red-600">{{ $error ?? $errors->first($name) }}</p>
    @endif
</div>
