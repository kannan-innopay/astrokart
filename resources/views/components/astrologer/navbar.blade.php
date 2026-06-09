<nav class="sticky top-0 z-40 border-b border-cosmic-100/50 bg-white/80 backdrop-blur-lg">
    <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <a href="{{ route('astrologer.dashboard') }}" class="flex items-center gap-2">
            <span class="font-display text-xl font-bold tracking-tight text-cosmic-900">
                Astro<span class="text-gold-500">kart</span>
            </span>
            <span class="rounded-full bg-cosmic-100 px-2 py-0.5 text-xs font-medium text-cosmic-700">Astrologer</span>
        </a>

        <div class="flex items-center gap-5">
            <a href="{{ route('astrologer.dashboard') }}"
               class="hidden text-sm font-medium text-gray-600 transition hover:text-cosmic-700 sm:inline {{ request()->routeIs('astrologer.dashboard') ? 'text-cosmic-700' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('astrologer.profile.edit') }}"
               class="hidden text-sm font-medium text-gray-600 transition hover:text-cosmic-700 sm:inline {{ request()->routeIs('astrologer.profile.*') ? 'text-cosmic-700' : '' }}">
                Profile
            </a>
            <a href="{{ route('astrologer.availability.edit') }}"
               class="hidden text-sm font-medium text-gray-600 transition hover:text-cosmic-700 sm:inline {{ request()->routeIs('astrologer.availability.*') ? 'text-cosmic-700' : '' }}">
                Availability
            </a>

            {{-- Online toggle --}}
            @if(auth()->user()->astrologerProfile?->isApproved())
                <x-online-toggle :is-online="auth()->user()->astrologerProfile->is_online" />
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm font-medium text-gray-400 transition hover:text-red-500">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/></svg>
                </button>
            </form>
        </div>
    </div>
</nav>

{{-- Incoming consultation request modal --}}
@if(auth()->user()->isAstrologer())
<div x-data="{
    incoming: null,
    show: false,
    countdown: 60,
    timer: null,

    init() {
        window.Echo.private(`astrologer.{{ auth()->id() }}`)
            .listen('.consultation.requested', (e) => {
                this.incoming = e;
                this.show = true;
                this.countdown = 60;
                this.timer = setInterval(() => {
                    this.countdown--;
                    if (this.countdown <= 0) {
                        this.dismiss();
                    }
                }, 1000);
            });
    },

    dismiss() {
        this.show = false;
        this.incoming = null;
        if (this.timer) clearInterval(this.timer);
    },
}">
    <div x-show="show" x-transition x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-night-950/60 backdrop-blur-sm">
        <div class="mx-4 w-full max-w-sm rounded-2xl border border-cosmic-200 bg-white p-6 shadow-2xl">
            <div class="text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-cosmic-100 text-cosmic-600">
                    <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                </div>

                <h3 class="mt-3 font-display text-lg font-semibold text-gray-900">New Consultation Request</h3>
                <p class="mt-1 text-sm text-gray-500">
                    <span class="font-medium text-gray-900" x-text="incoming?.user_name"></span> wants to chat with you
                </p>
                <p class="mt-1 text-xs text-gray-400">
                    ₹<span x-text="incoming ? (incoming.price_per_minute / 100).toFixed(0) : ''"></span>/min
                </p>

                {{-- Countdown --}}
                <div class="mt-3">
                    <div class="mx-auto h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                        <div class="h-full rounded-full bg-cosmic-500 transition-all duration-1000" :style="'width: ' + (countdown / 60 * 100) + '%'"></div>
                    </div>
                    <p class="mt-1 text-xs text-gray-400"><span x-text="countdown"></span>s remaining</p>
                </div>

                <div class="mt-4 flex gap-3">
                    <form :action="'/astrologer/consultation/' + incoming?.consultation_id + '/accept'" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-600">Accept</button>
                    </form>
                    <form :action="'/astrologer/consultation/' + incoming?.consultation_id + '/reject'" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" @click="dismiss()" class="w-full rounded-xl bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-200">Decline</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
