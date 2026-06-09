@props([
    'astrologer',
])

<a href="{{ route('astrologers.show', $astrologer) }}" class="group block">
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition-all duration-200 group-hover:-translate-y-0.5 group-hover:shadow-lg group-hover:shadow-cosmic-100/50">
        <div class="flex items-start gap-4">
            {{-- Avatar --}}
            <div class="relative shrink-0">
                @if($astrologer->photo)
                    <img src="{{ $astrologer->photo }}" alt="{{ $astrologer->user->name }}" class="h-14 w-14 rounded-xl object-cover">
                @else
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-cosmic-400 to-cosmic-600 text-lg font-bold text-white">
                        {{ substr($astrologer->user->name, 0, 1) }}
                    </div>
                @endif
                @if($astrologer->is_online)
                    <span class="absolute -top-1 -right-1 h-3.5 w-3.5 rounded-full border-2 border-white bg-emerald-500"></span>
                @endif
            </div>

            <div class="min-w-0 flex-1">
                <h3 class="font-display text-base font-semibold text-gray-900 group-hover:text-cosmic-700">{{ $astrologer->user->name }}</h3>

                <div class="mt-1 flex items-center gap-2 text-xs text-gray-500">
                    <span class="flex items-center gap-0.5">
                        <svg class="h-3.5 w-3.5 text-gold-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z"/></svg>
                        {{ number_format($astrologer->rating, 1) }}
                    </span>
                    <span>&middot;</span>
                    <span>{{ $astrologer->years_of_experience }}y exp</span>
                </div>

                {{-- Expertise tags --}}
                @if($astrologer->expertises->isNotEmpty())
                    <div class="mt-2.5 flex flex-wrap gap-1">
                        @foreach($astrologer->expertises->take(3) as $expertise)
                            <span class="rounded-md bg-cosmic-50 px-2 py-0.5 text-[10px] font-medium text-cosmic-600">{{ $expertise->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-4 flex items-center justify-between border-t border-gray-50 pt-3">
            <div class="text-sm">
                <span class="font-semibold text-gray-900">₹{{ number_format($astrologer->price_per_minute / 100) }}</span>
                <span class="text-gray-400">/min</span>
            </div>
            <span class="rounded-lg bg-cosmic-600 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm transition group-hover:bg-cosmic-700">
                Consult
            </span>
        </div>
    </div>
</a>
