<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TypeVehicule>
 */
class TypeVehiculeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // On pioche dans une liste de types de véhicules cohérents
            'libelle' => $this->faker->unique()->randomElement([
                'Berline', 
                'SUV', 
                'Pick-up', 
                'Camionnette', 
                'Motocyclette', 
                'Bus'
            ]),
            'statut' => 1, 
        ];
    }
}
