<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DipendenteController;
use App\Http\Controllers\CantiereController;
use App\Http\Controllers\MezzoController;
use App\Http\Controllers\TracciamentoController;
use App\Http\Controllers\TimbratureController;
use App\Http\Controllers\GestioneUtentiController;
use App\Http\Controllers\ProfiloController;

// Redirect dalla root al login
Route::get('/', function () {
    return redirect('/login');
});

// Route pubblica — login/register (gestite da Breeze)
require __DIR__.'/auth.php';

// Route protette — utente autenticato
Route::middleware(['auth'])->group(function () {

    // Dashboard — accessibile da entrambi i ruoli
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profilo', [ProfiloController::class, 'index'])->name('profilo.index');
    Route::post('/profilo/dati', [ProfiloController::class, 'aggiornaDati'])->name('profilo.dati');
    Route::post('/profilo/password', [ProfiloController::class, 'aggiornaPassword'])->name('profilo.password');

    // Route solo ADMIN
    Route::middleware(['admin'])->group(function () {
        Route::resource('dipendenti', DipendenteController::class)
            ->parameters(['dipendenti' => 'dipendente']);

        Route::resource('cantieri', CantiereController::class)
            ->except(['show'])
            ->parameters(['cantieri' => 'cantiere']);

        Route::resource('mezzi', MezzoController::class)
            ->parameters(['mezzi' => 'mezzo']);

        Route::resource('gestione_utenti', GestioneUtentiController::class)
        ->except(['show'])
        ->parameters(['gestione_utenti' => 'user']);

        Route::post('gestione_utenti/{user}/reset-password', [GestioneUtentiController::class, 'resetPassword'])
        ->name('gestione_utenti.reset-password');

        Route::post('dashboard/assegna-cantiere/{dipendente}', [DashboardController::class, 'assegnaCantiere'])->name('dashboard.assegna-cantiere');
        Route::post('dashboard/assegna-mezzo/{dipendente}', [DashboardController::class, 'assegnaMezzo'])->name('dashboard.assegna-mezzo');
        Route::post('dashboard/rimuovi-assegnazioni/{dipendente}', [DashboardController::class, 'rimuoviAssegnazioni'])->name('dashboard.rimuovi-assegnazioni');

        Route::get('cantieri/export-csv', [CantiereController::class, 'exportCsv'])->name('cantieri.export-csv');
        Route::get('tracciamento/export-csv', [TracciamentoController::class, 'exportCsv'])->name('tracciamento.export-csv');
        
    });

    // Route accessibili da entrambi i ruoli
    Route::resource('tracciamento', TracciamentoController::class);
    Route::get('/timbrature', [TimbratureController::class, 'index'])->name('timbrature.index');

    Route::post('/timbrature/entrata', [TimbratureController::class, 'entrata'])->name('timbrature.entrata');
    Route::post('/timbrature/uscita', [TimbratureController::class, 'uscita'])->name('timbrature.uscita');

});
