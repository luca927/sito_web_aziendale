@extends('layouts.app')

@section('title', 'Aggiungi Cantiere')

@section('content')
<div class="py-6 max-w-2xl">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('cantieri.index') }}"
           class="text-gray-400 hover:text-gray-600">← Indietro</a>
        <h1 class="text-2xl font-bold text-blue-700">🏗️ Aggiungi Cantiere</h1>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('cantieri.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome Cantiere</label>
                    <input type="text" name="nome" value="{{ old('nome') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nome') border-red-400 @enderror">
                    @error('nome') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Indirizzo</label>
                    <input type="text" name="indirizzo" value="{{ old('indirizzo') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('indirizzo') border-red-400 @enderror">
                    @error('indirizzo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Referente</label>
                    <input type="text" name="referente" value="{{ old('referente') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giorni Lavorativi</label>
                    <input type="text" name="giorni" value="{{ old('giorni') }}"
                           placeholder="Es. LUN,MAR,MER"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data Inizio</label>
                    <input type="date" name="data_inizio" value="{{ old('data_inizio') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data Fine</label>
                    <input type="date" name="data_fine" value="{{ old('data_fine') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Latitudine</label>
                    <input type="text" name="latitudine" value="{{ old('latitudine') }}"
                           placeholder="Es. 45.0706"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Longitudine</label>
                    <input type="text" name="longitudine" value="{{ old('longitudine') }}"
                           placeholder="Es. 7.6868"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stato</label>
                    <select name="stato"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="attivo">Attivo</option>
                        <option value="completato">Completato</option>
                        <option value="sospeso">Sospeso</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assegna Dipendenti</label>
                    <div class="grid grid-cols-2 gap-2 border border-gray-300 rounded-lg p-3 max-h-40 overflow-y-auto">
                        @foreach($dipendenti as $d)
                            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="checkbox" name="dipendenti[]" value="{{ $d->id }}"
                                       class="rounded border-gray-300 text-blue-600">
                                {{ $d->nome }} {{ $d->cognome }}
                            </label>
                        @endforeach
                    </div>
                </div>

            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Salva Cantiere
                </button>
                <a href="{{ route('cantieri.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Annulla
                </a>
            </div>

        </form>
    </div>
</div>
@endsection