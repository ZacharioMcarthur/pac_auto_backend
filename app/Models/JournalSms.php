<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalSms extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact',
        'contenu',
        'status_envoi',
        'date_envoi',
        'user_id',
        
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id')->with('direction');
    }

}
