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
            'nome'     => 'required|string|max:255',
            'cognome'  => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'telefono' => 'nullable|string|max:20',
            'mansione' => 'nullable|string|max:255',
            'password' => 'required|min:8',
        ]);

        // Crea l'utente collegato
        $user = User::create([
            'name'     => $request->nome . ' ' . $request->cognome,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'ruolo'    => 'dipendente',
        ]);

        // Crea il dipendente collegato all'utente
        Dipendente::create([
            'user_id'  => $user->id,
            'nome'     => $request->nome,
            'cognome'  => $request->cognome,
            'telefono' => $request->telefono,
            'mansione' => $request->mansione,
        ]);

        return redirect()->route('dipendenti.index')
                         ->with('success', 'Dipendente creato con successo!');
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

    public function update(Request $request, Dipendente $dipendente)
    {
        $request->validate([
            'nome'     => 'required|string|max:255',
            'cognome'  => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'mansione' => 'nullable|string|max:255',
        ]);

        $dipendente->update($request->only(['nome', 'cognome', 'telefono', 'mansione']));

        // Aggiorna anche il nome sull'utente collegato
        $dipendente->user->update([
            'name' => $request->nome . ' ' . $request->cognome
        ]);

        return redirect()->route('dipendenti.index')
                         ->with('success', 'Dipendente aggiornato!');
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