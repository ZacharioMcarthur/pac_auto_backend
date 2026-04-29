<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EntiteFactory extends Factory
{
    public function definition()
    {
        return [
            'nom' => $this->faker->unique()->company(),
            'code' => strtoupper($this->faker->unique()->bothify('ENT-###')), // Génère un code unique type ENT-123
            'type' => $this->faker->randomElement(['direction', 'departement', 'service', 'bureau']),
            'parent_id' => null, // On laisse à null par défaut pour les entrées de base
        ];
    }
}