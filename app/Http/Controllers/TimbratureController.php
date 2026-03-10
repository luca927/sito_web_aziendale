<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timbratura;
use App\Models\Cantiere;
use App\Models\Dipendente;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TimbratureController extends Controller
{
        public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $timbrature = Timbratura::with(['dipendente', 'cantiere'])
                                ->whereDate('entrata', today())
                                ->latest('entrata')
                                ->get();
            $dipendenti = Dipendente::all();
            return view('timbrature.index', compact('timbrature', 'dipendenti'));
        }

        $dipendente = $user->dipendente;
        $timbrature = Timbratura::with('cantiere')
                            ->where('dipendente_id', $dipendente->id)
                            ->whereDate('entrata', today())
                            ->latest('entrata')
                            ->get();

        $cantieri         = $dipendente->cantieri()->where('stato', 'attivo')->get();
        $timbraturaAperta = Timbratura::where('dipendente_id', $dipendente->id)
                                    ->whereNull('uscita')
                                    ->first();
        $dipendenti       = collect();

        return view('timbrature.index', compact('timbrature', 'cantieri', 'timbraturaAperta', 'dipendente', 'dipendenti'));
    }

    public function entrata(Request $request)
    {
        $request->validate([
            'cantiere_id' => 'required|exists:cantieri,id',
            'causale'     => 'nullable|string',
            'latitudine'  => 'nullable|numeric',
            'longitudine' => 'nullable|numeric',
        ]);

        $dipendente = auth()->user()->dipendente;

        $aperta = Timbratura::where('dipendente_id', $dipendente->id)
                            ->whereNull('uscita')
                            ->first();

        if ($aperta) {
            return redirect()->route('timbrature.index')
                             ->with('error', 'Hai già una timbratura aperta!');
        }

        Timbratura::create([
            'dipendente_id' => $dipendente->id,
            'cantiere_id'   => $request->cantiere_id,
            'causale'       => $request->causale ?? 'Lavoro Ordinario',
            'entrata'       => now(),
            'latitudine'    => $request->latitudine,
            'longitudine'   => $request->longitudine,
        ]);

        return redirect()->route('timbrature.index')
                         ->with('success', 'Entrata registrata!');
    }

    public function uscita(Request $request)
    {
        $dipendente = auth()->user()->dipendente;

        $timbratura = Timbratura::where('dipendente_id', $dipendente->id)
                                ->whereNull('uscita')
                                ->first();

        if (!$timbratura) {
            return redirect()->route('timbrature.index')
                             ->with('error', 'Nessuna timbratura aperta trovata!');
        }

        $timbratura->update([
            'uscita'      => now(),
            'latitudine'  => $request->latitudine,
            'longitudine' => $request->longitudine,
        ]);

        return redirect()->route('timbrature.index')
                         ->with('success', 'Uscita registrata!');
    }

    public function calendario(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $dipendente_id = $request->dipendente_id;
            $query = Timbratura::with('dipendente')
                ->whereNotNull('entrata');

            if ($dipendente_id) {
                $query->where('dipendente_id', $dipendente_id);
            }
        } else {
            $query = Timbratura::where('dipendente_id', $user->dipendente->id)
                ->whereNotNull('entrata');
        }

        $timbrature = $query->get()->map(function($t) {
            $entrata = \Carbon\Carbon::parse($t->entrata);
            $uscita  = $t->uscita ? \Carbon\Carbon::parse($t->uscita) : null;
            $ore     = $uscita ? round($entrata->diffInMinutes($uscita) / 60, 1) : null;

            return [
                'id'    => $t->id,
                'title' => $t->dipendente
                    ? $t->dipendente->nome . ' ' . $t->dipendente->cognome . ($ore ? " ({$ore}h)" : '')
                    : ($ore ? "{$ore}h" : 'Presente'),
                'start' => $entrata->format('Y-m-d'),
                'end'   => $uscita ? $uscita->format('Y-m-d') : $entrata->format('Y-m-d'),
                'color' => $uscita ? '#16a34a' : '#f59e0b',
                'extendedProps' => [
                    'entrata' => $entrata->format('H:i'),
                    'uscita'  => $uscita ? $uscita->format('H:i') : 'In corso',
                    'causale' => $t->causale,
                    'ore'     => $ore,
                ]
            ];
        });

        return response()->json($timbrature);
    }

        public function exportPdf(Request $request)
    {
        $mese    = $request->mese ?? now()->month;
        $anno    = $request->anno ?? now()->year;
        $user    = auth()->user();

        if ($user->isAdmin()) {
            $dipendente_id = $request->dipendente_id;
            $query = Timbratura::with(['dipendente', 'cantiere'])
                ->whereMonth('entrata', $mese)
                ->whereYear('entrata', $anno);

            if ($dipendente_id) {
                $query->where('dipendente_id', $dipendente_id);
            }

            $dipendente = $dipendente_id ? Dipendente::find($dipendente_id) : null;
        } else {
            $dipendente = $user->dipendente;
            $query = Timbratura::with('cantiere')
                ->where('dipendente_id', $dipendente->id)
                ->whereMonth('entrata', $mese)
                ->whereYear('entrata', $anno);
        }

        $timbrature = $query->orderBy('entrata')->get();

        // Calcola totale ore
        $totaleMinuti = $timbrature->sum(function($t) {
            if (!$t->uscita) return 0;
            return \Carbon\Carbon::parse($t->entrata)->diffInMinutes(\Carbon\Carbon::parse($t->uscita));
        });
        $totaleOre = floor($totaleMinuti / 60);
        $totaleMin = $totaleMinuti % 60;

        $nomeMese = \Carbon\Carbon::create($anno, $mese)->locale('it')->isoFormat('MMMM YYYY');

        $pdf = Pdf::loadView('pdf.timbrature', compact(
            'timbrature', 'dipendente', 'nomeMese', 'totaleOre', 'totaleMin', 'user'
        ))->setPaper('a4', 'portrait');

        return $pdf->download("timbrature_{$nomeMese}.pdf");
    }

    public function notifiche(Request $request)
        {
            $nuove = Timbratura::with('dipendente')
                ->where(function($q) {
                    // Nuove entrate negli ultimi 31 secondi
                    $q->where('created_at', '>=', now()->subSeconds(31))
                    ->whereNull('uscita');
                })
                ->orWhere(function($q) {
                    // Uscite registrate negli ultimi 31 secondi
                    $q->where('updated_at', '>=', now()->subSeconds(31))
                    ->whereNotNull('uscita');
                })
                ->get()
                ->map(function($t) {
                    $isUscita = $t->uscita && $t->updated_at >= now()->subSeconds(31);
                    return [
                        'id'         => $t->id . ($isUscita ? '_uscita' : '_entrata'),
                        'dipendente' => $t->dipendente->nome . ' ' . $t->dipendente->cognome,
                        'tipo'       => $isUscita ? 'uscita' : 'entrata',
                        'ora'        => $isUscita
                            ? \Carbon\Carbon::parse($t->uscita)->format('H:i')
                            : \Carbon\Carbon::parse($t->entrata)->format('H:i'),
                    ];
                });

            return response()->json($nuove);
        }
}