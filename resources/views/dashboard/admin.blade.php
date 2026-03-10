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
                        <div class="flex gap-2">
                            <a href="{{ route('dipendenti.edit', $d) }}"
                            class="w-8 h-8 bg-yellow-400 hover:bg-yellow-500 text-white rounded flex items-center justify-center transition-colors"
                            title="Modifica dipendente">
                                ✏️
                            </a>
                            <form method="POST" action="{{ route('dashboard.rimuovi-assegnazioni', $d) }}">
                                @csrf
                                <button type="submit"
                                        class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded flex items-center justify-center transition-colors"
                                        title="Rimuovi assegnazioni"
                                        onclick="return confirm('Rimuovere cantiere e mezzo da {{ $d->nome }} {{ $d->cognome }}?')">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-400">Nessun dipendente trovato.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Statistiche ore lavorate --}}
    <div class="bg-white rounded-xl shadow p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800">
                ⏱️ Ore Lavorate per Dipendente — {{ now()->locale('it')->isoFormat('MMMM YYYY') }}
            </h2>
            <span class="text-xs text-gray-400">Mese corrente</span>
        </div>

        @if($dati['ore_lavorate']->isEmpty())
            <p class="text-sm text-gray-400 text-center py-6">Nessuna timbratura registrata questo mese.</p>
        @else
            <canvas id="grafico-ore" height="100"></canvas>

            {{-- Tabella riepilogativa sotto il grafico --}}
            <div class="mt-6 border-t border-gray-100 pt-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @foreach($dati['ore_lavorate'] as $item)
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($item['nome'], 0, 1)) }}
                            </div>
                            <span class="text-sm text-gray-700 font-medium">{{ $item['nome'] }}</span>
                        </div>
                        <span class="text-sm font-bold {{ $item['ore'] > 0 ? 'text-blue-600' : 'text-gray-400' }}">
                            {{ $item['ore'] }}h
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        @endif
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const dashboard = () => ({});

    document.getElementById('cerca-combinati').addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.combinato-row').forEach(row => {
            row.style.display = row.dataset.nome.includes(query) ? '' : 'none';
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const canvas = document.getElementById('grafico-ore');
        if (!canvas) return;

        const dati = @json($dati['ore_lavorate']);

        const labels = dati.map(d => d.nome);
        const ore    = dati.map(d => d.ore);

        const colori = [
            '#3b82f6', '#10b981', '#f59e0b', '#ef4444',
            '#8b5cf6', '#06b6d4', '#f97316', '#84cc16'
        ];

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ore lavorate',
                    data: ore,
                    backgroundColor: colori.slice(0, labels.length),
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.raw}h lavorate`
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: val => val + 'h'
                        },
                        grid: {
                            color: '#f3f4f6'
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>

@endsection