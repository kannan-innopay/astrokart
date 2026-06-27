<x-layouts.admin title="Edit Astrologer">
    <x-slot:header>
        <a href="{{ route('admin.astrologers.show', $astrologer) }}" class="text-sm text-gray-500 hover:text-cosmic-600">&larr; Back</a>
        <span class="ml-2">Edit {{ $astrologer->user->name }}</span>
    </x-slot:header>

    <div class="max-w-3xl">
        <x-card>
            <form method="POST" action="{{ route('admin.astrologers.update', $astrologer) }}">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    <x-input name="name" label="Name" :value="old('name', $astrologer->user->name)" required />
                    <x-textarea name="bio" label="Bio" :rows="4">{{ old('bio', $astrologer->bio) }}</x-textarea>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <x-input name="years_of_experience" type="number" label="Years of experience" :value="old('years_of_experience', $astrologer->years_of_experience)" min="0" max="100" required />
                        <x-input name="price_per_minute" type="number" label="Price / min (₹)" :value="old('price_per_minute', intval($astrologer->price_per_minute / 100))" min="5" required />
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Consultation modes</label>
                            <div class="flex flex-wrap gap-3 pt-1">
                                @foreach(['chat' => 'Chat', 'call' => 'Call', 'video_call' => 'Video'] as $mode => $modeLabel)
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="consultation_modes[]" value="{{ $mode }}" @checked(in_array($mode, old('consultation_modes', $astrologer->consultation_modes ?? [])))
                                               class="h-4 w-4 rounded border-gray-300 text-cosmic-600 focus:ring-cosmic-500">
                                        <span class="text-sm text-gray-700">{{ $modeLabel }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Expertises</label>
                            <div class="max-h-40 space-y-2 overflow-y-auto rounded-xl border border-gray-200 p-3">
                                @foreach($allExpertises as $expertise)
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="expertise_ids[]" value="{{ $expertise->id }}" @checked(in_array($expertise->id, old('expertise_ids', $astrologer->expertises->pluck('id')->toArray())))
                                               class="h-4 w-4 rounded border-gray-300 text-cosmic-600 focus:ring-cosmic-500">
                                        <span class="text-sm text-gray-700">{{ $expertise->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700">Languages</label>
                            <div class="max-h-40 space-y-2 overflow-y-auto rounded-xl border border-gray-200 p-3">
                                @foreach($allLanguages as $language)
                                    <label class="flex items-center gap-2">
                                        <input type="checkbox" name="language_ids[]" value="{{ $language->id }}" @checked(in_array($language->id, old('language_ids', $astrologer->languages->pluck('id')->toArray())))
                                               class="h-4 w-4 rounded border-gray-300 text-cosmic-600 focus:ring-cosmic-500">
                                        <span class="text-sm text-gray-700">{{ $language->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">
                    <h3 class="font-display text-base font-semibold text-gray-900">Settlement / Bank Details</h3>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-input name="bank_account_name" label="Account holder name" :value="old('bank_account_name', $astrologer->bank_account_name)" />
                        <x-input name="bank_account_number" label="Account number" :value="old('bank_account_number', $astrologer->bank_account_number)" />
                        <x-input name="bank_ifsc_code" label="IFSC code" :value="old('bank_ifsc_code', $astrologer->bank_ifsc_code)" />
                        <x-input name="upi_id" label="UPI ID" :value="old('upi_id', $astrologer->upi_id)" placeholder="name@upi" />
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-button type="submit" variant="primary" size="sm">Save Changes</x-button>
                    </div>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.admin>
