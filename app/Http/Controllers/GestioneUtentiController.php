<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Dipendente;
use App\Models\User;

class GestioneUtentiController extends Controller
{
    public function index()
    {
        $utenti = User::with('dipendente')->latest()->get();

        $dati = [
            'totale'      => $utenti->count(),
            'admin'       => $utenti->where('ruolo', 'admin')->count(),
            'manager'     => $utenti->where('ruolo', 'manager')->count(),
            'dipendenti'  => $utenti->where('ruolo', 'dipendente')->count(),
            'utenti'      => $utenti,
        ];

        return view('gestione_utenti.index', compact('dati'));
    }

    public function create()
    {
        return view('gestione_utenti.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'ruolo'    => 'required|in:admin,manager,dipendente',
            'livello'  => 'nullable|in:junior,middle,senior',
            'password' => 'required|min:8|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'username' => $request->username,
            'ruolo'    => $request->ruolo,
            'livello'  => $request->livello,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('gestione_utenti.index')
                         ->with('success', 'Utente creato con successo!');
    }

    public function edit(User $user)
    {
        return view('gestione_utenti.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'username' => 'nullable|string|unique:users,username,' . $user->id,
            'ruolo'    => 'required|in:admin,manager,dipendente',
            'livello'  => 'nullable|in:junior,middle,senior',
        ]);

        $user->update([
            'name'     => $request->name,
            'email'    => $request->email,
            'username' => $request->username ?? $user->username,
            'ruolo'    => $request->ruolo,
            'livello'  => $request->livello,
        ]);

        return redirect()->route('gestione_utenti.index')
                        ->with('success', 'Utente aggiornato!');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('gestione_utenti.index')
                        ->with('success', "Password di {$user->name} resettata con successo!");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('gestione_utenti.index')
                             ->with('error', 'Non puoi eliminare te stesso!');
        }

        $user->delete();

        return redirect()->route('gestione_utenti.index')
                         ->with('success', 'Utente eliminato!');
    }
}