<x-layouts.base :title="$title ?? 'Login'">
    <div class="relative flex min-h-screen items-center justify-center bg-gradient-to-br from-night-950 via-night-900 to-cosmic-950 px-4 py-12">
        {{-- Animated constellations --}}
        <x-constellations class="opacity-60" />

        <div class="relative w-full max-w-md animate-fade-up">
            {{-- Logo --}}
            <div class="mb-8 text-center">
                <a href="/" class="inline-block">
                    <h1 class="font-display text-3xl font-bold tracking-tight text-white">
                        {{ config('app.name') }}
                    </h1>
                </a>
                @isset($subtitle)
                    <p class="mt-2 text-sm text-cosmic-200">{{ $subtitle }}</p>
                @endisset
            </div>

            {{-- Card --}}
            <div class="rounded-2xl border border-white/10 bg-white/5 p-8 shadow-2xl shadow-cosmic-950/50 backdrop-blur-xl">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-layouts.base>
