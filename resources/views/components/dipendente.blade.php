@extends('layouts.app')

@section('title', 'La mia Dashboard')

@section('content')
<div class="py-6">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        {{-- I miei cantieri --}}
        <div class="bg-white rounded-xl shadow p-6 md:col-span-2">
            <h2 class="text-base font-semibold text-gray-800 mb-4">I miei Cantieri</h2>
            @forelse($dati['cantieri'] as $cantiere)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $cantiere->nome }}</p>
                        <p class="text-xs text-gray-500">{{ $cantiere->indirizzo }}</p>
                    </div>
                    @if($cantiere->latitudine && $cantiere->longitudine)
                        <a href="https://maps.google.com/?q={{ $cantiere->latitudine }},{{ $cantiere->longitudine }}"
                           target="_blank"
                           class="text-xs text-indigo-600 hover:underline">
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
            <h2 class="text-base font-semibold text-gray-800 mb-4">I miei Mezzi</h2>
            @forelse($dati['mezzi'] as $mezzo)
                <div class="py-3 border-b border-gray-100 last:border-0">
                    <p class="text-sm font-medium text-gray-800">{{ $mezzo->modello ?? $mezzo->tipo }}</p>
                    <p class="text-xs text-gray-500">{{ $mezzo->targa }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-400">Nessun mezzo assegnato.</p>
            @endforelse
        </div>

    </div>

    {{-- Timbrature recenti --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Ultime Timbrature</h2>
        @forelse($dati['timbrature'] as $t)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <p class="text-sm text-gray-800">{{ $t->cantiere->nome }}</p>
                <div class="text-right">
                    <p class="text-xs text-gray-500">
                        Entrata: {{ \Carbon\Carbon::parse($t->entrata)->format('d/m/Y H:i') }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Uscita: {{ $t->uscita ? \Carbon\Carbon::parse($t->uscita)->format('H:i') : '—' }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">Nessuna timbratura recente.</p>
        @endforelse
    </div>

</div>
@endsection