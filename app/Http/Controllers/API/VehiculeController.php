<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Vehicule;
use App\Http\Responses\ApiResponse;
use Exception;

class VehiculeController extends Controller
{
    public function getVehicules()
    {
        try {
            // On récupère les véhicules avec leur type (ex: Camion, Berline)
            $vehicules = Vehicule::with('typeVehicule')->where('statut', 'disponible')->get();
            
            return ApiResponse::success($vehicules, 'Liste des véhicules récupérée');
        } catch (Exception $e) {
            return ApiResponse::error('Erreur lors de la récupération des véhicules', 500);
        }
    }
}
