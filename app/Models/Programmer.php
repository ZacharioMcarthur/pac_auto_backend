<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programmer extends Model
{

    protected $table = "programmation";

    use HasFactory;
    protected $fillable = [
        'chauffeur_id',
        'planning_garde_id',
        'date_fin_repos',
    ];

    public function planning(){
        return $this->belongsTo(PlanningGarde::class, 'planning_garde_id', 'id');
    }

    public function chauffeur(){
        return $this->belongsTo(Chauffeur::class, 'chauffeur_id', 'id');
    }
}
