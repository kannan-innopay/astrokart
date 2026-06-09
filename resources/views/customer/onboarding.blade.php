<x-layouts.auth title="Complete Your Profile" subtitle="Tell us about yourself to get personalized readings">
    <form method="POST" action="{{ route('onboarding.store') }}" x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <div class="space-y-5">
            <div>
                <label for="name" class="mb-1.5 block text-sm font-medium text-cosmic-100">Full Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name === 'User' ? '' : auth()->user()->name) }}" placeholder="Your full name" required
                       class="block w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder:text-cosmic-300/50 focus:border-cosmic-400 focus:outline-none focus:ring-2 focus:ring-cosmic-400/30">
                @error('name') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="gender" class="mb-1.5 block text-sm font-medium text-cosmic-100">Gender</label>
                    <select name="gender" id="gender" style="min-height: 44px; -webkit-appearance: menulist; appearance: menulist;"
                            class="block w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white focus:border-cosmic-400 focus:outline-none focus:ring-2 focus:ring-cosmic-400/30">
                        <option value="">Select</option>
                        <option value="male" @selected(old('gender') === 'male')>Male</option>
                        <option value="female" @selected(old('gender') === 'female')>Female</option>
                        <option value="other" @selected(old('gender') === 'other')>Other</option>
                    </select>
                    @error('gender') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="preferred_language" class="mb-1.5 block text-sm font-medium text-cosmic-100">Language</label>
                    <select name="preferred_language" id="preferred_language" style="min-height: 44px; -webkit-appearance: menulist; appearance: menulist;"
                            class="block w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white focus:border-cosmic-400 focus:outline-none focus:ring-2 focus:ring-cosmic-400/30">
                        @foreach($languages as $lang)
                            <option value="{{ $lang->code }}" @selected(old('preferred_language', 'en') === $lang->code)>{{ $lang->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-medium text-cosmic-100">
                    Date of Birth
                    <span class="font-normal text-cosmic-300/60">(for your birth chart)</span>
                </label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                       class="block w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white focus:border-cosmic-400 focus:outline-none focus:ring-2 focus:ring-cosmic-400/30">
                @error('date_of_birth') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="time_of_birth" class="mb-1.5 block text-sm font-medium text-cosmic-100">Time of Birth</label>
                <input type="time" name="time_of_birth" id="time_of_birth" value="{{ old('time_of_birth') }}"
                       class="block w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white focus:border-cosmic-400 focus:outline-none focus:ring-2 focus:ring-cosmic-400/30">
                @error('time_of_birth') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            {{-- City search with autocomplete --}}
            <div
                x-data="{
                    query: '{{ old('place_of_birth', '') }}',
                    results: [],
                    open: false,
                    loading: false,
                    selectedLat: '{{ old('birth_latitude', '') }}',
                    selectedLng: '{{ old('birth_longitude', '') }}',
                    timeout: null,

                    search() {
                        if (this.query.length < 2) { this.results = []; this.open = false; return; }
                        clearTimeout(this.timeout);
                        this.timeout = setTimeout(async () => {
                            this.loading = true;
                            try {
                                const res = await fetch(`{{ route('cities.search') }}?q=${encodeURIComponent(this.query)}`);
                                this.results = await res.json();
                                this.open = this.results.length > 0;
                            } catch (e) { this.results = []; }
                            finally { this.loading = false; }
                        }, 300);
                    },

                    select(city) {
                        this.query = city.name + ', ' + city.state_name;
                        this.selectedLat = city.latitude;
                        this.selectedLng = city.longitude;
                        this.open = false;
                    },
                }"
                @click.outside="open = false"
                class="relative"
            >
                <label for="place_of_birth" class="mb-1.5 block text-sm font-medium text-cosmic-100">Place of Birth</label>
                <input type="text" name="place_of_birth" id="place_of_birth"
                       x-model="query"
                       @input="search(); selectedLat = ''; selectedLng = ''"
                       @focus="if (results.length) open = true"
                       autocomplete="off"
                       placeholder="Search city..."
                       required
                       class="block w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder:text-cosmic-300/50 focus:border-cosmic-400 focus:outline-none focus:ring-2 focus:ring-cosmic-400/30">
                <input type="hidden" name="birth_latitude" :value="selectedLat">
                <input type="hidden" name="birth_longitude" :value="selectedLng">

                <div x-show="open" x-cloak class="absolute z-50 mt-1 max-h-48 w-full overflow-y-auto rounded-xl border border-white/10 bg-night-900 shadow-xl">
                    <template x-for="city in results" :key="city.id">
                        <button type="button" @click="select(city)"
                                class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm text-cosmic-100 transition-colors hover:bg-white/10">
                            <svg class="h-4 w-4 shrink-0 text-cosmic-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0 1 15 0Z"/></svg>
                            <span>
                                <span x-text="city.name" class="font-medium text-white"></span><span class="text-cosmic-400">, </span><span x-text="city.state_name" class="text-cosmic-300"></span>
                            </span>
                        </button>
                    </template>
                </div>

                <div x-show="selectedLat && selectedLng" x-cloak class="mt-1.5 flex items-center gap-1 text-xs text-green-400">
                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    <span>Coordinates mapped</span>
                </div>

                @error('place_of_birth') <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                    :disabled="loading"
                    class="w-full rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-5 py-3 text-sm font-semibold text-night-950 shadow-lg shadow-gold-500/25 transition hover:from-gold-600 hover:to-gold-700 disabled:opacity-50">
                <span x-show="!loading">Continue</span>
                <span x-show="loading" class="flex items-center justify-center gap-2">
                    <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Saving...
                </span>
            </button>
        </div>

        <p class="mt-4 text-center text-xs text-cosmic-300/50">
            <a href="{{ route('home') }}" class="text-cosmic-300 hover:text-white">Skip for now</a>
        </p>
    </form>
</x-layouts.auth>
