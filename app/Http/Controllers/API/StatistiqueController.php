<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chauffeur;
use App\Models\User;
use App\Models\Vehicule;
use App\Models\DemandeVehicule;
use App\Models\AffectationDemande;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DemandeVehiculeExport;
use App\Exports\ListeDemandeursExport; // ✅ utilisé pour l'export

class StatistiqueController extends Controller
{
    public function dashboard()
    {
        try {
            $chauffeursActifs = Chauffeur::where('statut', 1)->count();
            $vehiculesDispo = Vehicule::where('disponibilite', 1)->count();
            $totalUtilisateurs = User::count();
            $totalDemandes = DemandeVehicule::count();
            $affectationsActives = AffectationDemande::where('statut', 1)->count();

            $dernieresDemandes = DemandeVehicule::orderBy('created_at', 'desc')->take(5)->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'chauffeurs_actifs' => $chauffeursActifs,
                    'vehicules_disponibles' => $vehiculesDispo,
                    'utilisateurs' => $totalUtilisateurs,
                    'demandes' => $totalDemandes,
                    'affectations_actives' => $affectationsActives,
                    'dernieres_demandes' => $dernieresDemandes
                ]
            ], 200);
        } catch (\Exception $ex) {
            Log::error("Erreur dashboard : " . $ex->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement du tableau de bord.',
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    public function repartitionChauffeursDisponibilite()
    {
        try {
            $total = Chauffeur::count();
            $disponibles = Chauffeur::where('disponibilite', 1)->count();
            $indisponibles = Chauffeur::where('disponibilite', 0)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'disponibles' => $disponibles,
                    'indisponibles' => $indisponibles,
                    'pourcentage_dispo' => $total > 0 ? round(($disponibles / $total) * 100, 2) . '%' : '0%',
                    'pourcentage_indispo' => $total > 0 ? round(($indisponibles / $total) * 100, 2) . '%' : '0%',
                ]
            ], 200);
        } catch (\Exception $ex) {
            Log::error("Erreur repartitionChauffeursDisponibilite : " . $ex->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul de la répartition.',
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    public function statistiquesMensuelles()
    {
        try {
            $result = [];

            for ($month = 1; $month <= 12; $month++) {
                $query = DemandeVehicule::whereMonth('created_at', $month);
                $total = (clone $query)->count();

                $creees = (clone $query)->where('statut', 'CREEE')->count();
                $affectees = (clone $query)->where('statut', 'AFFECTEE')->count();
                $demarrees = (clone $query)->where('statut', 'DEMARREE')->count();
                $terminees = (clone $query)->where('statut', 'TERMINEE')->count();

                $result[] = [
                    'mois' => Carbon::create()->month($month)->format('F'),
                    'total' => $total,
                    'creees' => $creees,
                    'affectees' => $affectees,
                    'demarrees' => $demarrees,
                    'terminees' => $terminees,
                    'pourcentage_terminees' => $total > 0 ? round(($terminees / $total) * 100, 2) . '%' : '0%',
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Statistiques mensuelles générées.',
                'data' => $result
            ], 200);
        } catch (\Exception $ex) {
            Log::error("Erreur statistiquesMensuelles : " . $ex->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la récupération des statistiques mensuelles.",
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    public function statistiquesTrimestrielles()
    {
        try {
            $result = [];

            for ($trim = 1; $trim <= 4; $trim++) {
                $startMonth = ($trim - 1) * 3 + 1;
                $endMonth = $startMonth + 2;

                $query = DemandeVehicule::whereBetween(DB::raw('MONTH(created_at)'), [$startMonth, $endMonth]);
                $total = (clone $query)->count();

                $creees = (clone $query)->where('statut', 'CREEE')->count();
                $affectees = (clone $query)->where('statut', 'AFFECTEE')->count();
                $demarrees = (clone $query)->where('statut', 'DEMARREE')->count();
                $terminees = (clone $query)->where('statut', 'TERMINEE')->count();

                $result[] = [
                    'trimestre' => "T$trim",
                    'total' => $total,
                    'creees' => $creees,
                    'affectees' => $affectees,
                    'demarrees' => $demarrees,
                    'terminees' => $terminees,
                    'pourcentage_terminees' => $total > 0 ? round(($terminees / $total) * 100, 2) . '%' : '0%',
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Statistiques trimestrielles générées.',
                'data' => $result
            ], 200);
        } catch (\Exception $ex) {
            Log::error("Erreur statistiquesTrimestrielles : " . $ex->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la récupération des statistiques trimestrielles.",
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    public function exportDemandesStatistiques()
    {
        try {
            return Excel::download(new DemandeVehiculeExport, 'statistiques_demandes.xlsx');
        } catch (\Exception $ex) {
            Log::error("Erreur exportDemandesStatistiques : " . $ex->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de l'export des données.",
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    public function exportDemandesVehicules()
    {
        try {
            return Excel::download(new DemandeVehiculeExport, 'demandes_vehicules.xlsx');
        } catch (\Exception $ex) {
            Log::error("Erreur exportDemandesVehicules : " . $ex->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de l'export des demandes.",
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    public function listeDemandeurs(Request $request)
    {
        try {
            $dateDebut = $request->input('date_debut');
            $dateFin   = $request->input('date_fin');

            $query = DemandeVehicule::with([
                'user.entite',
                'chauffeur.user',
                'vehicule'
            ]);

            if ($dateDebut && $dateFin) {
                $query->whereBetween('date_depart', [$dateDebut, $dateFin]);
            } elseif ($dateDebut) {
                $query->whereDate('date_depart', '>=', $dateDebut);
            } elseif ($dateFin) {
                $query->whereDate('date_depart', '<=', $dateFin);
            }

            $demandes = $query->get()->map(function($demande) {
                return [
                    'date'        => $demande->date_depart ? Carbon::parse($demande->date_depart)->format('d/m/Y H:i') : null,
                    'entite'      => $demande->user?->entite?->nom,
                    'chauffeur'   => $demande->chauffeur?->user?->nom ?? 'Non affecté',
                    'vehicule'    => $demande->vehicule?->immatr ?? 'Non affecté',
                    'date_fin'    => $demande->date_retour ? Carbon::parse($demande->date_retour)->format('d/m/Y H:i') : null,
                    'trafic'      => null,
                    'objet'       => $demande->objet,
                    'statut'      => $demande->statut
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Liste des demandeurs sur la période',
                'data' => $demandes
            ]);

        } catch (\Exception $ex) {
            Log::error("Erreur listeDemandeurs : " . $ex->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la récupération des demandeurs.",
                'error' => $ex->getMessage()
            ], 500);
        }
    }

    public function exportListeDemandeurs(Request $request)
    {
        try {
            return Excel::download(
                new ListeDemandeursExport($request->date_debut, $request->date_fin),
                'liste_demandeurs.xlsx'
            );
        } catch (\Exception $ex) {
            Log::error("Erreur exportListeDemandeurs : " . $ex->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de l'export de la liste des demandeurs.",
                'error' => $ex->getMessage()
            ], 500);
        }
    }
}
