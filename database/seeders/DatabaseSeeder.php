<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. TABLES DE RÉFÉRENCE INDÉPENDANTES
        // Ces tables ne dépendent de personne d'autre.
        \App\Models\Role::factory(3)->create();
        \App\Models\Direction::factory(3)->create();
        \App\Models\CategorieUser::factory(2)->create();
        \App\Models\CategoriePermis::factory(4)->create();
        \App\Models\TypeVehicule::factory(5)->create();
        \App\Models\Motif::factory(5)->create();
        \App\Models\CritereNotation::factory(5)->create();

        // 2. GESTION HIÉRARCHIQUE DES ENTITÉS
        // On crée d'abord les directions mères (parent_id = null)
        \App\Models\Entite::factory(3)->create(['type' => 'direction']);

        // On crée ensuite des entités qui peuvent être des services ou bureaux (parent_id géré par la factory ou null)
        \App\Models\Entite::factory(5)->create();

        // 3. LES ACTEURS (Dépendent des Rôles, Entités, etc.)
        // On crée les utilisateurs pour le backend et l'app mobile
        \App\Models\User::factory(10)->create();
        
        // On crée les chauffeurs avec leurs permis
        \App\Models\Chauffeur::factory(10)->create();

        // 4. MATÉRIEL ET LOGISTIQUE
        // Les véhicules dépendent des types de véhicules et de l'utilisateur créateur
        \App\Models\Vehicule::factory(15)->create();
        
        // Planning de garde (double 'n' selon ta migration)
        \App\Models\PlanningGarde::factory(5)->create();
        
        // Programmation des chauffeurs dans le planning
        \App\Models\Programmer::factory(10)->create();

        // 5. OPÉRATIONS (Flux de demandes)
        // Les demandes dépendent des utilisateurs, motifs et types de véhicules
        \App\Models\DemandeVehicule::factory(20)->create();
    }
}