<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $pageTitle = isset($title) ? $title . ' — ' . config('app.name') : config('app.name') . ' — Vedic Astrology Consultations';
        $pageDescription = $description ?? config('app.description');
        $ogImage = $ogImage ?? asset('og-image.png');
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="min-h-screen bg-surface font-sans text-gray-800 antialiased">
    {{-- Flash messages --}}
    @if (session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if (session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    {{ $slot }}

    @stack('scripts')
</body>
</html>
