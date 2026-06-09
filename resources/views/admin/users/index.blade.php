<x-layouts.admin title="Users">
    <x-slot:header>Users</x-slot:header>

    <x-card :padding="false">
        <x-slot:header>
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, mobile..."
                       class="rounded-lg border border-gray-200 px-3 py-1.5 text-sm focus:border-cosmic-400 focus:outline-none focus:ring-2 focus:ring-cosmic-200">
                <x-button type="submit" variant="secondary" size="sm">Search</x-button>
            </form>
        </x-slot:header>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100 bg-gray-50/50 text-left">
                    <tr>
                        <th class="px-6 py-3 font-medium text-gray-500">Name</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Contact</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Role</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Joined</th>
                        <th class="px-6 py-3 font-medium text-gray-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="px-6 py-3 text-gray-500">
                                <div>{{ $user->mobile }}</div>
                                <div class="text-xs text-gray-400">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-3"><x-badge color="cosmic">{{ $user->role->value }}</x-badge></td>
                            <td class="px-6 py-3">
                                <x-badge :color="$user->account_status->value === 'active' ? 'green' : 'red'" :dot="true">
                                    {{ $user->account_status->value }}
                                </x-badge>
                            </td>
                            <td class="px-6 py-3 text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-3">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-sm font-medium text-cosmic-600 hover:text-cosmic-800">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot:footer>{{ $users->withQueryString()->links() }}</x-slot:footer>
    </x-card>
</x-layouts.admin>
