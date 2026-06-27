<x-layouts.sales title="Dashboard">
    <div class="mb-6">
        <h1 class="font-display text-2xl font-bold text-gray-900">My Astrologers</h1>
        <p class="mt-0.5 text-sm text-gray-500">Astrologers who signed up with your help.</p>
    </div>

    {{-- Stats --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-stat-card label="Total Signed Up" :value="$stats['total']" icon="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
        <x-stat-card label="Approved" :value="$stats['approved']" icon="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        <x-stat-card label="Pending" :value="$stats['pending']" icon="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </div>

    <x-card :padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-gray-100 bg-gray-50/50 text-left">
                    <tr>
                        <th class="px-6 py-3 font-medium text-gray-500">Name</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Mobile</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Status</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Expertise</th>
                        <th class="px-6 py-3 font-medium text-gray-500">Signed Up</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($astrologers as $astrologer)
                        @php
                            $statusColors = ['applied' => 'yellow', 'pending_verification' => 'blue', 'approved' => 'green', 'rejected' => 'red', 'suspended' => 'red', 'inactive' => 'gray'];
                        @endphp
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $astrologer->user->name }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $astrologer->user->mobile }}</td>
                            <td class="px-6 py-3">
                                <x-badge :color="$statusColors[$astrologer->status->value] ?? 'gray'" :dot="true">{{ $astrologer->status->value }}</x-badge>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($astrologer->expertises->take(2) as $expertise)
                                        <span class="rounded bg-cosmic-50 px-1.5 py-0.5 text-[10px] text-cosmic-600">{{ $expertise->name }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-3 text-gray-500">{{ $astrologer->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">No astrologers yet. Share your referral when helping someone sign up.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot:footer>{{ $astrologers->links() }}</x-slot:footer>
    </x-card>
</x-layouts.sales>
