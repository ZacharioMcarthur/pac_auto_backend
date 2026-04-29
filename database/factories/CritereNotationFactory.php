<?php

namespace Database\Factories;

use App\Models\CritereNotation;
use Illuminate\Database\Eloquent\Factories\Factory;

class CritereNotationFactory extends Factory
{
    /**
     * Le modèle correspondant à cette factory.
     */
    protected $model = CritereNotation::class;

    /**
     * Définition de l'état par défaut du modèle.
     */
    public function definition()
    {
        return [
            'libelle' => $this->faker->randomElement([
                'Propreté', 
                'Ponctualité', 
                'Conduite', 
                'Respect des délais'
            ]),
            
        ];
    }
}