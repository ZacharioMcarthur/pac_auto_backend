<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Vehicule;
use App\Models\TypeVehicule;
use App\Models\CategoriePermis;
use App\Models\Conduire;
use App\Http\Responses\ApiResponse;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class VehiculeController extends Controller
{
    /**
     * Liste des véhicules actifs avec leur type
     */
    public function getVehicules(): JsonResponse
    {
        try {
            $data = Vehicule::with('type')->where('statut', true)->get();
            return ApiResponse::success($data, 'Liste des véhicules récupérée');
        } catch (Exception $ex) {
            Log::error("Erreur getVehicules: " . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue', 500);
        }
    }

    /**
     * Vérifier l'existence d'un véhicule par ID ou Immatriculation
     * Correction P1132 : Ajout du type 'mixed' ou 'string|int' au paramètre
     */
    public function checkExistingVehicule($param): ?Vehicule
    {
        return Vehicule::where('id', $param)->orWhere('immatr', $param)->first();
    }

    /**
     * Enregistrer ou mettre à jour un véhicule
     */
    public function saveVehicule(Request $request): JsonResponse
    {
        try {
            $input = $request->input('body') ?? $request->all();

            // Validation de base
            $validator = Validator::make($input, [
                'immatr' => 'required|string',
                'type_vehicule_id' => 'required|exists:type_vehicules,id',
            ]);

            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors()->toArray());
            }

            if (!isset($input['id'])) {
                // Création
                $vehicule = Vehicule::create($input);
                return ApiResponse::success($vehicule, 'Véhicule ajouté avec succès', 201);
            } else {
                // Modification
                $vehicule = $this->checkExistingVehicule($input['id']);
                if (!$vehicule) {
                    return ApiResponse::error('Le véhicule sélectionné n\'existe pas.', 404);
                }
                $vehicule->update($input);
                return ApiResponse::success($vehicule, 'Le véhicule a bien été mis à jour.');
            }

        } catch (Exception $ex) {
            Log::error("Erreur saveVehicule: " . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue lors de l\'enregistrement', 500);
        }
    }

    /**
     * Liste des types de véhicules actifs
     */
    public function getTypesVehicules(): JsonResponse
    {
        try {
            $data = TypeVehicule::where('statut', true)->get();
            return ApiResponse::success($data, 'Types de véhicules récupérés');
        } catch (Exception $ex) {
            Log::error("Erreur getTypesVehicules: " . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue', 500);
        }
    }

    /**
     * Enregistrer ou mettre à jour un type de véhicule
     */
    public function saveTypeVehicule(Request $request): JsonResponse
    {
        try {
            $input = $request->input('body') ?? $request->all();
            
            if(!isset($input['libelle'])) {
                return ApiResponse::error('Le libellé est obligatoire', 400);
            }

            $type_vehicule = TypeVehicule::where('libelle', $input['libelle'])->first();
            
            if ($type_vehicule) {
                $type_vehicule->update($input);
            } else {
                TypeVehicule::create($input);
            }

            return ApiResponse::success(null, "Type véhicule '{$input['libelle']}' enregistré avec succès.");
        } catch (Exception $ex) {
            Log::error("Erreur saveTypeVehicule: " . $ex->getMessage());
            return ApiResponse::error('Erreur lors de l\'enregistrement du type', 500);
        }
    }

    /**
     * Détails d'un véhicule spécifique
     * Correction P1132 : Ajout du type 'int' ou 'string' au paramètre $id
     */
    public function getVehiculeById($id): JsonResponse
    {
        try {
            $data = Vehicule::with(['conduire', 'user', 'type'])->find($id);
            if (!$data) return ApiResponse::error('Véhicule introuvable', 404);

            return ApiResponse::success($data, 'Détails du véhicule récupérés');
        } catch (Exception $ex) {
            Log::error("Erreur getVehiculeById: " . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue', 500);
        }
    }

    /**
     * Catégories de permis
     */
    public function getCategoriePermis(): JsonResponse
    {
        try {
            $data = CategoriePermis::where('statut', true)->get();
            return ApiResponse::success($data, 'Catégories de permis récupérées');
        } catch (Exception $ex) {
            Log::error("Erreur getCategoriePermis: " . $ex->getMessage());
            return ApiResponse::error('Une erreur interne est survenue', 500);
        }
    }

    /**
     * Enregistrer/Mettre à jour catégorie permis
     */
    public function saveCategoriePermis(Request $request): JsonResponse
    {
        try {
            $input = $request->input('body') ?? $request->all();
            $categorie = CategoriePermis::where('libelle', $input['libelle'] ?? '')->first();
            
            if ($categorie) {
                $categorie->update($input);
            } else {
                CategoriePermis::create($input);
            }

            return ApiResponse::success(null, 'Catégorie de permis enregistrée.');
        } catch (Exception $ex) {
            Log::error("Erreur saveCategoriePermis: " . $ex->getMessage());
            return ApiResponse::error('Erreur lors de l\'enregistrement', 500);
        }
    }

    /**
     * Lier un permis à un véhicule (Conduire)
     */
    public function saveConduire(Request $request): JsonResponse
    {
        try {
            $input = $request->input('body') ?? $request->all();
            
            $conduire = Conduire::updateOrCreate(
                [
                    'categorie_permis_id' => $input['categorie_permis_id'],
                    'vehicule_id' => $input['vehicule_id']
                ],
                $input
            );

            return ApiResponse::success($conduire, 'Liaison permis-véhicule enregistrée.');
        } catch (Exception $ex) {
            Log::error("Erreur saveConduire: " . $ex->getMessage());
            return ApiResponse::error('Erreur lors de la liaison', 500);
        }
    }

    /**
     * Supprimer une liaison permis-véhicule
     */
    public function deleteConduire(Request $request): JsonResponse
    {
        try {
            $input = $request->input('body') ?? $request->all();
            $conduire = Conduire::where('categorie_permis_id', $input['categorie_permis_id'])
                                ->where('vehicule_id', $input['vehicule_id'])
                                ->first();

            if ($conduire) {
                $conduire->delete();
                return ApiResponse::success(null, 'Liaison supprimée avec succès.');
            }

            return ApiResponse::error('Liaison introuvable.', 404);
        } catch (Exception $ex) {
            Log::error("Erreur deleteConduire: " . $ex->getMessage());
            return ApiResponse::error('Erreur lors de la suppression', 500);
        }
    }
}