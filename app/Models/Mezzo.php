<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mezzo extends Model
{
    protected $table = 'mezzi';

    protected $fillable = [
        'dipendente_id', 'tipo', 'targa', 'modello', 
        'anno', 'stato', 'prossima_manutenzione'
    ];
    public function dipendente() {
        return $this->belongsTo(Dipendente::class);
    }
}