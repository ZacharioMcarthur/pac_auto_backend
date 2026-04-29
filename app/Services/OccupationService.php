<?php


namespace App\Services;


use App\Models\AffectationDemande;
use App\Models\Chauffeur;
use App\Models\DemandeVehicule;
use App\Models\Occupation;
use App\Models\Vehicule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class OccupationService
 * @package App\Services
 */
class OccupationService {

    private static $CHAUFFEUR_AVAILABILITY = "CHAUFFEUR";
    private static $VEHICULE_AVAILABILITY = "VEHICULE";

    /**
     * @param AffectationDemande $affectation
     * @param $date_depart
     * @param $date_retour
     */
    public static function saveOccupation(AffectationDemande $affectation, $date_depart, $date_retour) {
        Occupation::query()->create([
            'date_depart' => $date_depart,
             'date_retour'=> $date_retour,
            'vehicule_id'=> $affectation->vehicule_id,
            'chauffeur_id'=> $affectation->chauffeur_id,
            'demande_vehicule_id' => $affectation->demande_vehicule_id
        ]);
    }

    /**
     * @param $affectation
     */
    public static function deleteOldOccupation(DemandeVehicule $demandeVehicule) {
        Occupation::query()
            ->where('demande_vehicule_id', '=', $demandeVehicule->id)
            ->delete();
    }

    /**
     * Get vehicles available
     * @param $type_vehicule_id
     * @param DemandeVehicule $demande
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getVehiculesAvailable(DemandeVehicule $demande){
        return self::getAvailability(self::$VEHICULE_AVAILABILITY, $demande);
    }

    /**
     *
     * @param $type_vehicule_id
     * @param DemandeVehicule $demande
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getChauffeursAvailable(DemandeVehicule $demande) {
       return self::getAvailability(self::$CHAUFFEUR_AVAILABILITY, $demande);
    }

    /**
     * @param $type
     * @param DemandeVehicule $demandeVehicule
     * @return Builder
     */
    private static function getAvailability($type, DemandeVehicule $demandeVehicule) {
        $depart = $demandeVehicule->date_depart;
        $retour = $demandeVehicule->date_retour;
        if($type == self::$VEHICULE_AVAILABILITY) {
            $queryAvailability = Vehicule::query()
                ->orderBy('immatr')
                ->where('type_vehicule_id', '=', $demandeVehicule->type_vehicule_id);
        } else { // chauffeur
            $queryAvailability = Chauffeur::query()
                ->orderBy('matricule');
        }
        $queryAvailability
            ->where('disponibilite', '=', env('STATUT_DISPONIBLE'))
            ->where('statut', '=', 1)
            ->where(function (Builder $query) use($depart, $retour, $demandeVehicule, $type) {
                $query->whereDoesntHave('occupations')
               ->orWhereHas('occupations', function (Builder $query) use ($depart, $retour) {
                    // dates not overlaps
                    $query->whereHas('demandeCourse', function ($query) use ($depart, $retour) {
                        // (x1 < x2 || x1 > y2)
                        $query->where( function($query) use ($depart, $retour) {
                            $query->where('demande_vehicules.date_depart','<',$depart);
                            $query->orWhere('demande_vehicules.date_depart','>',$retour);
                        });
                        // (y1 > y2 || y1 < x2)
                        $query->where( function($query) use ($depart, $retour) {
                            $query->where('demande_vehicules.date_retour','>',$retour);
                            $query->orWhere('demande_vehicules.date_retour','<',$depart);
                        });
                        // ( x1 > y2 || y1 < x2)
                        $query->where( function($query) use ($depart, $retour) {
                            $query->where('demande_vehicules.date_depart','>',$retour);
                            $query->orWhere('demande_vehicules.date_retour','<',$depart);
                        });
                    });
                }, '=', self::getEvailabilityCount($type, $demandeVehicule));
            });

        return $queryAvailability;
    }

    /**
     * @param $type
     * @param DemandeVehicule $demandeVehicule
     * @return int
     */
    private static function getEvailabilityCount($type, DemandeVehicule $demandeVehicule) {
        if($type == self::$VEHICULE_AVAILABILITY) {
            $queryAvailability = Vehicule::query()
                ->where('type_vehicule_id', '=', $demandeVehicule->type_vehicule_id);
        } else { // chauffeur
            $queryAvailability = Chauffeur::query();
        }
        $queryAvailability
            ->where('disponibilite', '=', env('STATUT_DISPONIBLE'))
            ->where('statut', '=', 1)
            ->whereHas('occupations');
        $count = $queryAvailability->count();
        return $count > 0 ? $count : -1;
    }

}
