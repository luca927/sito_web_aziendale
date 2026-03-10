@extends('layouts.app')

@section('title', 'Timbrature')

@section('content')
<div class="py-6">

    @if(auth()->user()->isAdmin())

        {{-- VISTA ADMIN — solo tabella timbrature dipendenti --}}
        <h1 class="text-2xl font-bold text-blue-700 mb-6">🕐 Timbrature Dipendenti</h1>

        {{-- Bottoni export --}}
        <div class="flex gap-2 mb-6">
            <a href="{{ route('timbrature.export-pdf') }}"
            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                📄 PDF Mese Corrente
            </a>
            @foreach($dipendenti as $d)
            @endforeach
        </div>

        {{-- Select mese + dipendente per export --}}
        <div class="bg-white rounded-xl shadow p-4 mb-6 flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Mese</label>
                <select id="sel-mese" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m)->locale('it')->isoFormat('MMMM') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Anno</label>
                <select id="sel-anno" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @for($a = now()->year; $a >= now()->year - 2; $a--)
                        <option value="{{ $a }}">{{ $a }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">Dipendente</label>
                <select id="sel-dipendente" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Tutti</option>
                    @foreach($dipendenti as $d)
                        <option value="{{ $d->id }}">{{ $d->nome }} {{ $d->cognome }}</option>
                    @endforeach
                </select>
            </div>
            <button onclick="scaricaPdf()"
                    class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                📄 Scarica PDF
            </button>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-800">Timbrature di Oggi</h2>
                <span class="text-sm text-gray-400">{{ now()->format('d/m/Y') }}</span>
            </div>

            @forelse($timbrature as $t)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                            {{ strtoupper(substr($t->dipendente->nome, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">
                                {{ $t->dipendente->nome }} {{ $t->dipendente->cognome }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $t->cantiere->nome ?? '—' }} — {{ $t->causale }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-green-600">
                            ↗ {{ \Carbon\Carbon::parse($t->entrata)->format('H:i') }}
                        </p>
                        <p class="text-sm font-medium {{ $t->uscita ? 'text-red-500' : 'text-gray-400' }}">
                            ↙ {{ $t->uscita ? \Carbon\Carbon::parse($t->uscita)->format('H:i') : 'In corso...' }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 py-8">Nessuna timbratura registrata oggi.</p>
            @endforelse
        </div>

    @else

        {{-- VISTA DIPENDENTE — orologio + timbra --}}

        {{-- Orologio --}}
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-8 mb-6 text-center text-white">
            <p class="text-6xl font-bold tracking-widest mb-2" id="orologio">00:00:00</p>
            <p class="text-lg" id="data-oggi"></p>
        </div>

        {{-- Form Timbra --}}
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                🕐 Timbra Presenza
            </h2>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Causale (Opzionale):</label>
                <select id="causale"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="Lavoro Ordinario">Lavoro Ordinario</option>
                    <option value="Straordinario">Straordinario</option>
                    <option value="Trasferta">Trasferta</option>
                    <option value="Formazione">Formazione</option>
                </select>
            </div>

            @if(!$timbraturaAperta)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantiere:</label>
                    <select id="cantiere_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleziona cantiere</option>
                        @foreach($cantieri as $c)
                            <option value="{{ $c->id }}">{{ $c->nome }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-4 mb-4">

                <form method="POST" action="{{ route('timbrature.entrata') }}" id="form-entrata">
                    @csrf
                    <input type="hidden" name="cantiere_id" id="input-cantiere">
                    <input type="hidden" name="causale" id="input-causale">
                    <input type="hidden" name="latitudine" id="input-lat">
                    <input type="hidden" name="longitudine" id="input-lng">
                    <button type="button" onclick="timbra('entrata')"
                            class="w-full py-6 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold text-lg transition-colors flex flex-col items-center gap-2
                                   {{ $timbraturaAperta ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $timbraturaAperta ? 'disabled' : '' }}>
                        <span class="text-2xl">→</span>
                        ENTRATA
                    </button>
                </form>

                <form method="POST" action="{{ route('timbrature.uscita') }}" id="form-uscita">
                    @csrf
                    <input type="hidden" name="latitudine" id="input-lat-uscita">
                    <input type="hidden" name="longitudine" id="input-lng-uscita">
                    <button type="button" onclick="timbra('uscita')"
                            class="w-full py-6 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold text-lg transition-colors flex flex-col items-center gap-2
                                   {{ !$timbraturaAperta ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ !$timbraturaAperta ? 'disabled' : '' }}>
                        <span class="text-2xl">→</span>
                        USCITA
                    </button>
                </form>

            </div>

            <div id="gps-status"
                 class="bg-gray-100 rounded-lg px-4 py-2 text-center text-sm text-gray-500">
                📍 Ricerca posizione GPS...
            </div>
        </div>

        {{-- Timbrature di oggi --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                🔄 Timbrature di Oggi
            </h2>

            @forelse($timbrature as $t)
                <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                    <div>
                        <p class="text-sm text-gray-500">{{ $t->cantiere->nome ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $t->causale }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-green-600">
                            ↗ {{ \Carbon\Carbon::parse($t->entrata)->format('H:i') }}
                        </p>
                        <p class="text-sm font-medium {{ $t->uscita ? 'text-red-500' : 'text-gray-400' }}">
                            ↙ {{ $t->uscita ? \Carbon\Carbon::parse($t->uscita)->format('H:i') : 'In corso...' }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 py-4">Nessuna timbratura registrata oggi.</p>
            @endforelse
        </div>

    @endif

</div>

{{-- CALENDARIO PRESENZE — visibile a tutti --}}
<div class="mt-6 bg-white rounded-xl shadow p-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
        <h2 class="text-lg font-semibold text-gray-800">📅 Calendario Presenze</h2>

        @if(auth()->user()->isAdmin())
        <select id="filtro-dipendente"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-64">
            <option value="">— Tutti i dipendenti —</option>
            @foreach($dipendenti as $d)
                <option value="{{ $d->id }}">{{ $d->nome }} {{ $d->cognome }}</option>
            @endforeach
        </select>
        @endif
    </div>

    {{-- Legenda --}}
    <div class="flex gap-4 mb-4 text-xs text-gray-500">
        <span class="flex items-center gap-1">
            <span class="w-3 h-3 rounded-full bg-green-600 inline-block"></span> Completata
        </span>
        <span class="flex items-center gap-1">
            <span class="w-3 h-3 rounded-full bg-yellow-400 inline-block"></span> In corso
        </span>
    </div>

    <div id="calendario" data-is-admin="{{ auth()->user()->isAdmin() ? '1' : '0' }}"></div>

    {{-- Popup dettaglio --}}
    <div id="popup-timbratura"
         class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded-xl shadow-xl p-6 w-80">
            <h3 class="font-semibold text-gray-800 mb-3">📋 Dettaglio Timbratura</h3>
            <div class="space-y-2 text-sm text-gray-600">
                <p>🟢 Entrata: <strong id="popup-entrata">—</strong></p>
                <p>🔴 Uscita: <strong id="popup-uscita">—</strong></p>
                <p>⏱️ Ore lavorate: <strong id="popup-ore">—</strong></p>
                <p>📋 Causale: <strong id="popup-causale">—</strong></p>
            </div>
            <button onclick="document.getElementById('popup-timbratura').classList.add('hidden')"
                    class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium">
                Chiudi
            </button>
        </div>
    </div>
</div>

{{-- FullCalendar --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.11/locales/it.global.min.js"></script>


<script>
    const aggiornaOrologio = () => {
        const orologio = document.getElementById('orologio');
        const dataOggi = document.getElementById('data-oggi');
        if (!orologio || !dataOggi) return;

        const ora = new Date();
        const pad = n => String(n).padStart(2, '0');
        orologio.textContent = `${pad(ora.getHours())}:${pad(ora.getMinutes())}:${pad(ora.getSeconds())}`;

        const giorni = ['Domenica','Lunedì','Martedì','Mercoledì','Giovedì','Venerdì','Sabato'];
        const mesi = ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',
                    'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];
        dataOggi.textContent = `${giorni[ora.getDay()]} ${ora.getDate()} ${mesi[ora.getMonth()]} ${ora.getFullYear()}`;
    };

    setInterval(aggiornaOrologio, 1000);
    aggiornaOrologio();

    // GPS
    let latitudine = null;
    let longitudine = null;

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                latitudine  = pos.coords.latitude;
                longitudine = pos.coords.longitude;
                const gps = document.getElementById('gps-status');
                if (gps) {
                    gps.innerHTML = `📍 Posizione rilevata: ${latitudine.toFixed(4)}, ${longitudine.toFixed(4)}`;
                    gps.classList.replace('bg-gray-100', 'bg-green-50');
                }
            },
            () => {
                const gps = document.getElementById('gps-status');
                if (gps) gps.textContent = '⚠️ GPS non disponibile';
            }
        );
    }

    const timbra = (tipo) => {
        if (tipo === 'entrata') {
            const cantiere = document.getElementById('cantiere_id')?.value;
            if (!cantiere) {
                alert('Seleziona un cantiere prima di timbrare!');
                return;
            }
            document.getElementById('input-cantiere').value = cantiere;
            document.getElementById('input-causale').value = document.getElementById('causale').value;
            document.getElementById('input-lat').value = latitudine ?? '';
            document.getElementById('input-lng').value = longitudine ?? '';
            document.getElementById('form-entrata').submit();
        } else {
            document.getElementById('input-lat-uscita').value = latitudine ?? '';
            document.getElementById('input-lng-uscita').value = longitudine ?? '';
            document.getElementById('form-uscita').submit();
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        const calEl = document.getElementById('calendario');
        const isAdmin = calEl.dataset.isAdmin === '1';

        const calendar = new FullCalendar.Calendar(calEl, {
            initialView: 'dayGridMonth',
            locale: 'it',
            height: 500,
            headerToolbar: {
                left:   'prev,next today',
                center: 'title',
                right:  'dayGridMonth,listMonth'
            },
            events: (info, successCallback, failureCallback) => {
                const dipId = isAdmin
                    ? (document.getElementById('filtro-dipendente')?.value ?? '')
                    : '';

                fetch(`/timbrature/calendario?dipendente_id=${dipId}`)
                    .then(r => r.json())
                    .then(data => successCallback(data))
                    .catch(() => failureCallback());
            },
            eventClick: (info) => {
                const p = info.event.extendedProps;
                document.getElementById('popup-entrata').textContent = p.entrata ?? '—';
                document.getElementById('popup-uscita').textContent  = p.uscita  ?? '—';
                document.getElementById('popup-ore').textContent     = p.ore ? p.ore + 'h' : '—';
                document.getElementById('popup-causale').textContent = p.causale ?? '—';
                document.getElementById('popup-timbratura').classList.remove('hidden');
            },
            eventDisplay: 'block',
            displayEventTime: false,
        });

        calendar.render();

        // Filtro dipendente admin
        const filtro = document.getElementById('filtro-dipendente');
        if (filtro) {
            filtro.addEventListener('change', () => calendar.refetchEvents());
        }
    });

        const scaricaPdf = () => {
        const mese       = document.getElementById('sel-mese').value;
        const anno       = document.getElementById('sel-anno').value;
        const dipendente = document.getElementById('sel-dipendente')?.value ?? '';
        window.location.href = `/timbrature/export-pdf?mese=${mese}&anno=${anno}&dipendente_id=${dipendente}`;
    };
</script>

@endsection