<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tracciamento;
use App\Models\Cantiere;
use App\Models\Dipendente;
use App\Models\Mezzo;
use League\Csv\Writer;
use SplTempFileObject;

class TracciamentoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $tracciamenti = Tracciamento::with(['dipendente', 'cantiere', 'mezzo'])
                                ->latest('data_ora')
                                ->get();
            $cantieri   = Cantiere::where('stato', 'attivo')->get();
            $dipendenti = Dipendente::all();
            $mezzi      = Mezzo::all();

            return view('tracciamento.index', compact('tracciamenti', 'cantieri', 'dipendenti', 'mezzi'));
        }

        $dipendente   = $user->dipendente;
        $tracciamenti = Tracciamento::with(['cantiere', 'mezzo'])
                            ->where('dipendente_id', $dipendente->id)
                            ->latest('data_ora')
                            ->get();
        $cantieri = $dipendente->cantieri()->where('stato', 'attivo')->get();
        $mezzi    = $dipendente->mezzi;

        return view('tracciamento.index', compact('tracciamenti', 'cantieri', 'mezzi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cantiere_id'   => 'required|exists:cantieri,id',
            'mezzo_id'      => 'nullable|exists:mezzi,id',
            'tipo_attivita' => 'nullable|string',
            'data_ora'      => 'required|date',
            'note'          => 'nullable|string',
        ]);

        $dipendente_id = Auth::user()->isAdmin()
            ? $request->dipendente_id
            : Auth::user()->dipendente->id;

        // Prendi coordinate dal cantiere
        $cantiere = Cantiere::find($request->cantiere_id);

        Tracciamento::create([
            'dipendente_id' => $dipendente_id,
            'cantiere_id'   => $request->cantiere_id,
            'mezzo_id'      => $request->mezzo_id,
            'tipo_attivita' => $request->tipo_attivita,
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

        public function exportCsv()
    {
        $tracciamenti = Tracciamento::with(['dipendente', 'cantiere', 'mezzo'])->latest('data_ora')->get();

        $csv = Writer::createFromString();

        $csv->insertOne([
            'ID', 'Dipendente', 'Cantiere', 'Mezzo',
            'Tipo Attività', 'Latitudine', 'Longitudine', 'Data/Ora'
        ]);

        foreach ($tracciamenti as $t) {
            $csv->insertOne([
                $t->id,
                $t->dipendente->nome . ' ' . $t->dipendente->cognome,
                $t->cantiere->nome,
                $t->mezzo->modello ?? $t->mezzo->tipo ?? '—',
                $t->tipo_attivita ?? '—',
                $t->cantiere->latitudine ?? '—',
                $t->cantiere->longitudine ?? '—',
                \Carbon\Carbon::parse($t->data_ora)->format('d/m/Y H:i'),
            ]);
        }

        return response($csv->toString())
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="tracciamenti_' . now()->format('Y-m-d') . '.csv"');
    }
}