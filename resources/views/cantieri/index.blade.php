@extends('layouts.app')

@section('title', 'Gestione Cantieri')

@section('content')
<div class="py-6">

    {{-- Header --}}
    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <h1 class="text-xl font-bold text-blue-700 mb-1">🏗️ Gestione Cantieri</h1>
    </div>

    {{-- Titolo + bottoni --}}
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-blue-600">Cantieri Operativi</h2>
        <div class="flex gap-2">
            <a href="{{ route('cantieri.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
                + Aggiungi
            </a>
            <button class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                📊 CSV
            </button>
            <button class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors">
                📄 PDF
            </button>
        </div>
    </div>

    {{-- Filtri --}}
    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <p class="text-xs font-semibold text-blue-600 mb-3">FILTRI</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Nome Cantiere</label>
                <input type="text" id="cerca-nome" placeholder="Cerca per nome..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Indirizzo</label>
                <input type="text" id="cerca-indirizzo" placeholder="Cerca per indirizzo..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Nome o Cognome Referente</label>
                <input type="text" id="cerca-referente" placeholder="Cerca referente..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    {{-- Tabella --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">ID</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Nome</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Indirizzo</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Referente</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Inizio</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Fine</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Giorni</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Stato</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Operai</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Azioni</th>
                </tr>
            </thead>
            <tbody id="tabella-cantieri">
                @forelse($cantieri as $c)
                <tr class="border-b border-gray-100 hover:bg-gray-50 cantiere-row"
                    data-nome="{{ strtolower($c->nome) }}"
                    data-indirizzo="{{ strtolower($c->indirizzo) }}"
                    data-referente="{{ strtolower($c->referente ?? '') }}">

                    <td class="py-3 px-4 text-gray-500">{{ $c->id }}</td>

                    <td class="py-3 px-4 font-semibold text-gray-800">{{ $c->nome }}</td>

                    <td class="py-3 px-4 text-gray-600">{{ $c->indirizzo }}</td>

                    <td class="py-3 px-4 text-gray-600">{{ $c->referente ?? '—' }}</td>

                    <td class="py-3 px-4 text-gray-500">
                        {{ $c->data_inizio ? \Carbon\Carbon::parse($c->data_inizio)->format('Y-m-d') : '—' }}
                    </td>

                    <td class="py-3 px-4 text-gray-500">
                        {{ $c->data_fine ? \Carbon\Carbon::parse($c->data_fine)->format('Y-m-d') : '—' }}
                    </td>

                    <td class="py-3 px-4 text-gray-500 text-xs">{{ $c->giorni ?? '—' }}</td>

                    <td class="py-3 px-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $c->stato === 'attivo' ? 'bg-blue-100 text-blue-700' :
                               ($c->stato === 'completato' ? 'bg-gray-100 text-gray-600' :
                               'bg-yellow-100 text-yellow-700') }}">
                            {{ $c->stato }}
                        </span>
                    </td>

                    <td class="py-3 px-4 text-gray-600 text-xs">
                        {{ $c->dipendenti->map(fn($d) => $d->nome . ' ' . $d->cognome)->join(', ') ?: '—' }}
                    </td>

                    <td class="py-3 px-4">
                        <div class="flex gap-2">
                            <a href="{{ route('cantieri.edit', $c) }}"
                               class="w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded flex items-center justify-center transition-colors">
                                ✏️
                            </a>
                            <form method="POST" action="{{ route('cantieri.destroy', $c) }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded flex items-center justify-center transition-colors"
                                        onclick="return confirm('Eliminare {{ $c->nome }}?')">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="py-8 text-center text-gray-400">Nessun cantiere trovato.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<script>
const cerca = () => {
    const nome      = document.getElementById('cerca-nome').value.toLowerCase();
    const indirizzo = document.getElementById('cerca-indirizzo').value.toLowerCase();
    const referente = document.getElementById('cerca-referente').value.toLowerCase();

    document.querySelectorAll('.cantiere-row').forEach(row => {
        const matchNome      = row.dataset.nome.includes(nome);
        const matchIndirizzo = row.dataset.indirizzo.includes(indirizzo);
        const matchReferente = row.dataset.referente.includes(referente);
        row.style.display    = (matchNome && matchIndirizzo && matchReferente) ? '' : 'none';
    });
};

document.getElementById('cerca-nome').addEventListener('input', cerca);
document.getElementById('cerca-indirizzo').addEventListener('input', cerca);
document.getElementById('cerca-referente').addEventListener('input', cerca);
</script>

@endsection