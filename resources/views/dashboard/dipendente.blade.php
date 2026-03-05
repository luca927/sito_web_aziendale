@extends('layouts.app')

@section('title', 'La mia Dashboard')

@section('content')
<div class="py-6">

    <h1 class="text-2xl font-bold text-blue-700 mb-6">🏠 La mia Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        {{-- I miei cantieri --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">🏗️ I miei Cantieri</h2>
            @forelse($dati['cantieri'] as $cantiere)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $cantiere->nome }}</p>
                        <p class="text-xs text-gray-500">{{ $cantiere->indirizzo }}</p>
                    </div>
                    @if($cantiere->latitudine && $cantiere->longitudine)
                        <a href="https://maps.google.com/?q={{ $cantiere->latitudine }},{{ $cantiere->longitudine }}"
                           target="_blank"
                           class="text-xs bg-blue-100 text-blue-600 px-3 py-1 rounded-full hover:bg-blue-200">
                            📍 Mappa
                        </a>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-400">Nessun cantiere assegnato.</p>
            @endforelse
        </div>

        {{-- I miei mezzi --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">🚛 I miei Mezzi</h2>
            @forelse($dati['mezzi'] as $mezzo)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $mezzo->modello ?? $mezzo->tipo }}</p>
                        <p class="text-xs text-gray-500">{{ $mezzo->targa }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-semibold
                        {{ $mezzo->stato === 'attivo' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $mezzo->stato }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-gray-400">Nessun mezzo assegnato.</p>
            @endforelse
        </div>

    </div>

    {{-- Ultime timbrature --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">🕐 Ultime Timbrature</h2>
        @forelse($dati['timbrature'] as $t)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div>
                    <p class="text-sm text-gray-800">{{ $t->cantiere->nome ?? '—' }}</p>
                    <p class="text-xs text-gray-400">{{ $t->causale ?? 'Lavoro Ordinario' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-green-600">
                        ↗ {{ \Carbon\Carbon::parse($t->entrata)->format('d/m/Y H:i') }}
                    </p>
                    <p class="text-xs {{ $t->uscita ? 'text-red-500' : 'text-gray-400' }}">
                        ↙ {{ $t->uscita ? \Carbon\Carbon::parse($t->uscita)->format('H:i') : 'In corso...' }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">Nessuna timbratura recente.</p>
        @endforelse
    </div>

</div>
@endsection