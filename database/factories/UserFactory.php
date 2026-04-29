<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'tel' => $this->faker->phoneNumber(),
            'statut' => 1,
            // Astuce : On récupère un ID existant au hasard
            'role_id' => \App\Models\Role::all()->random()->id,
            'categorie_user_id' => \App\Models\CategorieUser::all()->random()->id,
            'entite_id' => \App\Models\Entite::all()->random()->id,
            'password' => bcrypt('password'),
            'remember_token' => \Illuminate\Support\Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
