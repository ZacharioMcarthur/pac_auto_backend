<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CategoriePermis>
 */
class CategoriePermisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            
            'libelle' => $this->faker->unique()->randomElement(['A', 'B', 'C1', 'C', 'D', 'E']),
            'statut' => 1, 
        ];
    }
}
