@extends('layouts.app')

@section('title', 'Aggiungi Dipendente')

@section('content')
<div class="py-6 max-w-4xl">

    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('dipendenti.index') }}"
           class="text-gray-400 hover:text-gray-600 transition-colors">← Indietro</a>
        <h1 class="text-2xl font-bold text-blue-700">👷 Aggiungi Dipendente</h1>
    </div>

    @if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm">
        <strong class="font-semibold">Correggi i seguenti errori:</strong>
        <ul class="mt-1 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-md overflow-hidden">

        <!-- Tab Headers -->
        <div class="border-b border-gray-200 bg-gray-50 px-6 pt-4">
            <nav class="flex gap-1 -mb-px" id="tabNav">
                <button type="button" onclick="switchTab('anagrafica')"
                        class="tab-btn active px-4 py-2.5 text-sm font-medium rounded-t-lg border border-b-0 transition-colors"
                        data-tab="anagrafica">
                    🧑 Anagrafica
                </button>
                <button type="button" onclick="switchTab('contatti')"
                        class="tab-btn px-4 py-2.5 text-sm font-medium rounded-t-lg border border-b-0 transition-colors"
                        data-tab="contatti">
                    📞 Contatti
                </button>
                <button type="button" onclick="switchTab('documenti')"
                        class="tab-btn px-4 py-2.5 text-sm font-medium rounded-t-lg border border-b-0 transition-colors"
                        data-tab="documenti">
                    📄 Documenti
                </button>
                <button type="button" onclick="switchTab('formazione')"
                        class="tab-btn px-4 py-2.5 text-sm font-medium rounded-t-lg border border-b-0 transition-colors"
                        data-tab="formazione">
                    🎓 Formazione
                </button>
                <button type="button" onclick="switchTab('assegnazioni')"
                        class="tab-btn px-4 py-2.5 text-sm font-medium rounded-t-lg border border-b-0 transition-colors"
                        data-tab="assegnazioni">
                    🔧 Assegnazioni
                </button>
            </nav>
        </div>

        <form method="POST" action="{{ route('dipendenti.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="p-6">

                <!-- ==============================
                     TAB 1: ANAGRAFICA
                ============================== -->
                <div id="tab-anagrafica" class="tab-panel">
                    <h2 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b">Dati Anagrafici</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="form-label">Nome *</label>
                            <input type="text" name="nome" value="{{ old('nome') }}"
                                   class="form-input @error('nome') border-red-400 @enderror"
                                   placeholder="Es. Mario">
                            @error('nome') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label">Cognome *</label>
                            <input type="text" name="cognome" value="{{ old('cognome') }}"
                                   class="form-input @error('cognome') border-red-400 @enderror"
                                   placeholder="Es. Rossi">
                            @error('cognome') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="form-label">Data di Nascita</label>
                            <input type="date" name="dataDiNascita" value="{{ old('dataDiNascita') }}"
                                   class="form-input">
                        </div>

                        <div>
                            <label class="form-label">Data Assunzione</label>
                            <input type="date" name="data_assunzione" value="{{ old('data_assunzione') }}"
                                   class="form-input">
                        </div>

                        <div>
                            <label class="form-label">Sesso</label>
                            <select name="sesso" class="form-input">
                                <option value="">— Seleziona —</option>
                                <option value="M" {{ old('sesso') === 'M' ? 'selected' : '' }}>Maschile</option>
                                <option value="F" {{ old('sesso') === 'F' ? 'selected' : '' }}>Femminile</option>
                                <option value="Altro" {{ old('sesso') === 'Altro' ? 'selected' : '' }}>Altro</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Stato Civile</label>
                            <select name="stato_civile" class="form-input">
                                <option value="">— Seleziona —</option>
                                <option value="celibe/nubile"  {{ old('stato_civile') === 'celibe/nubile'  ? 'selected' : '' }}>Celibe / Nubile</option>
                                <option value="coniugato/a"    {{ old('stato_civile') === 'coniugato/a'    ? 'selected' : '' }}>Coniugato/a</option>
                                <option value="divorziato/a"   {{ old('stato_civile') === 'divorziato/a'   ? 'selected' : '' }}>Divorziato/a</option>
                                <option value="vedovo/a"       {{ old('stato_civile') === 'vedovo/a'       ? 'selected' : '' }}>Vedovo/a</option>
                                <option value="separato/a"     {{ old('stato_civile') === 'separato/a'     ? 'selected' : '' }}>Separato/a</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="form-label">Indirizzo di Residenza</label>
                            <input type="text" name="indirizzoResidenza" value="{{ old('indirizzoResidenza') }}"
                                   class="form-input" placeholder="Es. Via Roma 1, Milano">
                        </div>

                    </div>
                </div>

                <!-- ==============================
                     TAB 2: CONTATTI
                ============================== -->
                <div id="tab-contatti" class="tab-panel hidden">
                    <h2 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b">Recapiti e Contatti</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="form-label">Telefono</label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}"
                                   class="form-input" placeholder="+39 333 000 0000">
                        </div>

                        <div>
                            <label class="form-label">Email *</label>
                            <input type="email" name="recapitieMail" value="{{ old('recapitieMail') }}"
                                   class="form-input @error('recapitieMail') border-red-400 @enderror"
                                   placeholder="mario.rossi@azienda.it">
                            @error('recapitieMail') <p class="form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="form-label">Indirizzo</label>
                            <input type="text" name="indirizzo" value="{{ old('indirizzo') }}"
                                   class="form-input" placeholder="Via, Numero civico, CAP, Città">
                        </div>

                        <div>
                            <label class="form-label">Latitudine (opzionale)</label>
                            <input type="text" name="lat" value="{{ old('lat') }}"
                                   class="form-input" placeholder="Es. 45.4642">
                        </div>

                        <div>
                            <label class="form-label">Longitudine (opzionale)</label>
                            <input type="text" name="lng" value="{{ old('lng') }}"
                                   class="form-input" placeholder="Es. 9.1900">
                        </div>

                        <div class="md:col-span-2 border-t pt-4 mt-2">
                            <h3 class="text-sm font-semibold text-gray-600 mb-3">🔐 Credenziali Accesso</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="form-label">Password *</label>
                                    <input type="password" name="password"
                                           class="form-input @error('password') border-red-400 @enderror">
                                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="form-label">Conferma Password *</label>
                                    <input type="password" name="password_confirmation" class="form-input">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ==============================
                     TAB 3: DOCUMENTI
                ============================== -->
                <div id="tab-documenti" class="tab-panel hidden">
                    <h2 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b">Documenti e Dati Bancari</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="form-label">Codice Fiscale</label>
                            <input type="text" name="codice_fiscale" value="{{ old('codice_fiscale') }}"
                                   class="form-input" placeholder="RSSMRA80A01H501U"
                                   maxlength="16" style="text-transform:uppercase"
                                   oninput="this.value = this.value.toUpperCase()">
                        </div>

                        <div>
                            <label class="form-label">IBAN</label>
                            <input type="text" name="iban" value="{{ old('iban') }}"
                                   class="form-input" placeholder="IT60 X054 2811 1010 0000 0123 456">
                        </div>

                        <div class="md:col-span-2">
                            <label class="form-label">Dati Bancari (note aggiuntive)</label>
                            <input type="text" name="DatiBancari" value="{{ old('DatiBancari') }}"
                                   class="form-input" placeholder="Es. Banca, filiale, intestatario">
                        </div>

                        <div class="md:col-span-2 border-t pt-4 mt-2">
                            <h3 class="text-sm font-semibold text-gray-600 mb-3">🪪 Documenti di Identità</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo Documento</label>
                                    <select name="tipo_documento"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            onchange="toggleDocumento(this)">
                                        <option value="">— Seleziona —</option>
                                        <option value="CI"          {{ old('tipo_documento', $dipendente->tipo_documento ?? '') === 'CI'          ? 'selected' : '' }}>Carta d'Identità</option>
                                        <option value="Passaporto"  {{ old('tipo_documento', $dipendente->tipo_documento ?? '') === 'Passaporto'  ? 'selected' : '' }}>Passaporto</option>
                                        <option value="Patente"     {{ old('tipo_documento', $dipendente->tipo_documento ?? '') === 'Patente'     ? 'selected' : '' }}>Patente</option>
                                        <option value="Permesso"    {{ old('tipo_documento', $dipendente->tipo_documento ?? '') === 'Permesso'    ? 'selected' : '' }}>Permesso di Soggiorno</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Numero Documento</label>
                                    <input type="text" name="numero_documento"
                                        value="{{ old('numero_documento', $dipendente->numero_documento ?? '') }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="Es. AB1234567">
                                </div>

                                <div id="campo_scadenza">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Data Scadenza</label>
                                    <input type="date" name="scadenza_documento"
                                        value="{{ old('scadenza_documento', $dipendente->scadenza_documento ?? '') }}"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

                <!-- ==============================
                     TAB 4: FORMAZIONE
                ============================== -->
                <div id="tab-formazione" class="tab-panel hidden">
                    <h2 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b">Formazione e Competenze</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="form-label">Livello Esperienza</label>
                            <select name="livello_esperienza" class="form-input">
                                <option value=""> Seleziona </option>
                                <option value="1" {{ old('livello_esperienza') == 1 ? 'selected' : '' }}>🟢 Junior</option>
                                <option value="2" {{ old('livello_esperienza') == 2 ? 'selected' : '' }}>🔵 Middle</option>
                                <option value="3" {{ old('livello_esperienza') == 3 ? 'selected' : '' }}>🟠 Senior</option>
                                <option value="4" {{ old('livello_esperienza') == 4 ? 'selected' : '' }}>🔴 Expert</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Corsi e Formazione</label>
                            <input type="text" name="Corsi_e_Formazione" value="{{ old('Corsi_e_Formazione') }}"
                                   class="form-input" placeholder="Es. Corso sicurezza, Excel avanzato...">
                        </div>

                        <div class="md:col-span-2">
                            <label class="form-label">Competenze Tecniche</label>
                            <textarea name="Competenze" rows="3"
                                      class="form-input resize-none"
                                      placeholder="Elenca le competenze tecniche principali...">{{ old('Competenze') }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="form-label">Esperienze Professionali</label>
                            <textarea name="Esperienze" rows="4"
                                      class="form-input resize-none"
                                      placeholder="Descrivi le esperienze lavorative precedenti...">{{ old('Esperienze') }}</textarea>
                        </div>

                    </div>
                </div>

                <!-- ==============================
                     TAB 5: ASSEGNAZIONI
                ============================== -->
                <div id="tab-assegnazioni" class="tab-panel hidden">
                    <h2 class="text-base font-semibold text-gray-700 mb-4 pb-2 border-b">Mansione e Patenti</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="form-label">Mansione</label>
                            <input type="text" name="mansione" value="{{ old('mansione') }}"
                                   class="form-input" placeholder="Es. Elettricista, Magazziniere...">
                        </div>

                        <div>
                            <label class="form-label">Patenti</label>
                            <input type="text" name="patenti" value="{{ old('patenti') }}"
                                   class="form-input" placeholder="Es. B, C, CQC, Muletto...">
                        </div>

                        <div class="md:col-span-2">
                            <label class="form-label">Note aggiuntive</label>
                            <textarea name="note" rows="3"
                                      class="form-input resize-none"
                                      placeholder="Eventuali note o informazioni aggiuntive...">{{ old('note') }}</textarea>
                        </div>

                    </div>
                </div>

            </div><!-- fine p-6 -->

            <!-- Footer con navigazione tab + salva -->
            <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
                <div class="flex gap-2">
                    <button type="button" id="btnPrev" onclick="prevTab()"
                            class="hidden bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        ← Indietro
                    </button>
                    <button type="button" id="btnNext" onclick="nextTab()"
                            class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Avanti →
                    </button>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('dipendenti.index') }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                        Annulla
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                        💾 Salva Dipendente
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<style>
/* Tab attivo */
.tab-btn {
    background: white;
    border-color: #e5e7eb;
    color: #6b7280;
}
.tab-btn:hover {
    color: #374151;
}
.tab-btn.active {
    background: white;
    border-color: #e5e7eb;
    border-bottom-color: white;
    color: #2563eb;
    font-weight: 600;
}
</style>
@endsection
<script>
const tabs = ['anagrafica', 'contatti', 'documenti', 'formazione', 'assegnazioni'];
let currentTab = 0;

function switchTab(tabName) {
    currentTab = tabs.indexOf(tabName);
    renderTab();
}

function nextTab() {
    if (currentTab < tabs.length - 1) { currentTab++; renderTab(); }
}

function prevTab() {
    if (currentTab > 0) { currentTab--; renderTab(); }
}

function renderTab() {
    // Nascondi tutti i pannelli
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

    // Mostra pannello attivo
    document.getElementById('tab-' + tabs[currentTab]).classList.remove('hidden');
    document.querySelector(`[data-tab="${tabs[currentTab]}"]`).classList.add('active');

    // Bottoni nav
    document.getElementById('btnPrev').classList.toggle('hidden', currentTab === 0);
    document.getElementById('btnNext').classList.toggle('hidden', currentTab === tabs.length - 1);

    // Scroll top form
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
function toggleDocumento(sel) {
    // Il Codice Fiscale non ha scadenza, tutto il resto sì
    const senzaScadenza = [''];
    const campo = document.getElementById('campo_scadenza');
    campo.style.opacity = senzaScadenza.includes(sel.value) ? '0.3' : '1';
    campo.querySelector('input').disabled = senzaScadenza.includes(sel.value);
}

// Init: se c'è già un valore salvato, applica la logica
document.addEventListener('DOMContentLoaded', () => {
    const sel = document.querySelector('[name="tipo_documento"]');
    if (sel) toggleDocumento(sel);
});

// Init
renderTab();
</script>