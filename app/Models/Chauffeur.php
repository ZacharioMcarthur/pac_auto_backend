<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chauffeur extends Model
{
    use HasFactory;

    // Constante pour les valeurs possibles de la colonne 'disponibilite' en tenant compte de la bd
    public const DISPONIBILITES = [
        'DISPONIBLE',
        'INDISPONIBLE',
        'REPOS',
        'COURSE',
    ];

    protected $fillable = [
        'matricule',
        'num_permis',
        'adresse',
        'annee_permis',
        'contact',
        'email',
        'statut',
        'disponibilite',
        'categorie_permis_id',
        'user_id',
        'created_by',
        'updated_by',
    ];

    // Relations

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function permis() {
        return $this->belongsTo(CategoriePermis::class, 'categorie_permis_id', 'id');
    }

    public function planningGardes() {
        return $this->belongsToMany(PlanningGarde::class, 'programmation', 'chauffeur_id', 'planning_garde_id');
    }

    public function affectations() {
        return $this->hasMany(AffectationDemande::class, 'chauffeur_id', 'id');
    }

    public function last_affectation() {  // Récupère la dernière affectation liée au chauffeur
        return $this->hasOne(AffectationDemande::class, 'chauffeur_id')->latestOfMany();
    }

    public function occupations() {
        return $this->hasMany(Occupation::class, 'chauffeur_id', 'id');
    }
}
