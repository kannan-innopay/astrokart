<x-layouts.admin title="Add Sales User">
    <x-slot:header>
        <a href="{{ route('admin.sales.index') }}" class="text-sm text-gray-500 hover:text-cosmic-600">&larr; Back</a>
        <span class="ml-2">Add Sales User</span>
    </x-slot:header>

    <div class="max-w-lg">
        <x-card>
            <form method="POST" action="{{ route('admin.sales.store') }}">
                @csrf

                <div class="space-y-4">
                    <x-input name="name" label="Full name" :value="old('name')" required />
                    <x-input name="email" type="email" label="Email" :value="old('email')" required />
                    <x-input name="mobile" label="Mobile (optional)" :value="old('mobile')" placeholder="10-digit mobile" />
                    <x-input name="password" type="password" label="Password" required />

                    <p class="text-xs text-gray-400">The sales user signs in with their email &amp; password at the admin login page.</p>

                    <x-button type="submit" variant="primary" size="sm" class="w-full">Create Sales User</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.admin>
