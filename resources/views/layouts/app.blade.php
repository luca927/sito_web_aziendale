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

    {{-- VUE GESTISCE SOLO QUESTO PEZZETTO --}}
    <div id="app">
        <toast-container></toast-container>
    </div> {{-- CHIUDI SUBITO IL DIV #APP QUI --}}

    {{-- TUTTO IL RESTO È FUORI DA VUE --}}
    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        <div class="hidden md:block">
            @include('components.sidebar')
        </div>

        <div class="flex flex-col flex-1 overflow-hidden">
            @include('components.header')

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

            <main class="flex-1 overflow-y-auto px-4 md:px-6 pb-20 md:pb-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Navbar mobile --}}
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-[#1a2a4a] border-t border-blue-900 z-50">
        <div class="flex items-center justify-around py-2">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'text-white' : 'text-blue-300' }}">
                <span class="text-xl">🏠</span>
                <span class="text-xs font-medium">Home</span>
            </a>
            <a href="{{ route('timbrature.index') }}" class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('timbrature.*') ? 'text-white' : 'text-blue-300' }}">
                <span class="text-xl">🕐</span>
                <span class="text-xs font-medium">Timbrature</span>
            </a>
            <a href="{{ route('tracciamento.index') }}" class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('tracciamento.*') ? 'text-white' : 'text-blue-300' }}">
                <span class="text-xl">📍</span>
                <span class="text-xs font-medium">Tracciamento</span>
            </a>
            <a href="{{ route('profilo.index') }}" class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('profilo.*') ? 'text-white' : 'text-blue-300' }}">
                <span class="text-xl">👤</span>
                <span class="text-xs font-medium">Profilo</span>
            </a>
            
            <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                @csrf
            </form>

            <a href="#" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg text-red-400 hover:text-red-300">
                <span class="text-xl">🚪</span>
                <span class="text-xs font-medium">Esci</span>
            </a>
        </div>
    </nav> {{-- CHIUSURA DIV #APP --}}

    {{-- SCRIPT GLOBALI (FUORI DA #APP) --}}
    @if(auth()->check() && auth()->user()->isAdmin())
    <script>
        const orarioAccessoPagina = new Date();
        const timbratureViste = new Set();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const controllaNotifiche = async () => {
            if (!csrfToken) return;
            try {
                const res = await fetch('/timbrature/notifiche', {
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                if (res.status === 419 || res.status === 401) return;
                
                const data = await res.json();
                data.forEach(t => {
                    const [ore, minuti] = t.ora.split(':');
                    const orarioT = new Date();
                    orarioT.setHours(ore, minuti, 0);

                    if (!timbratureViste.has(t.id) && orarioT >= orarioAccessoPagina) {
                        timbratureViste.add(t.id);
                        if (window.mostraToastVue) window.mostraToastVue(t.dipendente, t.tipo, t.ora);
                    }
                });
            } catch (e) { console.error('Errore notifiche:', e); }
        };
        setInterval(controllaNotifiche, 5000);
    </script>
    @endif

    {{-- QUESTO CARICA GLI SCRIPT DELLE PAGINE FIGLIE (Chart.js, FullCalendar, ecc) --}}
    @stack('scripts')

</body>
</html>