<x-layouts.customer title="Astrologers">
    <div class="mx-auto max-w-2xl px-4 py-16 text-center sm:px-6 sm:py-24">
        <div class="flex justify-center">
            <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-cosmic-100">
                <svg class="h-10 w-10 text-cosmic-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456Z" />
                </svg>
            </div>
        </div>

        <h1 class="mt-6 font-display text-3xl font-bold text-gray-900">Astrologer Consultations</h1>
        <p class="mt-2 text-lg text-cosmic-600 font-medium">Coming Soon</p>

        <p class="mt-6 max-w-md mx-auto text-sm leading-relaxed text-gray-500">
            We're onboarding verified Vedic astrologers to offer you personalized live consultations.
            In the meantime, explore your birth chart, daily predictions, and detailed analysis — all powered by traditional Vedic astrology.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('horoscope.analysis') }}" class="rounded-xl bg-gradient-to-r from-cosmic-600 to-cosmic-700 px-6 py-3 text-sm font-semibold text-white shadow transition hover:from-cosmic-700 hover:to-cosmic-800">
                View Your Analysis
            </a>
            <a href="{{ route('predictions.daily') }}" class="rounded-xl border border-cosmic-200 px-6 py-3 text-sm font-semibold text-cosmic-700 transition hover:bg-cosmic-50">
                Daily Predictions
            </a>
        </div>

        <div class="mt-12 rounded-2xl border border-gold-200 bg-gold-50 p-6">
            <p class="text-sm font-medium text-gold-800">Want to be notified when astrologer consultations launch?</p>
            <p class="mt-1 text-xs text-gold-600">Complete your profile and we'll let you know as soon as it's available.</p>
        </div>
    </div>
</x-layouts.customer>
