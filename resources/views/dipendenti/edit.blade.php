@extends('layouts.app')

@section('title', 'Modifica Dipendente')

@section('content')
<div class="py-6 max-w-2xl">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('dipendenti.index') }}"
           class="text-gray-400 hover:text-gray-600">← Indietro</a>
        <h1 class="text-2xl font-bold text-blue-700">✏️ Modifica Dipendente</h1>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <form method="POST" action="{{ route('dipendenti.update', $dipendente) }}">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                    <input type="text" name="nome" value="{{ old('nome', $dipendente->nome) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cognome</label>
                    <input type="text" name="cognome" value="{{ old('cognome', $dipendente->cognome) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $dipendente->telefono) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Codice Fiscale</label>
                    <input type="text" name="codice_fiscale" value="{{ old('codice_fiscale', $dipendente->codice_fiscale) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mansione</label>
                    <input type="text" name="mansione" value="{{ old('mansione', $dipendente->mansione) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Data Assunzione</label>
                    <input type="date" name="data_assunzione"
                           value="{{ old('data_assunzione', $dipendente->data_assunzione) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Indirizzo</label>
                    <input type="text" name="indirizzo" value="{{ old('indirizzo', $dipendente->indirizzo) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Salva Modifiche
                </button>
                <a href="{{ route('dipendenti.index') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium transition-colors">
                    Annulla
                </a>
            </div>

        </form>
    </div>
</div>
@endsection