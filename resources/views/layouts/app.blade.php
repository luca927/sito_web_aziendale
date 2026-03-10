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

        <a href="{{ route('dashboard') }}"
           class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg transition-colors
                  {{ request()->routeIs('dashboard') ? 'text-white' : 'text-blue-300' }}">
            <span class="text-xl">🏠</span>
            <span class="text-xs font-medium">Home</span>
        </a>

        <a href="{{ route('timbrature.index') }}"
           class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg transition-colors
                  {{ request()->routeIs('timbrature.*') ? 'text-white' : 'text-blue-300' }}">
            <span class="text-xl">🕐</span>
            <span class="text-xs font-medium">Timbrature</span>
        </a>

        <a href="{{ route('tracciamento.index') }}"
           class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg transition-colors
                  {{ request()->routeIs('tracciamento.*') ? 'text-white' : 'text-blue-300' }}">
            <span class="text-xl">📍</span>
            <span class="text-xs font-medium">Tracciamento</span>
        </a>

        <a href="{{ route('profilo.index') }}"
           class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg transition-colors
                  {{ request()->routeIs('profilo.*') ? 'text-white' : 'text-blue-300' }}">
            <span class="text-xl">👤</span>
            <span class="text-xs font-medium">Profilo</span>
        </a>

        {{-- Logout mobile --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="flex flex-col items-center gap-1 px-3 py-2 rounded-lg transition-colors text-red-400 hover:text-red-300">
                <span class="text-xl">🚪</span>
                <span class="text-xs font-medium">Esci</span>
            </button>
        </form>

    </div>
</nav>

{{-- Sistema Toast Notifiche --}}
<div id="toast-container"
     class="fixed top-4 right-4 z-50 flex flex-col gap-3 pointer-events-none"
     style="max-width: 320px;">
</div>

@if(auth()->check() && auth()->user()->isAdmin())
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const mostraToast = (dipendente, tipo, ora) => {
    const container = document.getElementById('toast-container');
    const isEntrata = tipo === 'entrata';

    const toast = document.createElement('div');
    toast.className = [
        'pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg',
        'border-l-4 bg-white text-sm font-medium text-gray-800',
        'transform translate-x-full transition-transform duration-300',
        isEntrata ? 'border-green-500' : 'border-red-500'
    ].join(' ');

    toast.innerHTML = `
        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0 ${isEntrata ? 'bg-green-500' : 'bg-red-500'}">
            ${dipendente.charAt(0).toUpperCase()}
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-semibold truncate">${dipendente}</p>
            <p class="text-xs ${isEntrata ? 'text-green-600' : 'text-red-500'}">
                ${isEntrata ? '↗ Entrata' : '↙ Uscita'} alle ${ora}
            </p>
        </div>
        <button onclick="this.parentElement.remove()"
                class="text-gray-300 hover:text-gray-500 text-lg leading-none flex-shrink-0">✕</button>
    `;

    container.appendChild(toast);

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full');
        });
    });

    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
};

const timbratureViste = new Set();

const controllaNotifiche = async () => {
    try {
        const res = await fetch('/timbrature/notifiche', {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            }
        });

        // Se sessione scaduta smetti di fare polling
        if (res.status === 419 || res.status === 401) {
            clearInterval(intervalNotifiche);
            return;
        }

        const data = await res.json();
        data.forEach(t => {
            if (!timbratureViste.has(t.id)) {
                timbratureViste.add(t.id);
                mostraToast(t.dipendente, t.tipo, t.ora);
            }
        });
    } catch (e) {
        console.error('Errore notifiche:', e);
    }
};

controllaNotifiche();
const intervalNotifiche = setInterval(controllaNotifiche, 30000);
</script>
@endif
</body>
</html>