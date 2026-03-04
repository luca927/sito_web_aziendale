<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 flex-shrink-0">

    <div></div>

    <div class="flex items-center gap-4">
        <span class="text-sm text-gray-600">
            Ciao, <strong>{{ auth()->user()->name }}</strong>
        </span>
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </button>
            <div x-show="open" @click.away="open = false"
                 class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg border border-gray-100 z-50">
                <a href="{{ route('profilo.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profilo</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

</header>