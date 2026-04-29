<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriePermis extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'statut',
    ];

    //  Relation vers les chauffeurs ayant ce permis (des chauffeurs peuvent avoir la meme categorie de permis)
    public function chauffeurs() {
        return $this->hasMany(Chauffeur::class, 'categorie_permis_id', 'id');
    }

    //  Option : si chaque véhicule est lié à un permis (à confirmer)
    public function vehicules(){
        return $this->hasMany(Vehicule::class, 'categorie_permis_id', 'id');
    }
}
