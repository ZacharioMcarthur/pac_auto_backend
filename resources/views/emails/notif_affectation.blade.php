<!doctype html>
<html lang="fr">
<head></head>
<!-- Load CSS Files -->

<body>

<div class="row container">
      <div style="text-align: center">
          <a href="{{ config('urls.front_end') }}" target="_blank">
              <img style="width: 40%" src="{{asset('assets/parc-logo.png')}}" alt="{{env('APP_NAME')}}" class="img-fluid logo">           </a>
      </div>

      <div class="row container">
            <div class="row">
                  <div class="col-4"></div>
                  <div class="col-8">
                        <br>
                        Cher/Chère <strong>{{$nom}} {{ $prenom }}</strong>, <br>
                        Vous recevez ce message parce que votre demande de course sur 
                        <strong>{{env('APP_NAME')}}</strong>  a été affectée avec succès. Le chauffeur
                        qui vous a été affecté se dénomme Monsieur {{ $chauffeur->user->nom }} {{ $chauffeur->user->prenom }} au 
                        vehicule {{ $vehicule->marque }} immatriculé {{ $vehicule->immatr }}
                        <br> 

                        <div>
                              <br>
                              Cliquez sur le lien suivant pour accéder à la page de connexion :
                              <a target="_blank" href="{{ config('urls.front_end') }}" class="btn btn-block btn-primary">Page de connexion</a>
                        </div>

                        <div>
                              <br><br>
                              Cordialement, <br>
                              L'Equipe d'admnistration de la plateforme <strong>{{env('APP_NAME')}}</strong>.
                        </div>
                  </div>
            </div>
      </div>
</div>

</body>

</html>
