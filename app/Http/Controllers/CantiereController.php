<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cantiere;
use App\Models\Dipendente;

class CantiereController extends Controller
{
    public function index()
    {
        $cantieri = Cantiere::with('dipendenti')->latest()->get();
        return view('cantieri.index', compact('cantieri'));
    }

    public function create()
    {
        $dipendenti = Dipendente::all();
        return view('cantieri.create', compact('dipendenti'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'       => 'required|string|max:255',
            'indirizzo'  => 'required|string',
            'latitudine' => 'nullable|numeric',
            'longitudine'=> 'nullable|numeric',
            'data_inizio'=> 'nullable|date',
            'data_fine'  => 'nullable|date',
            'stato'      => 'required|in:attivo,completato,sospeso',
        ]);

        $cantiere = Cantiere::create($request->all());

        // Assegna dipendenti se selezionati
        if ($request->has('dipendenti')) {
            $cantiere->dipendenti()->attach($request->dipendenti, [
                'data_assegnazione' => now()
            ]);
        }

        return redirect()->route('cantieri.index')
                         ->with('success', 'Cantiere creato con successo!');
    }

    public function show(Cantiere $cantiere)
    {
        $cantiere->load('dipendenti', 'tracciamenti');
        return view('cantieri.show', compact('cantiere'));
    }

    public function edit(Cantiere $cantiere)
    {
        $dipendenti = Dipendente::all();
        $assegnati  = $cantiere->dipendenti->pluck('id')->toArray();
        return view('cantieri.edit', compact('cantiere', 'dipendenti', 'assegnati'));
    }

    public function update(Request $request, Cantiere $cantiere)
    {
        $request->validate([
            'nome'       => 'required|string|max:255',
            'indirizzo'  => 'required|string',
            'latitudine' => 'nullable|numeric',
            'longitudine'=> 'nullable|numeric',
            'data_inizio'=> 'nullable|date',
            'data_fine'  => 'nullable|date',
            'stato'      => 'required|in:attivo,completato,sospeso',
        ]);

        $cantiere->update($request->all());

        // Aggiorna dipendenti assegnati
        if ($request->has('dipendenti')) {
            $cantiere->dipendenti()->sync(collect($request->dipendenti)->mapWithKeys(fn($id) => [
                $id => ['data_assegnazione' => now()]
            ]));
        }

        return redirect()->route('cantieri.index')
                         ->with('success', 'Cantiere aggiornato!');
    }

    public function destroy(Cantiere $cantiere)
    {
        $cantiere->delete();
        return redirect()->route('cantieri.index')
                         ->with('success', 'Cantiere eliminato!');
    }
}