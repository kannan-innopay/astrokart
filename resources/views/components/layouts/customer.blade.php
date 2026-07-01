@props([
    'title' => null,
    'description' => null,
    'ogImage' => null,
    'container' => true,
])

@if($isNativeApp ?? false)
    {{-- Mobile: native TopBar + BottomNav, no web navbar/footer --}}
    <x-layouts.mobile :title="$title">
        @if($container)
            <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6">{{ $slot }}</div>
        @else
            {{ $slot }}
        @endif
    </x-layouts.mobile>
@else
    {{-- Web: standard navbar + footer --}}
    <x-layouts.base :title="$title" :description="$description" :og-image="$ogImage">
        <div class="flex min-h-screen flex-col">
            <x-customer.navbar />

            <main class="flex-1">
                @if($container)
                    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">{{ $slot }}</div>
                @else
                    {{ $slot }}
                @endif
            </main>

            <x-customer.footer />
        </div>
    </x-layouts.base>
@endif
