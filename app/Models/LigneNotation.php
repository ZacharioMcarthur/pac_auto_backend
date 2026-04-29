<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LigneNotation extends Model
{
    use HasFactory;
    protected $fillable = [
        'notation_id',
        'critere_notation_id',
        'chauffeur_id',
        'valeur',
    ];

    public function chauffeur(){
        return $this->belongsTo(Chauffeur::class, 'chauffeur_id', 'id');
    }

    public function critere(){
        return $this->belongsTo(CritereNotation::class, 'critere_notation_id', 'id');
    }
}
