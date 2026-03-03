<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timbratura;
use App\Models\Cantiere;

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
            return view('timbrature.index', compact('timbrature'));
        }

        $dipendente = $user->dipendente;
        $timbrature = Timbratura::with('cantiere')
                            ->where('dipendente_id', $dipendente->id)
                            ->whereDate('entrata', today())
                            ->latest('entrata')
                            ->get();

        $cantieri = $dipendente->cantieri()->where('stato', 'attivo')->get();
        $timbraturaAperta = Timbratura::where('dipendente_id', $dipendente->id)
                                      ->whereNull('uscita')
                                      ->first();

        return view('timbrature.index', compact('timbrature', 'cantieri', 'timbraturaAperta'));
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
}