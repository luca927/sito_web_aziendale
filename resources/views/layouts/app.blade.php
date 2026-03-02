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

    {{-- Sidebar --}}
    @include('components.sidebar')

    {{-- Contenuto principale --}}
    <div class="flex flex-col flex-1 overflow-hidden">

        {{-- Header --}}
        @include('components.header')

        {{-- Messaggi flash --}}
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    ❌ {{ session('error') }}
                </div>
            @endif
        </div>

        {{-- Contenuto pagina --}}
        <main class="flex-1 overflow-y-auto px-6 pb-6">
            @yield('content')
        </main>

    </div>
</div>

</body>
</html>