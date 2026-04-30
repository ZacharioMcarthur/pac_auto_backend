<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Entite;
use App\Models\Role;
use App\Models\CategorieUser;
use App\Models\DemandeVehicule;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $appends = ['name'];

    protected $fillable = [
        'nom', 'prenom', 'email', 'tel', 'statut', 
        'role_id', 'categorie_user_id', 'entite_id', 'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['email_verified_at' => 'datetime'];

    public function entite() { return $this->belongsTo(Entite::class, 'entite_id'); }
    public function role() { return $this->belongsTo(Role::class, 'role_id'); }
    public function categorieUser() { return $this->belongsTo(CategorieUser::class, 'categorie_user_id'); }
    public function demandeVehicules() { return $this->hasMany(DemandeVehicule::class, 'user_id'); }

    public function getNameAttribute()
    {
        return trim("{$this->nom} {$this->prenom}");
    }
}