@if($isNativeApp ?? false)
    {{-- Mobile: native TopBar + BottomNav, no web navbar/footer --}}
    <x-layouts.mobile :title="$title ?? null">
        {{ $slot }}
    </x-layouts.mobile>
@else
    {{-- Web: standard navbar + footer --}}
    <x-layouts.base :title="$title ?? null" :description="$description ?? null" :og-image="$ogImage ?? null">
        <div class="flex min-h-screen flex-col">
            <x-customer.navbar />

            <main class="flex-1">
                {{ $slot }}
            </main>

            <x-customer.footer />
        </div>
    </x-layouts.base>
@endif
