<!DOCTYPE html>
<html lang="fr">
<head>
</head>
<body>
    <div class="container">
        <div style="text-align: center">
            <a href="{{ config('urls.front_end') }}" target="_blank">
                <img style="width: 40%" src="{{asset('assets/parc-logo.png')}}" alt="{{env('APP_NAME')}}" class="img-fluid logo">           </a>
        </div>
       <p>Monsieur <b>{{ $demande->user->nom }} {{ $demande->user->prenom }}</b> nous vous prions de noter votre demande
            pour <b>{{ $demande->motif->libelle }}
        </b></p>
    </div> 
</body>
</html>