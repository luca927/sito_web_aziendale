@extends('layouts.app')

@section('title', 'Gestione Utenti')

@section('content')
<div class="py-6" x-data="gestioneUtenti()">

    {{-- Titolo --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-blue-700">👥 Gestione Utenti</h1>
            <p class="text-sm text-gray-500 mt-1">Crea, modifica ed elimina gli account dei dipendenti</p>
        </div>
        <a href="{{ route('gestione_utenti.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-full text-sm font-medium flex items-center gap-2 transition-colors">
            👤+ Nuovo Utente
        </a>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-2xl">👥</div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Totale</p>
                <p class="text-3xl font-bold text-gray-800">{{ $dati['totale'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-2xl">🛡️</div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Admin</p>
                <p class="text-3xl font-bold text-gray-800">{{ $dati['admin'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center text-2xl">👔</div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Manager</p>
                <p class="text-3xl font-bold text-gray-800">{{ $dati['manager'] }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-2xl">👷</div>
            <div>
                <p class="text-xs text-gray-500 uppercase font-semibold">Dipendenti</p>
                <p class="text-3xl font-bold text-gray-800">{{ $dati['dipendenti'] }}</p>
            </div>
        </div>

    </div>

    {{-- Tabella --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">

        {{-- Header tabella --}}
        <div class="bg-[#1a2a4a] px-6 py-4 flex items-center justify-between">
            <h2 class="text-white font-semibold flex items-center gap-2">
                ☰ Elenco Utenti
            </h2>
            <input type="text" id="search" placeholder="Cerca..."
                   class="bg-white border-0 rounded-lg px-4 py-2 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        {{-- Filtri ruolo --}}
        <div class="px-6 py-3 border-b border-gray-100 flex gap-2">
            <button onclick="filtraRuolo('tutti')"
                    class="filtro-btn px-4 py-1.5 rounded-full text-sm font-medium bg-blue-600 text-white transition-colors"
                    data-ruolo="tutti">
                Tutti
            </button>
            <button onclick="filtraRuolo('admin')"
                    class="filtro-btn px-4 py-1.5 rounded-full text-sm font-medium border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors"
                    data-ruolo="admin">
                Admin
            </button>
            <button onclick="filtraRuolo('manager')"
                    class="filtro-btn px-4 py-1.5 rounded-full text-sm font-medium border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors"
                    data-ruolo="manager">
                Manager
            </button>
            <button onclick="filtraRuolo('dipendente')"
                    class="filtro-btn px-4 py-1.5 rounded-full text-sm font-medium border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors"
                    data-ruolo="dipendente">
                Dipendenti
            </button>
        </div>

        {{-- Tabella utenti --}}
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Utente</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Email</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Ruolo</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Livello</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Assunto il</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Creato il</th>
                    <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Azioni</th>
                </tr>
            </thead>
            <tbody id="tabella-utenti">
                @forelse($dati['utenti'] as $utente)
                <tr class="border-b border-gray-100 hover:bg-gray-50 utente-row"
                    data-ruolo="{{ $utente->ruolo }}"
                    data-nome="{{ strtolower($utente->name) }}">

                    {{-- Utente con avatar --}}
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($utente->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $utente->name }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ '@' }}{{ $utente->username ?? strtolower(str_replace(' ', '.', $utente->name)) }}
                                </p>
                            </div>
                        </div>
                    </td>

                    <td class="py-4 px-4 text-gray-600">{{ $utente->email }}</td>

                    {{-- Badge ruolo --}}
                    <td class="py-4 px-4">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $utente->ruolo === 'admin' ? 'bg-red-100 text-red-700' :
                               ($utente->ruolo === 'manager' ? 'bg-yellow-100 text-yellow-700' :
                               'bg-blue-100 text-blue-700') }}">
                            {{ $utente->ruolo }}
                        </span>
                    </td>

                    <td class="py-4 px-4 text-gray-600 capitalize">
                        {{ $utente->livello ?? '—' }}
                    </td>

                    <td class="py-4 px-4 text-gray-500">
                        {{ $utente->dipendente?->data_assunzione
                            ? \Carbon\Carbon::parse($utente->dipendente->data_assunzione)->format('d/m/Y')
                            : '—' }}
                    </td>

                    <td class="py-4 px-4 text-gray-500">
                        {{ $utente->created_at->format('d/m/Y') }}
                    </td>

                    {{-- Azioni --}}
                    <td class="py-4 px-4">
                        <div class="flex gap-2">
                            {{-- Modifica --}}
                            <button @click="apriModifica({{ $utente->id }}, '{{ $utente->name }}', '{{ $utente->email }}', '{{ $utente->username }}', '{{ $utente->ruolo }}', '{{ $utente->livello }}')"
                                    class="w-8 h-8 bg-blue-600 hover:bg-blue-700 text-white rounded flex items-center justify-center transition-colors"
                                    title="Modifica">
                                ✏️
                            </button>
                            {{-- Reset password --}}
                            <button @click="apriReset({{ $utente->id }}, '{{ $utente->name }}')"
                                    class="w-8 h-8 bg-blue-400 hover:bg-blue-500 text-white rounded flex items-center justify-center transition-colors"
                                    title="Reset Password">
                                🔑
                            </button>
                            {{-- Elimina --}}
                            <button @click="apriElimina({{ $utente->id }}, '{{ $utente->name }}')"
                                    class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded flex items-center justify-center transition-colors"
                                    title="Elimina">
                                🗑️
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-gray-400">Nessun utente trovato.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
    {{-- MODALE MODIFICA --}}
    <div x-show="modaleModifica"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        @click.self="modaleModifica = false">

        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">✏️ Modifica Utente</h3>
                <button @click="modaleModifica = false" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
            </div>

            <form id="form-modifica" action="" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                <div class="grid grid-cols-2 gap-4 mb-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome completo</label>
                        <input type="text" name="name" x-model="utenteSelezionato.name"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" x-model="utenteSelezionato.username"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" x-model="utenteSelezionato.email"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ruolo</label>
                        <select name="ruolo" x-model="utenteSelezionato.ruolo"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="dipendente">Dipendente</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Livello</label>
                        <select name="livello" x-model="utenteSelezionato.livello"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">— Seleziona —</option>
                            <option value="junior">Junior</option>
                            <option value="middle">Middle</option>
                            <option value="senior">Senior</option>
                        </select>
                    </div>

                </div>

                <div class="flex gap-3 justify-end">
                    <button type="button" @click="modaleModifica = false"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium">
                        Annulla
                    </button>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium">
                        Salva Modifiche
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODALE RESET PASSWORD --}}
    <div x-show="modaleReset"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        @click.self="modaleReset = false">

        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">🔑 Reset Password</h3>
                <button @click="modaleReset = false" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
            </div>

            <form :action="'/gestione_utenti/' + utenteSelezionato.id + '/reset-password'" method="POST" class="p-6">
                @csrf

                <p class="text-sm text-gray-600 mb-4">
                    Stai per resettare la password di <strong x-text="utenteSelezionato.name"></strong>.
                </p>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nuova Password</label>
                    <input type="password" name="password"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Minimo 8 caratteri">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conferma Password</label>
                    <input type="password" name="password_confirmation"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex gap-3 justify-end">
                    <button type="button" @click="modaleReset = false"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium">
                        Annulla
                    </button>
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-medium">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODALE ELIMINA --}}
    <div x-show="modaleElimina"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
        @click.self="modaleElimina = false">

        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-red-600">🗑️ Elimina Utente</h3>
                <button @click="modaleElimina = false" class="text-gray-400 hover:text-gray-600 text-xl">✕</button>
            </div>

            <div class="p-6">
                <p class="text-sm text-gray-600 mb-6">
                    Sei sicuro di voler eliminare <strong x-text="utenteSelezionato.name"></strong>?
                    Questa azione non può essere annullata.
                </p>

                <form :action="'/gestione_utenti/' + utenteSelezionato.id" method="POST">
                    @csrf @method('DELETE')
                    <div class="flex gap-3 justify-end">
                        <button type="button" @click="modaleElimina = false"
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium">
                            Annulla
                        </button>
                        <button type="submit"
                                class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg text-sm font-medium">
                            Elimina
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const gestioneUtenti = () => ({
    modaleModifica: false,
    modaleReset: false,
    modaleElimina: false,
    utenteSelezionato: {
        id: null, name: '', email: '', username: '', ruolo: '', livello: ''
    },

        apriModifica(id, name, email, username, ruolo, livello) {
        this.utenteSelezionato = { id, name, email, username, ruolo, livello };
        this.modaleModifica = true;
        this.$nextTick(() => {
            const form = document.getElementById('form-modifica');
            form.action = `/gestione_utenti/${id}`;
            form.querySelector('input[name="_method"]').value = 'PUT';
        });
    },

    apriReset(id, name) {
        this.utenteSelezionato = { id, name };
        this.modaleReset = true;
        this.$nextTick(() => {
            document.getElementById('form-reset').action = `/gestione_utenti/${id}/reset-password`;
        });
    },

    apriElimina(id, name) {
        this.utenteSelezionato = { id, name };
        this.modaleElimina = true;
        this.$nextTick(() => {
            document.getElementById('form-elimina').action = `/gestione_utenti/${id}`;
        });
    },

    // Filtro ruolo
    filtraRuolo(ruolo) {
        document.querySelectorAll('.filtro-btn').forEach(btn => {
            if (btn.dataset.ruolo === ruolo) {
                btn.classList.add('bg-blue-600', 'text-white');
                btn.classList.remove('border', 'border-gray-300', 'text-gray-600');
            } else {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('border', 'border-gray-300', 'text-gray-600');
            }
        });
        document.querySelectorAll('.utente-row').forEach(row => {
            row.style.display = (ruolo === 'tutti' || row.dataset.ruolo === ruolo) ? '' : 'none';
        });
    }
});

// Ricerca per nome
document.getElementById('search').addEventListener('input', (e) => {
    const query = e.target.value.toLowerCase();
    document.querySelectorAll('.utente-row').forEach(row => {
        row.style.display = row.dataset.nome.includes(query) ? '' : 'none';
    });
});
</script>

@endsection