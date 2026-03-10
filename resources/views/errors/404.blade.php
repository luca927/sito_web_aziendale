<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsnet - Pagina non trovata</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center px-4">

    <div class="text-center max-w-md">

        {{-- Numero 404 --}}
        <div class="text-9xl font-bold text-blue-600 mb-4 leading-none">404</div>

        {{-- Icona --}}
        <div class="text-6xl mb-6">🏗️</div>

        {{-- Testo --}}
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Pagina non trovata</h1>
        <p class="text-gray-500 mb-8">
            La pagina che stai cercando non esiste o è stata spostata.
        </p>

        {{-- Bottoni --}}
        <div class="flex gap-3 justify-center">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl text-sm font-medium transition-colors">
                    🏠 Torna alla Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl text-sm font-medium transition-colors">
                    🔑 Vai al Login
                </a>
            @endauth

            <button onclick="history.back()"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-xl text-sm font-medium transition-colors">
                ← Indietro
            </button>
        </div>

        {{-- Footer --}}
        <p class="text-xs text-gray-400 mt-8">Arsnet — Gestionale Aziendale</p>

    </div>

</body>
</html>