<?php

namespace App\Http\Controllers;

use App\Http\Requests\DemandeVehiculeRequest;
use App\Services\DateService;
use App\Services\MoovApiService;
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

class DemandeCourseController extends Controller
{
    //
    public function saveDemande(DemandeVehiculeRequest $request){
        try{
            $user_id = $request->input('user_id');
            $beneficiaire_id = $request->input('beneficiaire_id');
            $point_depart = $request->input('point_depart');
            $point_destination = $request->input('point_destination');
            $type_vehicule = $request->input('type_vehicule');
            $motif = $request->input('motif');
            $nbre_personnes = $request->input('nbre_personnes');
            $escales = $request->input('escales');
            $objet = $request->input('objet');
            $date_depart = $request->input('date_depart');
            $date_retour = $request->input('date_retour');
            $heure_depart = $request->input('heure_depart');
            $heure_retour = $request->input('heure_retour');
            $date = Carbon::now();
            $demande = DemandeVehicule::create([
                'point_depart' => $point_depart,
                'point_destination' => $point_destination,
                'type_vehicule_id' => $type_vehicule,
                'motif_id' => $motif,
                'nbre_personnes' => $nbre_personnes,
                'escales' => $escales,
                'user_id' => $user_id,
                'beneficiaire_id' => $beneficiaire_id,
                'objet' => $objet,
                'statut' => env('STATUT_DEMANDE_COURSE_CREEE'),
                'date_depart' => DateService::addTimeToDate($date_depart, $heure_depart),
                'date_retour' => DateService::addTimeToDate($date_retour, $heure_retour),
                'heure_depart' => $heure_depart,
                'heure_retour' => $heure_retour,
                'date'=>$date,
            ])->id;

            if($demande){
                if(isset($user_id)){
                    $user = User::where('id',$user_id)->first();
                    $demande_new = DemandeVehicule::where('id',$demande)->with('beneficiaire')->first();
                    if($user !=null){

                        // envoi de mail à l'utilisateur qui a crée la demande
                        $mailable = new NewDemandeMail($user->nom,$user->prenom, $user->email);
                        Mail::to($user->email)->send($mailable);



                        $admin = User::whereHas('role',function($query){
                            $query->where('libelle',env('ROLE_ADMIN'));
                        });

                        $emails_admin = $admin->pluck('email')->toArray();

                        //envoi de mail à tous les administrateurs
                        Mail::send('emails.new_demande_admin', [
                                    'nom' => $user->nom,
                                    'prenom' => $user->prenom,
                                ], function($message) use ($emails_admin)
                        {
                            $message->from(env('CONTACT_EMAIL') ,env('MAIL_FROM_NAME'));
                            $message->to($emails_admin)->subject(env('APP_NAME') . " [ Demande de course créée] ");
                        });

                        $moovService = new MoovApiService();
                        $message=''.env('MOOV_MESSAGE_HEADER')." ".$demande_new->beneficiaire->nom." ".$demande_new->beneficiaire->prenom." ".env('MOOV_MESSAGE_DEMANDE_COURSE').env('APP_NAME').'';
                        $jsonResponse = $moovService->sendSms($demande_new->beneficiaire->tel,$message,$demande_new->beneficiaire_id);
                        $journalSms = new JournalSms();
                        $journalSms->contact = $demande_new->beneficiaire->tel;
                        $journalSms->contenu = $message;
                        $journalSms->status_envoi = $jsonResponse;
                        $journalSms->date_envoi = Carbon::now();
                        $journalSms->user_id = $demande_new->beneficiaire_id;
                        $journalSms->save();
                        //JournalSms::create($jsonResponse->toArray());

                        foreach($admin->get() as $administrateur){
                            $message=''.env('MOOV_MESSAGE_HEADER')." ".$administrateur->nom." ".$administrateur->prenom." ".env('MOOV_MESSAGE_DEMANDE_COURSE_ADMIN_DEBUT')."Monsieur ".$demande_new->beneficiaire->nom." ".$demande_new->beneficiaire->prenom.env('MOOV_MESSAGE_DEMANDE_COURSE_ADMIN_FIN').env('APP_NAME').'';
                            $jsonResponse = $moovService->sendSms($administrateur->tel,$message,$administrateur->id);

                            $journalSms = new JournalSms();
                            $journalSms->contact = $administrateur->tel;
                            $journalSms->contenu = $message;
                            $journalSms->status_envoi = $jsonResponse;
                            $journalSms->date_envoi = Carbon::now();
                            $journalSms->user_id = $administrateur->id;
                            $journalSms->save();

                            //JournalSms::create($jsonResponse->toArray());
                        }


                    }
                }
            }
            return response()->json([
                'data' => '',
                'message' => 'Demande de courses créée avec succès',
                'status' => 200
            ],200);

        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ], 500);
        }

    }

    public function verifierNotation($user_id){
        try{
            $demande = DemandeVehicule::where('beneficiaire_id',$user_id)
                ->where('statut',env('STATUT_DEMANDE_COURSE_TERMINEE'))
                ->where('is_note',0)
                ->first('id');
            return response()->json([
                'data' => $demande,
                'message' => '',
                'status' => 200
            ],200);
        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ], 500);
        }
    }

    public function editDemande(Request $request){
        try{
            DemandeVehicule::where('id', $request->input('demande_id'))
                    ->update([
                        'point_depart' => $request->input('point_depart'),
                        'point_destination' => $request->input('point_destination'),
                        'nbre_personnes' => $request->input('nbre_personnes'),
                        'objet' => $request->input('objet'),
                        'type_vehicule_id' => $request->input('type_vehicule'),
                        'motif_id' => $request->input('motif'),
                        'beneficiaire_id' => $request->input('beneficiaire_id'),
                        'escales' => $request->input('escales'),
                        'date_depart' => DateService::addTimeToDate($request->input('date_depart'), $request->input('heure_depart')),
                        'heure_depart' => $request->input('heure_depart'),
                    ]);

            return response()->json([
                'success' => "success",
                'status' => 200
            ], 200);

        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ], 500);
        }
    }

    public function getDemandeCourseEnCour($user_id,$role){
        try{

            $data = [];
            if($role == env('ROLE_ADMIN')){
                $data = DemandeVehicule::with('typeVehicule', 'motif', 'affectation','user','beneficiaire')
                ->where('is_note','=',false)
                ->orderBy('created_at', 'DESC')
                ->get();
            }
            else{
                $data = DemandeVehicule::with('typeVehicule', 'motif', 'affectation','user','beneficiaire')
                ->where('is_note','=',false)
                ->where('user_id','=',$user_id)
                ->orWhere('beneficiaire_id','=',$user_id)
                ->orderBy('created_at', 'DESC')
                ->get();
            }

            return response()->json([
                'data' => $data,
                'success' => 'success',
                'status' => 200
            ]);

        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ]);
        }
    }

    public function getDemandeCourseEnCourFiltrer(Request $request){
        try{
            $debut=$request->input('debut');
            $fin=$request->input('fin');
            $user_id = $request->input('user_id');

            $debut = Carbon::parse($debut)->startOfDay();
            $fin = Carbon::parse($fin)->endOfDay();

            $demandeVehicules = [];

            if($request->input('role') == env('ROLE_ADMIN')){
                $demandeVehicules=DemandeVehicule::with('typeVehicule', 'motif', 'affectation','user','beneficiaire')->whereBetween('created_at',[$debut,$fin])
                ->where('is_note','=',0)
                ->orderBy('created_at', 'DESC')
                ->get();
            }
            else{
                $demandeVehicules=DemandeVehicule::with('typeVehicule', 'motif', 'affectation','user','beneficiaire')->whereBetween('created_at',[$debut,$fin])
                ->where('is_note','=',0)
                ->where('user_id','=',$user_id)
                ->orWhere('beneficiaire_id','=',$user_id)
                ->orderBy('created_at', 'DESC')
                ->get();
            }

            return response()->json([
                'data'=> $demandeVehicules,
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

    public function listDemandeVehicule($user_id,$role){
        try{
            $actualDate = Carbon::today();

            $data = [];

            if($role == env('ROLE_ADMIN')){
                $data = DemandeVehicule::with('typeVehicule', 'motif', 'affectation','user','beneficiaire')
                ->orderBy('created_at', 'DESC')
                ->get();
            }
            else{
                $data = DemandeVehicule::with('typeVehicule', 'motif', 'affectation','user','beneficiaire')
                ->where('user_id','=',$user_id)
                ->orWhere('beneficiaire_id','=',$user_id)
                ->orderBy('created_at', 'DESC')
                ->get();
            }

            return response()->json([
                'data' => $data,
                'success' => 'success',
                'status' => 200
            ]);

        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ]);
        }
    }

    public function listDemandeVehiculeByUser($user_id)
    {
        try {
            $user = auth()->user();
            $role = optional($user->role)->libelle;

            if ($role == env('ROLE_ADMIN')) {
                $data = DemandeVehicule::with('typeVehicule', 'motif', 'affectation', 'user', 'beneficiaire')
                    ->orderBy('created_at', 'DESC')
                    ->get();
            } else {
                $data = DemandeVehicule::with('typeVehicule', 'motif', 'affectation', 'user', 'beneficiaire')
                    ->where('user_id', '=', $user_id)
                    ->orWhere('beneficiaire_id', '=', $user_id)
                    ->orderBy('created_at', 'DESC')
                    ->get();
            }

            return response()->json([
                'data' => $data,
                'success' => 'success',
                'status' => 200
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());

            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue.',
                'status' => 500
            ]);
        }
    }

    public function getLastDemande($user_id)
    {
        try {
            $data = DemandeVehicule::with('typeVehicule', 'motif', 'affectation', 'user', 'beneficiaire')
                ->where(function ($query) use ($user_id) {
                    $query->where('user_id', $user_id)
                          ->orWhere('beneficiaire_id', $user_id);
                })
                ->orderByDesc('created_at')
                ->first();

            return response()->json([
                'data' => $data,
                'success' => 'success',
                'status' => 200
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());

            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue.',
                'status' => 500
            ]);
        }
    }

    public function saveDemandePourMoi(Request $request)
    {
        try {
            $data = DemandeVehicule::create([
                'point_depart' => $request->input('lieuDepart'),
                'point_destination' => $request->input('lieuArriver'),
                'type_vehicule_id' => $request->input('typeVehicule_id'),
                'motif_id' => $request->input('motif_id'),
                'nbre_personnes' => $request->input('nbre_personnes', 1),
                'escales' => $request->input('escales'),
                'user_id' => $request->input('user_id'),
                'beneficiaire_id' => $request->input('user_id_demande'),
                'objet' => $request->input('objet', 'Demande de course'),
                'statut' => env('STATUT_DEMANDE_COURSE_CREEE'),
                'date_depart' => $request->input('dateDepart'),
                'date_retour' => $request->input('dateArriver'),
                'heure_depart' => $request->input('HeureDepart') ?? $request->input('heureDepart'),
                'heure_retour' => $request->input('HeureArriver') ?? $request->input('heureArriver'),
                'date' => now(),
            ]);

            return response()->json([
                'data' => $data,
                'message' => 'Demande de course créée avec succès',
                'status' => 200
            ], 200);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());

            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue.',
                'status' => 500
            ], 500);
        }
    }

    public function editDemandeById(Request $request, $demandeId)
    {
        try {
            $request->merge(['demande_id' => $demandeId]);
            return $this->editDemande($request);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());

            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue.',
                'status' => 500
            ], 500);
        }
    }

    public function deleteDemandeCourseById($demandeId)
    {
        try {
            $request = request();
            $request->merge(['demande_id' => $demandeId]);
            return $this->deleteDemandeCourse($request);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());

            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue.',
                'status' => 500
            ], 500);
        }
    }

    public function getDemandeCourseById($demandeId){
        try{

            $demande = DemandeVehicule::with('typeVehicule', 'motif', 'affectation','user','beneficiaire')->where('id',$demandeId)->first();

            if($demande == null) return response()->json([
                'error' => "error",
                'message' => "Ressource non autorisée",
                'status' => 404
            ], 404);

            return response()->json([
                'data' => $demande,
                'success' => 'success',
                'status' => 200
            ]);

        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ]);
        }
    }

    public function deleteDemandeCourse(Request $request){
        try{

            $demande_id = $request->input('demande_id');

            $demande = DemandeVehicule::where('id',$demande_id)->first();

            if($demande == null) return response()->json([
                'error' => "error",
                'message' => "Ressource non autorisée",
                'status' => 404
            ], 404);

            $demande->delete();
            // delete old occupation
            OccupationService::deleteOldOccupation($demande);
            return response()->json([
                'data' => [],
                'success' => "success",
                'message' => "Demande de course retirée",
                'status' => 201
            ], 201);

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
     * @param $typeVehiculeId
     * @param $demande_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAttributaffecterDemande($typeVehiculeId,$demande_id){
        try{

            $demande = DemandeVehicule::where('id',$demande_id)->first();
            //$vehicules = DemandeCourseService::getVehiculesAvailable($typeVehiculeId, $demande)->get();
            //$chauffeurs = DemandeCourseService::getChauffeursAvailable(
            //    $typeVehiculeId, $demande
            //)->with('user')->get();

            $vehicules = Vehicule::where('type_vehicule_id', $typeVehiculeId)
                ->where('disponibilite','=', env('STATUT_DISPONIBLE'))
                ->where('statut','=', 1)
                ->get();
            $chauffeurs = Chauffeur::with('user')
                ->where('disponibilite','=', env('STATUT_DISPONIBLE'))
                ->where('statut','=', 1)
                ->get();
                // Log::debug($chauffeurs);
            return response()->json([
                'data' => array([
                    'vehicules' => $vehicules,
                    'chauffeurs' => $chauffeurs
                ]),
                'success' => 'success',
                'message' =>  '',
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

    public function verifiedChauffeurAffectation($chauffeurId, $demandeId){

  try{

            $demande = DemandeVehicule::where('id', $demandeId)->first();

            $timeDebut = Carbon::parse($demande->date_depart);
            $timeLast = Carbon::parse($demande->date_depart)->addMinutes(env('TIME_MARGE_ADD'));
            $timeLess = Carbon::parse($demande->date_depart)->addMinutes(env('TIME_MARGE_LESS'));

            // Log::debug($timeDebut);
            // Log::debug($timeLast);

            $countReturn = $this->countPlace($chauffeurId, $demande->type_vehicule_id, $timeLess, $timeLast, $demandeId);

            // Log::debug($countReturn);

            if($countReturn == 1){
                return response()->json([
                    'message' => "Chauffeur déja affecté à une course",
                    'data' => "",
                    'status' => 403
                ],403);
            }

            // Log::debug('Return');

            $verified = AffectationDemande::with('demandeCourse')
                ->where('chauffeur_id', $chauffeurId)
                ->whereHas('demandeCourse',  function($query) use($demande, $timeDebut, $timeLast, $timeLess){
                    $query->where('id', '!=', $demande->id)
                            ->where('date_depart','>=', $timeLess)
                            ->where('date_depart','<=', $timeLast)
                            ->where('statut', '!=', env('STATUT_DEMANDE_COURSE_TERMINEE'));
                },1)
                ->first();

            // Log::debug($verified);

          if($verified != ''){
                return response()->json([
                    'response'=>'1'
                ]);
            }else{
                return response()->json([
                    'response'=>'0'
                ]);
            }

        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ]);
        }
    }

    public function countPlace($chauffeur, $type_vehicule, $timeLess, $timeLast,$demande_id){
        try{

            $demandeAffected =  DemandeVehicule::with('affectation')
                ->whereHas('affectation',  function($query) use($chauffeur){
                    $query->where('chauffeur_id', $chauffeur);
                },1)
                ->where('date_depart','>=', $timeLess)
                ->where('date_depart','<=', $timeLast)
                ->where('id','!=', $demande_id)
                ->where('statut', '!=', env('STATUT_DEMANDE_COURSE_TERMINEE'))->get()
                ->sum('nbre_personnes');

                Log::debug($demandeAffected);


            $demande_en_attente = DemandeVehicule::where('id',$demande_id)->first();

            $nombre_total = $demandeAffected + $demande_en_attente->nbre_personnes;



            $typeVehicule = TypeVehicule::where('id', $type_vehicule)
                                    ->first();
            // Log::debug($typeVehicule);

            if($nombre_total > $typeVehicule->nbr_place){
                return 1;
            }else{
                return 0;
            }

        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ]);
        }
    }

    public function affecterDemande(Request $request, $response=0){
        try{
            $demande_id = $request->input('demande_id');
            $vehicule = $request->input('vehicule_id');
            $chauffeur = $request->input('chauffeur_id');

            $demande = DemandeVehicule::with('user','motif','beneficiaire')
            ->where('id','=',$demande_id)->first();

            if($demande->statut !=env('STATUT_DEMANDE_COURSE_CREEE'))
            {
                return response()->json( [
                    'error'=>"error",
                    'message' => "Ressource non autorisée",
                    'status' => 401
                ]);
            }

            // $chauffeurAffected = AffectationDemande::with('demandeCourse')
            //     ->whereHas('demandeCourse', function($query){
            //         $query->whereNotIn('statut',[env('STATUT_DEMANDE_COURSE_TERMINEE')]);
            //     })
            //     ->where('chauffeur_id', $chauffeur)
            //     ->get();

            // if($chauffeurAffected && $response != 1){
            //     return response()->json([
            //         'message' => "Ce chauffeur a ete deja affecte a une course voulez vous quand meme l'affecte ?",
            //         'response' => 1
            //     ]);
            // }

            $affectation = AffectationDemande::create([
                'vehicule_id' => $vehicule,
                'demande_vehicule_id' => $demande_id,
                'chauffeur_id' => $chauffeur,
            ]);

            $demande->statut = env('STATUT_DEMANDE_COURSE_AFFECTEE');
            $demande->save();
            // save new occupation
            OccupationService::saveOccupation($affectation, $demande->date_depart, $demande->date_retour);

            $moovService = new MoovApiService();
            $chauffeur_affecter = Chauffeur::with('user')
            ->where('id',$chauffeur)->first();
            $vehicule_affecter = Vehicule::with('type')
            ->where('id',$vehicule)->first();
            $mailable = new NotificationChauffeurMail($chauffeur_affecter,$demande,$vehicule_affecter);
            Mail::to($demande->user->email)->send($mailable);
            Log::debug($chauffeur_affecter);
            //envoi de sms au demandeur de course
            $message=''.env('MOOV_MESSAGE_HEADER')." ".$demande->beneficiaire->nom." ".$demande->beneficiaire->prenom." ".env('MOOV_MESSAGE_AFFECTATION_DEMANDEUR_DEBUT').$chauffeur_affecter->user->nom." ".$chauffeur_affecter->user->prenom." tel: ".$chauffeur_affecter->user->tel." ".env('MOOV_MESSAGE_AFFECTATION_DEMANDEUR_FIN').$vehicule_affecter->marque."-".$vehicule_affecter->immatr.'';
            $jsonResponse = $moovService->sendSms($demande->beneficiaire->tel,$message,$demande->beneficiaire_id);

            $journalSms = new JournalSms();
            $journalSms->contact = $demande->beneficiaire->tel;
            $journalSms->contenu = $message;
            $journalSms->status_envoi = $jsonResponse;
            $journalSms->date_envoi = Carbon::now();
            $journalSms->user_id = $demande->beneficiaire_id;
            $journalSms->save();

            //envoi de sms au chauffeur
            $date_format = Carbon::parse($demande->date_depart)->format('d/m/Y à H:i:s');
            $message_debut=''.env('MOOV_MESSAGE_HEADER')." ".$chauffeur_affecter->user->nom." ".$chauffeur_affecter->user->prenom." ".env('MOOV_MESSAGE_AFFECTATION_CHAUFFEUR')."\n";
            $demande_nom = "Demandeur de course: ".$demande->beneficiaire->nom." ".$demande->beneficiaire->prenom."\n";
            $num_tel = "Téléphone: ".$demande->beneficiaire->tel."\n";
            $vehicule_course = "Vehicule: ".$vehicule_affecter->marque."-".$vehicule_affecter->immatr."\n";
            $point_depart_course = "Point de départ: ".$demande->point_depart."\n";
            $date_heure_depart = "Date et heure de départ: ".$date_format;
            $message_chauffeur = $message_debut.$demande_nom.$num_tel.$vehicule_course.$point_depart_course.$date_heure_depart;
            $jsonResponse = $moovService->sendSms($chauffeur_affecter->user->tel,$message_chauffeur,$chauffeur_affecter->user_id);

            $journalSms = new JournalSms();
            $journalSms->contact = $chauffeur_affecter->user->tel;
            $journalSms->contenu = $message;
            $journalSms->status_envoi = $jsonResponse;
            $journalSms->date_envoi = Carbon::now();
            $journalSms->user_id = $demande->beneficiaire_id;
            $journalSms->save();

            return response()->json([
                'message' => 'Affactation effectuée avec succès',
                'success' => 'success',
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

    public function getDemandeAffecte(){
        try {
            $demandes = AffectationDemande::with('chauffeur')
            ->with('vehicule')
            ->with('demandeCourse')
            ->get();

            return response()->json([
                'data' => $demandes,
                'success' => 'success',
                'message' => '',
                'status' => 200
            ], 200);
        } catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status' => 500
            ]);
        }
    }


    public function demmarerCourse($demande_course_id){
        try{
            $data =  DemandeVehicule::where('id', $demande_course_id)->first();

            if($data->statut !=env('STATUT_DEMANDE_COURSE_AFFECTEE'))
            {
                return response()->json( [
                    'error'=>"error",
                    'message' => "Ressource non autorisée",
                    'status' => 401
                ]);
            }

            $data->statut = env('STATUT_DEMANDE_COURSE_DEMARREE');
            $date =Carbon::now()->format("Y-m-d H:i:s");
            $data->date_depart_effectif = $date;
            $data->save();
            return response()->json([
                'data' => $data,
                'success' => 'success',
                'message' => '',
                'status' => 200
            ], 200);

        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status'  => 500
            ], 500);
        }


    }

    public function arreterCourse($demande_course_id){
        try{
            $data =  DemandeVehicule::with('user','motif','beneficiaire')
            ->where('id', $demande_course_id)->first();

            if($data->statut !=env('STATUT_DEMANDE_COURSE_DEMARREE'))
            {
                return response()->json( [
                    'error'=>"error",
                    'message' => "Ressource non autorisée",
                    'status' => 401
                ]);
            }

            $data->statut = env('STATUT_DEMANDE_COURSE_TERMINEE');
            $date =Carbon::now()->format("Y-m-d H:i:s");
            $data->date_retour_effectif = $date;
            $data->save();
            // delete old occupation
            OccupationService::deleteOldOccupation($data);

            $mailable = new DemandeNonNoteMail($data);
            Mail::to($data->user->email)->send($mailable);
            $admin = User::whereHas('role',function($query){
                $query->where('libelle',env('ROLE_ADMIN'));
            })->get();

            $moovService = new MoovApiService();

            foreach($admin as $administrateur){
                $message=''.env('MOOV_MESSAGE_HEADER')." ".$administrateur->nom." ".$administrateur->prenom." ".env('MOOV_MESSAGE_DEMANDE_COURSE_TERMINEE_DEBUT').$data->user->nom." ".$data->user->prenom." ".env('MOOV_MESSAGE_DEMANDE_COURSE_TERMINEE_FIN').'';
                $jsonResponse = $moovService->sendSms($administrateur->tel,$message,$administrateur->id);

                $journalSms = new JournalSms();
                $journalSms->contact = $administrateur->tel;
                $journalSms->contenu = $message;
                $journalSms->status_envoi = $jsonResponse;
                $journalSms->date_envoi = Carbon::now();
                $journalSms->user_id = $administrateur->id;
                $journalSms->save();

               // JournalSms::create($jsonResponse->toArray());
            }

            // message sms au demandeur de course
            $message=''.env('MOOV_MESSAGE_HEADER')." ".$data->user->nom." ".$data->user->prenom." ".env('MOOV_MESSAGE_DEMANDE_COURSE_DEMANDEUR_TERMINEE')." ";
            $jsonResponse = $moovService->sendSms($data->user->tel,$message,$data->user_id);
            $journalSms = new JournalSms();
            $journalSms->contact = $data->user->tel;
            $journalSms->contenu = $message;
            $journalSms->status_envoi = $jsonResponse;
            $journalSms->date_envoi = Carbon::now();
            $journalSms->user_id = $data->user_id;
            $journalSms->save();
            //JournalSms::create($jsonResponse->toArray());

            return response()->json([
                'data' => $data,
                'success' => 'success',
                'message' => '',
                'status' => 200
            ], 200);

        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status'  => 500
            ], 500);
        }


    }


    public function updateAffectation(Request $request){
        try{
            $demande = $request->input('demande_id');
            $vehicule = $request->input('vehicule_id');
            $chauffeur = $request->input('chauffeur_id');
            $affectation = AffectationDemande::where('demande_vehicule_id',$demande)->first();

            if($affectation != null){
                $affectation->vehicule_id = $vehicule;
                $affectation->chauffeur_id = $chauffeur;
                $affectation->save();

                return response()->json([
                    'success' => 'success',
                    'message' => 'Affectation modifiée avec succès',
                    'status' => 200
                ], 200);
            }
        }catch(Exception $ex){
            Log::error($ex->getMessage());

            return response()->json([
                'error' => "error",
                'message' => "Une erreur interne est survenue.",
                'status'  => 500
            ], 500);
        }
    }

}
