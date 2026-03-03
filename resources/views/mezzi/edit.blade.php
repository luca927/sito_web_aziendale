@extends('layouts.app')

@section('title', 'Modifica Mezzo')

@section('content')
<div class="py-6 max-w-2xl">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('mezzi.index') }}"
           class="text-gray-400 hover:text-gray-600">← Indietro</a>
        <h1 class="text-2xl font-bold text-blue-700">✏️ Modifica Mezzo</h1>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('mezzi.update', $mezzo) }}">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                    <select name="tipo"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="auto" {{ $mezzo->tipo === 'auto' ? 'selected' : '' }}>Auto</option>
                        <option value="furgone" {{ $mezzo->tipo === 'furgone' ? 'selected' : '' }}>Furgone</option>
                        <option value="autocarro" {{ $mezzo->tipo === 'autocarro' ? 'selected' : '' }}>Autocarro</option>
                        <option value="moto" {{ $mezzo->tipo === 'moto' ? 'selected' : '' }}>Moto</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modello</label>
                    <input type="text" name="modello" value="{{ old('modello', $mezzo->modello) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Targa</label>
                    <input type="text" name="targa" value="{{ old('targa', $mezzo->targa) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Anno</label>
                    <input type="number" name="anno" value="{{ old('anno', $mezzo->anno) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           min="1990" max="{{ date('Y') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dipendente Assegnato</label>
                    <select name="dipendente_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Nessuno —</option>
                        @foreach($dipendenti as $d)
                            <option value="{{ $d->id }}" {{ $mezzo->dipendente_id == $d->id ? 'selected' : '' }}>
                                {{ $d->nome }} {{ $d->cognome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stato</label>
                    <select name="stato"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="attivo" {{ $mezzo->stato === 'attivo' ? 'selected' : '' }}>Attivo</option>
                        <option value="in_manutenzione" {{ $mezzo->stato === 'in_manutenzione' ? 'selected' : '' }}>In Manutenzione</option>
                        <option value="fuori_uso" {{ $mezzo->stato === 'fuori_uso' ? 'selected' : '' }}>Fuori Uso</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prossima Manutenzione</label>
                    <input type="date" name="prossima_manutenzione"
                           value="{{ old('prossima_manutenzione', $mezzo->prossima_manutenzione) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Salva Modifiche
                </button>
                <a href="{{ route('mezzi.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Annulla
                </a>
            </div>

        </form>
    </div>
</div>
@endsection