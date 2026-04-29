<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicule>
 */
class VehiculeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'immatr' => $this->faker->unique()->bothify('??-####-??'),
            'marque' => $this->faker->randomElement(['Toyota', 'Nissan', 'Mitsubishi']),
            'date_mise_circulation' => $this->faker->date('Y-m-d'),
            'capacite' => $this->faker->numberBetween(2, 15),
            'type_vehicule_id' => \App\Models\TypeVehicule::all()->random()->id ?? 1,
            'disponibilite' => 'Disponible',
            'statut' => 1,
            'created_by' => \App\Models\User::all()->random()->id ?? 1,
        ];
    }
}
