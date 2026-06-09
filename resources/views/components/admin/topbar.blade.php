<header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-cosmic-100 bg-white/80 px-6 backdrop-blur-lg lg:px-8">
    <div class="flex items-center gap-3">
        {{-- Mobile sidebar toggle --}}
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-gray-600 lg:hidden">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
        </button>
        <span class="text-sm text-gray-400">Admin Panel</span>
    </div>

    <div class="flex items-center gap-4">
        <span class="text-sm font-medium text-gray-600">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-gray-400 transition hover:text-red-500">Logout</button>
        </form>
    </div>
</header>
