<x-layouts.admin title="Expertises">
    <x-slot:header>Expertises</x-slot:header>

    <div class="mb-6 flex justify-end">
        <x-button href="{{ route('admin.expertises.create') }}" variant="primary" size="sm">Add Expertise</x-button>
    </div>

    <x-card :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100 bg-gray-50/50 text-left">
                    <tr>
                        <th class="px-6 py-3 font-medium text-gray-500">Name</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Slug</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Astrologers</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 font-medium text-gray-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($expertises as $expertise)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $expertise->name }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $expertise->slug }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $expertise->astrologers_count }}</td>
                            <td class="px-6 py-3">
                                <x-badge :color="$expertise->is_active ? 'green' : 'gray'" :dot="true">{{ $expertise->is_active ? 'Active' : 'Inactive' }}</x-badge>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.expertises.edit', $expertise) }}" class="text-sm font-medium text-cosmic-600 hover:text-cosmic-800">Edit</a>
                                    <form method="POST" action="{{ route('admin.expertises.destroy', $expertise) }}" onsubmit="return confirm('Delete this expertise?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-700">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</x-layouts.admin>
