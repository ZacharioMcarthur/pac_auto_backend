<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewDemandeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nom;
    public $prenom;
    public $email;

        /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nom,$prenom, $email)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(['address' =>env('CONTACT_EMAIL') , 'name' => env('MAIL_FROM_NAME')])
            ->to($this->email)
            ->subject(env('APP_NAME') . " [ Demande de course Créée] ")
            ->view('emails.new_demande', [
                  'nom' => $this->nom,
                  'prenom' => $this->prenom,
                  'email' => $this->email
            ]);
    }
}
