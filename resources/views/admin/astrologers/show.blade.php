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

            @if($astrologer->photos->isNotEmpty())
                <x-card title="Profile Photos">
                    <div class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                        @foreach($astrologer->photos as $photo)
                            <a href="{{ $photo->url }}" target="_blank" class="relative block">
                                <img src="{{ $photo->url }}" alt="Photo" class="h-24 w-full rounded-lg object-cover ring-1 ring-gray-200">
                                @if($photo->is_primary)
                                    <span class="absolute left-1 top-1 rounded bg-gold-500 px-1.5 py-0.5 text-[10px] font-semibold text-night-950">Primary</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </x-card>
            @endif

            <x-card title="KYC Documents">
                @forelse($astrologer->documents as $document)
                    <div class="flex items-center justify-between border-b border-gray-100 py-2.5 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $document->document_type->label() }}</p>
                            @if($document->document_number)
                                <p class="text-xs text-gray-500">{{ $document->document_number }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <x-badge :color="$document->is_verified ? 'green' : 'gray'">{{ $document->is_verified ? 'Verified' : 'Unverified' }}</x-badge>
                            <a href="{{ route('admin.astrologer-documents.download', $document) }}" class="text-sm font-medium text-cosmic-600 hover:text-cosmic-700">Download</a>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">No KYC documents uploaded. This astrologer is unverified.</p>
                @endforelse
            </x-card>

            <x-card title="Settlement / Bank Details">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">Account Name</dt><dd class="text-gray-900">{{ $astrologer->bank_account_name ?: '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Account Number</dt><dd class="text-gray-900">{{ $astrologer->bank_account_number ?: '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">IFSC</dt><dd class="text-gray-900">{{ $astrologer->bank_ifsc_code ?: '—' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">UPI ID</dt><dd class="text-gray-900">{{ $astrologer->upi_id ?: '—' }}</dd></div>
                </dl>
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
