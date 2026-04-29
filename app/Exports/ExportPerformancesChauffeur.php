<?php

namespace App\Exports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Carbon\Carbon;
use App\Models\Chauffeur;
use Illuminate\Support\Facades\DB;

class ExportPerformancesChauffeur extends Controller implements FromArray 
{
    protected $debut;
    protected $fin;
    protected $chauffeur_id;

    public function __construct($debut, $fin, $chauffeur_id)
    {
        $this->debut = $debut;
        $this->fin = $fin;
        $this->chauffeur_id = $chauffeur_id;
    }


    public function array(): array
    {
        $ligne_notations = DB::select("select `libelle`, round(avg(`valeur`),0) as valeur from `ligne_notations`, 
            `critere_notations` where `ligne_notations`.`created_at` >= ? and `ligne_notations`.`created_at` <= ? and `chauffeur_id` = ? and `critere_notations`.`id`=`ligne_notations`.`critere_notation_id` 
            group by `libelle`", [$this->debut, $this->fin, $this->chauffeur_id]);

        $chauffeur = Chauffeur::with('user')->with('permis')->where('id', (int) $this->chauffeur_id)->first();
        
        $journal_array[] = array(
            '' => 'CHAFFEUR : '.$chauffeur->matricule.'  - '.$chauffeur->user->nom .' - '.$chauffeur->user->prenom .'  '.$chauffeur->user->email  .' - '.$chauffeur->user->tel,
        );
        
        $journal_array[] = array(
            'Numéro',
            'Critère de Performance',
            'Note Obtenue', 
        );
            $i=0;
            foreach($ligne_notations as $ligne_notation)
            {   $i++;
                $journal_array[] = array(
                    'Numéro' => $i,
                    'Critère de Performance' => $ligne_notation->libelle,  
                    'Note Obtenue' => $ligne_notation->valeur, 
                );
                
            }         
        
        return $journal_array;
    }
}
