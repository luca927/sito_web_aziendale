@extends('layouts.app')

@section('title', 'Dipendenti')

@section('content')
<div class="py-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-blue-700">👷 Dipendenti</h1>
        <a href="{{ route('dipendenti.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
            + Aggiungi Dipendente
        </a>
    </div>

    {{-- Filtri --}}
    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <p class="text-xs font-semibold text-blue-600 mb-3">FILTRI</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Nome Dipendente</label>
                <input type="text" id="cerca-nome" placeholder="Cerca per nome..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Indirizzo</label>
                <input type="text" id="cerca-indirizzo" placeholder="Cerca per indirizzo..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Codice Fiscale</label>
                <input type="text" id="cerca-cf" placeholder="Cerca per codice fiscale..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    {{-- Tabella --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Nome</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Cognome</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Telefono</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">C. Fiscale</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Assunzione</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Azioni</th>
                </tr>
            </thead>
            <tbody id="tabella-dipendenti">
                @forelse($dipendenti as $d)
                <tr class="border-b border-gray-100 hover:bg-gray-50 dipendente-row"
                    data-nome="{{ strtolower($d->nome) }}"
                    data-cognome="{{ strtolower($d->cognome) }}"
                    data-indirizzo="{{ strtolower($d->indirizzo ?? '') }}"
                    data-cf="{{ strtolower($d->codice_fiscale ?? '') }}">
                    <td class="py-3 px-4 text-gray-800">{{ $d->nome }}</td>
                    <td class="py-3 px-4 text-gray-800">{{ $d->cognome }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ $d->telefono ?? '—' }}</td>
                    <td class="py-3 px-4">
                        @if($d->codice_fiscale)
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-mono">
                                {{ $d->codice_fiscale }}
                            </span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-gray-500">
                        {{ $d->data_assunzione ? \Carbon\Carbon::parse($d->data_assunzione)->format('d/m/Y') : '—' }}
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex gap-2">
                            <a href="{{ route('dipendenti.edit', $d) }}"
                               class="w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded flex items-center justify-center transition-colors">
                                ✏️
                            </a>
                            <form method="POST" action="{{ route('dipendenti.destroy', $d) }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded flex items-center justify-center transition-colors"
                                        onclick="return confirm('Eliminare {{ $d->nome }} {{ $d->cognome }}?')">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-400">Nessun dipendente trovato.</td>
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
    const cf        = document.getElementById('cerca-cf').value.toLowerCase();

    document.querySelectorAll('.dipendente-row').forEach(row => {
        const matchNome      = (row.dataset.nome + ' ' + row.dataset.cognome).includes(nome);
        const matchIndirizzo = row.dataset.indirizzo.includes(indirizzo);
        const matchCf        = row.dataset.cf.includes(cf);
        row.style.display    = (matchNome && matchIndirizzo && matchCf) ? '' : 'none';
    });
};

document.getElementById('cerca-nome').addEventListener('input', cerca);
document.getElementById('cerca-indirizzo').addEventListener('input', cerca);
document.getElementById('cerca-cf').addEventListener('input', cerca);
</script>

@endsection