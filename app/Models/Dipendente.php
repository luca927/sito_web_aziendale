<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dipendente extends Model
{
    protected $table = 'dipendenti';
    
    protected $fillable = [
    'user_id', 'nome', 'cognome', 'codice_fiscale', 
    'indirizzo', 'telefono', 'mansione', 'foto', 'data_assunzione'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function cantieri() {
        return $this->belongsToMany(Cantiere::class)
                    ->withPivot('data_assegnazione')
                    ->withTimestamps();
    }

    public function mezzi() {
        return $this->hasMany(Mezzo::class);
    }

    public function tracciamenti() {
        return $this->hasMany(Tracciamento::class);
    }

    public function timbrature() {
        return $this->hasMany(Timbratura::class);
    }
}