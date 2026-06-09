<x-layouts.admin title="Astrologers">
    <x-slot:header>Astrologers</x-slot:header>

    {{-- Status tabs --}}
    <div class="mb-6 flex flex-wrap gap-2">
        @php
            $statuses = ['' => 'All', 'applied' => 'Applied', 'pending_verification' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'suspended' => 'Suspended'];
        @endphp
        @foreach($statuses as $value => $label)
            <a href="{{ route('admin.astrologers.index', ['status' => $value]) }}"
               class="rounded-lg px-3 py-1.5 text-sm font-medium transition {{ request('status', '') === $value ? 'bg-cosmic-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <x-card :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100 bg-gray-50/50 text-left">
                    <tr>
                        <th class="px-6 py-3 font-medium text-gray-500">Name</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Expertise</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Exp.</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Price</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Rating</th>
                        <th class="px-6 py-3 font-medium text-gray-500"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($astrologers as $astrologer)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3">
                                <div class="font-medium text-gray-900">{{ $astrologer->user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $astrologer->user->mobile }}</div>
                            </td>
                            <td class="px-6 py-3">
                                @php
                                    $statusColors = ['applied' => 'yellow', 'pending_verification' => 'blue', 'approved' => 'green', 'rejected' => 'red', 'suspended' => 'red', 'inactive' => 'gray'];
                                @endphp
                                <x-badge :color="$statusColors[$astrologer->status->value] ?? 'gray'" :dot="true">{{ $astrologer->status->value }}</x-badge>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($astrologer->expertises->take(2) as $expertise)
                                        <span class="rounded bg-cosmic-50 px-1.5 py-0.5 text-[10px] text-cosmic-600">{{ $expertise->name }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-3 text-gray-500">{{ $astrologer->years_of_experience }}y</td>
                            <td class="px-6 py-3 text-gray-500">₹{{ number_format($astrologer->price_per_minute / 100) }}/m</td>
                            <td class="px-6 py-3 text-gray-500">{{ number_format($astrologer->rating, 1) }}</td>
                            <td class="px-6 py-3">
                                <a href="{{ route('admin.astrologers.show', $astrologer) }}" class="text-sm font-medium text-cosmic-600 hover:text-cosmic-800">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-8 text-center text-gray-400">No astrologers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot:footer>{{ $astrologers->withQueryString()->links() }}</x-slot:footer>
    </x-card>
</x-layouts.admin>
