<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlanningGarde>
 */
class PlanningGardeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'date_debut' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'date_fin' => $this->faker->dateTimeBetween('now', '+1 week'),
            'statut' => 1,
        ];
    }
}
