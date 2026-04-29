<?php


namespace App\Services;

use App\Models\DemandeVehicule;

class DemandeCourseService {


    /**
     * Get vehicles available
     * @param $type_vehicule_id
     * @param DemandeVehicule $demande
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getVehiculesAvailable($type_vehicule_id, DemandeVehicule $demande){
        return OccupationService::getVehiculesAvailable($demande);
    }

    /**
     *
     * @param $type_vehicule_id
     * @param DemandeVehicule $demande
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getChauffeursAvailable($type_vehicule_id, DemandeVehicule $demande) {
        return OccupationService::getChauffeursAvailable($demande);
    }
}
