<x-layouts.admin title="Add Expertise">
    <x-slot:header>
        <a href="{{ route('admin.expertises.index') }}" class="text-sm text-gray-500 hover:text-cosmic-600">&larr; Back</a>
        <span class="ml-2">Add Expertise</span>
    </x-slot:header>

    <x-card class="max-w-lg">
        <form method="POST" action="{{ route('admin.expertises.store') }}">
            @csrf
            <div class="space-y-4">
                <x-input name="name" label="Name" :value="old('name')" required />
                <x-textarea name="description" label="Description" :rows="3">{{ old('description') }}</x-textarea>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))
                           class="h-4 w-4 rounded border-gray-300 text-cosmic-600 focus:ring-cosmic-500">
                    <span class="text-sm text-gray-700">Active</span>
                </label>
                <x-button type="submit" variant="primary">Create Expertise</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.admin>
