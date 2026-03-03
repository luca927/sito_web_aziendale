@extends('layouts.app')

@section('title', 'Dashboard Amministrativa')

@section('content')
<div class="py-6" x-data="dashboard()">

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

    {{-- Tabella combinata --}}
    <div class="bg-white rounded-xl shadow p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800">👷 Riepilogo Operativo</h2>
            <input type="text" id="cerca-combinati" placeholder="Cerca dipendente..."
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Dipendente</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Cantiere</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Mezzo</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Stato</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Azioni</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dati['dati_combinati'] as $d)
                <tr class="border-b border-gray-100 hover:bg-gray-50 combinato-row"
                    data-nome="{{ strtolower($d->nome . ' ' . $d->cognome) }}">

                    {{-- Dipendente --}}
                    <td class="py-3 px-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold flex-shrink-0">
                                {{ strtoupper(substr($d->nome, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $d->nome }} {{ $d->cognome }}</p>
                                <p class="text-xs text-gray-400">{{ $d->mansione ?? '—' }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Cantiere — inline edit --}}
                    <td class="py-3 px-4">
                        <div x-data="{ editing: false }">
                            {{-- Vista normale --}}
                            <div x-show="!editing" class="flex items-center gap-2">
                                <div>
                                    @forelse($d->cantieri->where('stato', 'attivo') as $c)
                                        <span class="inline-block bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs mb-1">
                                            🏗️ {{ $c->nome }}
                                        </span>
                                    @empty
                                        <span class="text-gray-400 text-xs">Nessun cantiere</span>
                                    @endforelse
                                </div>
                                <button @click="editing = true"
                                        class="text-gray-300 hover:text-blue-500 transition-colors ml-1">
                                    ✏️
                                </button>
                            </div>

                            {{-- Inline edit form --}}
                            <div x-show="editing">
                                <form method="POST" action="{{ route('dashboard.assegna-cantiere', $d) }}"
                                      class="flex items-center gap-2">
                                    @csrf
                                    <select name="cantiere_id"
                                            class="border border-blue-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">— Nessuno —</option>
                                        @foreach($dati['cantieri'] as $c)
                                            <option value="{{ $c->id }}"
                                                {{ $d->cantieri->pluck('id')->contains($c->id) ? 'selected' : '' }}>
                                                {{ $c->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                            class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700">
                                        ✓
                                    </button>
                                    <button type="button" @click="editing = false"
                                            class="bg-gray-200 text-gray-600 px-2 py-1 rounded text-xs hover:bg-gray-300">
                                        ✕
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>

                    {{-- Mezzo — inline edit --}}
                    <td class="py-3 px-4">
                        <div x-data="{ editing: false }">
                            {{-- Vista normale --}}
                            <div x-show="!editing" class="flex items-center gap-2">
                                <div>
                                    @forelse($d->mezzi as $m)
                                        <div class="text-sm text-gray-700">
                                            🚛 {{ $m->modello ?? $m->tipo }}
                                            <span class="bg-gray-700 text-white px-1.5 py-0.5 rounded text-xs font-mono ml-1">
                                                {{ $m->targa }}
                                            </span>
                                        </div>
                                    @empty
                                        <span class="text-gray-400 text-xs">Nessun mezzo</span>
                                    @endforelse
                                </div>
                                <button @click="editing = true"
                                        class="text-gray-300 hover:text-blue-500 transition-colors ml-1">
                                    ✏️
                                </button>
                            </div>

                            {{-- Inline edit form --}}
                            <div x-show="editing">
                                <form method="POST" action="{{ route('dashboard.assegna-mezzo', $d) }}"
                                      class="flex items-center gap-2">
                                    @csrf
                                    <select name="mezzo_id"
                                            class="border border-blue-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">— Nessuno —</option>
                                        @foreach($dati['mezzi'] as $m)
                                            <option value="{{ $m->id }}"
                                                {{ $d->mezzi->pluck('id')->contains($m->id) ? 'selected' : '' }}>
                                                {{ $m->modello ?? $m->tipo }} - {{ $m->targa }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit"
                                            class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700">
                                        ✓
                                    </button>
                                    <button type="button" @click="editing = false"
                                            class="bg-gray-200 text-gray-600 px-2 py-1 rounded text-xs hover:bg-gray-300">
                                        ✕
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>

                    {{-- Stato --}}
                    <td class="py-3 px-4">
                        @if($d->cantieri->where('stato', 'attivo')->count() > 0)
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                ● Attivo
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                ● Non assegnato
                            </span>
                        @endif
                    </td>

                    {{-- Azioni --}}
                    <td class="py-3 px-4">
                        <a href="{{ route('dipendenti.edit', $d) }}"
                           class="w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded flex items-center justify-center transition-colors">
                            ✏️
                        </a>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-400">Nessun dipendente trovato.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Ultimi Tracciamenti --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">📍 Ultimi Tracciamenti</h2>
        @forelse($dati['ultimi_tracciamenti'] as $t)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-800">
                        {{ $t->dipendente->nome }} {{ $t->dipendente->cognome }}
                    </p>
                    <p class="text-xs text-gray-500">{{ $t->cantiere->nome }}</p>
                </div>
                <span class="text-xs text-gray-400">
                    {{ \Carbon\Carbon::parse($t->data_ora)->format('d/m/Y H:i') }}
                </span>
            </div>
        @empty
            <p class="text-sm text-gray-400">Nessun tracciamento ancora.</p>
        @endforelse
    </div>

</div>

<script>
const dashboard = () => ({});

document.getElementById('cerca-combinati').addEventListener('input', (e) => {
    const query = e.target.value.toLowerCase();
    document.querySelectorAll('.combinato-row').forEach(row => {
        row.style.display = row.dataset.nome.includes(query) ? '' : 'none';
    });
});
</script>

@endsection