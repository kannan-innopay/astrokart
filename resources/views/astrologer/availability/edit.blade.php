<x-layouts.astrologer title="Manage Availability">
    <h1 class="mb-6 font-display text-2xl font-bold text-gray-900">Manage Availability</h1>

    <x-card>
        <form method="POST" action="{{ route('astrologer.availability.update') }}">
            @csrf
            @method('PUT')

            <x-availability-grid :slots="$slots" />

            @error('slots')
                <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="mt-6 flex justify-end">
                <x-button type="submit" variant="primary">Save Availability</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.astrologer>
