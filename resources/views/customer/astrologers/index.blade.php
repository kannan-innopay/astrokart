<x-layouts.customer title="Browse Astrologers">
    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="font-display text-2xl font-bold text-gray-900">Browse Astrologers</h1>
        <p class="mt-1 text-sm text-gray-500">Find the right astrologer for your consultation</p>

        <div class="mt-6">
            <x-filter-bar :expertises="$expertises" :languages="$languages" />
        </div>

        @if($astrologers->isNotEmpty())
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($astrologers as $astrologer)
                    <x-astrologer-card :astrologer="$astrologer" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $astrologers->withQueryString()->links() }}
            </div>
        @else
            <x-empty-state
                title="No astrologers found"
                description="Try adjusting your filters or check back later."
                icon="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z"
            />
        @endif
    </div>
</x-layouts.customer>
