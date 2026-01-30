<?php

namespace Database\Factories;

use App\Models\Combat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CombatCharacter>
 */
class CombatCharacterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'combat_id' => Combat::factory(),
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'initiative' => fake()->numberBetween(1, 20),
            'original_initiative' => function (array $attributes) {
                return $attributes['initiative'];
            },
            'max_hp' => 20,
            'current_hp' => 20,
            'is_player' => true,
            'order' => 0,
        ];
    }
}
