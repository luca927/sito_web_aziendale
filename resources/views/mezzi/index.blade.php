@extends('layouts.app')

@section('title', 'Gestione Parco Mezzi')

@section('content')
<div class="py-6">

    {{-- Banner manutenzioni scadute --}}
    @if($mezziScaduti->count() > 0)
        <div class="bg-red-50 border border-red-300 rounded-xl p-4 mb-4">
            <div class="flex items-start gap-3">
                <span class="text-2xl">🚨</span>
                <div>
                    <p class="font-semibold text-red-700 mb-2">
                        {{ $mezziScaduti->count() }} mezzo/i con manutenzione SCADUTA!
                    </p>
                    @foreach($mezziScaduti as $m)
                        <div class="flex items-center gap-2 text-sm text-red-600">
                            <span>•</span>
                            <span class="font-medium">{{ $m->modello ?? $m->tipo }}</span>
                            <span class="font-mono bg-red-100 px-1.5 rounded">{{ $m->targa }}</span>
                            <span>— scaduta il {{ \Carbon\Carbon::parse($m->prossima_manutenzione)->format('d/m/Y') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Banner manutenzioni in scadenza --}}
    @if($mezziInScadenza->count() > 0)
        <div class="bg-yellow-50 border border-yellow-300 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <span class="text-2xl">⚠️</span>
                <div>
                    <p class="font-semibold text-yellow-700 mb-2">
                        {{ $mezziInScadenza->count() }} mezzo/i con manutenzione in scadenza entro 30 giorni
                    </p>
                    @foreach($mezziInScadenza as $m)
                        @php
                            $giorni = round(now()->floatDiffInDays(\Carbon\Carbon::parse($m->prossima_manutenzione)));
                        @endphp
                        <div class="flex items-center gap-2 text-sm text-yellow-700">
                            <span>•</span>
                            <span class="font-medium">{{ $m->modello ?? $m->tipo }}</span>
                            <span class="font-mono bg-yellow-100 px-1.5 rounded">{{ $m->targa }}</span>
                            <span>— scade tra <strong>{{ $giorni }} giorni</strong>
                                ({{ \Carbon\Carbon::parse($m->prossima_manutenzione)->format('d/m/Y') }})
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-blue-700">🚛 Gestione Parco Mezzi</h1>
        <a href="{{ route('mezzi.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-colors">
            + Aggiungi Mezzo
        </a>
    </div>

    {{-- Filtri --}}
    <div class="bg-white rounded-xl shadow p-5 mb-6">
        <p class="text-xs font-semibold text-blue-600 mb-3">FILTRI</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Nome Mezzo</label>
                <input type="text" id="cerca-nome" placeholder="Cerca per nome..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Targa</label>
                <input type="text" id="cerca-targa" placeholder="Cerca per targa..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">Anno</label>
                <input type="text" id="cerca-anno" placeholder="Cerca per anno..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    {{-- Tabella --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Mezzo / Modello</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Targa</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Anno</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Manutenzione</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Stato</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Azioni</th>
                </tr>
            </thead>
            <tbody id="tabella-mezzi">
                @forelse($mezzi as $m)
                <tr class="border-b border-gray-100 hover:bg-gray-50 mezzo-row"
                    data-nome="{{ strtolower($m->modello ?? $m->tipo) }}"
                    data-targa="{{ strtolower($m->targa) }}"
                    data-anno="{{ $m->anno }}">

                    <td class="py-3 px-4">
                        <p class="font-semibold text-gray-800 uppercase">{{ $m->modello ?? '—' }}</p>
                        <p class="text-xs text-gray-500">{{ $m->tipo }}</p>
                    </td>

                    <td class="py-3 px-4">
                        <span class="bg-gray-700 text-white px-2 py-1 rounded text-xs font-mono">
                            {{ $m->targa }}
                        </span>
                    </td>

                    <td class="py-3 px-4 text-gray-600">{{ $m->anno ?? '—' }}</td>

                    <td class="py-3 px-4 text-gray-600">
                        @if($m->prossima_manutenzione)
                            Prossima:
                            <span class="text-red-500 font-medium">
                                {{ \Carbon\Carbon::parse($m->prossima_manutenzione)->format('d/m/Y') }}
                            </span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>

                    <td class="py-3 px-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $m->stato === 'attivo' ? 'bg-green-100 text-green-700' :
                               ($m->stato === 'in_manutenzione' ? 'bg-yellow-100 text-yellow-700' :
                               'bg-red-100 text-red-700') }}">
                            {{ str_replace('_', ' ', $m->stato) }}
                        </span>
                    </td>

                    <td class="py-3 px-4">
                        <div class="flex gap-2">
                            <a href="{{ route('mezzi.edit', $m) }}"
                               class="w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded flex items-center justify-center transition-colors">
                                ✏️
                            </a>
                            <form method="POST" action="{{ route('mezzi.destroy', $m) }}">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded flex items-center justify-center transition-colors"
                                        onclick="return confirm('Eliminare {{ $m->modello ?? $m->tipo }}?')">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-400">Nessun mezzo trovato.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<script>
const cerca = () => {
    const nome  = document.getElementById('cerca-nome').value.toLowerCase();
    const targa = document.getElementById('cerca-targa').value.toLowerCase();
    const anno  = document.getElementById('cerca-anno').value.toLowerCase();

    document.querySelectorAll('.mezzo-row').forEach(row => {
        const matchNome  = row.dataset.nome.includes(nome);
        const matchTarga = row.dataset.targa.includes(targa);
        const matchAnno  = row.dataset.anno.includes(anno);
        row.style.display = (matchNome && matchTarga && matchAnno) ? '' : 'none';
    });
};

document.getElementById('cerca-nome').addEventListener('input', cerca);
document.getElementById('cerca-targa').addEventListener('input', cerca);
document.getElementById('cerca-anno').addEventListener('input', cerca);
</script>

@endsection