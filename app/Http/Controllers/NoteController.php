<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotationRequest;
use Illuminate\Http\Request;
use App\Models\LigneNotation;
use App\Models\DemandeVehicule;
use App\Models\Notation;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class NoteController extends Controller
{
    public function saveNewNotes(NotationRequest $request){
        try{
            $data=$request->input("data");

        $demande_id=$data['demandeId'];
        $verifier=DemandeVehicule::where('id',$demande_id)->where('statut',env('STATUT_DEMANDE_COURSE_TERMINEE'))->first();
        if(!$verifier){
            return response()->json([
                'data'=>'',
                'message'=>'Demande véhicule non terminée',
                'status'=>400
            ],400 );
        }
        $commentaire=$data['commentaire'];
        $date=Carbon::now();
        $user_id=$data['user_id'];
        $demandeNote = Notation::get()->where('demande_vehicule_id',$demande_id)->first();
        $demande = DemandeVehicule::where('id', $demande_id)->first();
        if($demandeNote!=null){
            return response()->json([
                'data'=>'',
                'message'=>'notation déjà existante',
                'status'=>401
            ],401);
        };

        $table_notes=$data['notes'];


        $ret=Notation::create([
            'demande_vehicule_id'=>$demande_id,
            'commentaire'=>$commentaire,
            'date_de_notation'=>$date,
            'user_id'=>$user_id,
        ]);

        foreach($table_notes as $note){
            LigneNotation::create([
                'notation_id' =>$ret['id'],
                'critere_notation_id'=>$note['id'],
                'valeur'=>$note['stars']['value'],
                'chauffeur_id'=>$demande->id,
            ]);
        }
        $verifier->is_note=true;
        $verifier->save();
        return response()->json([
            'data' => '',
            'message' => 'note créée avec succès',
            'status' => 200
        ],200);

        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est suvenue'
            ], 500);
        }

    }
    public function getNoteByIdDemande($demande_id){
        try{
            $notation=Notation::get()->where('demande_vehicule_id',$demande_id)->first();
            $ligneNotations = LigneNotation::where('notation_id',$notation->id)->get();
            return response()->json([
                'data'=>[
                    $notation,$ligneNotations
                ],
                'message'=>'note récupérer avec succès',
                'status'=>200
            ],200);
        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est suvenue'
            ], 500);
        }
    }
}
