@extends('layouts.app')

@section('title', 'Il mio Profilo')

@section('content')
<div class="py-6 max-w-4xl">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-6">
            @foreach($errors->all() as $error)
                <p>❌ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Card profilo sinistra --}}
        <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center text-center">

            {{-- Avatar --}}
            <div class="w-24 h-24 rounded-full bg-blue-600 text-white flex items-center justify-center text-4xl font-bold mb-4">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>

            <h2 class="text-xl font-bold text-gray-800 mb-1">{{ $user->name }}</h2>
            <p class="text-sm text-gray-400 mb-3">{{ $user->email }}</p>

            {{-- Badge ruolo --}}
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold mb-4
                {{ $user->ruolo === 'admin' ? 'bg-red-600 text-white' :
                   ($user->ruolo === 'manager' ? 'bg-yellow-500 text-white' :
                   'bg-blue-600 text-white') }}">
                🛡️ {{ $user->ruolo }}
            </span>

            <hr class="w-full border-gray-100 mb-4">

            {{-- Data iscrizione --}}
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span>📅</span>
                <span>Membro dal {{ $user->created_at->format('d/m/Y') }}</span>
            </div>

            @if($user->livello)
            <div class="flex items-center gap-2 text-sm text-gray-500 mt-2">
                <span>⭐</span>
                <span class="capitalize">Livello {{ $user->livello }}</span>
            </div>
            @endif

        </div>

        {{-- Colonna destra --}}
        <div class="md:col-span-2 flex flex-col gap-6">

            {{-- Dati personali --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                    👤 Dati Personali
                </h3>

                <form method="POST" action="{{ route('profilo.dati') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" value="{{ $user->username ?? strtolower(str_replace(' ', '.', $user->name)) }}"
                                   class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-400 cursor-not-allowed"
                                   disabled>
                            <p class="text-xs text-gray-400 mt-1">Lo username non può essere modificato</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-full text-sm font-medium transition-colors flex items-center gap-2">
                            💾 Salva Modifiche
                        </button>
                    </div>
                </form>
            </div>

            {{-- Cambia password --}}
            <div class="bg-white rounded-xl shadow p-6" x-data="{ mostraAttuale: false, mostraNuova: false, mostraConferma: false }">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4 flex items-center gap-2">
                    🔒 Cambia Password
                </h3>

                <form method="POST" action="{{ route('profilo.password') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password Attuale</label>
                        <div class="relative">
                            <input :type="mostraAttuale ? 'text' : 'password'"
                                   name="password_attuale"
                                   placeholder="Password attuale"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password_attuale') border-red-400 @enderror">
                            <button type="button" @click="mostraAttuale = !mostraAttuale"
                                    class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                <span x-text="mostraAttuale ? '🙈' : '👁️'"></span>
                            </button>
                        </div>
                        @error('password_attuale')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nuova Password</label>
                            <div class="relative">
                                <input :type="mostraNuova ? 'text' : 'password'"
                                       name="password"
                                       placeholder="Minimo 8 caratteri"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror">
                                <button type="button" @click="mostraNuova = !mostraNuova"
                                        class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                    <span x-text="mostraNuova ? '🙈' : '👁️'"></span>
                                </button>
                            </div>
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Conferma Password</label>
                            <div class="relative">
                                <input :type="mostraConferma ? 'text' : 'password'"
                                       name="password_confirmation"
                                       placeholder="Ripeti la nuova password"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="button" @click="mostraConferma = !mostraConferma"
                                        class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                    <span x-text="mostraConferma ? '🙈' : '👁️'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-full text-sm font-medium transition-colors flex items-center gap-2">
                            🔑 Aggiorna Password
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection