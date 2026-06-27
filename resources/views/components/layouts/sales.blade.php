<x-layouts.base :title="$title ?? 'Sales'">
    <div class="flex min-h-screen flex-col bg-surface-alt">
        {{-- Top bar --}}
        <nav class="sticky top-0 z-40 border-b border-cosmic-100/60 bg-white/90 backdrop-blur-lg">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <a href="{{ route('sales.dashboard') }}" class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-cosmic-600 text-sm font-bold text-white">S</div>
                    <span class="font-display text-lg font-bold tracking-tight text-cosmic-900">{{ config('app.name') }} <span class="text-sm font-medium text-gray-400">Sales</span></span>
                </a>

                <div class="flex items-center gap-4">
                    <span class="hidden text-sm text-gray-500 sm:block">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg px-3 py-1.5 text-sm font-medium text-red-600 transition hover:bg-red-50">Logout</button>
                    </form>
                </div>
            </div>
        </nav>

        <main class="flex-1">
            <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</x-layouts.base>
