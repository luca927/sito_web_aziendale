@extends('layouts.app')

@section('title', 'Dashboard Amministrativa')

@section('content')
<div class="py-6">

    {{-- Titolo --}}
    <h1 class="text-2xl font-bold text-blue-700 mb-6">📊 Dashboard Amministrativa</h1>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <div class="bg-blue-500 rounded-xl p-6 text-white">
            <div class="flex items-center gap-3 mb-2">
                <span class="text-2xl">👷</span>
                <span class="text-lg font-semibold">Dipendenti</span>
            </div>
            <p class="text-4xl font-bold">{{ $dati['totale_dipendenti'] }}</p>
        </div>

        <div class="bg-green-700 rounded-xl p-6 text-white">
            <div class="flex items-center gap-3 mb-2">
                <span class="text-2xl">🏗️</span>
                <span class="text-lg font-semibold">Cantieri Attivi</span>
            </div>
            <p class="text-4xl font-bold">{{ $dati['cantieri_attivi'] }}</p>
        </div>

        <div class="bg-yellow-500 rounded-xl p-6 text-white">
            <div class="flex items-center gap-3 mb-2">
                <span class="text-2xl">🚛</span>
                <span class="text-lg font-semibold">Mezzi Disponibili</span>
            </div>
            <p class="text-4xl font-bold">{{ $dati['totale_mezzi'] }}</p>
        </div>

    </div>

    {{-- Registra Nuova Operazione --}}
    <div class="bg-white rounded-xl shadow p-6 mb-8 border-l-4 border-blue-500">
        <h2 class="text-lg font-semibold text-blue-600 mb-4">Registra Nuova Operazione</h2>
        <form method="POST" action="{{ route('tracciamento.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Tipo Attività</label>
                    <select name="tipo_attivita"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleziona</option>
                        <option value="lavorazione">Lavorazione</option>
                        <option value="manutenzione">Manutenzione</option>
                        <option value="sopralluogo">Sopralluogo</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Dipendente</label>
                    <select name="dipendente_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleziona Dipendente</option>
                        @foreach($dati['dipendenti'] as $d)
                            <option value="{{ $d->id }}">{{ $d->nome }} {{ $d->cognome }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Cantiere</label>
                    <select name="cantiere_id" id="cantiere_select"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleziona Cantiere</option>
                        @foreach($dati['cantieri'] as $c)
                            <option value="{{ $c->id }}"
                                    data-lat="{{ $c->latitudine }}"
                                    data-lng="{{ $c->longitudine }}">
                                {{ $c->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Mezzo</label>
                    <select name="mezzo_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleziona Mezzo</option>
                        @foreach($dati['mezzi'] as $m)
                            <option value="{{ $m->id }}">{{ $m->modello ?? $m->tipo }} - {{ $m->targa }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <input type="hidden" name="data_ora" value="{{ now() }}">

            <button type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors">
                + AGGIUNGI OPERAZIONE
            </button>

            <p class="text-xs text-gray-400 mt-2">
                ℹ️ Le coordinate vengono caricate automaticamente in base al cantiere selezionato.
            </p>
        </form>
    </div>

    {{-- Tabella operazioni --}}
    <div class="bg-white rounded-xl shadow p-6">

        {{-- Toolbar --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                Mostra
                <select class="border border-gray-300 rounded px-2 py-1 text-sm">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                </select>
                voci
            </div>
            <div class="flex items-center gap-3">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                    📋 Report
                </button>
                <input type="text" placeholder="Cerca..."
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        {{-- Tabella --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left py-3 px-2 text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                    <th class="text-left py-3 px-2 text-xs font-semibold text-gray-500 uppercase">Dipendente</th>
                    <th class="text-left py-3 px-2 text-xs font-semibold text-gray-500 uppercase">Cantiere</th>
                    <th class="text-left py-3 px-2 text-xs font-semibold text-gray-500 uppercase">Mezzo</th>
                    <th class="text-left py-3 px-2 text-xs font-semibold text-gray-500 uppercase">Coordinate</th>
                    <th class="text-left py-3 px-2 text-xs font-semibold text-gray-500 uppercase">Data</th>
                    <th class="text-left py-3 px-2 text-xs font-semibold text-gray-500 uppercase">Azioni</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dati['ultimi_tracciamenti'] as $t)
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-3 px-2">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 uppercase">
                            {{ $t->tipo_attivita ?? 'lavorazione' }}
                        </span>
                    </td>
                    <td class="py-3 px-2 text-blue-600 font-medium">
                        {{ $t->dipendente->nome }} {{ $t->dipendente->cognome }}
                    </td>
                    <td class="py-3 px-2 text-gray-700">{{ $t->cantiere->nome }}</td>
                    <td class="py-3 px-2 text-gray-700">
                        {{ $t->mezzo->modello ?? $t->mezzo->tipo ?? '—' }}
                    </td>
                    <td class="py-3 px-2">
                        @if($t->cantiere->latitudine && $t->cantiere->longitudine)
                            <a href="https://maps.google.com/?q={{ $t->cantiere->latitudine }},{{ $t->cantiere->longitudine }}"
                               target="_blank"
                               class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs hover:bg-blue-200 transition-colors">
                                📍 Vedi Mappa
                            </a>
                        @else
                            <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                    <td class="py-3 px-2 text-gray-500">
                        {{ \Carbon\Carbon::parse($t->data_ora)->format('d/m/Y') }}
                    </td>
                    <td class="py-3 px-2">
                        <div class="flex gap-2">
                            <a href="#"
                               class="w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded flex items-center justify-center transition-colors">
                                ✏️
                            </a>
                            <form method="POST" action="#">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded flex items-center justify-center transition-colors"
                                        onclick="return confirm('Sicuro?')">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-gray-400">Nessuna operazione registrata.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>
@endsection