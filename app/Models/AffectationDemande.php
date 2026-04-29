<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffectationDemande extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicule_id',
        'demande_vehicule_id',
        'chauffeur_id',
    ];

    /**
     * Get the chauffeurs that owns the AffectationDemande
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class, 'chauffeur_id', 'id');
    }

    /**
     * Get the vehicules that owns the AffectationDemande
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicule_id', 'id');
    }

    /**
     * Get the demandeCourses that owns the AffectationDemande
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function demandeCourse()
    {
        return $this->belongsTo(DemandeVehicule::class, 'demande_vehicule_id', 'id');
    }
}
