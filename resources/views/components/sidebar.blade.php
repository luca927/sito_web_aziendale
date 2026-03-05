<aside class="w-64 bg-[#1a2a4a] text-white flex flex-col flex-shrink-0 h-full min-h-screen">

    {{-- Logo --}}
    <div class="h-16 flex items-center px-6 border-b border-blue-900">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-500 rounded flex items-center justify-center text-xs font-bold">
                D
            </div>
            <span class="text-lg font-bold tracking-wide">Arsnet</span>
        </div>
    </div>

    {{-- Navigazione --}}
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
            <span>🏠</span> Dashboard
        </a>

        @if(auth()->user()->isAdmin())

            {{-- Dipendenti con sottomenu --}}
            <div x-data="{ open: {{ request()->routeIs('dipendenti.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-medium transition-colors
                               {{ request()->routeIs('dipendenti.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
                    <span class="flex items-center gap-3"><span>👷</span> Dipendenti</span>
                    <span x-text="open ? '▲' : '▼'" class="text-xs"></span>
                </button>
                <div x-show="open" class="ml-4 mt-1 space-y-1">
                    <a href="{{ route('dipendenti.index') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-blue-200 hover:bg-blue-800">
                        Lista Dipendenti
                    </a>
                    <a href="{{ route('dipendenti.create') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-blue-200 hover:bg-blue-800">
                        + Aggiungi
                    </a>
                </div>
            </div>

            {{-- Mezzi con sottomenu --}}
            <div x-data="{ open: {{ request()->routeIs('mezzi.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                        class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg text-sm font-medium transition-colors
                               {{ request()->routeIs('mezzi.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
                    <span class="flex items-center gap-3"><span>🚛</span> Mezzi</span>
                    <span x-text="open ? '▲' : '▼'" class="text-xs"></span>
                </button>
                <div x-show="open" class="ml-4 mt-1 space-y-1">
                    <a href="{{ route('mezzi.index') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-blue-200 hover:bg-blue-800">
                        Lista Mezzi
                    </a>
                    <a href="{{ route('mezzi.create') }}"
                       class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-blue-200 hover:bg-blue-800">
                        + Aggiungi
                    </a>
                </div>
            </div>

            {{-- Cantieri --}}
            <a href="{{ route('cantieri.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors
                      {{ request()->routeIs('cantieri.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
                <span>🏗️</span> Cantieri
            </a>

            {{-- Gestione Utenti --}}
            <a href="{{ route('gestione_utenti.index') }}"
               class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors text-blue-100 hover:bg-blue-800">
                <span>👥</span> Gestione Utenti
            </a>

        @endif

        {{-- Timbrature --}}
        <a href="{{ route('timbrature.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('timbrature.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
            <span>🕐</span> Timbrature
        </a>

        {{-- Tracciamento --}}
        <a href="{{ route('tracciamento.index') }}"
           class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('tracciamento.*') ? 'bg-blue-600 text-white' : 'text-blue-100 hover:bg-blue-800' }}">
            <span>📍</span> Tracciamento
        </a>

    </nav>

    {{-- Utente in basso --}}
    <div class="border-t border-blue-900 p-4">
        <a href="{{ route('profilo.index') }}"
        class="flex items-center gap-3 hover:bg-blue-800 rounded-lg p-2 transition-colors">
            <div class="w-9 h-9 rounded-full bg-blue-500 flex items-center justify-center text-sm font-bold flex-shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-blue-300 capitalize">{{ auth()->user()->ruolo }}</p>
            </div>
            <span class="text-blue-300 text-xs">⚙️</span>
        </a>
    </div>

</aside>