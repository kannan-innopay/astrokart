<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, user-scalable=no">
    <meta name="theme-color" content="#0a0618">

    <title>{{ config('app.name') }}</title>

    @vite(['resources/css/app.css'])

    <style>
        body { margin: 0; overflow: hidden; }
        .landing-bg {
            background: linear-gradient(160deg, #0a0618 0%, #110a24 30%, #2d0a66 60%, #4c1d95 100%);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }
        @keyframes twinkle {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }
        .animate-float { animation: float 4s ease-in-out infinite; }
        .star { animation: twinkle linear infinite; }
    </style>
</head>
<body class="font-sans antialiased nativephp-safe-area">
    <div class="landing-bg landing-safe-top relative flex min-h-screen flex-col items-center justify-between px-6 pb-10">

        {{-- Decorative stars --}}
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="star absolute top-[8%] left-[12%] h-1 w-1 rounded-full bg-gold-300" style="animation-duration: 2.5s"></div>
            <div class="star absolute top-[15%] right-[18%] h-1.5 w-1.5 rounded-full bg-cosmic-300" style="animation-duration: 3.2s; animation-delay: 0.5s"></div>
            <div class="star absolute top-[30%] left-[75%] h-1 w-1 rounded-full bg-gold-200" style="animation-duration: 2.8s; animation-delay: 1s"></div>
            <div class="star absolute top-[45%] left-[8%] h-0.5 w-0.5 rounded-full bg-white" style="animation-duration: 4s; animation-delay: 0.3s"></div>
            <div class="star absolute top-[55%] left-[85%] h-1 w-1 rounded-full bg-cosmic-200" style="animation-duration: 3.5s; animation-delay: 1.5s"></div>
            <div class="star absolute top-[22%] left-[45%] h-0.5 w-0.5 rounded-full bg-white" style="animation-duration: 2.2s; animation-delay: 0.8s"></div>
            <div class="star absolute top-[68%] left-[30%] h-1 w-1 rounded-full bg-gold-300" style="animation-duration: 3s; animation-delay: 2s"></div>
            <div class="star absolute top-[72%] right-[25%] h-0.5 w-0.5 rounded-full bg-cosmic-300" style="animation-duration: 2.6s; animation-delay: 0.7s"></div>
            <div class="star absolute top-[5%] left-[55%] h-1 w-1 rounded-full bg-white" style="animation-duration: 3.8s; animation-delay: 1.2s"></div>
            <div class="star absolute top-[38%] left-[22%] h-1 w-1 rounded-full bg-gold-200" style="animation-duration: 2.9s; animation-delay: 0.4s"></div>
        </div>

        {{-- Center: Logo + tagline --}}
        <div class="relative flex flex-1 flex-col items-center justify-center text-center">
            {{-- Celestial glow behind logo --}}
            <div class="absolute -top-10 h-40 w-40 rounded-full bg-cosmic-500/20 blur-3xl"></div>

            {{-- Logo icon --}}
            <div class="animate-float relative mb-6">
                <div class="flex h-24 w-24 items-center justify-center rounded-3xl bg-gradient-to-br from-cosmic-400 via-cosmic-600 to-cosmic-800 shadow-2xl shadow-cosmic-600/40">
                    <svg class="h-12 w-12 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456Z" />
                    </svg>
                </div>
            </div>

            {{-- Brand name --}}
            <h1 class="font-display text-4xl font-bold tracking-tight text-white">
                {{ config('app.name') }}
            </h1>

            <p class="mt-3 max-w-xs text-base leading-relaxed text-cosmic-200">
                Your personal Vedic astrology companion. Connect with expert astrologers for guidance.
            </p>

            {{-- Feature pills --}}
            <div class="mt-8 flex flex-wrap justify-center gap-2">
                <span class="rounded-full border border-cosmic-400/30 bg-cosmic-900/50 px-3.5 py-1.5 text-xs font-medium text-cosmic-200">
                    Birth Charts
                </span>
                <span class="rounded-full border border-cosmic-400/30 bg-cosmic-900/50 px-3.5 py-1.5 text-xs font-medium text-cosmic-200">
                    Live Chat
                </span>
                <span class="rounded-full border border-cosmic-400/30 bg-cosmic-900/50 px-3.5 py-1.5 text-xs font-medium text-cosmic-200">
                    Daily Hora
                </span>
                <span class="rounded-full border border-cosmic-400/30 bg-cosmic-900/50 px-3.5 py-1.5 text-xs font-medium text-cosmic-200">
                    Transits
                </span>
                <span class="rounded-full border border-cosmic-400/30 bg-cosmic-900/50 px-3.5 py-1.5 text-xs font-medium text-cosmic-200">
                    Pariharam
                </span>
            </div>
        </div>

        {{-- Bottom: CTA buttons --}}
        <div class="landing-safe-bottom relative w-full max-w-sm space-y-3">
            <a href="{{ route('login') }}"
               onclick="event.preventDefault(); window.location.replace('{{ route('login') }}');"
               class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-gold-500 to-gold-600 px-6 py-4 text-base font-bold text-night-950 shadow-lg shadow-gold-500/30 transition active:scale-[0.98]">
                Get Started
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
            </a>

            <p class="pt-2 text-center text-[10px] text-cosmic-400">
                By continuing, you agree to our <a href="{{ route('terms') }}" class="underline">Terms</a> & <a href="{{ route('privacy') }}" class="underline">Privacy Policy</a>
            </p>
        </div>
    </div>

    <script>
    // Prevent back navigation to this page after login
    history.replaceState(null, '', window.location.href);
    window.addEventListener('popstate', function() {
        history.pushState(null, '', window.location.href);
    });
    </script>
</body>
</html>
