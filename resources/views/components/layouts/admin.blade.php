<x-layouts.base :title="$title ?? 'Admin'">
    <div class="flex min-h-screen" x-data="{ sidebarOpen: true }">
        <x-admin.sidebar />

        <div class="flex flex-1 flex-col transition-all duration-200" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-16'">
            <x-admin.topbar />

            <main class="flex-1 bg-surface-alt p-6 lg:p-8">
                @isset($header)
                    <div class="mb-6">
                        <h1 class="font-display text-2xl font-semibold text-gray-900">{{ $header }}</h1>
                    </div>
                @endisset

                {{ $slot }}
            </main>
        </div>
    </div>
</x-layouts.base>
