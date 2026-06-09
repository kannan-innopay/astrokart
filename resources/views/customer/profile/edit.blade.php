<x-layouts.customer title="My Profile">
    <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6">
        <h1 class="font-display text-2xl font-bold text-gray-900">My Profile</h1>

        <x-card class="mt-6">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    <x-input name="name" label="Full Name" :value="old('name', $user->name)" />

                    <x-select name="gender" label="Gender" :options="['male' => 'Male', 'female' => 'Female', 'other' => 'Other']" :selected="old('gender', $user->gender?->value)" placeholder="Select" />
                    <x-select name="preferred_language" label="Preferred Language" :options="$languages->pluck('name', 'code')->toArray()" :selected="old('preferred_language', $user->preferred_language)" :placeholder="false" />

                    <x-input name="date_of_birth" label="Date of Birth" type="date" :value="old('date_of_birth', $user->date_of_birth?->format('Y-m-d'))" />

                    <x-input name="time_of_birth" label="Time of Birth" type="time" :value="old('time_of_birth', $user->time_of_birth)" />

                    <x-city-search
                        label="Place of Birth"
                        name="place_of_birth"
                        :value="$user->place_of_birth"
                        :latitude="$user->birth_latitude"
                        :longitude="$user->birth_longitude"
                    />

                    <div class="flex justify-end pt-2">
                        <x-button type="submit" variant="primary">Save Changes</x-button>
                    </div>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.customer>
