<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\CombatStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Combat>
 */
class CombatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->words(3, true),
            'status' => CombatStatus::Active,
            'current_round' => 1,
            'current_turn_index' => 0,
        ];
    }
}
