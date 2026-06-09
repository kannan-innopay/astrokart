<nav class="sticky top-0 z-40 border-b border-cosmic-100/50 bg-white/80 backdrop-blur-lg" x-data="{ mobileOpen: false }">
    <div class="mx-auto flex h-16 max-w-5xl items-center justify-between px-4 sm:px-6 lg:px-8">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <span class="font-display text-xl font-bold tracking-tight text-cosmic-900">
                {{ config('app.name') }}
            </span>
        </a>

        {{-- Desktop nav --}}
        <div class="hidden items-center gap-6 md:flex">
            @if($featureAstrologers ?? false)
                <a href="{{ route('astrologers.index') }}"
                   class="text-sm font-medium text-gray-600 transition hover:text-cosmic-700 {{ request()->routeIs('astrologers.*') ? 'text-cosmic-700' : '' }}">
                    Astrologers
                </a>
            @endif

            @auth
                @if($featureAstrologers ?? false)
                @php
                    $activeConsult = \App\Models\Consultation::where(function($q) {
                        $q->where('user_id', auth()->id());
                        if (auth()->user()->astrologerProfile) {
                            $q->orWhere('astrologer_id', auth()->user()->astrologerProfile->id);
                        }
                    })->whereIn('status', ['pending', 'active'])->first();
                @endphp

                @if($activeConsult)
                    <a href="{{ route('consultation.chat', $activeConsult) }}"
                       class="flex items-center gap-1.5 rounded-lg bg-emerald-50 px-3 py-1.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-100">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                        </span>
                        Live Chat
                    </a>
                @endif
                @endif

                <a href="{{ route('horoscope.show') }}"
                   class="text-sm font-medium text-gray-600 transition hover:text-cosmic-700 {{ request()->routeIs('horoscope.show') ? 'text-cosmic-700' : '' }}">
                    My Chart
                </a>
                <a href="{{ route('horoscope.transits') }}"
                   class="text-sm font-medium text-gray-600 transition hover:text-cosmic-700 {{ request()->routeIs('horoscope.transits') ? 'text-cosmic-700' : '' }}">
                    Transits
                </a>
                <a href="{{ route('horoscope.hora') }}"
                   class="text-sm font-medium text-gray-600 transition hover:text-cosmic-700 {{ request()->routeIs('horoscope.hora') ? 'text-cosmic-700' : '' }}">
                    Hora
                </a>
                <a href="{{ route('horoscope.analysis') }}"
                   class="text-sm font-medium text-gray-600 transition hover:text-cosmic-700 {{ request()->routeIs('horoscope.analysis') ? 'text-cosmic-700' : '' }}">
                    Analysis
                </a>
                <a href="{{ route('predictions.daily') }}"
                   class="text-sm font-medium text-gray-600 transition hover:text-cosmic-700 {{ request()->routeIs('predictions.*') ? 'text-cosmic-700' : '' }}">
                    Predictions
                </a>
                <a href="{{ route('muhurtham.index') }}"
                   class="text-sm font-medium text-gray-600 transition hover:text-cosmic-700 {{ request()->routeIs('muhurtham.*') ? 'text-cosmic-700' : '' }}">
                    Muhurtham
                </a>
                <a href="{{ route('wallet.index') }}"
                   class="text-sm font-medium text-gray-600 transition hover:text-cosmic-700 {{ request()->routeIs('wallet.*') ? 'text-cosmic-700' : '' }}">
                    Wallet
                </a>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-2 text-sm font-medium text-gray-600 transition hover:text-cosmic-700">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-cosmic-100 text-xs font-semibold text-cosmic-700">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <svg class="h-4 w-4 transition" :class="open && 'rotate-180'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/></svg>
                    </button>

                    <div x-show="open" x-transition.opacity class="absolute right-0 mt-2 w-48 rounded-xl border border-gray-100 bg-white py-2 shadow-xl">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-cosmic-50">Profile</a>
                        @if(auth()->user()->isAstrologer())
                            <a href="{{ route('astrologer.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-cosmic-50">Astrologer Dashboard</a>
                        @endif
                        <hr class="my-1 border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}">
                    <x-button variant="primary" size="sm">Login</x-button>
                </a>
            @endauth
        </div>

        {{-- Mobile hamburger --}}
        <button @click="mobileOpen = !mobileOpen" class="md:hidden">
            <svg class="h-6 w-6 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Mobile menu --}}
    <div x-show="mobileOpen" x-transition class="border-t border-cosmic-100 bg-white px-4 py-4 md:hidden">
        @if($featureAstrologers ?? false)
            <a href="{{ route('astrologers.index') }}" class="block py-2 text-sm font-medium text-gray-600">Astrologers</a>
        @endif
        @auth
            <a href="{{ route('wallet.index') }}" class="block py-2 text-sm font-medium text-gray-600">Wallet</a>
            <a href="{{ route('profile.edit') }}" class="block py-2 text-sm font-medium text-gray-600">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block py-2 text-sm font-medium text-red-600">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="block py-2 text-sm font-medium text-cosmic-700">Login</a>
        @endauth
    </div>
</nav>
