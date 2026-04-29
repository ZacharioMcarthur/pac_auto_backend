<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use App\Models\AffectationDemande;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use App\Models\DemandeVehicule;
use App\Models\Vehicule;
use App\Models\Chauffeur;
use Illuminate\Queue\SerializesModels;

class NotificationChauffeurMail extends Mailable
{
    use Queueable, SerializesModels;

    public $chauffeur,$demande,$vehicule;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Chauffeur $chauffeur,DemandeVehicule $demande,Vehicule $vehicule)
    {
        //
        $this->chauffeur = $chauffeur;
        $this->vehicule = $vehicule;
        $this->demande = $demande;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(['address' =>env('CONTACT_EMAIL') , 'name' => env('MAIL_FROM_NAME')])
            ->to($this->demande->user->email)
            ->subject(env('APP_NAME') . " [ Demande de course affectée] ")
            ->view('emails.notif_affectation', [
                  'nom' => $this->demande->user->nom,
                  'prenom' => $this->demande->user->prenom,
                  'email' => $this->demande->user->email,
                  'chauffeur' => $this->chauffeur,
                  'vehicule' => $this->vehicule
            ]);
    }
}
