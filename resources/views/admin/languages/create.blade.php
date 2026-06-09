<x-layouts.admin title="Add Language">
    <x-slot:header>
        <a href="{{ route('admin.languages.index') }}" class="text-sm text-gray-500 hover:text-cosmic-600">&larr; Back</a>
        <span class="ml-2">Add Language</span>
    </x-slot:header>

    <x-card class="max-w-lg">
        <form method="POST" action="{{ route('admin.languages.store') }}">
            @csrf
            <div class="space-y-4">
                <x-input name="name" label="Name" :value="old('name')" required placeholder="e.g. Hindi" />
                <x-input name="code" label="Code" :value="old('code')" required placeholder="e.g. hi" />
                <x-button type="submit" variant="primary">Create Language</x-button>
            </div>
        </form>
    </x-card>
</x-layouts.admin>
