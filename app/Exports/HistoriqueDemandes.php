<?php

namespace App\Exports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromArray;
use Carbon\Carbon;
use App\Models\DemandeVehicule;

class HistoriqueDemandes extends Controller implements FromArray 
{
    protected $debut;
    protected $fin;
    protected $point_destination;
    protected $vehicule_id;
    protected $chauffeur_id;

    public function __construct($debut, $fin, $point_destination, $vehicule_id, $chauffeur_id)
    {
        $this->debut = $debut;
        $this->fin = $fin;
        $this->point_destination = $point_destination;
        $this->vehicule_id = $vehicule_id;
        $this->chauffeur_id = $chauffeur_id;
    }


    public function array(): array
    {
        //Parmas date_debut and date_fin
        if(!$this->vehicule_id && !$this->chauffeur_id && !$this->point_destination){
            $debut = $this->debut;
            $fin = $this->fin;
            $debut = Carbon::parse($debut)->startOfDay();
            $fin = Carbon::parse($fin)->endOfDay();
            $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                $query->with('direction');
            }, 'chauffeur' => function ($query) {
                $query->with('user');
            }])->whereBetween('date', [$debut, $fin])->get();
            
        }

        //Parmas date_debut, date_fin and véhicule
        if($this->vehicule_id && !$this->chauffeur_id && !$this->point_destination){
            $debut = $this->debut;
            $fin = $this->fin;
            $debut = Carbon::parse($debut)->startOfDay();
            $fin = Carbon::parse($fin)->endOfDay();
                            
            $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                $query->with('direction');
            }, 'chauffeur' => function ($query) {
                $query->with('user');
            }])->where('vehicule_id', $this->vehicule_id)->whereBetween('date', [$debut, $fin])->get();
            
        }

        //Parmas date_debut, date_fin and chauffeur
        if(!$this->vehicule_id && $this->chauffeur_id && !$this->point_destination){
            $debut = $this->debut;
            $fin = $this->fin;
            $debut = Carbon::parse($debut)->startOfDay();
            $fin = Carbon::parse($fin)->endOfDay();
                            
            $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                $query->with('direction');
            }, 'chauffeur' => function ($query) {
                $query->with('user');
            }])->where('chauffeur_id', $this->chauffeur_id)->whereBetween('date', [$debut, $fin])->get();

        }

        //Parmas date_debut, date_fin and destination
        if(!$this->vehicule_id && !$this->chauffeur_id && $this->point_destination){
            $debut = $this->debut;
            $fin = $this->fin;
            $debut = Carbon::parse($debut)->startOfDay();
            $fin = Carbon::parse($fin)->endOfDay();
            
            $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                $query->with('direction');
            }, 'chauffeur' => function ($query) {
                $query->with('user');
            }])->where('point_destination', 'like', '%'.$this->point_destination.'%')->whereBetween('date', [$debut, $fin])->get();

        }

        //All Parmas is set
        if($this->vehicule_id && $this->chauffeur_id && $this->point_destination){
            $debut = $this->debut;
            $fin = $this->fin;
            $debut = Carbon::parse($debut)->startOfDay();
            $fin = Carbon::parse($fin)->endOfDay();
                            
            $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                $query->with('direction');
            }, 'chauffeur' => function ($query) {
                $query->with('user');
            }])->where('point_destination', 'like', '%'.$this->point_destination.'%')
            ->where('chauffeur_id', $this->chauffeur_id)
            ->where('vehicule_id', $this->vehicule_id)
            ->whereBetween('date', [$debut, $fin])->get();
            
        }

        //Parmas Chauffeur + Vehicule
        if($this->vehicule_id && $this->chauffeur_id && !$this->point_destination){
            $debut = $this->debut;
            $fin = $this->fin;
            $debut = Carbon::parse($debut)->startOfDay();
            $fin = Carbon::parse($fin)->endOfDay();
                            
            $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                $query->with('direction');
            }, 'chauffeur' => function ($query) {
                $query->with('user');
            }])->where('chauffeur_id', $this->chauffeur_id)
            ->where('vehicule_id', $this->vehicule_id)
            ->whereBetween('date', [$debut, $fin])->get();
            
        }

        //Parmas is set Chauffeur + Destination
        if(!$this->vehicule_id && $this->chauffeur_id && $this->point_destination){
            $debut = $this->debut;
            $fin = $this->fin;
            $debut = Carbon::parse($debut)->startOfDay();
            $fin = Carbon::parse($fin)->endOfDay();
                            
            $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                $query->with('direction');
            }, 'chauffeur' => function ($query) {
                $query->with('user');
            }])->where('point_destination', 'like', '%'.$this->point_destination.'%')
            ->where('chauffeur_id', $this->chauffeur_id)
            ->whereBetween('date', [$debut, $fin])->get();
            
        }
        
        //Parmas Vehicule + Destination
        if($this->vehicule_id && !$this->chauffeur_id && $this->point_destination){
            $debut = $this->debut;
            $fin = $this->fin;
            $debut = Carbon::parse($debut)->startOfDay();
            $fin = Carbon::parse($fin)->endOfDay();
                            
            $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                $query->with('direction');
            }, 'chauffeur' => function ($query) {
                $query->with('user');
            }])->where('point_destination', 'like', '%'.$this->point_destination.'%')
            ->where('vehicule_id', $this->vehicule_id)
            ->whereBetween('date', [$debut, $fin])->get();
            
        }

        
        $journal_array[] = array(
            'Référence',
            'Date Demande', 
            'Demandeur', 
            'Direction', 
            'Chauffeur',
            'Vehicule', 
            'Type Véhicule', 
            'Effectif', 
            'Trafic',
            'Escales',
            'Objet',
            'Date Fin',
            'Statut',
        );
        
            foreach($demandes as $demande)
            {
                if($demande->statut == env('STATUT_DEMANDE_COURSE_CREEE')){
                    $journal_array[] = array(
                        'Reference' => $demande->id,
                        'Date Demande' => $demande->date,  
                        'Demandeur' => $demande->user->nom.' '.$demande->user->prenom, 
                        'Direction' => $demande->user->direction->code,
                        'Chauffeur' => 'Non affectée',
                        'Vehicule' => 'Non affectée',
                        'Type Véhicule'=>$demande->typeVehicule->libelle, 
                        'Effectif'=> $demande->nbre_personnes, 
                        'Trafic' => $demande->point_depart.' à '.$demande->point_destination,
                        'Escales' => $demande->escales,
                        'Objet' => $demande->objet, 
                        'Date Fin' => $demande->date_retour. ' '.$demande->heure_retour , 
                        'Statut' => $demande->statut, 
                    );
                } else {
                    $journal_array[] = array(
                        'Reference' => $demande->id,
                        'Date Demande' => $demande->date,  
                        'Demandeur' => $demande->user->nom.' '.$demande->user->prenom, 
                        'Direction' => $demande->user->direction->code,
                        'Chauffeur' => $demande->chauffeur->user->nom.' '.$demande->chauffeur->user->prenom,
                        'Vehicule' =>  $demande->vehicule->immatr, 
                        'Type Véhicule'=>$demande->typeVehicule->libelle, 
                        'Effectif'=> $demande->nbre_personnes, 
                        'Trafic' => $demande->point_depart.' à '.$demande->point_destination,
                        'Escales' => $demande->escales,
                        'Objet' => $demande->objet, 
                        'Date Fin' => $demande->date_retour. ' '.$demande->heure_retour , 
                        'Statut' => $demande->statut, 
                    );
                }
                
            }           
        
        Log::info($journal_array);
        
        return $journal_array;
    }
}
