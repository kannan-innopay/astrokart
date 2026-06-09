<x-layouts.base :title="$title ?? 'Login'">
    <div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-night-950 via-night-900 to-cosmic-950 px-4 py-12">
        {{-- Decorative stars --}}
        <div class="pointer-events-none fixed inset-0 overflow-hidden opacity-30">
            <div class="absolute top-[10%] left-[15%] h-1 w-1 rounded-full bg-gold-300"></div>
            <div class="absolute top-[25%] right-[20%] h-1.5 w-1.5 rounded-full bg-cosmic-300"></div>
            <div class="absolute top-[60%] left-[80%] h-1 w-1 rounded-full bg-gold-200"></div>
            <div class="absolute top-[40%] left-[5%] h-0.5 w-0.5 rounded-full bg-white"></div>
            <div class="absolute top-[75%] left-[45%] h-1 w-1 rounded-full bg-cosmic-200"></div>
            <div class="absolute top-[15%] left-[60%] h-0.5 w-0.5 rounded-full bg-white"></div>
            <div class="absolute top-[85%] right-[30%] h-1 w-1 rounded-full bg-gold-300"></div>
        </div>

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
