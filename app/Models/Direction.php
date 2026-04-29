<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direction extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'libelle',
    ];

    public function user(){
        return $this->hasMany(User::class, 'direction_id', 'id');
    }
}
