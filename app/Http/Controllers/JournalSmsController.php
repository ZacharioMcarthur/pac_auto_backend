<?php

namespace App\Http\Controllers;
use App\Http\Requests\DemandeVehiculeRequest;
use App\Services\DateService;
use App\Services\DemandeCourseService;
use App\Services\OccupationService;
use App\Mail\NewDemandeMail;
use App\Mail\DemandeNonNoteMail;
use App\Mail\NotificationChauffeurMail;
use App\Mail\NewDemandeMailAdmin;
use Exception;
use Carbon\Carbon;
use App\Models\Vehicule;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Models\Chauffeur;
use App\Models\JournalSms;

use Illuminate\Http\Request;
use App\Models\DemandeVehicule;

use App\Models\AffectationDemande;
use App\Models\TypeVehicule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isEmpty;

class JournalSmsController extends Controller
{
    //

    public function getAllSMS(){
        try{

            $startOfMonth = Carbon::now()->startOfMonth()->startOfDay();
            $actualDate = Carbon::today()->endOfDay();

            $smsList = [];

            
                $smsList = JournalSms::with('user')
                ->whereBetween('created_at',[$startOfMonth,$actualDate])
                ->orderBy('created_at', 'DESC')
                ->get();
            

            return response()->json([
                'data'=> $smsList,
                'debut' => Carbon::parse($startOfMonth)->format('d/m/Y'),
                'fin' => Carbon::parse($actualDate)->format('d/m/Y'),
                'success'=>'succes',
                'message'=>'',
                'status'=>200
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

    public function searchSMS(Request $request){
        try{

            $debut=$request->input('debut');
            $fin=$request->input('fin');
            $user_id = $request->input('user_id');

            $debut = Carbon::parse($debut)->startOfDay();
            $fin = Carbon::parse($fin)->endOfDay();

            $smsList = [];

            
                $smsList = JournalSms::with('user')
                ->whereBetween('created_at',[$debut,$fin])
                ->orderBy('created_at', 'DESC')
                ->get();
            

            return response()->json([
                'data'=> $smsList,
                'debut' => Carbon::parse($debut)->format('d/m/Y'),
                'fin' => Carbon::parse($fin)->format('d/m/Y'),
                'success'=>'succes',
                'message'=>'',
                'status'=>200
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
}
