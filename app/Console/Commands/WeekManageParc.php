<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Vehicule;
use App\Models\Chauffeur;
use Carbon\Carbon;
use App\Models\DemandeVehicule;
use Illuminate\Console\Command;
use App\Models\AffectationDemande;
use Illuminate\Support\Facades\Log;
use App\Services\DemandeCourseService;

use function PHPUnit\Framework\isEmpty;

class WeekManageParc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assignment:job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Respectively send an exclusive quote to everyone daily via email.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try{
            $demandeVehicule = DemandeVehicule::where('statut',env('STATUT_DEMANDE_COURSE_CREEE'))->get();

            if($demandeVehicule->isEmpty()){
                $this->info('Aucune demande a statut creee disponible');
                return 0;
            }

            foreach($demandeVehicule as $demande){
                if(Carbon::parse($demande->date_depart)->isWeekend() || (Carbon::parse($demande->date_depart)->isWeekday() && $demande->heure_depart >= '18:00:00')){

                    $vehicule = DemandeCourseService::getVehiculesAvailable($demande->type_vehicule_id, $demande)
                        ->inRandomOrder()
                        ->limit(1)
                        ->first();

                    $chauffeur = DemandeCourseService::getChauffeursAvailable(
                        $demande->type_vehicule_id, $demande
                    )->with('user')->inRandomOrder()->limit(1)->first();
                    // $chauffeur = Chauffeur::inRandomOrder()
                    //             ->limit(1)
                    //             ->first();

                    // $vehicule = Vehicule::where('type_vehicule_id', $demande->type_vehicule_id)
                    //             ->inRandomOrder()
                    //             ->limit(1)
                    //             ->first();

                    if($chauffeur != null && $vehicule != null){
                        $affectation = new AffectationDemande();
                        $affectation->vehicule_id = $vehicule->id;
                        $affectation->demande_vehicule_id = $demande->id;
                        $affectation->chauffeur_id = $chauffeur->id;
                        $affectation->save();

                        $demande->statut = env('STATUT_DEMANDE_COURSE_AFFECTEE');
                        $demande->save();

                        $message = "Affectation du ".$chauffeur->user->nom." au vehicule ".$vehicule->immatr." pour la demande ".$demande->reference."";

                        Log::debug($message);
                    }
                    else{
                        $message = "Aucune affectation effectué";
                    }
                }else{
                    $this->info('Aucune demande effectue pour la semaine dans la soire ou pour le weekend');
                }
            }
            $this->info($message);

        }catch(Exception $ex){

            Log::error($ex->getMessage());

            return response()->json([
                'error'=>'error',
                'message' => 'Une erreur interne est survenue.',
                'status' => 500
            ],500);
        }
    }
}
