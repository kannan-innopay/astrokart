<x-layouts.admin title="Languages">
    <x-slot:header>Languages</x-slot:header>

    <div class="mb-6 flex justify-end">
        <x-button href="{{ route('admin.languages.create') }}" variant="primary" size="sm">Add Language</x-button>
    </div>

    <x-card :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100 bg-gray-50/50 text-left">
                    <tr>
                        <th class="px-6 py-3 font-medium text-gray-500">Name</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Code</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Astrologers</th>
                        <th class="px-6 py-3 font-medium text-gray-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($languages as $language)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $language->name }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $language->code }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $language->astrologers_count }}</td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.languages.edit', $language) }}" class="text-sm font-medium text-cosmic-600 hover:text-cosmic-800">Edit</a>
                                    <form method="POST" action="{{ route('admin.languages.destroy', $language) }}" onsubmit="return confirm('Delete this language?')">
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
