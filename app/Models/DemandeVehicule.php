<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeVehicule extends Model
{
    use HasFactory;

    protected $appends = [
        'status',
        'user_id_demande',
        'typeVehicule_id',
        'dateDepart',
        'dateArriver',
        'pointDepart',
        'pointDestination',
    ];

    protected $fillable = [
        'reference',
        'objet',
        'point_depart',
        'point_destination',
        'nbre_personnes',
        'statut',
        'escales',
        'user_id',
        'beneficiaire_id',
        'motif_id',
        'is_note',
        'type_vehicule_id',
        'date_depart',
        'date_retour',
        'heure_depart',
        'heure_retour',
        'date_depart_effectif',
        'date_retour_effectif',
        'date',
        'vehicule_id',
        'chauffeur_id'
    ];

    public function critereNotation()
    {
        return $this->belongsToMany(CritereNotation::class, 'notes', 'demande_vehicule_id', 'critere_notation_id');
    }

    public function motif()
    {
        return $this->belongsTo(Motif::class, 'motif_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->with('entite');
    }

    public function beneficiaire()
    {
        return $this->belongsTo(User::class, 'beneficiaire_id', 'id')->with('entite');
    }

    public function typeVehicule()
    {
        return $this->belongsTo(TypeVehicule::class, 'type_vehicule_id', 'id');
    }

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class, 'chauffeur_id', 'id');
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicule_id', 'id');
    }

    public function getStatusAttribute()
    {
        return match ($this->statut) {
            env('STATUT_DEMANDE_COURSE_CREEE') => 'En Attente',
            env('STATUT_DEMANDE_COURSE_AFFECTEE') => 'Valider',
            env('STATUT_DEMANDE_COURSE_DEMARREE') => 'En cours',
            env('STATUT_DEMANDE_COURSE_TERMINEE') => 'Terminer',
            default => $this->statut,
        };
    }

    public function getUserIdDemandeAttribute()
    {
        return $this->beneficiaire_id;
    }

    public function getTypeVehiculeIdAttribute()
    {
        return $this->type_vehicule_id;
    }

    public function getDateDepartAttribute()
    {
        return $this->date_depart;
    }

    public function getDateArriverAttribute()
    {
        return $this->date_retour;
    }

    public function getPointDepartAttribute()
    {
        return $this->point_depart;
    }

    public function getPointDestinationAttribute()
    {
        return $this->point_destination;
    }

    public function affectation()
    {
        return $this->hasOne(AffectationDemande::class, 'demande_vehicule_id')
                    ->with('vehicule', 'chauffeur', 'chauffeur.user')
                    ->latest();
    }

    // 🔹 Chauffeur via la table affectations
    public function chauffeurViaAffectation()
    {
        return $this->hasOneThrough(
            Chauffeur::class,
            AffectationDemande::class,
            'demande_vehicule_id', // Foreign key on affectation_demandes
            'id',                  // Foreign key on chauffeurs
            'id',                  // Local key on demandes_vehicules
            'chauffeur_id'         // Local key on affectation_demandes
        );
    }

    // 🔹 Véhicule via la table affectations
    public function vehiculeViaAffectation()
    {
        return $this->hasOneThrough(
            Vehicule::class,
            AffectationDemande::class,
            'demande_vehicule_id',
            'id',
            'id',
            'vehicule_id'
        );
    }
}
