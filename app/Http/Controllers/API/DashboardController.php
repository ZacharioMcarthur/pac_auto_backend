<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chauffeur;
use App\Models\User;
use App\Models\Vehicule;
use App\Models\DemandeVehicule;
use App\Models\AffectationDemande;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Récupérer les statistiques du dashboard global
     */
    public function getDashboardStats(): JsonResponse
    {
        // Nombre total de chauffeurs actifs
        $totalChauffeursActifs = Chauffeur::where('statut', 1)->count();

        // Nombre de véhicules disponibles
        $vehiculesDisponibles = Vehicule::where('disponibilite', 'DISPONIBLE')->count();

        // Total de demandes de véhicules
        $totalDemandes = DemandeVehicule::count();

        // Nombre total d'utilisateurs actifs
        $totalUtilisateurs = User::where('statut', 1)->count();

        // Nombre d'affectations actives (via jointure avec demande_vehicules)
        $affectationsActives = AffectationDemande::join('demande_vehicules', 'affectation_demandes.demande_vehicule_id', '=', 'demande_vehicules.id')
            ->where('demande_vehicules.statut', 'en_cours')
            ->count();

        // Liste des 5 dernières demandes de course
        $dernieresDemandes = DemandeVehicule::with(['user', 'vehicule'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Répartition des chauffeurs par disponibilité
        $repartitionDisponibilite = Chauffeur::select('disponibilite')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('disponibilite')
            ->get();

        //  Retour des statistiques globales du dashboard
        return response()->json([
            'chauffeurs_actifs' => $totalChauffeursActifs,
            'vehicules_disponibles' => $vehiculesDisponibles,
            'total_demandes' => $totalDemandes,
            'total_utilisateurs' => $totalUtilisateurs,
            'affectations_actives' => $affectationsActives,
            'dernieres_demandes' => $dernieresDemandes,
            'repartition_chauffeurs_disponibilite' => $repartitionDisponibilite
        ]);
    }
}
