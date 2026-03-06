<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Mezzo;
use Carbon\Carbon;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Condivide i mezzi in scadenza con tutte le view
        View::composer('*', function ($view) {
            if (auth()->check() && auth()->user()->isAdmin()) {
                $mezziInScadenza = Mezzo::whereNotNull('prossima_manutenzione')
                    ->where('prossima_manutenzione', '<=', Carbon::now()->addDays(30))
                    ->where('prossima_manutenzione', '>=', Carbon::now())
                    ->get();

                $mezziScaduti = Mezzo::whereNotNull('prossima_manutenzione')
                    ->where('prossima_manutenzione', '<', Carbon::now())
                    ->get();

                $view->with('mezziInScadenza', $mezziInScadenza);
                $view->with('mezziScaduti', $mezziScaduti);
                $view->with('totaleManutenzioni', $mezziInScadenza->count() + $mezziScaduti->count());
            } else {
                $view->with('mezziInScadenza', collect());
                $view->with('mezziScaduti', collect());
                $view->with('totaleManutenzioni', 0);
            }
        });
    }
}