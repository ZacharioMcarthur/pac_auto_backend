<?php

namespace App\Console\Commands;

use App\Mail\DemandeNonNoteMail;
use Illuminate\Console\Command;
use App\Models\DemandeVehicule;
use Mail;
use Illuminate\Support\Facades\Log;

class NoteQuote extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notation:job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

            $demandes_vehicules = DemandeVehicule::with('user','motif')
                ->where('statut',env('STATUT_DEMANDE_COURSE_TERMINEE'))
                ->where('is_note',0)
                ->get();

            foreach($demandes_vehicules as $demande){
                $mailable = new DemandeNonNoteMail($demande);
                Mail::to($demande->user->email)->send($mailable);
                
                $message = "Email envoyé à ".$demande->user->nom." ".$demande->user->prenom." avec succès";

            }

            $this->info('Email envoyé avec succès');

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
