<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Motif>
 */
class MotifFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'libelle' => $this->faker->unique()->randomElement([
                'Mission de service',
                'Entretien périodique',
                'Réparation mécanique',
                'Transport de courrier',
                'Courses administratives',
                'Urgence'
            ]),
            'statut' => 1,
        ];
    }
}
