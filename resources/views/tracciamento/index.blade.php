@extends('layouts.app')

@section('title', 'Gestione Tracciamenti')

@section('content')
<div class="py-6">

    {{-- Header --}}
    <div class="bg-blue-600 rounded-xl px-6 py-4 mb-6 flex items-center justify-between">
        <h1 class="text-xl font-bold text-white flex items-center gap-2">
            📍 Gestione Tracciamenti
        </h1>
    </div>

    {{-- Titolo + bottone aggiungi --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-blue-600">Registro Attività</h2>
        <div class="flex gap-2">
            <a href="{{ route('tracciamento.export-csv') }}"
            class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                📊 Export CSV
            </a>
            <button onclick="document.getElementById('modale-aggiungi').classList.remove('hidden')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                + Aggiungi Tracciamento
            </button>
        </div>
    </div>

    {{-- Tabella --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">ID</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Dipendente</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Cantiere</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Mezzo</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Latitudine</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Longitudine</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Data/Ora</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Azioni</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tracciamenti as $t)
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-3 px-4 text-gray-500">{{ $t->id }}</td>
                    <td class="py-3 px-4 font-medium text-gray-800">
                        {{ $t->dipendente->nome }} {{ $t->dipendente->cognome }}
                    </td>
                    <td class="py-3 px-4 text-gray-700">{{ $t->cantiere->nome }}</td>
                    <td class="py-3 px-4 text-gray-700">
                        {{ $t->mezzo->modello ?? $t->mezzo->tipo ?? '—' }}
                    </td>
                    <td class="py-3 px-4 text-gray-500">
                        {{ $t->cantiere->latitudine ?? '—' }}
                    </td>
                    <td class="py-3 px-4 text-gray-500">
                        {{ $t->cantiere->longitudine ?? '—' }}
                    </td>
                    <td class="py-3 px-4 text-gray-500">
                        {{ \Carbon\Carbon::parse($t->data_ora)->format('Y-m-d H:i:s') }}
                    </td>
                    <td class="py-3 px-4">
                        <form method="POST" action="{{ route('tracciamento.destroy', $t) }}">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded flex items-center justify-center transition-colors"
                                    onclick="return confirm('Eliminare questo tracciamento?')">
                                🗑️
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-8 text-center text-gray-400">
                        Nessun tracciamento registrato.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- MODALE AGGIUNGI TRACCIAMENTO --}}
<div id="modale-aggiungi"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">

    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">+ Aggiungi Tracciamento</h3>
            <button onclick="document.getElementById('modale-aggiungi').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
        </div>

        <form method="POST" action="{{ route('tracciamento.store') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 gap-4 mb-4">

                @if(auth()->user()->isAdmin())
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dipendente</label>
                    <select name="dipendente_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleziona dipendente</option>
                        @foreach($dipendenti as $d)
                            <option value="{{ $d->id }}">{{ $d->nome }} {{ $d->cognome }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantiere</label>
                    <select name="cantiere_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleziona cantiere</option>
                        @foreach($cantieri as $c)
                            <option value="{{ $c->id }}">{{ $c->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mezzo</label>
                    <select name="mezzo_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleziona mezzo</option>
                        @foreach($mezzi as $m)
                            <option value="{{ $m->id }}">{{ $m->modello ?? $m->tipo }} - {{ $m->targa }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Attività</label>
                    <select name="tipo_attivita"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="lavorazione">Lavorazione</option>
                        <option value="manutenzione">Manutenzione</option>
                        <option value="sopralluogo">Sopralluogo</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data e Ora</label>
                    <input type="datetime-local" name="data_ora"
                           value="{{ now()->format('Y-m-d\TH:i') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                    <textarea name="note" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Opzionale..."></textarea>
                </div>

            </div>

            <div class="flex gap-3 justify-end">
                <button type="button"
                        onclick="document.getElementById('modale-aggiungi').classList.add('hidden')"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium">
                    Annulla
                </button>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium">
                    Salva
                </button>
            </div>
        </form>
    </div>
</div>

@endsection