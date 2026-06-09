<x-layouts.auth title="Admin Login" subtitle="Sign in to the admin panel">
    <form method="POST" action="{{ route('admin.login.submit') }}" x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <div class="space-y-5">
            <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-cosmic-100">Email</label>
                <input type="email"
                       name="email"
                       id="email"
                       value="{{ old('email') }}"
                       placeholder="admin@astrokart.com"
                       required
                       autofocus
                       class="block w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder:text-cosmic-300/50 focus:border-cosmic-400 focus:outline-none focus:ring-2 focus:ring-cosmic-400/30">
                @error('email')
                    <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-cosmic-100">Password</label>
                <input type="password"
                       name="password"
                       id="password"
                       placeholder="Enter your password"
                       required
                       class="block w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white placeholder:text-cosmic-300/50 focus:border-cosmic-400 focus:outline-none focus:ring-2 focus:ring-cosmic-400/30">
                @error('password')
                    <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    :disabled="loading"
                    class="w-full rounded-xl bg-cosmic-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-cosmic-600/25 transition hover:bg-cosmic-700 disabled:opacity-50">
                <span x-show="!loading">Sign In</span>
                <span x-show="loading" class="flex items-center justify-center gap-2">
                    <svg class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    Signing in...
                </span>
            </button>
        </div>

        <p class="mt-6 text-center text-xs text-cosmic-300/60">
            Not an admin? <a href="{{ route('login') }}" class="text-cosmic-300 hover:text-white">User login</a>
        </p>
    </form>
</x-layouts.auth>
