<x-layouts.auth title="Login" subtitle="Enter your mobile number to get started">
    <div x-data="{
        step: '{{ session('otp_sent') ? 'verify' : 'mobile' }}',
        mobile: '{{ old('mobile', '') }}',
        loading: false,
    }">
        {{-- Step 1: Mobile number --}}
        <form x-show="step === 'mobile'"
              method="POST"
              action="{{ route('login.otp.request') }}"
              @submit="loading = true">
            @csrf

            <div class="space-y-5">
                <div>
                    <label for="mobile" class="mb-1.5 block text-sm font-medium text-cosmic-100">Mobile Number</label>
                    <div class="flex items-center gap-2">
                        <span class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-cosmic-200">+91</span>
                        <input type="tel"
                               name="mobile"
                               id="mobile"
                               x-model="mobile"
                               value="{{ old('mobile') }}"
                               placeholder="Enter 10-digit mobile"
                               maxlength="10"
                               inputmode="numeric"
                               pattern="[6-9][0-9]{9}"
                               autofocus
                               required
                               class="block w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder:text-cosmic-300/50 focus:border-cosmic-400 focus:outline-none focus:ring-2 focus:ring-cosmic-400/30">
                    </div>
                    @error('mobile')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        :disabled="loading || mobile.length < 10"
                        class="w-full rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-5 py-3 text-sm font-semibold text-night-950 shadow-lg shadow-gold-500/25 transition hover:from-gold-600 hover:to-gold-700 disabled:opacity-50">
                    <span x-show="!loading">Send OTP</span>
                    <span x-show="loading" class="flex items-center justify-center gap-2">
                        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Sending...
                    </span>
                </button>
            </div>
        </form>

        {{-- Step 2: OTP verification --}}
        <form x-show="step === 'verify'"
              x-cloak
              method="POST"
              action="{{ route('login.otp.verify') }}"
              @submit="loading = true">
            @csrf
            <input type="hidden" name="mobile" :value="mobile">

            <div class="space-y-5">
                <div class="text-center">
                    <p class="text-sm text-cosmic-200">
                        OTP sent to <span class="font-semibold text-white" x-text="'+91 ' + mobile"></span>
                    </p>
                    <button type="button" @click="step = 'mobile'; loading = false" class="mt-1 text-xs text-gold-400 hover:text-gold-300">
                        Change number
                    </button>
                </div>

                <div>
                    <label class="mb-2 block text-center text-sm font-medium text-cosmic-100">Enter OTP</label>
                    <x-otp-input />
                    @error('otp')
                        <p class="mt-2 text-center text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        :disabled="loading"
                        class="w-full rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-5 py-3 text-sm font-semibold text-night-950 shadow-lg shadow-gold-500/25 transition hover:from-gold-600 hover:to-gold-700 disabled:opacity-50">
                    <span x-show="!loading">Verify & Login</span>
                    <span x-show="loading" class="flex items-center justify-center gap-2">
                        <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Verifying...
                    </span>
                </button>
            </div>
        </form>

        @unless(($isNativeApp ?? false) || preg_match('/Mobile|Android|iPhone|iPad/i', request()->userAgent() ?? ''))
            <p class="mt-6 text-center text-xs text-cosmic-300/60">
                Admin? <a href="{{ route('admin.login') }}" class="text-cosmic-300 hover:text-white">Login here</a>
            </p>
        @endunless
    </div>
</x-layouts.auth>
