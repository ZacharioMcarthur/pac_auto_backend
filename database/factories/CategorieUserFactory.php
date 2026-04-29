<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategorieUserFactory extends Factory
{
    public function definition()
    {
        return [
           
            'libelle' => $this->faker->randomElement(['AGENT', 'INVITE']),
            'statut' => 'Actif', 
        ];
    }
}