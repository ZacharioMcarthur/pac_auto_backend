<?php

namespace App\Http\Controllers;
use App\Models\Direction;
use App\Models\DemandeVehicule;
use App\Models\CritereNotation;
use App\Models\AffectationDemande;
use App\Models\LigneNotation;
use Exception;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel as FacadesExcel;

use Carbon\Carbon;

use App\Exports\HistoriqueDemandes;
use App\Exports\ExportPerformancesChauffeur;

use Illuminate\Http\Request;

class HistoriqueController extends Controller
{
    public $chauffeur_id;
    public $vehicule_id;
    public $export_demandes;
    //Liste des Directions
    public function getDirections(){
        try{
            $direction = Direction::get();

            return response()->json([
                'data' => $direction,
                'message' => '',
                'status' => 200
            ],200);
        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ]);
        }
    }

    //Historique des Performances Chauffeurs
    public function getHistoriquesChaufeurs(Request $request ){
        try{
            $input = $request->input('body');
            $chauffeur_id = $input['chauffeur_id'];
            $debut = $input['date_debut'];
            $fin = $input['date_fin'];
            $debut = Carbon::parse($debut)->startOfDay();
            $fin = Carbon::parse($fin)->endOfDay();

            $ligne_notations = DB::select("select `libelle`, round(avg(`valeur`),0) as valeur from `ligne_notations`,
            `critere_notations` where `ligne_notations`.`created_at` >= ? and `ligne_notations`.`created_at` <= ? and `chauffeur_id` = ? and `critere_notations`.`id`=`ligne_notations`.`critere_notation_id`
            group by `libelle`", [$debut, $fin, $chauffeur_id]);

            return response()->json([
                'data' => $ligne_notations,
                'message' => '',
                'status' => 200
            ],200);
        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ]);
        }
    }

    /**
     * Performance Chauffeur Export xls
     */
    public function exportPerformancesChauffeur(Request $request)
    {
        if ($request->isMethod('POST')) {
            $input = $request->input();
            $chauffeur_id = $input['chauffeur_id'];
            $debut = $input['date_debut'];
            $fin = $input['date_fin'];
            $debut = Carbon::parse($debut)->startOfDay();
            $fin = Carbon::parse($fin)->endOfDay();
            try {
                $file_name = 'PerformancesChauffeur'.$chauffeur_id.'.xls';
                ob_end_clean();
                ob_start();
            return FacadesExcel::download(new ExportPerformancesChauffeur($debut, $fin, $chauffeur_id), $file_name);
            } catch (\Exception $ex) {
                return response()->json([
                    'error' => "error",
                    'message' => "Une erreur interne est survenue.",
                    'status' => 500
                ]);
            }
        } else {
            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ]);
        }
    }

    //Historique Demande
    public function getHistoriquesDemandes(Request $request){
        try{

            $input = $request->input('body');
            $demandes = [];
            $demandesNouvelles = 0;
            $demandesEncours = 0;
            $demandesTerminees = 0;

            //Parmas date_debut and date_fin
            if(!$input['vehiculeID'] && !$input['chauffeurID'] && !$input['point_destination']){
                $debut = $input['date_debut'];
                $fin = $input['date_fin'];
                $debut = Carbon::parse($debut)->startOfDay();
                $fin = Carbon::parse($fin)->endOfDay();

                $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                    $query->with('direction');
                }, 'chauffeur' => function ($query) {
                    $query->with('user');
                }])->whereBetween('date', [$debut, $fin])->get();

                $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
                $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();
            }

            //Parmas date_debut, date_fin and véhicule
            if($input['vehiculeID'] && !$input['chauffeurID'] && !$input['point_destination']){
                $debut = $input['date_debut'];
                $fin = $input['date_fin'];
                $debut = Carbon::parse($debut)->startOfDay();
                $fin = Carbon::parse($fin)->endOfDay();

                $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                    $query->with('direction');
                }, 'chauffeur' => function ($query) {
                    $query->with('user');
                }])->where('vehicule_id', $input['vehiculeID'])->whereBetween('date', [$debut, $fin])->get();

                $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
                $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();
            }

            //Parmas date_debut, date_fin and chauffeur
            if(!$input['vehiculeID'] && $input['chauffeurID'] && !$input['point_destination']){
                $debut = $input['date_debut'];
                $fin = $input['date_fin'];
                $debut = Carbon::parse($debut)->startOfDay();
                $fin = Carbon::parse($fin)->endOfDay();

                $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                    $query->with('direction');
                }, 'chauffeur' => function ($query) {
                    $query->with('user');
                }])->where('chauffeur_id', $input['chauffeurID'])->whereBetween('date', [$debut, $fin])->get();

                $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
                $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();
            }

            //Parmas date_debut, date_fin and destination
            if(!$input['vehiculeID'] && !$input['chauffeurID'] && $input['point_destination']){
                $debut = $input['date_debut'];
                $fin = $input['date_fin'];
                $debut = Carbon::parse($debut)->startOfDay();
                $fin = Carbon::parse($fin)->endOfDay();

                $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                    $query->with('direction');
                }, 'chauffeur' => function ($query) {
                    $query->with('user');
                }])->where('point_destination', 'like', '%'.$input['point_destination'].'%')->whereBetween('date', [$debut, $fin])->get();

                $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
                $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();
            }

            //All Parmas is set
            if($input['vehiculeID'] && $input['chauffeurID'] && $input['point_destination']){
                $debut = $input['date_debut'];
                $fin = $input['date_fin'];
                $debut = Carbon::parse($debut)->startOfDay();
                $fin = Carbon::parse($fin)->endOfDay();

                $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                    $query->with('direction');
                }, 'chauffeur' => function ($query) {
                    $query->with('user');
                }])->where('point_destination', 'like', '%'.$input['point_destination'].'%')
                ->where('chauffeur_id', $input['chauffeurID'])
                ->where('vehicule_id', $input['vehiculeID'])
                ->whereBetween('date', [$debut, $fin])->get();

                $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
                $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();
            }

            //Parmas Chauffeur + Vehicule
            if($input['vehiculeID'] && $input['chauffeurID'] && !$input['point_destination']){
                $debut = $input['date_debut'];
                $fin = $input['date_fin'];
                $debut = Carbon::parse($debut)->startOfDay();
                $fin = Carbon::parse($fin)->endOfDay();

                $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                    $query->with('direction');
                }, 'chauffeur' => function ($query) {
                    $query->with('user');
                }])->where('chauffeur_id', $input['chauffeurID'])
                ->where('vehicule_id', $input['vehiculeID'])
                ->whereBetween('date', [$debut, $fin])->get();

                $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
                $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();
            }

            //Parmas is set Chauffeur + Destination
            if(!$input['vehiculeID'] && $input['chauffeurID'] && $input['point_destination']){
                $debut = $input['date_debut'];
                $fin = $input['date_fin'];
                $debut = Carbon::parse($debut)->startOfDay();
                $fin = Carbon::parse($fin)->endOfDay();

                $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                    $query->with('direction');
                }, 'chauffeur' => function ($query) {
                    $query->with('user');
                }])->where('point_destination', 'like', '%'.$input['point_destination'].'%')
                ->where('chauffeur_id', $input['chauffeurID'])
                ->whereBetween('date', [$debut, $fin])->get();

                $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
                $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();
            }

            //Parmas Vehicule + Destination
            if($input['vehiculeID'] && !$input['chauffeurID'] && $input['point_destination']){
                $debut = $input['date_debut'];
                $fin = $input['date_fin'];
                $debut = Carbon::parse($debut)->startOfDay();
                $fin = Carbon::parse($fin)->endOfDay();

                $demandes = DemandeVehicule::with(['vehicule', 'typeVehicule', 'motif', 'user' => function ($query) {
                    $query->with('direction');
                }, 'chauffeur' => function ($query) {
                    $query->with('user');
                }])->where('point_destination', 'like', '%'.$input['point_destination'].'%')
                ->where('vehicule_id', $input['vehiculeID'])
                ->whereBetween('date', [$debut, $fin])->get();

                $demandesNouvelles = $demandes->where('statut', 'CREEE')->count();
                $demandesEncours = $demandes->whereIn('statut', ['AFFECTEE', 'DEMARREE'])->count();
                $demandesTerminees = $demandes->where('statut', 'TERMINEE')->count();
            }

            $this->export_demandes = $demandes;

            return response()->json([
                'data' => $demandes,
                'demandesNouvelles' => $demandesNouvelles,
                'demandesEncours' => $demandesEncours,
                'demandesTerminees' => $demandesTerminees,
                'message' => '',
                'status' => 200
            ],200);
        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ]);
        }
    }

    /**
     * Journal Caisse Export xls
     */
    public function exportHistoriqueDemandesCourses(Request $request)
    {
        if ($request->isMethod('POST')) {
            $input = $request->input();
            $vehicule_id = $input['vehiculeID'];
            $chauffeur_id = $input['chauffeurID'];
            $point_destination = $input['point_destination'];
            $debut = $input['date_debut'];
            $fin = $input['date_fin'];
            $debut = Carbon::parse($debut)->startOfDay();
            $fin = Carbon::parse($fin)->endOfDay();
            try {
                $file_name = 'Demande_Courses'.$fin.'.xls';
                ob_end_clean();
                ob_start();
            return FacadesExcel::download(new HistoriqueDemandes($debut, $fin, $point_destination, $vehicule_id, $chauffeur_id), $file_name);
            } catch (\Exception $ex) {
                return response()->json([
                    'error' => "error",
                    'message' => "Une erreur interne est survenue.",
                    'status' => 500
                ]);
            }
        } else {
            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ]);        }
    }

}
