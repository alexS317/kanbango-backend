<?php

namespace Database\Factories;

use App\Models\Board;
use App\Models\BoardCategory;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
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
            'created_by' => User::factory(),
            'assigned_to' => User::factory(),
            'title' => fake()->words(rand(0, 3), true),
            'description' => fake()->sentences(rand(0, 4), true),
            'category_id' => BoardCategory::factory(),
        ];
    }
}
