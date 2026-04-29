<?php

namespace Database\Factories;

use App\Models\Chauffeur;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChauffeurFactory extends Factory
{
    protected $model = Chauffeur::class;

    public function definition()
    {
        return [
            'matricule' => $this->faker->unique()->numberBetween(10000, 99999),
            'num_permis' => strtoupper($this->faker->bothify('??######')),
            'annee_permis' => (string) $this->faker->year(),
            'adresse' => $this->faker->address(),
            'contact' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'statut' => 1,
            'disponibilite' => $this->faker->randomElement(Chauffeur::DISPONIBILITES),
            'categorie_permis_id' => \App\Models\CategoriePermis::all()->random()->id,
            'user_id' => \App\Models\User::all()->random()->id,
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}