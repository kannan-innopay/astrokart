@props(['align' => 'right'])

@php($locales = config('app.supported_locales', []))

<div x-data="{ open: false }" class="relative">
    <button type="button" @click="open = !open" @click.outside="open = false"
            class="inline-flex items-center gap-1.5 rounded-lg border border-white/15 bg-white/5 px-3 py-1.5 text-xs font-medium text-cosmic-100 transition hover:bg-white/10">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.949 8.949 0 0 0 12 21Zm3-11.25a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.6 9h16.8M3.6 15h16.8"/></svg>
        <span>{{ $locales[app()->getLocale()] ?? strtoupper(app()->getLocale()) }}</span>
        <svg class="h-3.5 w-3.5 transition" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
    </button>

    <div x-show="open" x-transition x-cloak
         class="absolute {{ $align === 'right' ? 'right-0' : 'left-0' }} z-30 mt-2 w-36 overflow-hidden rounded-xl border border-white/10 bg-night-900 py-1 shadow-2xl shadow-black/40">
        @foreach($locales as $code => $name)
            <a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
               class="block px-3 py-2 text-sm transition {{ app()->getLocale() === $code ? 'bg-white/10 text-white' : 'text-cosmic-200 hover:bg-white/5' }}">
                {{ $name }}
            </a>
        @endforeach
    </div>
</div>
