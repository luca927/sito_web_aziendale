<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
            background: #fff;
        }

        .header {
            background: #1a2a4a;
            color: white;
            padding: 20px 30px;
            margin-bottom: 24px;
        }

        .header h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .header p {
            font-size: 11px;
            opacity: 0.8;
        }

        .info-box {
            background: #f3f4f6;
            border-radius: 6px;
            padding: 12px 20px;
            margin: 0 30px 20px 30px;
            display: flex;
            justify-content: space-between;
        }

        .info-box .label {
            font-size: 9px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 3px;
        }

        .info-box .value {
            font-size: 13px;
            font-weight: bold;
            color: #1a2a4a;
        }

        .content { padding: 0 30px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead tr {
            background: #1a2a4a;
            color: white;
        }

        thead th {
            padding: 10px 12px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        tbody td {
            padding: 9px 12px;
            font-size: 10px;
        }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-green { background: #dcfce7; color: #15803d; }
        .badge-yellow { background: #fef9c3; color: #a16207; }
        .badge-blue { background: #dbeafe; color: #1d4ed8; }

        .totale-box {
            background: #1a2a4a;
            color: white;
            padding: 14px 20px;
            border-radius: 8px;
            margin: 0 30px 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .totale-box .label { font-size: 10px; opacity: 0.7; }
        .totale-box .ore { font-size: 22px; font-weight: bold; }

        .footer {
            margin-top: 30px;
            padding: 12px 30px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #9ca3af;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h1>📋 Report Timbrature</h1>
        <p>{{ $nomeMese }} — Generato il {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    {{-- Info box --}}
    <div class="info-box">
        <div>
            <div class="label">Dipendente</div>
            <div class="value">
                {{ $dipendente ? $dipendente->nome . ' ' . $dipendente->cognome : 'Tutti i dipendenti' }}
            </div>
        </div>
        <div>
            <div class="label">Periodo</div>
            <div class="value">{{ $nomeMese }}</div>
        </div>
        <div>
            <div class="label">Presenze</div>
            <div class="value">{{ $timbrature->count() }}</div>
        </div>
    </div>

    {{-- Tabella --}}
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    @if(!$dipendente)
                        <th>Dipendente</th>
                    @endif
                    <th>Cantiere</th>
                    <th>Causale</th>
                    <th>Entrata</th>
                    <th>Uscita</th>
                    <th>Ore</th>
                </tr>
            </thead>
            <tbody>
                @forelse($timbrature as $t)
                @php
                    $ore = '—';
                    if ($t->uscita) {
                        $min = \Carbon\Carbon::parse($t->entrata)->diffInMinutes(\Carbon\Carbon::parse($t->uscita));
                        $ore = floor($min/60) . 'h ' . ($min%60) . 'm';
                    }
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($t->entrata)->format('d/m/Y') }}</td>
                    @if(!$dipendente)
                        <td>{{ $t->dipendente->nome ?? '—' }} {{ $t->dipendente->cognome ?? '' }}</td>
                    @endif
                    <td>{{ $t->cantiere->nome ?? '—' }}</td>
                    <td>
                        <span class="badge {{ $t->causale === 'Straordinario' ? 'badge-yellow' : ($t->causale === 'Trasferta' ? 'badge-blue' : 'badge-green') }}">
                            {{ $t->causale ?? 'Lavoro Ordinario' }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($t->entrata)->format('H:i') }}</td>
                    <td>{{ $t->uscita ? \Carbon\Carbon::parse($t->uscita)->format('H:i') : 'In corso' }}</td>
                    <td><strong>{{ $ore }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding: 20px; color: #9ca3af;">
                        Nessuna timbratura nel periodo selezionato.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Totale ore --}}
    <div class="totale-box">
        <div>
            <div class="label">TOTALE ORE LAVORATE</div>
            <div class="ore">{{ $totaleOre }}h {{ $totaleMin }}m</div>
        </div>
        <div style="text-align:right">
            <div class="label">GIORNI LAVORATI</div>
            <div class="ore">{{ $timbrature->whereNotNull('uscita')->count() }}</div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <span>Arsnet — Gestionale Aziendale</span>
        <span>Report generato automaticamente</span>
    </div>

</body>
</html>