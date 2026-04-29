<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanningGarde extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_debut',
        'date_fin',
        'heure_debut',
        'heure_fin',
        'statut',
        'created_by',
        'updated_by',
    ];
    public function chauffeurs(){
        return $this->belongsToMany(Chauffeur::class, 'programmers', 'planning_garde_id', 'chauffeur_id');
    }

}
