<?php

namespace App\Http\Controllers;

use App\Http\Requests\DemandeVehiculeRequest;
use App\Http\Responses\ApiResponse;
use App\Services\{DateService, MoovApiService, DemandeCourseService, OccupationService};
use App\Mail\{NewDemandeMail, DemandeNonNoteMail, NotificationChauffeurMail};
use App\Models\{Vehicule, User, Chauffeur, JournalSms, DemandeVehicule, AffectationDemande};
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Mail, Log};
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class DemandeCourseController extends Controller
{
    /**
     * Enregistre une nouvelle demande.
     */
    public function saveDemande(DemandeVehiculeRequest $request): JsonResponse
    {
        try {
            $demande = DemandeVehicule::create(array_merge($request->validated(), [
                'statut' => env('STATUT_DEMANDE_COURSE_CREEE'),
                'date_depart' => DateService::addTimeToDate($request->date_depart, $request->heure_depart),
                'date_retour' => DateService::addTimeToDate($request->date_retour, $request->heure_retour),
                'date' => Carbon::now(),
            ]));

            $demande_new = DemandeVehicule::with(['beneficiaire', 'user'])->find($demande->id);

            if ($demande_new && $demande_new->user) {
                $user = $demande_new->user;
                Mail::to($user->email)->send(new NewDemandeMail($user->nom, $user->prenom, $user->email));

                $admins = User::whereHas('role', fn($q) => $q->where('libelle', env('ROLE_ADMIN')))->get();
                $emails_admin = $admins->pluck('email')->toArray();

                if (!empty($emails_admin)) {
                    Mail::send('emails.new_demande_admin', ['nom' => $user->nom, 'prenom' => $user->prenom], function($m) use ($emails_admin) {
                        $m->from(env('CONTACT_EMAIL'), env('MAIL_FROM_NAME'))->to($emails_admin)->subject(env('APP_NAME') . " [Demande de course créée]");
                    });
                }

                $moov = new MoovApiService();
                if ($demande_new->beneficiaire) {
                    $msgB = env('MOOV_MESSAGE_HEADER') . " {$demande_new->beneficiaire->nom} {$demande_new->beneficiaire->prenom} " . env('MOOV_MESSAGE_DEMANDE_COURSE') . env('APP_NAME');
                    $this->logSms($demande_new->beneficiaire->tel, $msgB, $moov->sendSms($demande_new->beneficiaire->tel, $msgB, $demande_new->beneficiaire_id), (int)$demande_new->beneficiaire_id);
                }

                foreach ($admins as $admin) {
                    $msgA = env('MOOV_MESSAGE_HEADER') . " {$admin->nom} {$admin->prenom} " . env('MOOV_MESSAGE_DEMANDE_COURSE_ADMIN_DEBUT') . "Monsieur {$demande_new->beneficiaire->nom} " . env('MOOV_MESSAGE_DEMANDE_COURSE_ADMIN_FIN') . env('APP_NAME');
                    $this->logSms($admin->tel, $msgA, $moov->sendSms($admin->tel, $msgA, $admin->id), (int)$admin->id);
                }
            }

            return ApiResponse::created($demande_new, 'Demande de courses créée avec succès');
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue.', 500);
        }
    }

    /**
     * Correction P1132 : Ajout des types string et int pour les paramètres.
     */
    private function logSms(string $tel, string $msg, $status, int $userId): void
    {
        JournalSms::create([
            'contact' => $tel,
            'contenu' => $msg,
            'status_envoi' => $status,
            'date_envoi' => now(),
            'user_id' => $userId
        ]);
    }

    public function verifierNotation($user_id): JsonResponse
    {
        if (!is_numeric($user_id)) {
            return ApiResponse::validationError(['user_id' => ['Identifiant utilisateur invalide.']]);
        }

        $demande = DemandeVehicule::where('beneficiaire_id', $user_id)
            ->where('statut', env('STATUT_DEMANDE_COURSE_TERMINEE'))
            ->where('is_note', 0)
            ->first(['id']);
        return ApiResponse::success($demande, 'Vérification de notation effectuée');
    }

    public function getDemandeCourseEnCour($user_id, string $role): JsonResponse
    {
        if (!is_numeric($user_id) || trim($role) === '') {
            return ApiResponse::validationError([
                'user_id' => ['Identifiant utilisateur invalide.'],
                'role' => ['Rôle invalide.'],
            ]);
        }

        $query = DemandeVehicule::with(['typeVehicule', 'motif', 'affectation', 'user', 'beneficiaire'])
            ->where('is_note', false);

        if ($role !== env('ROLE_ADMIN')) {
            $query->where(fn($q) => $q->where('user_id', $user_id)->orWhere('beneficiaire_id', $user_id));
        }

        return ApiResponse::success($query->orderBy('created_at', 'DESC')->get(), 'Demandes en cours récupérées');
    }

    public function listDemandeVehicule($user_id, string $role): JsonResponse
    {
        if (!is_numeric($user_id) || trim($role) === '') {
            return ApiResponse::validationError([
                'user_id' => ['Identifiant utilisateur invalide.'],
                'role' => ['Rôle invalide.'],
            ]);
        }

        $query = DemandeVehicule::with(['typeVehicule', 'motif', 'affectation', 'user', 'beneficiaire']);

        if ($role !== env('ROLE_ADMIN')) {
            $query->where(fn($q) => $q->where('user_id', $user_id)->orWhere('beneficiaire_id', $user_id));
        }

        return ApiResponse::success($query->orderBy('created_at', 'DESC')->get(), 'Demandes récupérées');
    }

    public function getDemandeCourseById($demandeId): JsonResponse
    {
        if (!is_numeric($demandeId)) {
            return ApiResponse::validationError(['demande_id' => ['Identifiant de demande invalide.']]);
        }

        $demande = DemandeVehicule::with(['typeVehicule', 'motif', 'affectation', 'user', 'beneficiaire'])->find($demandeId);
        return $demande
            ? ApiResponse::success($demande, 'Demande récupérée')
            : ApiResponse::notFound('Demande introuvable');
    }

    public function deleteDemandeCourse(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'demande_id' => 'required|integer|exists:demande_vehicules,id',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->toArray());
        }

        $demande = DemandeVehicule::find($request->demande_id);
        if (!$demande) {
            return ApiResponse::notFound('Demande introuvable');
        }
        
        OccupationService::deleteOldOccupation($demande);
        $demande->delete();
        return ApiResponse::success(null, 'Demande de course retirée');
    }

    public function affecterDemande(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'demande_id' => 'required|integer|exists:demande_vehicules,id',
                'vehicule_id' => 'required|integer|exists:vehicules,id',
                'chauffeur_id' => 'required|integer|exists:chauffeurs,id',
            ]);

            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors()->toArray());
            }

            $demande = DemandeVehicule::with(['user', 'beneficiaire'])->find($request->demande_id);
            if (!$demande || $demande->statut != env('STATUT_DEMANDE_COURSE_CREEE')) {
                return ApiResponse::forbidden('La demande ne peut pas être affectée dans son état actuel.');
            }

            $affectation = AffectationDemande::create([
                'vehicule_id' => $request->vehicule_id,
                'demande_vehicule_id' => $request->demande_id,
                'chauffeur_id' => $request->chauffeur_id,
            ]);

            $demande->update(['statut' => env('STATUT_DEMANDE_COURSE_AFFECTEE')]);
            OccupationService::saveOccupation($affectation, $demande->date_depart, $demande->date_retour);

            $chauffeur = Chauffeur::with('user')->find($request->chauffeur_id);
            $vehicule = Vehicule::find($request->vehicule_id);
            
            if ($demande->user) {
                Mail::to($demande->user->email)->send(new NotificationChauffeurMail($chauffeur, $demande, $vehicule));
            }

            return ApiResponse::success($affectation, 'Affectation effectuée');
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue.', 500);
        }
    }

    public function demmarerCourse($id): JsonResponse
    {
        if (!is_numeric($id)) {
            return ApiResponse::validationError(['demande_id' => ['Identifiant de demande invalide.']]);
        }

        $demande = DemandeVehicule::find($id);
        if (!$demande || $demande->statut != env('STATUT_DEMANDE_COURSE_AFFECTEE')) {
            return ApiResponse::forbidden('La course ne peut pas être démarrée dans son état actuel.');
        }
        
        $demande->update(['statut' => env('STATUT_DEMANDE_COURSE_DEMARREE'), 'date_depart_effectif' => now()]);
        return ApiResponse::success($demande, 'Course démarrée');
    }

    public function arreterCourse($id): JsonResponse
    {
        if (!is_numeric($id)) {
            return ApiResponse::validationError(['demande_id' => ['Identifiant de demande invalide.']]);
        }

        $demande = DemandeVehicule::with(['user', 'beneficiaire'])->find($id);
        if (!$demande || $demande->statut != env('STATUT_DEMANDE_COURSE_DEMARREE')) {
            return ApiResponse::forbidden('La course ne peut pas être arrêtée dans son état actuel.');
        }

        $demande->update(['statut' => env('STATUT_DEMANDE_COURSE_TERMINEE'), 'date_retour_effectif' => now()]);
        OccupationService::deleteOldOccupation($demande);

        if ($demande->user) {
            Mail::to($demande->user->email)->send(new DemandeNonNoteMail($demande));
        }

        return ApiResponse::success($demande, 'Course arrêtée');
    }
}