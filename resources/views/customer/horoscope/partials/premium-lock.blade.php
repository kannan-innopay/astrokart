<div class="relative mt-4 overflow-hidden rounded-xl border border-gray-100 bg-gray-50 p-6 text-center">
    <div class="pointer-events-none select-none text-sm leading-relaxed text-gray-300" aria-hidden="true" style="filter: blur(4px);">
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.
    </div>
    <div class="absolute inset-0 flex flex-col items-center justify-center bg-white/80">
        <svg class="h-8 w-8 text-cosmic-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
        </svg>
        <p class="mt-2 text-sm font-medium text-gray-700">Unlock detailed {{ $feature ?? 'analysis' }}</p>
        <a href="{{ route('subscription.index') }}" class="mt-3 rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-5 py-2 text-sm font-semibold text-night-950 shadow transition hover:from-gold-600 hover:to-gold-700">
            Start from ₹3/day
        </a>
    </div>
</div>
