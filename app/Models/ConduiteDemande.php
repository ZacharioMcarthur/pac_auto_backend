<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConduiteDemande extends Model
{
    use HasFactory;
    protected $fillable = [
        'categorie_permis_id',
        'vehicule_id',
    ];
}
