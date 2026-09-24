<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\BoardCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoardCategory>
 */
class BoardCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'board_id' => Board::factory(),
            'name' => fake()->words(rand(0, 2), true),
            'position' => fake()->randomNumber(),
        ];
    }
}
