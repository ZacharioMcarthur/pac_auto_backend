<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DirectionFactory extends Factory
{
    public function definition()
    {
        return [
            'libelle' => $this->faker->unique()->word() . ' Direction',
            'code' => strtoupper($this->faker->unique()->bothify('DIR-###')),
        ];
    }
}