<x-layouts.admin title="Edit User">
    <x-slot:header>
        <a href="{{ route('admin.users.show', $user) }}" class="text-sm text-gray-500 hover:text-cosmic-600">&larr; Back</a>
        <span class="ml-2">Edit {{ $user->name }}</span>
    </x-slot:header>

    <div class="max-w-2xl">
        <x-card>
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <x-input name="name" label="Full name" :value="old('name', $user->name)" required />

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-input name="email" type="email" label="Email" :value="old('email', $user->email)" />
                        <x-input name="mobile" label="Mobile" :value="old('mobile', $user->mobile)" placeholder="10-digit mobile" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-select name="gender" label="Gender"
                                  :options="['male' => 'Male', 'female' => 'Female', 'other' => 'Other']"
                                  :selected="old('gender', $user->gender?->value)" placeholder="Not specified" />
                        <x-input name="date_of_birth" type="date" label="Date of birth"
                                 :value="old('date_of_birth', $user->date_of_birth?->format('Y-m-d'))" />
                    </div>

                    <x-select name="preferred_language" label="Preferred language"
                              :options="config('app.supported_locales')"
                              :selected="old('preferred_language', $user->preferred_language)" :placeholder="false" />

                    <hr class="border-gray-100">

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <x-select name="role" label="Role"
                                  :options="$assignableRoles"
                                  :selected="old('role', $user->role->value)" :placeholder="false" />
                        <x-select name="account_status" label="Account status"
                                  :options="['active' => 'Active', 'suspended' => 'Suspended', 'deactivated' => 'Deactivated']"
                                  :selected="old('account_status', $user->account_status->value)" :placeholder="false" />
                    </div>

                    @if(! in_array($user->role->value, array_keys($assignableRoles)))
                        <p class="text-xs text-amber-600">Note: changing the role to "Astrologer" creates an incomplete profile until the astrologer completes their application.</p>
                    @endif

                    <div class="flex justify-end pt-2">
                        <x-button type="submit" variant="primary" size="sm">Save Changes</x-button>
                    </div>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.admin>
