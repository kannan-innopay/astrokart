<x-layouts.base :title="$title ?? 'Astrologer Dashboard'">
    <div class="flex min-h-screen flex-col">
        <x-astrologer.navbar />

        <main class="flex-1 bg-surface-alt">
            <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</x-layouts.base>
