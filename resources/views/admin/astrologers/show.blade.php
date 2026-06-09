<x-layouts.admin title="Astrologer Details">
    <x-slot:header>
        <a href="{{ route('admin.astrologers.index') }}" class="text-sm text-gray-500 hover:text-cosmic-600">&larr; Back</a>
        <span class="ml-2">{{ $astrologer->user->name }}</span>
    </x-slot:header>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <x-card title="Profile">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Name</dt><dd class="font-medium text-gray-900">{{ $astrologer->user->name }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Mobile</dt><dd class="text-gray-900">{{ $astrologer->user->mobile }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Experience</dt><dd class="text-gray-900">{{ $astrologer->years_of_experience }} years</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Price</dt><dd class="text-gray-900">₹{{ number_format($astrologer->price_per_minute / 100) }}/min</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Rating</dt><dd class="text-gray-900">{{ number_format($astrologer->rating, 1) }} ({{ $astrologer->total_reviews }} reviews)</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Online</dt><dd><x-badge :color="$astrologer->is_online ? 'green' : 'gray'" :dot="true">{{ $astrologer->is_online ? 'Yes' : 'No' }}</x-badge></dd></div>
                </dl>

                @if($astrologer->bio)
                    <div class="mt-4 border-t border-gray-100 pt-4">
                        <p class="text-sm text-gray-600">{{ $astrologer->bio }}</p>
                    </div>
                @endif

                <div class="mt-4 flex flex-wrap gap-4">
                    <div>
                        <span class="text-xs font-medium text-gray-400 uppercase">Expertises</span>
                        <div class="mt-1 flex flex-wrap gap-1">
                            @foreach($astrologer->expertises as $expertise)
                                <x-badge color="cosmic">{{ $expertise->name }}</x-badge>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <span class="text-xs font-medium text-gray-400 uppercase">Languages</span>
                        <div class="mt-1 flex flex-wrap gap-1">
                            @foreach($astrologer->languages as $language)
                                <x-badge color="blue">{{ $language->name }}</x-badge>
                            @endforeach
                        </div>
                    </div>
                </div>
            </x-card>

            @if($astrologer->verification_notes)
                <x-card title="Verification Notes">
                    <p class="text-sm text-gray-600">{{ $astrologer->verification_notes }}</p>
                </x-card>
            @endif
        </div>

        <div>
            <x-card title="Update Status">
                <form method="POST" action="{{ route('admin.astrologers.update-status', $astrologer) }}">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-4">
                        <x-select name="status" label="Status" :options="[
                            'applied' => 'Applied',
                            'pending_verification' => 'Pending Verification',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                            'suspended' => 'Suspended',
                            'inactive' => 'Inactive',
                        ]" :selected="$astrologer->status->value" :placeholder="false" />

                        <x-textarea name="notes" label="Notes" :rows="3" placeholder="Add verification notes...">{{ old('notes') }}</x-textarea>

                        <x-button type="submit" variant="primary" size="sm" class="w-full">Update Status</x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-layouts.admin>
