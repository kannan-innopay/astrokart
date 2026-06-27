<x-layouts.admin title="Sales">
    <x-slot:header>Sales Team</x-slot:header>

    <div class="mb-4 flex justify-end">
        <x-button href="{{ route('admin.sales.create') }}" variant="primary" size="sm">+ Add Sales User</x-button>
    </div>

    <x-card :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100 bg-gray-50/50 text-left">
                    <tr>
                        <th class="px-6 py-3 font-medium text-gray-500">Name</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Contact</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Astrologers</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Joined</th>
                        <th class="px-6 py-3 font-medium text-gray-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($salesUsers as $sales)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $sales->name }}</td>
                            <td class="px-6 py-3 text-gray-500">
                                <div>{{ $sales->email }}</div>
                                <div class="text-xs text-gray-400">{{ $sales->mobile }}</div>
                            </td>
                            <td class="px-6 py-3">
                                <x-badge color="cosmic">{{ $sales->referred_astrologers_count }}</x-badge>
                            </td>
                            <td class="px-6 py-3 text-gray-500">{{ $sales->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-3">
                                <form method="POST" action="{{ route('admin.sales.destroy', $sales) }}"
                                      onsubmit="return confirm('Remove this sales user? Their astrologers will remain but lose the attribution.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">Remove</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">No sales users yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot:footer>{{ $salesUsers->links() }}</x-slot:footer>
    </x-card>
</x-layouts.admin>
