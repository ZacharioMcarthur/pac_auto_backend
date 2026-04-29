<?php

namespace App\Http\Controllers;
use App\Models\PlanningGarde;
use App\Models\Programmer;
use Carbon\Carbon;
use DateTime;
use PDF;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PlanningGardeController extends Controller
{
    //
    public function getPlanningGardes(){

        try{
            $data = PlanningGarde::with(['chauffeurs' => function($query){
                $query->with('user');
            }])->where('statut', true)->get();
            return response()->json([
                'data' => $data,
                'success' => 'success',
                'status' => 200
            ]);

        }catch(Exception $ex){
            Log::error($ex->getMessage());
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue',
                'status' => 500
            ]);
        }
    }

    public function savePlanningGardes(Request $request){
        try{
            $input = $request->input('body');

            if(!isset($input['id'])){//Création
                try{
                    $planning = PlanningGarde::create($input);
                    //Définir la date de fin de répos
                    $date_fin_garde = $planning->date_fin;
                    $date_fin_repos = date('Y-m-d', strtotime($date_fin_garde . ' +1 day'));
                    if ($planning) {
                        $chauffeurs = $input['chauffeurs'];
                        
                        foreach ($chauffeurs as $key => $chauffeur) {
                            $programmer = new Programmer();
                            $programmer->chauffeur_id = $chauffeur['id'];
                            $programmer->planning_garde_id = $planning->id;
                            $programmer->date_fin_repos = $date_fin_repos;
                            $programmer->save();

                        }
                    }
                    return response()->json([
                        'success' => 'success',
                        'message' => 'Le planning a été créé avec succès.',
                        'status' => 200
                    ]);
                }catch(Exception $ex){
                    return response()->json([
                        'success' => 'error',
                        'message' => 'Une erreur est survenue, veuillez contacter l\'administrateur.',
                        'status' => 500
                    ]);
                }
            } else{
                try{
                    $planning = PlanningGarde::where('id', $input['id'])->first();
                    
                    //Définir la date de fin de répos
                    $date_fin_garde = $planning->date_fin;
                    $date_fin_repos = date('Y-m-d', strtotime($date_fin_garde . ' +1 day'));
                    if($planning->update(array('date_debut' => $input['date_debut'], 'date_fin' => $input['date_fin'], 'heure_debut' => $input['heure_debut'], 'heure_fin' => $input['heure_fin']))){                        
                        Programmer::where('planning_garde_id', $input['id'])->delete();
                        $chauffeurs = $input['chauffeurs'];
                        
                        foreach ($chauffeurs as $key => $chauffeur) {
                            $programmer = new Programmer();
                            $programmer->chauffeur_id = $chauffeur['id'];
                            $programmer->planning_garde_id = $input['id'];
                            $programmer->date_fin_repos = $date_fin_repos;
                            $programmer->save();

                        }
                    }
                    return response()->json([
                    'error' => 'success',
                    'message' => 'Le planning a été mise à jour avec succès.',
                    'status' => 200
                    ]);
                } catch(Exception $ex){
                    return response()->json([
                        'success' => 'error',
                        'message' => 'Une erreur est survenue, veuillez contacter l\'administrateur.',
                        'status' => 500
                    ]);
                }                
            }
        } catch(Exception $ex){
            return response()->json([
                'success' => 'error',
                'message' => 'Une erreur est survenue, veuillez contacter l\'administrateur.',
                'status' => 500
            ]);
        } 
    }

    public function deletePlanningGardes($planningId){
        try{
            
            PlanningGarde::where('id', $planningId)->delete();
            Programmer::where('planning_garde_id', $planningId)->delete();
            
            return response()->json([
                'success' => 'success',
                'message' => 'Le Planning a été supprimé avec succès.',
                'status' => 200
            ]);
        }catch(Exception $ex){
            Log::error($ex->getMessage());
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue',
                'status' => 500
            ]);
        }
    }
    
    public function getPlanningGardesById($id){
        try{
            $data = PlanningGarde::with(['chauffeurs' => function($query){
                $query->with(['user', 'permis']);
            }])->where('id', $id)->first();
            return response()->json([
                'data' => $data,
                'success' => 'success',
                'status' => 200
            ]);

        }catch(Exception $ex){
            Log::error($ex->getMessage());
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue',
                'status' => 500
            ]);
        }
    }

    //Planning Download PDF
    public function downloadPlanning(Request $request)
    {
        try {
            $planning_garde_id = $request->input('planning_garde_id');
            $planning_garde = PlanningGarde::where('id',$planning_garde_id)->first();
            $plannings = Programmer::with(['planning', 'chauffeur' =>function($query){
                $query->with('user');
            }])->where('planning_garde_id', $planning_garde_id)->get();
            
            $viewData = ['planning_garde' => $planning_garde, 'plannings' => $plannings, 'planning_garde_id' => $planning_garde_id]; 
            
            //Generate pdf
            $file_name = 'Planning_Garde'.$planning_garde_id.'.pdf';
            $filePath = 'public/plannings/'.$file_name;

            $pdf = PDF::loadView('plannings.planning-garde', $viewData);
            Storage::put($filePath, $pdf->output());

            $this->response = response()->json(['statut' => 1, 'message' => 'Aperçu généré avec succès.', 'data' => ["filename" => $file_name, "planning_garde_id" => $planning_garde_id]]);
            return $this->response;	

        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue',
                'status' => 500
            ]);
        }
    }

}
