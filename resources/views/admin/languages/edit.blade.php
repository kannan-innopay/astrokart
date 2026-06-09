<x-layouts.admin title="Edit Language">
    <x-slot:header>
        <a href="{{ route('admin.languages.index') }}" class="text-sm text-gray-500 hover:text-cosmic-600">&larr; Back</a>
        <span class="ml-2">Edit: {{ $language->name }}</span>
    </x-slot:header>

    <x-card class="max-w-lg">
        <form method="POST" action="{{ route('admin.languages.update', $language) }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <x-input name="name" label="Name" :value="old('name', $language->name)" required />
                <x-input name="code" label="Code" :value="old('code', $language->code)" required />
                <x-button type="submit" variant="primary">Update Language</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.admin>
