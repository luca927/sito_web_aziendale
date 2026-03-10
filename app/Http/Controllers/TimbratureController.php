<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timbratura;
use App\Models\Cantiere;
use App\Models\Dipendente;
use Illuminate\Support\Facades\Auth;

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
}