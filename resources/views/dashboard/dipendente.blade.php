@extends('layouts.app')

@section('title', 'La mia Dashboard')

@section('content')
<div class="py-6">

    {{-- Saluto + Avatar --}}
    <div class="flex flex-col items-center mb-6">
        <div class="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center text-2xl font-bold mb-3">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-800">
                Buongiorno, {{ explode(' ', auth()->user()->name)[0] }} 👋
            </h1>
            <p class="text-gray-400 text-sm">
                {{ now()->locale('it')->isoFormat('dddd DD MMMM YYYY') }}
            </p>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        {{-- Cantiere attuale --}}
        <div class="bg-blue-600 rounded-xl p-5 text-white flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">
                🏗️
            </div>
            <div>
                <p class="text-xs uppercase font-semibold text-blue-200 mb-1">Cantiere Attuale</p>
                <p class="text-lg font-bold leading-tight">
                    {{ $dati['cantieri']->first()?->nome ?? 'Non assegnato' }}
                </p>
            </div>
        </div>

        {{-- Mezzo assegnato --}}
        <div class="bg-green-700 rounded-xl p-5 text-white flex items-center gap-4">
            <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">
                🚛
            </div>
            <div>
                <p class="text-xs uppercase font-semibold text-green-200 mb-1">Mezzo Assegnato</p>
                <p class="text-lg font-bold leading-tight">
                    {{ $dati['mezzi']->first() ? strtoupper($dati['mezzi']->first()->modello ?? $dati['mezzi']->first()->tipo) : 'Non assegnato' }}
                </p>
            </div>
        </div>

        {{-- Attività totali --}}
        <div class="bg-orange-500 rounded-xl p-5 text-white flex items-center gap-4">
            <div class="w-12 h-12 bg-orange-400 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">
                📋
            </div>
            <div>
                <p class="text-xs uppercase font-semibold text-orange-100 mb-1">Attività Totali</p>
                <p class="text-3xl font-bold">{{ $dati['totale_attivita'] }}</p>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Colonna sinistra — profilo --}}
        <div class="flex flex-col gap-4">

            {{-- Card profilo --}}
            <div class="bg-white rounded-xl shadow p-6 text-center">
                <div class="w-20 h-20 rounded-full bg-blue-600 text-white flex items-center justify-center text-3xl font-bold mx-auto mb-3">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <h2 class="text-lg font-bold text-gray-800 mb-1">{{ auth()->user()->name }}</h2>
                @if(auth()->user()->livello)
                    <span class="bg-blue-600 text-white text-xs px-3 py-1 rounded-full font-medium capitalize">
                        {{ auth()->user()->livello }}
                    </span>
                @endif

                <div class="mt-4 space-y-3 text-left">
                    @if($dati['dipendente']->telefono)
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <span>📞</span>
                        <div>
                            <p class="text-xs text-gray-400">Telefono</p>
                            <p class="font-medium">{{ $dati['dipendente']->telefono }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <span>✉️</span>
                        <div>
                            <p class="text-xs text-gray-400">Email</p>
                            <p class="font-medium">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    @if($dati['dipendente']->data_assunzione)
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <span>💼</span>
                        <div>
                            <p class="text-xs text-gray-400">Assunto il</p>
                            <p class="font-medium">
                                {{ \Carbon\Carbon::parse($dati['dipendente']->data_assunzione)->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Card cantiere attuale --}}
            @if($dati['cantieri']->first())
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="bg-blue-600 px-4 py-3 flex items-center gap-2">
                    <span>📍</span>
                    <h3 class="text-white font-semibold text-sm">Cantiere Attuale</h3>
                </div>
                <div class="p-4">
                    <p class="font-bold text-gray-800 mb-1">{{ $dati['cantieri']->first()->nome }}</p>
                    <p class="text-sm text-gray-500 flex items-center gap-1">
                        <span>📍</span> {{ $dati['cantieri']->first()->indirizzo }}
                    </p>
                    @if($dati['cantieri']->first()->latitudine && $dati['cantieri']->first()->longitudine)
                        <a href="https://maps.google.com/?q={{ $dati['cantieri']->first()->latitudine }},{{ $dati['cantieri']->first()->longitudine }}"
                           target="_blank"
                           class="mt-3 w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                            🗺️ Apri Mappa
                        </a>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- Colonna destra --}}
        <div class="md:col-span-2 flex flex-col gap-4">

            {{-- Attività recenti --}}
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="bg-blue-600 px-4 py-3 flex items-center gap-2">
                    <span>📋</span>
                    <h3 class="text-white font-semibold text-sm">Le Mie Attività Recenti</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50">
                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Cantiere</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Mezzo</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Targa</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dati['timbrature'] as $t)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold uppercase
                                        {{ $t->causale === 'Straordinario' ? 'bg-yellow-100 text-yellow-700' :
                                           ($t->causale === 'Trasferta' ? 'bg-purple-100 text-purple-700' :
                                           'bg-green-100 text-green-700') }}">
                                        {{ $t->causale ?? 'Lavoro Ordinario' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-700">{{ $t->cantiere->nome ?? '—' }}</td>
                                <td class="py-3 px-4 text-gray-700 uppercase">—</td>
                                <td class="py-3 px-4">
                                    <span class="bg-gray-800 text-white px-2 py-0.5 rounded text-xs font-mono">—</span>
                                </td>
                                <td class="py-3 px-4 text-gray-500">
                                    {{ \Carbon\Carbon::parse($t->entrata)->format('d/m/Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-400">Nessuna attività recente.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Mezzo assegnato --}}
            @if($dati['mezzi']->first())
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="bg-blue-600 px-4 py-3 flex items-center gap-2">
                    <span>🚛</span>
                    <h3 class="text-white font-semibold text-sm">Mezzo Assegnato</h3>
                </div>
                <div class="p-4 flex items-center gap-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center text-3xl flex-shrink-0">
                        🚛
                    </div>
                    <div>
                        <p class="font-bold text-gray-800 uppercase text-lg">
                            {{ $dati['mezzi']->first()->modello ?? $dati['mezzi']->first()->tipo }}
                        </p>
                        <span class="bg-gray-800 text-white px-3 py-1 rounded-full text-xs font-mono">
                            # {{ $dati['mezzi']->first()->targa }}
                        </span>
                    </div>
                </div>
            </div>
            @endif

        </div>

    </div>

</div>
@endsection