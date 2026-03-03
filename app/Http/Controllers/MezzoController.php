<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mezzo;
use App\Models\Dipendente;

class MezzoController extends Controller
{
    public function index()
    {
        $mezzi = Mezzo::with('dipendente')->latest()->get();
        return view('mezzi.index', compact('mezzi'));
    }

    public function create()
    {
        $dipendenti = Dipendente::all();
        return view('mezzi.create', compact('dipendenti'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dipendente_id' => 'required|exists:dipendenti,id',
            'tipo'          => 'required|string|max:255',
            'targa'         => 'required|string|max:20|unique:mezzi,targa',
            'modello'       => 'nullable|string|max:255',
            'stato'         => 'required|in:attivo,in_manutenzione,fuori_uso',
        ]);

        Mezzo::create($request->all());

        return redirect()->route('mezzi.index')
                         ->with('success', 'Mezzo aggiunto con successo!');
    }

    public function show(Mezzo $mezzo)
    {
        $mezzo->load('dipendente');
        return view('mezzi.show', compact('mezzo'));
    }

    public function edit(Mezzo $mezzo)
    {
        $dipendenti = Dipendente::all();
        return view('mezzi.edit', compact('mezzo', 'dipendenti'));
    }

    public function update(Request $request, Mezzo $mezzo)
    {
        $request->validate([
            'dipendente_id' => 'required|exists:dipendenti,id',
            'tipo'          => 'required|string|max:255',
            'targa'         => 'required|string|max:20|unique:mezzi,targa,' . $mezzo->id,
            'modello'       => 'nullable|string|max:255',
            'stato'         => 'required|in:attivo,in_manutenzione,fuori_uso',
        ]);

        $mezzo->update($request->all());

        return redirect()->route('mezzi.index')
                         ->with('success', 'Mezzo aggiornato!');
    }

    public function destroy(Mezzo $mezzo)
    {
        $mezzo->delete();
        return redirect()->route('mezzi.index')
                         ->with('success', 'Mezzo eliminato!');
    }
}