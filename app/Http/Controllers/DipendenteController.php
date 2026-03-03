<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dipendente;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DipendenteController extends Controller
{
    public function index()
    {
        $dipendenti = Dipendente::with(['user', 'cantieri', 'mezzi'])->latest()->get();
        return view('dipendenti.index', compact('dipendenti'));
    }

    public function create()
    {
        return view('dipendenti.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'           => 'required|string|max:255',
            'cognome'        => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'telefono'       => 'nullable|string|max:20',
            'codice_fiscale' => 'nullable|string|max:16',
            'mansione'       => 'nullable|string|max:255',
            'indirizzo'      => 'nullable|string|max:255',
            'data_assunzione'=> 'nullable|date',
            'password'       => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->nome . ' ' . $request->cognome,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'ruolo'    => 'dipendente',
        ]);

        Dipendente::create([
            'user_id'        => $user->id,
            'nome'           => $request->nome,
            'cognome'        => $request->cognome,
            'telefono'       => $request->telefono,
            'codice_fiscale' => $request->codice_fiscale,
            'mansione'       => $request->mansione,
            'indirizzo'      => $request->indirizzo,
            'data_assunzione'=> $request->data_assunzione,
        ]);

        return redirect()->route('dipendenti.index')
                        ->with('success', 'Dipendente aggiunto con successo!');
    }

    public function update(Request $request, Dipendente $dipendente)
    {
        $request->validate([
            'nome'           => 'required|string|max:255',
            'cognome'        => 'required|string|max:255',
            'telefono'       => 'nullable|string|max:20',
            'codice_fiscale' => 'nullable|string|max:16',
            'mansione'       => 'nullable|string|max:255',
            'indirizzo'      => 'nullable|string|max:255',
            'data_assunzione'=> 'nullable|date',
        ]);

        $dipendente->update($request->only([
            'nome', 'cognome', 'telefono', 'codice_fiscale',
            'mansione', 'indirizzo', 'data_assunzione'
        ]));

        $dipendente->user->update([
            'name' => $request->nome . ' ' . $request->cognome
        ]);

        return redirect()->route('dipendenti.index')
                        ->with('success', 'Dipendente aggiornato!');
    }
    public function show(Dipendente $dipendente)
    {
        $dipendente->load(['user', 'cantieri', 'mezzi', 'timbrature']);
        return view('dipendenti.show', compact('dipendente'));
    }

    public function edit(Dipendente $dipendente)
    {
        return view('dipendenti.edit', compact('dipendente'));
    }

    public function destroy(Dipendente $dipendente)
    {
        // Elimina anche l'utente collegato
        $dipendente->user->delete();
        $dipendente->delete();

        return redirect()->route('dipendenti.index')
                         ->with('success', 'Dipendente eliminato!');
    }
}