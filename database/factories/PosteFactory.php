<?php

namespace Database\Factories;

use App\Models\Poste;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Poste>
 */
class PosteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $experienceMin = fake()->randomFloat(1, 0, 5);

        return [
            'user_id' => User::factory(),
            'titre' => fake()->jobTitle(),
            'description' => fake()->paragraph(),
            'competences_requises' => array_values(fake()->randomElements(
                ['php', 'laravel', 'mysql', 'javascript', 'vue', 'docker'],
                2,
            )),
            'langues_attendues' => ['français', 'anglais'],
            'experience_min' => $experienceMin,
            'experience_ideale' => fake()->randomFloat(1, $experienceMin, $experienceMin + 5),
            'niveau_etudes_min' => fake()->randomElement(Poste::NIVEAUX_ETUDES),
            'actif' => true,
        ];
    }
}
