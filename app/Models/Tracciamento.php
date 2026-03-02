<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracciamento extends Model
{
    protected $table = 'tracciamenti';

    protected $fillable = [
        'dipendente_id', 'cantiere_id', 'tipo_attivita', 'mezzo_id', 'data_ora', 'note'
    ];

    public function dipendente() {
        return $this->belongsTo(Dipendente::class);
    }

    public function cantiere() {
        return $this->belongsTo(Cantiere::class);
    }

    public function mezzo() {
        return $this->belongsTo(Mezzo::class);
    }
}