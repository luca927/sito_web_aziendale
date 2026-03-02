<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cantiere extends Model
{
    protected $table = 'cantieri';
    
    protected $fillable = [
        'nome', 'referente', 'indirizzo', 'giorni', 'latitudine', 
        'longitudine', 'data_inizio', 'data_fine', 'stato'
    ];

    public function dipendenti() {
        return $this->belongsToMany(Dipendente::class)
                    ->withPivot('data_assegnazione')
                    ->withTimestamps();
    }

    public function tracciamenti() {
        return $this->hasMany(Tracciamento::class);
    }

    public function timbrature() {
        return $this->hasMany(Timbratura::class);
    }
}