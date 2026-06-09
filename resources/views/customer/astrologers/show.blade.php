<x-layouts.customer :title="$astrologer->user->name">
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <a href="{{ route('astrologers.index') }}" class="mb-6 inline-flex items-center gap-1 text-sm text-gray-500 hover:text-cosmic-600">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            Back to listing
        </a>

        <x-card>
            {{-- Avatar: centered on mobile, left-aligned on desktop --}}
            <div class="flex flex-col items-center sm:flex-row sm:items-start sm:gap-6">
                <div class="relative shrink-0 mb-4 sm:mb-0">
                    @if($astrologer->photo)
                        <img src="{{ $astrologer->photo }}" alt="{{ $astrologer->user->name }}" class="h-24 w-24 rounded-full object-cover ring-4 ring-white shadow-lg sm:h-28 sm:w-28 sm:rounded-2xl">
                    @else
                        <div class="flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-cosmic-400 to-cosmic-600 text-3xl font-bold text-white ring-4 ring-white shadow-lg sm:h-28 sm:w-28 sm:rounded-2xl">
                            {{ substr($astrologer->user->name, 0, 1) }}
                        </div>
                    @endif
                    {{-- Online/offline dot --}}
                    <span class="absolute top-0 right-0 h-5 w-5 rounded-full border-[3px] border-white {{ $astrologer->is_online ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                </div>

                <div class="flex-1 text-center sm:text-left">
                    {{-- Name --}}
                    <h1 class="font-display text-2xl font-bold text-gray-900">{{ $astrologer->user->name }}</h1>

                    {{-- Rating + Experience: single line, no wrap --}}
                    <div class="mt-1 flex items-center justify-center gap-2 text-sm text-gray-500 whitespace-nowrap sm:justify-start">
                        <span class="flex items-center gap-0.5">
                            <svg class="h-4 w-4 text-gold-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 0 0 .95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 0 0-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 0 0-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 0 0-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 0 0 .951-.69l1.07-3.292Z"/></svg>
                            {{ number_format($astrologer->rating, 1) }}
                        </span>
                        <span class="text-gray-300">&middot;</span>
                        <span>{{ $astrologer->total_reviews }} reviews</span>
                        <span class="text-gray-300">&middot;</span>
                        <span>{{ $astrologer->years_of_experience }}y exp</span>
                    </div>

                    {{-- Bio --}}
                    @if($astrologer->bio)
                        <p class="mt-3 text-sm leading-relaxed text-gray-600">{{ $astrologer->bio }}</p>
                    @endif

                    {{-- Expertise + Languages --}}
                    <div class="mt-4 flex flex-wrap justify-center gap-4 sm:justify-start">
                        <div>
                            <span class="text-xs font-medium text-gray-400 uppercase">Expertise</span>
                            <div class="mt-1 flex flex-wrap gap-1">
                                @foreach($astrologer->expertises as $expertise)
                                    <x-badge color="cosmic">{{ $expertise->name }}</x-badge>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <span class="text-xs font-medium text-gray-400 uppercase">Languages</span>
                            <div class="mt-1 flex flex-wrap gap-1">
                                @foreach($astrologer->languages as $language)
                                    <x-badge color="blue">{{ $language->name }}</x-badge>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Price + Chat button --}}
                    <div class="mt-6 flex items-center justify-center gap-4 sm:justify-start">
                        <div class="text-lg">
                            <span class="font-display font-bold text-gray-900">₹{{ number_format($astrologer->price_per_minute / 100) }}</span>
                            <span class="text-sm text-gray-400">/min</span>
                        </div>
                        <div class="ml-auto">
                            @auth
                                @if($astrologer->is_online && $astrologer->isApproved())
                                    <form method="POST" action="{{ route('consultation.start', $astrologer) }}">
                                        @csrf
                                        <x-button type="submit" variant="gold">Chat Now</x-button>
                                    </form>
                                @else
                                    <x-button variant="gold" disabled>
                                        {{ $astrologer->is_online ? 'Chat Now' : 'Offline' }}
                                    </x-button>
                                @endif
                            @else
                                <x-button href="{{ route('login') }}" variant="gold">Login to Chat</x-button>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </x-card>
    </div>
</x-layouts.customer>
