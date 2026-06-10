<?php

namespace Database\Factories;

use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;

/**
 * @extends Factory<Subscriber>
 */
class SubscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'email' => fake()->unique()->safeEmail(),
            'external_id' => fake()->uuid(),
            'name' => fake()->name(),
            'notification_count' => fake()->numberBetween(0, 50),
            'last_notified_at' => now(),
            'metadata' => [
                'team' => fake()->word(),
            ],
        ];
    }
}
