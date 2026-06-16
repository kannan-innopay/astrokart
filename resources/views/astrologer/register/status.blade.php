<x-layouts.auth title="Application Status">
    @php
        $state = match($astrologer->status) {
            \App\Enums\AstrologerStatus::Approved => ['icon' => '✅', 'title' => 'You are verified!', 'message' => 'Your astrologer profile is live. You can now go online and accept consultations.'],
            \App\Enums\AstrologerStatus::Rejected => ['icon' => '❌', 'title' => 'Application not approved', 'message' => 'Unfortunately your application was not approved. See the note below for details.'],
            \App\Enums\AstrologerStatus::Suspended => ['icon' => '⛔', 'title' => 'Account suspended', 'message' => 'Your astrologer account is currently suspended. Please contact support.'],
            default => ['icon' => '⏳', 'title' => 'Application under review', 'message' => 'Thanks for applying! Our team is reviewing your profile and documents. We will notify you once it is verified — usually within 1–2 business days.'],
        };
    @endphp

    <div class="text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white/5 text-3xl">{{ $state['icon'] }}</div>
        <h2 class="text-lg font-semibold text-white">{{ $state['title'] }}</h2>
        <p class="mt-2 text-sm text-cosmic-200">{{ $state['message'] }}</p>

        @if($astrologer->verification_notes)
            <div class="mt-4 rounded-xl border border-white/10 bg-white/5 p-4 text-left text-sm text-cosmic-100">
                <span class="text-xs font-semibold uppercase tracking-wider text-cosmic-300">Admin note</span>
                <p class="mt-1">{{ $astrologer->verification_notes }}</p>
            </div>
        @endif

        <div class="mt-6 flex flex-col gap-2">
            @if($astrologer->isApproved())
                <a href="{{ route('astrologer.dashboard') }}"
                   class="w-full rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-5 py-3 text-sm font-semibold text-night-950 shadow-lg shadow-gold-500/25 transition hover:from-gold-600 hover:to-gold-700">
                    Go to dashboard
                </a>
            @else
                <a href="{{ route('astrologer.dashboard') }}" class="text-sm text-cosmic-300 hover:text-white">Go to dashboard</a>
            @endif
        </div>
    </div>
</x-layouts.auth>
