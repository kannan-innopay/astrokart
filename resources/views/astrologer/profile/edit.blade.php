<x-layouts.astrologer title="Edit Profile">
    <h1 class="mb-6 font-display text-2xl font-bold text-gray-900">Edit Profile</h1>

    <x-card>
        <form method="POST" action="{{ route('astrologer.profile.update') }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <x-textarea name="bio" label="Bio" :rows="4" placeholder="Tell users about your experience and specialties...">{{ old('bio', $astrologer->bio) }}</x-textarea>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <x-input name="years_of_experience" label="Years of Experience" type="number" :value="old('years_of_experience', $astrologer->years_of_experience)" min="0" max="100" />
                    <x-input name="price_per_minute" label="Price per Minute (paise)" type="number" :value="old('price_per_minute', $astrologer->price_per_minute)" min="100" />
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700">Consultation Modes</label>
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
                                    <input type="checkbox" name="expertise_ids[]" value="{{ $expertise->id }}" @checked($astrologer->expertises->contains($expertise->id))
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
                                    <input type="checkbox" name="language_ids[]" value="{{ $language->id }}" @checked($astrologer->languages->contains($language->id))
                                           class="h-4 w-4 rounded border-gray-300 text-cosmic-600 focus:ring-cosmic-500">
                                    <span class="text-sm text-gray-700">{{ $language->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">
                <h3 class="font-display text-lg font-semibold text-gray-900">Payout Details</h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-input name="bank_account_name" label="Account Holder Name" :value="old('bank_account_name', $astrologer->bank_account_name)" />
                    <x-input name="bank_account_number" label="Account Number" :value="old('bank_account_number', $astrologer->bank_account_number)" />
                    <x-input name="bank_ifsc_code" label="IFSC Code" :value="old('bank_ifsc_code', $astrologer->bank_ifsc_code)" />
                    <x-input name="upi_id" label="UPI ID" :value="old('upi_id', $astrologer->upi_id)" placeholder="yourname@upi" />
                </div>

                <div class="flex justify-end pt-2">
                    <x-button type="submit" variant="primary">Save Profile</x-button>
                </div>
            </div>
        </form>
    </x-card>
</x-layouts.astrologer>
