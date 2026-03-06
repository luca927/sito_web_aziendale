<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cantiere;
use App\Models\Dipendente;
use League\Csv\Writer;
use SplTempFileObject;

class CantiereController extends Controller
{
    public function index()
    {
        $cantieri = Cantiere::with('dipendenti')->oldest()->get();
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
            'referente'  => 'nullable|string|max:255',
            'giorni'     => 'nullable|string|max:255',
            'latitudine' => 'nullable|numeric',
            'longitudine'=> 'nullable|numeric',
            'data_inizio'=> 'nullable|date',
            'data_fine'  => 'nullable|date',
            'stato'      => 'required|in:attivo,completato,sospeso',
        ]);

        $cantiere = Cantiere::create($request->except('dipendenti'));

        if ($request->has('dipendenti')) {
            $cantiere->dipendenti()->attach($request->dipendenti, [
                'data_assegnazione' => now()
            ]);
        }

        return redirect()->route('cantieri.index')
                        ->with('success', 'Cantiere creato con successo!');
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
            'referente'  => 'nullable|string|max:255',
            'giorni'     => 'nullable|string|max:255',
            'latitudine' => 'nullable|numeric',
            'longitudine'=> 'nullable|numeric',
            'data_inizio'=> 'nullable|date',
            'data_fine'  => 'nullable|date',
            'stato'      => 'required|in:attivo,completato,sospeso',
        ]);

        $cantiere->update($request->except('dipendenti'));

        $cantiere->dipendenti()->sync(
            collect($request->dipendenti ?? [])->mapWithKeys(fn($id) => [
                $id => ['data_assegnazione' => now()]
            ])
        );

        return redirect()->route('cantieri.index')
                        ->with('success', 'Cantiere aggiornato!');
    }

    public function destroy(Cantiere $cantiere)
    {
        $cantiere->delete();
        return redirect()->route('cantieri.index')
                         ->with('success', 'Cantiere eliminato!');
    }

    public function exportCsv()
    {
        $cantieri = Cantiere::with('dipendenti')->get();

        $csv = Writer::createFromString();

        $csv->insertOne([
            'ID', 'Nome', 'Indirizzo', 'Referente',
            'Data Inizio', 'Data Fine', 'Stato', 'Dipendenti'
        ]);

        foreach ($cantieri as $c) {
            $csv->insertOne([
                $c->id,
                $c->nome,
                $c->indirizzo,
                $c->referente ?? '—',
                $c->data_inizio ? \Carbon\Carbon::parse($c->data_inizio)->format('d/m/Y') : '—',
                $c->data_fine ? \Carbon\Carbon::parse($c->data_fine)->format('d/m/Y') : '—',
                $c->stato,
                $c->dipendenti->map(fn($d) => $d->nome . ' ' . $d->cognome)->join(', '),
            ]);
        }

        return response($csv->toString())
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="cantieri_' . now()->format('Y-m-d') . '.csv"');
    }
}