<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#6d28d9">

    <title>{{ isset($title) ? $title . ' — Astrokart' : 'Astrokart' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body.keyboard-visible .mobile-bottom-nav { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-surface font-sans text-gray-800 antialiased">

    {{-- Header: background extends behind status bar, content pushed down --}}
    <header class="fixed-app-header bg-cosmic-700 text-white">
        <div class="fixed-app-header-inner">
            <h1 class="font-display text-lg font-bold tracking-tight">
                Astro<span class="text-gold-400">kart</span>
            </h1>
        </div>
    </header>

    {{-- Flash messages --}}
    @if (session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if (session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    {{-- Main content --}}
    <main class="fixed-app-main">
        {{ $slot }}
    </main>

    {{-- Bottom navigation --}}
    <nav class="mobile-bottom-nav fixed-app-nav border-t border-gray-200 bg-white">
        <div class="flex items-center justify-around py-2">
            @php
                $tabs = [
                    ['route' => 'home', 'label' => 'Home', 'icon' => 'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25'],
                    ['route' => 'horoscope.show', 'label' => 'Chart', 'icon' => 'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 0 0-2.455 2.456Z'],
                    ['route' => 'predictions.daily', 'label' => 'Predictions', 'icon' => 'M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z'],
                    ['route' => 'horoscope.hora', 'label' => 'Hora', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
                    ['route' => 'profile.edit', 'label' => 'Profile', 'icon' => 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z'],
                ];
            @endphp
            @foreach($tabs as $tab)
                @php $active = request()->routeIs($tab['route'] . '*'); @endphp
                <a href="{{ route($tab['route']) }}" class="flex flex-col items-center gap-0.5 px-2 py-1 {{ $active ? 'text-cosmic-600' : 'text-gray-400' }}">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $tab['icon'] }}"/></svg>
                    <span class="text-[10px] font-medium">{{ $tab['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    @stack('scripts')
</body>
</html>
