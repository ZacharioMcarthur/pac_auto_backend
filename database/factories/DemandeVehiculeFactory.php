<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DemandeVehicule>
 */
class DemandeVehiculeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
{
    return [
            'reference' => 'DEM-' . $this->faker->unique()->numberBetween(1000, 9999),
            'objet' => $this->faker->sentence(3),
            'date_depart' => now()->addDays(2),
            'date_retour' => now()->addDays(3),
            'point_depart' => 'PAC Cotonou',
            'point_destination' => $this->faker->city(),
            'nbre_personnes' => $this->faker->numberBetween(1, 8),
            'user_id' => \App\Models\User::all()->random()->id,
            'motif_id' => \App\Models\Motif::all()->random()->id,
            'type_vehicule_id' => \App\Models\TypeVehicule::all()->random()->id,
            'statut' => 'En attente',
        ];
}
}
