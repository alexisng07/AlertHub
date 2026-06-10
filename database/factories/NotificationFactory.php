<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Project;
use App\Models\Subscriber;
use App\Models\AlertRule;
use Illuminate\Support\Str;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'project_id' => Project::factory(),
            'subscriber_id' => Subscriber::factory(),
            'alert_rule_id' => AlertRule::factory(),
            'channel' => fake()->randomElement([
                'email',
                'webhook'
            ]),
            'subject' => fake()->sentence(),
            'body' => fake()->paragraph(),
            'payload' => [
                'event' => fake()->word(),
            ],
            'status' => fake()->randomElement([
                'pending',
                'sent',
                'failed',
                'escalated'
            ]),
            'sent_at' => now(),
        ];
    }
}
