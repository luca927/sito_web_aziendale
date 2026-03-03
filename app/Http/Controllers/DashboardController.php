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
            ];

            return view('dashboard.admin', compact('dati'));
        }

        // Dipendente
        $dipendente = $user->dipendente;

        if (!$dipendente) {
            // Crea automaticamente il profilo dipendente se non esiste
            $nomeCognome = explode(' ', $user->name, 2);
            $dipendente = Dipendente::create([
                'user_id' => $user->id,
                'nome'    => $nomeCognome[0],
                'cognome' => $nomeCognome[1] ?? '',
            ]);
        }

        $dati = [
            'cantieri'   => $dipendente->cantieri()->where('stato', 'attivo')->get(),
            'mezzi'      => $dipendente->mezzi,
            'timbrature' => $dipendente->timbrature()->latest('entrata')->take(5)->get(),
        ];

        return view('dashboard.dipendente', compact('dati'));
    }
}