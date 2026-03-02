<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timbratura extends Model
{
    protected $table = 'timbrature';

    protected $fillable = [
        'dipendente_id', 'cantiere_id', 'entrata', 'uscita'
    ];

    public function dipendente() {
        return $this->belongsTo(Dipendente::class);
    }

    public function cantiere() {
        return $this->belongsTo(Cantiere::class);
    }
}