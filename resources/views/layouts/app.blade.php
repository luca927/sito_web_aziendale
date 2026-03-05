<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Arsnet - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar — solo desktop --}}
    <div class="hidden md:block">
        @include('components.sidebar')
    </div>

    {{-- Contenuto principale --}}
    <div class="flex flex-col flex-1 overflow-hidden">

        {{-- Header --}}
        @include('components.header')

        {{-- Messaggi flash --}}
        <div class="px-4 md:px-6 pt-4">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center gap-2">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex items-center gap-2">
                    ❌ {{ session('error') }}
                </div>
            @endif
        </div>

        {{-- Contenuto pagina --}}
        <main class="flex-1 overflow-y-auto px-4 md:px-6 pb-20 md:pb-6">
            @yield('content')
        </main>

    </div>
</div>

{{-- Navbar mobile in basso — solo mobile --}}
<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-[#1a2a4a] border-t border-blue-900 z-50">
    <div class="flex items-center justify-around py-2">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="flex flex-col items-center gap-1 px-4 py-2 rounded-lg transition-colors
                  {{ request()->routeIs('dashboard') ? 'text-white' : 'text-blue-300' }}">
            <span class="text-xl">🏠</span>
            <span class="text-xs font-medium">Home</span>
        </a>

        {{-- Timbrature --}}
        <a href="{{ route('timbrature.index') }}"
           class="flex flex-col items-center gap-1 px-4 py-2 rounded-lg transition-colors
                  {{ request()->routeIs('timbrature.*') ? 'text-white' : 'text-blue-300' }}">
            <span class="text-xl">🕐</span>
            <span class="text-xs font-medium">Timbrature</span>
        </a>

        {{-- Tracciamento --}}
        <a href="{{ route('tracciamento.index') }}"
           class="flex flex-col items-center gap-1 px-4 py-2 rounded-lg transition-colors
                  {{ request()->routeIs('tracciamento.*') ? 'text-white' : 'text-blue-300' }}">
            <span class="text-xl">📍</span>
            <span class="text-xs font-medium">Tracciamento</span>
        </a>

        {{-- Profilo --}}
        <a href="{{ route('profilo.index') }}"
           class="flex flex-col items-center gap-1 px-4 py-2 rounded-lg transition-colors
                  {{ request()->routeIs('profilo.*') ? 'text-white' : 'text-blue-300' }}">
            <span class="text-xl">👤</span>
            <span class="text-xs font-medium">Profilo</span>
        </a>

    </div>
</nav>

</body>
</html>