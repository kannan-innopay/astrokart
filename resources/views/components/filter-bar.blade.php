@props([
    'expertises' => collect(),
    'languages' => collect(),
])

<div x-data="{ filtersOpen: false }" class="mb-6">
    {{-- Mobile toggle --}}
    <button @click="filtersOpen = !filtersOpen" class="mb-3 flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-medium text-gray-600 shadow-sm lg:hidden">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/></svg>
        Filters
    </button>

    <form method="GET" action="{{ route('astrologers.index') }}"
          class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm lg:block"
          :class="filtersOpen ? 'block' : 'hidden lg:block'">
        <div class="flex flex-wrap items-end gap-3">
            <div class="w-full sm:w-auto">
                <x-select name="expertise_id" label="Expertise" :options="$expertises->pluck('name', 'id')->toArray()" :selected="request('expertise_id')" placeholder="All Expertises" />
            </div>

            <div class="w-full sm:w-auto">
                <x-select name="language_id" label="Language" :options="$languages->pluck('name', 'id')->toArray()" :selected="request('language_id')" placeholder="All Languages" />
            </div>

            <div class="w-full sm:w-auto">
                <x-select name="sort_by" label="Sort By" :options="['rating' => 'Rating', 'price_per_minute' => 'Price', 'years_of_experience' => 'Experience', 'total_reviews' => 'Reviews']" :selected="request('sort_by', 'rating')" :placeholder="false" />
            </div>

            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 px-4 py-2.5">
                <input type="checkbox" name="is_online" value="1" @checked(request('is_online')) class="h-4 w-4 rounded border-gray-300 text-cosmic-600 focus:ring-cosmic-500">
                <span class="text-sm text-gray-700">Online Now</span>
            </label>

            <x-button type="submit" variant="primary" size="sm">Apply</x-button>

            @if(request()->hasAny(['expertise_id', 'language_id', 'is_online', 'sort_by']))
                <x-button href="{{ route('astrologers.index') }}" variant="ghost" size="sm">Clear</x-button>
            @endif
        </div>
    </form>
</div>
