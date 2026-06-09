@props([
    'label' => 'Place of Birth',
    'name' => 'place_of_birth',
    'value' => null,
    'latitude' => null,
    'longitude' => null,
    'latitudeName' => 'birth_latitude',
    'longitudeName' => 'birth_longitude',
    'error' => null,
])

<div
    x-data="{
        query: '{{ old($name, $value ?? '') }}',
        results: [],
        open: false,
        loading: false,
        selectedLat: '{{ old($latitudeName, $latitude ?? '') }}',
        selectedLng: '{{ old($longitudeName, $longitude ?? '') }}',
        timeout: null,

        search() {
            if (this.query.length < 2) {
                this.results = [];
                this.open = false;
                return;
            }

            clearTimeout(this.timeout);
            this.timeout = setTimeout(async () => {
                this.loading = true;
                try {
                    const res = await fetch(`{{ route('cities.search') }}?q=${encodeURIComponent(this.query)}`);
                    this.results = await res.json();
                    this.open = this.results.length > 0;
                } catch (e) {
                    this.results = [];
                } finally {
                    this.loading = false;
                }
            }, 300);
        },

        select(city) {
            this.query = city.name + ', ' + city.state_name;
            this.selectedLat = city.latitude;
            this.selectedLng = city.longitude;
            this.open = false;
            this.results = [];
        },

        clear() {
            this.selectedLat = '';
            this.selectedLng = '';
        }
    }"
    @click.outside="open = false"
    class="relative"
>
    @if($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif

    <input
        type="text"
        name="{{ $name }}"
        id="{{ $name }}"
        x-model="query"
        @input="search(); clear()"
        @focus="if (results.length) open = true"
        autocomplete="off"
        placeholder="Search city..."
        class="block w-full rounded-xl border px-4 py-2.5 text-sm transition-colors placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-0 {{ ($error ?? $errors->first($name)) ? 'border-red-300 text-red-900 focus:border-red-500 focus:ring-red-200' : 'border-gray-200 text-gray-900 focus:border-cosmic-400 focus:ring-cosmic-200' }}"
    >

    {{-- Hidden fields for lat/lng --}}
    <input type="hidden" name="{{ $latitudeName }}" :value="selectedLat">
    <input type="hidden" name="{{ $longitudeName }}" :value="selectedLng">

    {{-- Loading indicator --}}
    <div x-show="loading" class="pointer-events-none absolute right-3 top-[2.4rem]">
        <svg class="h-4 w-4 animate-spin text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
    </div>

    {{-- Dropdown results --}}
    <div
        x-show="open"
        x-cloak
        class="absolute z-50 mt-1 max-h-60 w-full overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-lg"
    >
        <template x-for="city in results" :key="city.id">
            <button
                type="button"
                @click="select(city)"
                class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm hover:bg-cosmic-50 transition-colors"
            >
                <svg class="h-4 w-4 shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0 1 15 0Z"/></svg>
                <span>
                    <span x-text="city.name" class="font-medium text-gray-900"></span><span class="text-gray-400">, </span><span x-text="city.state_name" class="text-gray-500"></span>
                    <span x-show="city.country_code !== 'IN'" class="ml-1 text-xs text-gray-400" x-text="'(' + city.country_code + ')'"></span>
                </span>
            </button>
        </template>
    </div>

    {{-- Selected coordinates indicator --}}
    <div x-show="selectedLat && selectedLng" x-cloak class="mt-1.5 flex items-center gap-1 text-xs text-green-600">
        <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
        <span>Coordinates mapped</span>
        <span class="text-gray-400" x-text="'(' + Number(selectedLat).toFixed(4) + ', ' + Number(selectedLng).toFixed(4) + ')'"></span>
    </div>

    @if($error ?? $errors->first($name))
        <p class="mt-1.5 text-xs text-red-600">{{ $error ?? $errors->first($name) }}</p>
    @endif
</div>
