<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProgrammerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'chauffeur_id' => function () {
                return \App\Models\Chauffeur::inRandomOrder()->first()->id ?? \App\Models\Chauffeur::factory();
            },
            'planning_garde_id' => function () {
                return \App\Models\PlanningGarde::inRandomOrder()->first()->id ?? \App\Models\PlanningGarde::factory();
            },
            'date_fin_repos' => $this->faker->dateTimeBetween('now', '+1 month'),
        ];
    }
}