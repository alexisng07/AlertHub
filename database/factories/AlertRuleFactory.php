<?php

namespace Database\Factories;

use App\Models\AlertRule;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;

/**
 * @extends Factory<AlertRule>
 */
class AlertRuleFactory extends Factory
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
            'name' => fake()->sentence(3),
            'source_type' => fake()->randomElement([
                'github',
                'stripe',
                'monitoring',
                'custom'
            ]),
            'event_type' => fake()->word(),
            'conditions' => [
                'threshold' => fake()->numberBetween(1, 100),
            ],
            'action' => fake()->randomElement([
                'notify',
                'escalate',
                'digest'
            ]),
            'priority' => fake()->randomElement([
                'low',
                'medium',
                'high',
                'critical'
            ]),
            'is_active' => true,
        ];
    }
}
