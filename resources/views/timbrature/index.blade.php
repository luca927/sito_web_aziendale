@extends('layouts.app')

@section('title', 'Timbrature')

@section('content')
<div class="py-6">

    @if(auth()->user()->isAdmin())

        {{-- VISTA ADMIN — solo tabella timbrature dipendenti --}}
        <h1 class="text-2xl font-bold text-blue-700 mb-6">🕐 Timbrature Dipendenti</h1>

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
</script>

@endsection