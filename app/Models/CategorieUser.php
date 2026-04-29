<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'statut',
    ];

    public function user(){
        return $this->hasMany(User::class, 'categorie_user_id', 'id');
    }
}
