@if($featureAstrologers ?? false)
    <div class="mt-4 text-center">
        <a href="{{ route('astrologers.index') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-6 py-3 text-sm font-semibold text-night-950 shadow transition hover:from-gold-600 hover:to-gold-700">
            {{ $slot ?? 'Consult an astrologer' }} &rarr;
        </a>
    </div>
@endif
