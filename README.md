# ParcAuto Backend API

Backend Laravel du projet Parc Automobile PAC.  
Ce service expose les endpoints API consommés par Flutter et gère la persistance MySQL.

## Stack
- Laravel 10
- PHP 8.1+
- MySQL
- Laravel Sanctum (token Bearer)

## Démarrage rapide
1. Installer les dépendances:
   - `composer install`
2. Initialiser l'environnement:
   - Copier `.env.example` vers `.env`
   - Configurer la base de données
   - `php artisan key:generate`
3. Lancer les migrations:
   - `php artisan migrate`
4. Démarrer le serveur local:
   - `php artisan serve`

## Authentification API
- Connexion: `POST /api/login`
- Routes protégées: middleware `auth:sanctum`
- Header à envoyer côté client Flutter:
  - `Authorization: Bearer <token>`
  - `Accept: application/json`

## Contrat de réponse JSON
Les endpoints doivent retourner un format homogène:
- `success` (bool)
- `status` (int)
- `message` (string)
- `data` (mixed)
- `errors` (array, en cas de validation)

## Variables sensibles
Ne pas versionner de vrais secrets dans `.env.example`:
- DB password
- API keys SMS/tiers
- comptes admin réels

## Documentation projet
Consulter:
- `docs/ARCHITECTURE.md`
- `docs/FILE_IMPORTANCE.md`
- `docs/API_CONTRACT.md`
- `docs/ENDPOINTS.md`
- `docs/DEPLOYMENT.md`
- `docs/ENV_REFERENCE.md`
