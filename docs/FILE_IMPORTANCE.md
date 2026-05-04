# Importance des fichiers et modules

## Fichiers critiques
- `routes/api.php`: source de vérité des endpoints disponibles pour Flutter.
- `config/auth.php`: définit les guards d'authentification (Sanctum côté API).
- `config/cors.php`: contrôle les origines autorisées pour clients frontend/mobile.
- `.env.example`: modèle de configuration; ne doit contenir aucun secret réel.

## Contrôleurs API principaux
- `app/Http/Controllers/AuthController.php`  
  Rôle: login/profile/logout et émission/révocation de tokens.
- `app/Http/Controllers/UserController.php`  
  Rôle: gestion profil utilisateur, changement de mot de passe, désactivation.
- `app/Http/Controllers/VehiculeController.php`  
  Rôle: gestion des véhicules, types, permis.
- `app/Http/Controllers/DemandeCourseController.php`  
  Rôle: cycle de vie complet d'une demande de course.

## Couche réponse
- `app/Http/Responses/ApiResponse.php`  
  Rôle: normaliser le contrat JSON pour simplifier l'intégration Flutter.

## Modèles clés
- `app/Models/User.php`: identité applicative + relations métier.
- `app/Models/DemandeVehicule.php`: entité centrale des demandes.
- `app/Models/AffectationDemande.php`: lien demande/vehicule/chauffeur.
- `app/Models/Vehicule.php`, `app/Models/Chauffeur.php`: ressources opérationnelles.

## Base de données
- `database/migrations/*.php`: description versionnée du schéma.
- `database/seeders/DatabaseSeeder.php`: données de bootstrap.
- `database/factories/*.php`: génération de jeux de test.

## Pourquoi cette structure est importante
- Sépare transport HTTP, logique métier et persistence.
- Réduit le couplage mobile/backend via contrat API stable.
- Rend les déploiements reproductibles grâce aux migrations.
