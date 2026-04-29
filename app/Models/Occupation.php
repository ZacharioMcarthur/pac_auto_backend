<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occupation extends Model
{
    use HasFactory;

    protected $table = "occupations";

    protected $fillable = [
        'date_depart',
        'date_retour',
        'vehicule_id',
        'chauffeur_id',
        'demande_vehicule_id'
    ];

    public function vehicule(){
        return $this->belongsTo(Vehicule::class, 'vehicule_id', 'id');
    }

    public function chauffeur(){
        return $this->belongsTo(Chauffeur::class, 'chauffeur_id', 'id');
    }

    public function demandeCourse(){
        return $this->belongsTo(DemandeVehicule::class, 'demande_vehicule_id', 'id');
    }
}
