<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Timbratura;
use App\Models\Cantiere;

class TimbratureController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Admin vede tutte le timbrature
        if ($user->isAdmin()) {
            $timbrature = Timbratura::with(['dipendente', 'cantiere'])
                                ->latest('entrata')
                                ->get();
            return view('timbrature.index', compact('timbrature'));
        }

        // Dipendente vede solo le sue
        $dipendente = $user->dipendente;
        $timbrature = Timbratura::with('cantiere')
                            ->where('dipendente_id', $dipendente->id)
                            ->latest('entrata')
                            ->get();

        $cantieri = $dipendente->cantieri()->where('stato', 'attivo')->get();

        return view('timbrature.index', compact('timbrature', 'cantieri'));
    }

    // Dipendente timbra entrata
    public function entrata(Request $request)
    {
        $request->validate([
            'cantiere_id' => 'required|exists:cantieri,id',
        ]);

        $dipendente = auth()->user()->dipendente;

        // Controlla se ha già una timbratura aperta
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
            'entrata'       => now(),
        ]);

        return redirect()->route('timbrature.index')
                         ->with('success', 'Entrata registrata!');
    }

    // Dipendente timbra uscita
    public function uscita()
    {
        $dipendente = auth()->user()->dipendente;

        $timbratura = Timbratura::where('dipendente_id', $dipendente->id)
                                ->whereNull('uscita')
                                ->first();

        if (!$timbratura) {
            return redirect()->route('timbrature.index')
                             ->with('error', 'Nessuna timbratura aperta trovata!');
        }

        $timbratura->update(['uscita' => now()]);

        return redirect()->route('timbrature.index')
                         ->with('success', 'Uscita registrata!');
    }
}