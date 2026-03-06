<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dipendente;
use App\Models\Cantiere;
use App\Models\Mezzo;
use App\Models\Tracciamento;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user instanceof \App\Models\User) {
            Auth::logout();
            return redirect('/login');
        }

        if ($user->isAdmin()) {
            $dati = [
                'totale_dipendenti'   => Dipendente::count(),
                'totale_cantieri'     => Cantiere::count(),
                'cantieri_attivi'     => Cantiere::where('stato', 'attivo')->count(),
                'totale_mezzi'        => Mezzo::count(),
                'ultimi_tracciamenti' => Tracciamento::with(['dipendente', 'cantiere'])
                                            ->latest('data_ora')
                                            ->take(10)
                                            ->get(),
                'cantieri'   => Cantiere::where('stato', 'attivo')->get(),
                'dipendenti' => Dipendente::all(),
                'mezzi'      => Mezzo::all(),
                // Dati combinati
                'dati_combinati' => Dipendente::with(['cantieri', 'mezzi'])->get(),
            ];

            return view('dashboard.admin', compact('dati'));
        }

        // Dipendente
    $dipendente = $user->dipendente;

    if (!$dipendente) {
        $nomeCognome = explode(' ', $user->name, 2);
        $dipendente = Dipendente::create([
            'user_id' => $user->id,
            'nome'    => $nomeCognome[0],
            'cognome' => $nomeCognome[1] ?? '',
        ]);
    }

    $dati = [
        'dipendente'      => $dipendente,
        'cantieri'        => $dipendente->cantieri()->where('stato', 'attivo')->get(),
        'mezzi'           => $dipendente->mezzi,
        'timbrature'      => $dipendente->timbrature()->with('cantiere')->latest('entrata')->take(5)->get(),
        'totale_attivita' => $dipendente->timbrature()->count(),
    ];

    return view('dashboard.dipendente', compact('dati'));
    }

    public function assegnaCantiere(Request $request, Dipendente $dipendente)
    {
        if ($request->cantiere_id) {
            $dipendente->cantieri()->sync([
                $request->cantiere_id => ['data_assegnazione' => now()]
            ]);

            // Crea tracciamento solo se non esiste già oggi
            $esisteGia = Tracciamento::where('dipendente_id', $dipendente->id)
                                    ->where('cantiere_id', $request->cantiere_id)
                                    ->whereDate('data_ora', today())
                                    ->exists();

            if (!$esisteGia) {
                Tracciamento::create([
                    'dipendente_id' => $dipendente->id,
                    'cantiere_id'   => $request->cantiere_id,
                    'mezzo_id'      => $dipendente->mezzi->first()?->id,
                    'tipo_attivita' => 'assegnazione',
                    'data_ora'      => now(),
                    'note'          => 'Assegnazione automatica da dashboard',
                ]);
            }
        } else {
            $dipendente->cantieri()->detach();
        }

        return redirect()->route('dashboard')
                        ->with('success', 'Cantiere aggiornato!');
    }

    public function assegnaMezzo(Request $request, Dipendente $dipendente)
    {
        if ($request->mezzo_id) {
            Mezzo::where('dipendente_id', $dipendente->id)->update(['dipendente_id' => null]);
            Mezzo::find($request->mezzo_id)->update(['dipendente_id' => $dipendente->id]);

            $cantiere = $dipendente->cantieri()->where('stato', 'attivo')->first();

            if ($cantiere) {
                // Aggiorna il mezzo sul tracciamento esistente invece di crearne uno nuovo
                $tracciamento = Tracciamento::where('dipendente_id', $dipendente->id)
                                            ->where('cantiere_id', $cantiere->id)
                                            ->whereDate('data_ora', today())
                                            ->first();

                if ($tracciamento) {
                    $tracciamento->update(['mezzo_id' => $request->mezzo_id]);
                } else {
                    Tracciamento::create([
                        'dipendente_id' => $dipendente->id,
                        'cantiere_id'   => $cantiere->id,
                        'mezzo_id'      => $request->mezzo_id,
                        'tipo_attivita' => 'assegnazione',
                        'data_ora'      => now(),
                        'note'          => 'Assegnazione automatica da dashboard',
                    ]);
                }
            }
        } else {
            Mezzo::where('dipendente_id', $dipendente->id)->update(['dipendente_id' => null]);
        }

        return redirect()->route('dashboard')
                        ->with('success', 'Mezzo aggiornato!');
    }

    public function rimuoviAssegnazioni(Dipendente $dipendente)
    {
        // Rimuove solo le assegnazioni, non il dipendente
        $dipendente->cantieri()->detach();
        Mezzo::where('dipendente_id', $dipendente->id)->update(['dipendente_id' => null]);

        return redirect()->route('dashboard')
                        ->with('success', 'Assegnazioni rimosse!');
    }
}