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
                        Vous recevez ce message parce que vous venez de créer une demande de course sur 
                        <strong>{{env('APP_NAME')}}</strong>  avec votre adresse email.
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
