<?php

namespace App\Http\Controllers;

use App\Models\Chauffeur;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class ChauffeurController extends Controller
{
    // Liste paginée des chauffeurs actifs, filtrable par disponibilité
    public function getChauffeurs(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $dispo = $request->input('disponibilite');

            $query = Chauffeur::with(['user', 'permis'])
                        ->where('statut', true);

            if ($dispo && in_array($dispo, Chauffeur::DISPONIBILITES)) {
                $query->where('disponibilite', $dispo);
            }

            $chauffeurs = $query->paginate($perPage);

            return response()->json([
                'data' => $chauffeurs,
                'success' => 'success',
                'status' => 200
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue',
                'status' => 500
            ]);
        }
    }

    // Liste des utilisateurs actifs qui ne sont pas encore chauffeurs
    public function getAgents()
    {
        try {
            $chauffeurUserIds = Chauffeur::where('statut', true)->pluck('user_id')->toArray();

            $data = User::where('statut', true)
                ->whereNotIn('id', $chauffeurUserIds)
                ->get();

            return response()->json([
                'data' => $data,
                'success' => 'success',
                'status' => 200
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue',
                'status' => 500
            ]);
        }
    }

    // Vérifie si un chauffeur existe pour un utilisateur
    public function checkExistingChauffeur($userId)
    {
        try {
            return Chauffeur::where('user_id', $userId)->first();
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return null;
        }
    }

    //  Créer ou mettre à jour un chauffeur
    public function saveChauffeur(Request $request)
    {
        try {
            $input = $request->input('body');

            $chauffeur = $this->checkExistingChauffeur($input['user_id']);
            if ($chauffeur) {
                $chauffeur->update($input);
                return response()->json([
                    'success' => 'success',
                    'message' => 'Le chauffeur a été mis à jour avec succès.',
                    'status' => 200
                ]);
            }

            Chauffeur::create($input);

            return response()->json([
                'success' => 'success',
                'message' => 'Le chauffeur a été ajouté avec succès.',
                'status' => 200
            ]);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return response()->json([
                'error' => 'error',
                'message' => 'Une erreur interne est survenue',
                'status' => 500
            ]);
        }
    }

    //  Récupérer un chauffeur par son ID
    public function getChauffeurById($id)
    {
        try {
            $data = Chauffeur::with(['user', 'permis'])->find($id);

            if (!$data) {
                return response()->json([
                    'error' => 'not_found',
                    'message' => 'Chauffeur non trouvé.',
                    'status' => 404
                ]);
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
                'message' => 'Une erreur interne est survenue',
                'status' => 500
            ]);
        }
    }

    //  Mettre à jour de la disponibilité d’un chauffeur
    public function updateDisponibilite(Request $request, $chauffeurId)
    {
        try {
            $chauffeur = Chauffeur::find($chauffeurId);

            if (!$chauffeur) {
                return response()->json([
                    'error' => 'not_found',
                    'message' => 'Chauffeur non trouvé.',
                    'status' => 404
                ]);
            }

            $nouvelleDispo = $request->input('disponibilite');

            if (!in_array($nouvelleDispo, Chauffeur::DISPONIBILITES)) {
                return response()->json([
                    'error' => 'invalid_value',
                    'message' => 'Valeur de disponibilité invalide.',
                    'status' => 400
                ]);
            }

            $chauffeur->disponibilite = $nouvelleDispo;
            $chauffeur->save();

            return response()->json([
                'success' => 'success',
                'message' => 'Disponibilité mise à jour avec succès.',
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
}
