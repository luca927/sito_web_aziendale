<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DipendenteController;
use App\Http\Controllers\CantiereController;
use App\Http\Controllers\MezzoController;
use App\Http\Controllers\TracciamentoController;
use App\Http\Controllers\TimbratureController;

// Route pubblica — login/register (gestite da Breeze)
require __DIR__.'/auth.php';

// Route protette — utente autenticato
Route::middleware(['auth'])->group(function () {

    // Dashboard — accessibile da entrambi i ruoli
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Route solo ADMIN
    Route::middleware(['admin'])->group(function () {
        Route::resource('dipendenti', DipendenteController::class);
        Route::resource('cantieri', CantiereController::class);
        Route::resource('mezzi', MezzoController::class);
    });

    // Route accessibili da entrambi i ruoli
    Route::resource('tracciamento', TracciamentoController::class);
    Route::get('/timbrature', [TimbratureController::class, 'index'])->name('timbrature.index');

    Route::post('/timbrature/entrata', [TimbratureController::class, 'entrata'])->name('timbrature.entrata');
    Route::post('/timbrature/uscita', [TimbratureController::class, 'uscita'])->name('timbrature.uscita');

});
