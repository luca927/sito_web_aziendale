<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfiloController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profilo.index', compact('user'));
    }

    public function aggiornaDati(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('profilo.index')
                         ->with('success', 'Dati aggiornati con successo!');
    }

    public function aggiornaPassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password_attuale' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->password_attuale, $user->password)) {
            return back()->withErrors(['password_attuale' => 'La password attuale non è corretta.']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('profilo.index')
                         ->with('success', 'Password aggiornata con successo!');
    }
}