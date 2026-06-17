<x-layouts.base :title="__('astrologer.application_title')">
    <div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-night-950 via-night-900 to-cosmic-950 px-4 py-6 sm:py-10">
        <x-constellations class="opacity-50" />
        @php
            $stepFields = [
                1 => ['name', 'bio', 'years_of_experience', 'price_per_minute', 'consultation_modes', 'expertise_ids', 'language_ids'],
                2 => ['photos'],
                3 => ['aadhaar_number', 'aadhaar_file', 'pan_number', 'pan_file', 'certificate_file'],
                4 => ['bank_account_name', 'bank_account_number', 'bank_ifsc_code', 'upi_id'],
            ];
            $initialStep = 1;
            foreach ($stepFields as $stepNumber => $fields) {
                foreach ($fields as $field) {
                    if ($errors->has($field) || $errors->has($field.'.*')) {
                        $initialStep = $stepNumber;
                        break 2;
                    }
                }
            }
        @endphp

        <div class="relative z-10 mx-auto w-full max-w-2xl animate-fade-up">
            <div class="mb-6">
                <div class="mb-3 flex justify-end">
                    <x-locale-switcher />
                </div>
                <div class="text-center">
                    <h1 class="font-display text-2xl font-bold text-white">{{ __('astrologer.application_title') }}</h1>
                    <p class="mt-1 text-sm text-cosmic-200">{{ __('astrologer.application_subtitle') }}</p>
                </div>
            </div>

            @if($errors->any())
                <div class="mb-4 rounded-xl border border-red-400/30 bg-red-500/10 p-4 text-sm text-red-200">
                    <p class="font-semibold">{{ __('astrologer.fix_following') }}</p>
                    <ul class="mt-1.5 list-inside list-disc space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('astrologer.register.store') }}"
                  enctype="multipart/form-data"
                  novalidate
                  class="signup-form"
                  x-data="astrologerSignup()"
                  @submit="submitting = true">
                @csrf

                {{-- Progress --}}
                <div class="mb-2 flex items-center justify-center gap-4 sm:gap-6">
                    <template x-for="(label, i) in steps" :key="i">
                        <div class="flex items-center gap-2">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-semibold transition"
                                  :class="step >= i + 1 ? 'bg-gold-500 text-night-950' : 'bg-white/10 text-cosmic-300'"
                                  x-text="i + 1"></span>
                            <span class="hidden text-xs font-medium sm:block" :class="step >= i + 1 ? 'text-white' : 'text-cosmic-400'" x-text="label"></span>
                        </div>
                    </template>
                </div>
                {{-- Current step label (mobile) --}}
                <p class="mb-4 text-xs font-medium text-cosmic-200 sm:hidden" x-text="stepLabel"></p>

                <div class="rounded-2xl border border-white/10 bg-white p-5 shadow-2xl shadow-cosmic-950/50 sm:p-6">

                    {{-- Step 1: Professional details --}}
                    <div x-show="step === 1" class="space-y-4">
                        <h2 class="text-base font-semibold text-gray-900">{{ __('astrologer.professional_details') }}</h2>

                        <x-input name="name" :label="__('astrologer.full_name')" :value="old('name')" required />
                        <x-textarea name="bio" :label="__('astrologer.short_bio')" :rows="3" placeholder="{{ __('astrologer.bio_placeholder') }}">{{ old('bio') }}</x-textarea>

                        <div class="grid grid-cols-2 gap-4">
                            <x-input name="years_of_experience" type="number" :label="__('astrologer.years_experience')" :value="old('years_of_experience')" min="0" max="100" required />
                            <x-input name="price_per_minute" type="number" :label="__('astrologer.price_per_minute')" :value="old('price_per_minute', 5)" min="5" required />
                        </div>

                        <div>
                            <span class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('astrologer.consultation_modes') }}</span>
                            <div class="flex flex-wrap gap-2">
                                @php($modes = ['chat' => __('astrologer.mode_chat'), 'call' => __('astrologer.mode_call'), 'video_call' => __('astrologer.mode_video')])
                                @php($selectedModes = old('consultation_modes', ['chat']))
                                @foreach($modes as $value => $label)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="consultation_modes[]" value="{{ $value }}" class="peer sr-only" @checked(in_array($value, $selectedModes))>
                                        <span class="inline-block rounded-lg border border-gray-200 px-3.5 py-2 text-sm text-gray-600 peer-checked:border-cosmic-500 peer-checked:bg-cosmic-50 peer-checked:text-cosmic-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <div class="mb-1.5 flex items-baseline gap-2">
                                <span class="text-sm font-medium text-gray-700">{{ __('astrologer.expertise') }} <span class="text-red-500">*</span></span>
                                <span class="text-xs text-gray-400">{{ __('astrologer.select_all') }}</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @php($selectedExpertise = old('expertise_ids', []))
                                @foreach($expertises as $expertise)
                                    <label class="group cursor-pointer">
                                        <input type="checkbox" name="expertise_ids[]" value="{{ $expertise->id }}" class="sr-only" @checked(in_array($expertise->id, $selectedExpertise))>
                                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3.5 py-2 text-sm text-gray-600 transition group-has-[:checked]:border-cosmic-600 group-has-[:checked]:bg-cosmic-600 group-has-[:checked]:text-white">
                                            <svg class="hidden h-3.5 w-3.5 group-has-[:checked]:block" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                            {{ $expertise->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <div class="mb-1.5 flex items-baseline gap-2">
                                <span class="text-sm font-medium text-gray-700">{{ __('astrologer.languages_label') }} <span class="text-red-500">*</span></span>
                                <span class="text-xs text-gray-400">{{ __('astrologer.select_all') }}</span>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @php($selectedLanguages = old('language_ids', []))
                                @foreach($languages as $language)
                                    <label class="group cursor-pointer">
                                        <input type="checkbox" name="language_ids[]" value="{{ $language->id }}" class="sr-only" @checked(in_array($language->id, $selectedLanguages))>
                                        <span class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 px-3.5 py-2 text-sm text-gray-600 transition group-has-[:checked]:border-cosmic-600 group-has-[:checked]:bg-cosmic-600 group-has-[:checked]:text-white">
                                            <svg class="hidden h-3.5 w-3.5 group-has-[:checked]:block" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                            {{ $language->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Photos --}}
                    <div x-show="step === 2" x-cloak class="space-y-4">
                        <h2 class="text-base font-semibold text-gray-900">{{ __('astrologer.profile_photos') }}</h2>
                        <p class="text-sm text-gray-500">{{ __('astrologer.photos_help', ['max' => 6]) }}</p>

                        {{-- Hidden input that actually submits the combined files --}}
                        <input type="file" name="photos[]" multiple x-ref="photoStore" class="sr-only">

                        <div class="grid grid-cols-2 gap-3">
                            {{-- Choose from gallery --}}
                            <label :class="files.length >= maxPhotos && 'pointer-events-none opacity-50'"
                                   class="flex cursor-pointer flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-200 px-4 py-6 text-center transition hover:border-cosmic-400">
                                <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 19.5h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                                <span class="text-sm font-medium text-cosmic-700">{{ __('astrologer.choose_gallery') }}</span>
                                <input type="file" accept="image/*" multiple class="sr-only" @change="addPhotos($event)">
                            </label>

                            {{-- Take a photo (webcam on desktop, native camera on mobile) --}}
                            <button type="button" @click="takePhoto()" :disabled="files.length >= maxPhotos"
                                    :class="files.length >= maxPhotos && 'pointer-events-none opacity-50'"
                                    class="flex flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-200 px-4 py-6 text-center transition hover:border-cosmic-400">
                                <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/></svg>
                                <span class="text-sm font-medium text-cosmic-700">{{ __('astrologer.take_photo') }}</span>
                            </button>
                            {{-- Fallback used on mobile / when webcam is unavailable --}}
                            <input type="file" accept="image/*" capture="environment" class="sr-only" x-ref="cameraInput" @change="addPhotos($event)">
                        </div>

                        <p class="text-xs text-gray-400" x-show="files.length"><span x-text="files.length"></span> / <span x-text="maxPhotos"></span> {{ __('astrologer.photos_added') }}</p>

                        <div class="grid grid-cols-3 gap-3" x-show="previews.length">
                            <template x-for="(src, i) in previews" :key="src">
                                <div class="relative">
                                    <img :src="src" class="h-24 w-full rounded-lg object-cover">
                                    <span x-show="i === 0" class="absolute left-1 top-1 rounded bg-gold-500 px-1.5 py-0.5 text-[10px] font-semibold text-night-950">{{ __('astrologer.primary') }}</span>
                                    <button type="button" @click="removePhoto(i)" class="absolute right-1 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-black/60 text-white transition hover:bg-black/80">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        {{-- Webcam capture modal (desktop) --}}
                        <div x-show="camera.open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4" @keydown.escape.window="closeCamera()">
                            <div class="w-full max-w-md rounded-2xl bg-white p-4 shadow-2xl">
                                <div class="relative overflow-hidden rounded-xl bg-black">
                                    <video x-ref="video" autoplay playsinline muted class="h-64 w-full object-cover"></video>
                                    <p x-show="camera.error" x-cloak class="absolute inset-0 flex items-center justify-center p-4 text-center text-sm text-white" x-text="camera.error"></p>
                                </div>
                                <div class="mt-4 flex items-center justify-between gap-3">
                                    <button type="button" @click="closeCamera()" class="rounded-xl px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-100">{{ __('astrologer.cancel') }}</button>
                                    <button type="button" @click="capturePhoto()" :disabled="!camera.ready"
                                            class="inline-flex items-center gap-2 rounded-xl bg-cosmic-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-cosmic-600/25 transition hover:bg-cosmic-700 disabled:opacity-50">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0Z"/></svg>
                                        {{ __('astrologer.capture') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: KYC --}}
                    <div x-show="step === 3" x-cloak class="space-y-4">
                        <h2 class="text-base font-semibold text-gray-900">{{ __('astrologer.identity_verification') }} <span class="text-sm font-normal text-gray-400">{{ __('astrologer.optional') }}</span></h2>
                        <div class="rounded-lg bg-gold-50 px-4 py-3 text-sm text-gold-800">
                            {{ __('astrologer.verified_badge_hint') }}
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-input name="aadhaar_number" :label="__('astrologer.aadhaar_number')" :value="old('aadhaar_number')" inputmode="numeric" maxlength="12" placeholder="{{ __('astrologer.aadhaar_placeholder') }}" />
                            <div>
                                <label for="aadhaar_file" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('astrologer.aadhaar_document') }}</label>
                                <input type="file" name="aadhaar_file" id="aadhaar_file" accept="image/*,application/pdf" class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-cosmic-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-cosmic-700">
                            </div>
                            <x-input name="pan_number" :label="__('astrologer.pan_number')" :value="old('pan_number')" maxlength="10" placeholder="ABCDE1234F" class="uppercase" />
                            <div>
                                <label for="pan_file" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('astrologer.pan_document') }}</label>
                                <input type="file" name="pan_file" id="pan_file" accept="image/*,application/pdf" class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-cosmic-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-cosmic-700">
                            </div>
                        </div>
                        <div>
                            <label for="certificate_file" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('astrologer.certificate') }}</label>
                            <input type="file" name="certificate_file" id="certificate_file" accept="image/*,application/pdf" class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-cosmic-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-cosmic-700">
                        </div>
                    </div>

                    {{-- Step 4: Bank details --}}
                    <div x-show="step === 4" x-cloak class="space-y-4">
                        <h2 class="text-base font-semibold text-gray-900">{{ __('astrologer.settlement_details') }} <span class="text-sm font-normal text-gray-400">{{ __('astrologer.optional') }}</span></h2>
                        <p class="text-sm text-gray-500">{{ __('astrologer.settlement_help') }}</p>

                        <x-input name="bank_account_name" :label="__('astrologer.account_holder')" :value="old('bank_account_name')" />
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-input name="bank_account_number" :label="__('astrologer.account_number')" :value="old('bank_account_number')" />
                            <x-input name="bank_ifsc_code" :label="__('astrologer.ifsc')" :value="old('bank_ifsc_code')" maxlength="11" placeholder="HDFC0001234" class="uppercase" />
                        </div>
                        <x-input name="upi_id" :label="__('astrologer.upi')" :value="old('upi_id')" placeholder="name@bank" />
                    </div>

                    {{-- Navigation --}}
                    <div class="mt-6 flex items-center justify-between gap-3 border-t border-gray-100 pt-5">
                        <button type="button" x-show="step > 1" @click="step--" class="shrink-0 rounded-xl px-4 py-3 text-sm font-medium text-gray-600 hover:bg-gray-100">&larr; {{ __('astrologer.back') }}</button>
                        <span x-show="step === 1"></span>

                        <button type="button" x-show="step < 4" @click="step++"
                                class="ml-auto rounded-xl bg-cosmic-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-cosmic-600/25 transition hover:bg-cosmic-700">
                            {{ __('astrologer.continue') }} &rarr;
                        </button>

                        <button type="submit" x-show="step === 4" :disabled="submitting"
                                class="ml-auto rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-6 py-3 text-sm font-semibold text-night-950 shadow-lg shadow-gold-500/25 transition hover:from-gold-600 hover:to-gold-700 disabled:opacity-50">
                            <span x-show="!submitting">{{ __('astrologer.submit_application') }}</span>
                            <span x-show="submitting">{{ __('astrologer.submitting') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
        <style>
            /* Prevent iOS Safari from auto-zooming when focusing form fields (< 16px) */
            @media (max-width: 640px) {
                .signup-form input,
                .signup-form select,
                .signup-form textarea {
                    font-size: 16px;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function astrologerSignup() {
                return {
                    step: {{ $initialStep ?? 1 }},
                    steps: @js([__('astrologer.steps.details'), __('astrologer.steps.photos'), __('astrologer.steps.kyc'), __('astrologer.steps.bank')]),
                    stepTemplate: @js(__('astrologer.step_progress')),
                    i18n: @js(['cameraDenied' => __('astrologer.camera_denied'), 'cameraUnavailable' => __('astrologer.camera_unavailable')]),
                    submitting: false,
                    get stepLabel() {
                        return this.stepTemplate.replace(':current', this.step).replace(':total', 4) + ' · ' + this.steps[this.step - 1];
                    },
                    maxPhotos: 6,
                    files: [],
                    previews: [],
                    camera: { open: false, ready: false, error: '', stream: null },
                    addPhotos(event) {
                        for (const file of Array.from(event.target.files)) {
                            if (this.files.length >= this.maxPhotos) break;
                            this.files.push(file);
                        }
                        event.target.value = ''; // allow re-picking the same file / next capture
                        this.syncStore();
                    },
                    removePhoto(index) {
                        this.files.splice(index, 1);
                        this.syncStore();
                    },
                    syncStore() {
                        const data = new DataTransfer();
                        this.files.forEach(file => data.items.add(file));
                        this.$refs.photoStore.files = data.files;
                        this.previews.forEach(url => URL.revokeObjectURL(url));
                        this.previews = this.files.map(file => URL.createObjectURL(file));
                    },
                    isMobile() {
                        return /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
                    },
                    takePhoto() {
                        // Phones get the native camera app; desktops open the webcam in-page.
                        if (this.isMobile() || !navigator.mediaDevices?.getUserMedia) {
                            this.$refs.cameraInput.click();
                            return;
                        }
                        this.openCamera();
                    },
                    async openCamera() {
                        this.camera.open = true;
                        this.camera.ready = false;
                        this.camera.error = '';
                        try {
                            this.camera.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
                            this.$refs.video.srcObject = this.camera.stream;
                            await this.$refs.video.play();
                            this.camera.ready = true;
                        } catch (e) {
                            this.camera.error = e?.name === 'NotAllowedError' ? this.i18n.cameraDenied : this.i18n.cameraUnavailable;
                        }
                    },
                    capturePhoto() {
                        const video = this.$refs.video;
                        if (!video?.videoWidth || this.files.length >= this.maxPhotos) {
                            this.closeCamera();
                            return;
                        }
                        const canvas = document.createElement('canvas');
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                        canvas.toBlob((blob) => {
                            if (blob) {
                                this.files.push(new File([blob], `webcam-${Date.now()}.jpg`, { type: 'image/jpeg' }));
                                this.syncStore();
                            }
                            this.closeCamera();
                        }, 'image/jpeg', 0.9);
                    },
                    closeCamera() {
                        this.camera.stream?.getTracks().forEach(track => track.stop());
                        if (this.$refs.video) {
                            this.$refs.video.srcObject = null;
                        }
                        this.camera.stream = null;
                        this.camera.open = false;
                        this.camera.ready = false;
                    },
                };
            }
        </script>
    @endpush
</x-layouts.base>
