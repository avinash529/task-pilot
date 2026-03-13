<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraphs(2, true),
            'priority' => fake()->randomElement(TaskPriority::values()),
            'status' => fake()->randomElement(TaskStatus::values()),
            'due_date' => fake()->dateTimeBetween('now', '+14 days'),
            'assigned_to' => User::factory(),
            'ai_summary' => fake()->sentence(16),
            'ai_priority' => fake()->randomElement(TaskPriority::values()),
        ];
    }

    public function withoutAi(): static
    {
        return $this->state(fn (array $attributes) => [
            'ai_summary' => null,
            'ai_priority' => null,
        ]);
    }
}
