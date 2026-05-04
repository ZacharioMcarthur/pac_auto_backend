# Architecture ParcAuto Backend

## Vue d'ensemble
Le backend suit une architecture Laravel classique:
- `routes/api.php`: points d'entrée API
- `app/Http/Controllers`: logique HTTP
- `app/Models`: accès aux données via Eloquent
- `database/migrations`: schéma de base de données
- `app/Services`: logique métier réutilisable

## Flux Flutter -> Laravel -> MySQL
1. Flutter appelle un endpoint API.
2. Le contrôleur valide la requête et applique les règles métier.
3. Les modèles Eloquent lisent/écrivent en base MySQL.
4. La réponse JSON standardisée est retournée au mobile.

## Modules métier principaux
- `Auth`: connexion, profil, déconnexion.
- `User`: gestion des utilisateurs et mot de passe.
- `Vehicule`: véhicules, types, catégories de permis.
- `DemandeCourse`: demandes, affectations, démarrage/arrêt de course.
- `Entite/Statistiques`: administration et indicateurs.

## Points d'attention
- Auth API unifiée sur Sanctum.
- Réponses JSON homogènes via `ApiResponse`.
- Validation stricte sur endpoints d'écriture.
- Migrations rollback corrigées pour éviter les échecs CI/CD.
