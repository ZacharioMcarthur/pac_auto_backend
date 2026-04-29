<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CritereNotation extends Model
{
    use HasFactory;
    protected $fillable = [
        'libelle',
    ];
    public function demandeVehicule(){
        return $this->belongsToMany(DemandeVehicule::class, 'notes', 'critere_notation_id', 'demande_vehicule_id');
    }
}
