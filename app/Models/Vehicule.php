<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    use HasFactory;

    protected $table = "vehicules";

    protected $fillable = [
        'immatr',
        'marque',
        'date_mise_circulation',
        'statut',
        'nomMembre',
        'disponibilite',
        'capacite',
        'type_vehicule_id',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at'
    ];

    public function type(){
        return $this->belongsTo(TypeVehicule::class, 'type_vehicule_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function categoriePermis(){
        return $this->belongsToMany(CategoriePermis::class, 'conduite_demandes', 'vehicule_id', 'categorie_permis_id');
    }

    public function conduire(){
        return $this->belongsToMany(CategoriePermis::class, 'conduires', 'vehicule_id', 'categorie_permis_id');
    }

    public function affectations() {
        return $this->hasMany(AffectationDemande::class, "vehicule_id", "id");
    }

    public function last_affectation() {
        return $this->hasOne(AffectationDemande::class, "vehicule_id", "id")->latest();
    }

    public function occupations() {
        return $this->hasMany(Occupation::class, "vehicule_id", "id");
    }
}
