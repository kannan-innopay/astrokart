@props([
    'isOnline' => false,
])

<div x-data="{ online: @js($isOnline), loading: false }"
     class="flex items-center gap-2">
    <span class="text-xs font-medium" :class="online ? 'text-emerald-600' : 'text-gray-400'" x-text="online ? 'Online' : 'Offline'"></span>

    <button
        @click="
            loading = true;
            fetch(online ? '{{ route('astrologer.go-offline') }}' : '{{ route('astrologer.go-online') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' }
            })
            .then(r => { if (r.ok) online = !online; })
            .finally(() => loading = false)
        "
        :disabled="loading"
        class="relative h-6 w-11 rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-cosmic-300 focus:ring-offset-2"
        :class="online ? 'bg-emerald-500' : 'bg-gray-300'">
        <span class="absolute top-0.5 left-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition-transform duration-200"
              :class="online && 'translate-x-5'"></span>
    </button>
</div>
