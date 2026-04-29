<?php


namespace App\Services;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Support\Facades\Log;
use App\Models\JournalSms;
use Carbon\Carbon;

class MoovApiService {

    public function sendSms($numDestinataire,$message,$idUser){
       try{
           // Log::error($numDestinataire);
          // Log::error($message);
           // $smsMessage = str_replace(' ', '+', $message);

            $apiURL = env("MOOV_API_HOST") .':' .env("MOOV_API_PORT").'/' .env('MOOV_API_URL');
            $parameters = [
                'username' => env("MOOV_API_USERNAME"),
                'password' => env('MOOV_API_PASSWORD'),
                'apikey' => env('MOOV_API_KEY'),
                'src' => env('MOOV_API_SRC'),
                'dst' => $numDestinataire,
                'text' => $message,
                'refnumber' => env('MOOV_API_REF_NUMBER '),
                'type' => 'web'
            ];
                
            $client = new Client(['verify' => false]);
            $response = $client->request('GET', $apiURL, ['query' => $parameters]);
    
            $statusCode = $response->getStatusCode();
            Log::error($response->getBody());
            return "ENVOYE";
            //return $response->getBody()->getContents();
       }catch(TransferException $ex){
            Log::error("Message en erreur ".$message);
            Log::error($ex);

            // $journalSms = new JournalSms();
            // $journalSms->contact = $numDestinataire;
            // $journalSms->contenu = $message;
            // $journalSms->status_envoi = "ECHEC";
            // $journalSms->date_envoi = Carbon::now();
            // $journalSms->user_id = $idUser;

            // return $journalSms;
            return "ECHEC";
       }
    }
}