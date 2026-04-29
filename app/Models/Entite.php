<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entite extends Model
{
    use HasFactory;

    /**
     * Les colonnes autorisées à l’insertion en masse
     */
    protected $fillable = [
        'nom',
        'code',
        'type',
        'parent_id',
    ];

    /**
     * Une entité peut avoir plusieurs utilisateurs
     */
    public function utilisateurs()
    {
        return $this->hasMany(User::class, 'entite_id');
    }

    /**
     * Entité parente : ne se charge que si l'entité a un parent différent d'elle-même
     */
    public function parent()
    {
        return $this->belongsTo(Entite::class, 'parent_id')->whereColumn('id', '!=', 'parent_id');
    }

    /**
     * Entités enfants : toutes les entités dépendantes
     */
    public function enfants()
    {
        return $this->hasMany(Entite::class, 'parent_id');
    }
}
