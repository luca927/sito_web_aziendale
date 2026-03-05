<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsnet - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-3 mb-2">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-xl">
                    A
                </div>
                <span class="text-3xl font-bold text-[#1a2a4a]">Arsnet</span>
            </div>
            <p class="text-gray-500 text-sm">Accedi al gestionale aziendale</p>
        </div>

        {{-- Card login --}}
        <div class="bg-white rounded-2xl shadow-lg p-8">

            {{-- Session status --}}
            @if(session('status'))
                <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           required autofocus autocomplete="username"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-4" x-data="{ mostra: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <div class="relative">
                        <input id="password" :type="mostra ? 'text' : 'password'"
                               name="password"
                               required autocomplete="current-password"
                               class="w-full border border-gray-300 rounded-xl px-4 py-3 pr-12 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror">
                        <button type="button" @click="mostra = !mostra"
                                class="absolute right-4 top-3.5 text-gray-400 hover:text-gray-600">
                            <span x-text="mostra ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        Ricordami
                    </label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-sm text-blue-600 hover:text-blue-700 hover:underline">
                            Password dimenticata?
                        </a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl text-sm font-semibold transition-colors">
                    Accedi
                </button>

            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            © {{ date('Y') }} Arsnet — Gestionale Aziendale
        </p>

    </div>

</body>
</html>