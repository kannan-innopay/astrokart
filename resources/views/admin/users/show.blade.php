<x-layouts.admin title="User Details">
    <x-slot:header>
        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-500 hover:text-cosmic-600">&larr; Back</a>
        <span class="ml-2">{{ $user->name }}</span>
    </x-slot:header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <x-card title="User Information">
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Name</dt><dd class="font-medium text-gray-900">{{ $user->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Mobile</dt><dd class="font-medium text-gray-900">{{ $user->mobile ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd class="font-medium text-gray-900">{{ $user->email ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Role</dt><dd><x-badge color="cosmic">{{ $user->role->value }}</x-badge></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Joined</dt><dd class="text-gray-900">{{ $user->created_at->format('M d, Y') }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Wallet Balance</dt><dd class="font-medium text-gray-900">₹{{ number_format(($user->wallet?->balance ?? 0) / 100, 2) }}</dd></div>
            </dl>
        </x-card>

        <x-card title="Account Status">
            <form method="POST" action="{{ route('admin.users.update-status', $user) }}">
                @csrf
                @method('PATCH')

                <x-select name="account_status" label="Status" :options="['active' => 'Active', 'suspended' => 'Suspended', 'deactivated' => 'Deactivated']" :selected="$user->account_status->value" :placeholder="false" />

                <div class="mt-4">
                    <x-button type="submit" variant="primary" size="sm">Update Status</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.admin>
