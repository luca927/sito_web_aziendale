<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tracciamento;
use App\Models\Cantiere;
use App\Models\Dipendente;

class TracciamentoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Admin vede tutti i tracciamenti
        if ($user->isAdmin()) {
            $tracciamenti = Tracciamento::with(['dipendente', 'cantiere'])
                                ->latest('data_ora')
                                ->get();
            $cantieri   = Cantiere::where('stato', 'attivo')->get();
            $dipendenti = Dipendente::all();

            return view('tracciamento.index', compact('tracciamenti', 'cantieri', 'dipendenti'));
        }

        // Dipendente vede solo i suoi
        $dipendente   = $user->dipendente;
        $tracciamenti = Tracciamento::with('cantiere')
                            ->where('dipendente_id', $dipendente->id)
                            ->latest('data_ora')
                            ->get();
        $cantieri = $dipendente->cantieri()->where('stato', 'attivo')->get();

        return view('tracciamento.index', compact('tracciamenti', 'cantieri'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cantiere_id' => 'required|exists:cantieri,id',
            'data_ora'    => 'required|date',
            'note'        => 'nullable|string',
        ]);

        $dipendente_id = auth()->user()->isAdmin()
            ? $request->dipendente_id
            : auth()->user()->dipendente->id;

        Tracciamento::create([
            'dipendente_id' => $dipendente_id,
            'cantiere_id'   => $request->cantiere_id,
            'data_ora'      => $request->data_ora,
            'note'          => $request->note,
        ]);

        return redirect()->route('tracciamento.index')
                         ->with('success', 'Tracciamento aggiunto!');
    }

    public function destroy(Tracciamento $tracciamento)
    {
        $tracciamento->delete();
        return redirect()->route('tracciamento.index')
                         ->with('success', 'Tracciamento eliminato!');
    }
}