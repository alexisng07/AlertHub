<?php

namespace Database\Factories;

use App\Models\WebhookSource;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;

/**
 * @extends Factory<WebhookSource>
 */
class WebhookSourceFactory extends Factory
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
            'source_key' => fake()->unique()->slug(),
            'source_type' => fake()->randomElement([
                'github',
                'stripe',
                'monitoring',
                'custom'
            ]),
            'name' => fake()->company(),
            'signing_secret' => fake()->sha256(),
            'event_mappings' => [
                'push' => 'deployment_failed',
            ],
            'is_active' => true,
        ];
    }
}
