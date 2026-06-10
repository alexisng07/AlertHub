<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Subscriber;
use App\Models\AlertRule;
use App\Models\Notification;
use App\Models\WebhookSource;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Organization::factory()
            ->count(2)
            ->create()
            ->each(function (Organization $organization) {

                Project::factory()
                    ->count(3)
                    ->create([
                        'organization_id' => $organization->id,
                    ])
                    ->each(function (Project $project) {

                        $subscribers = Subscriber::factory()
                            ->count(10)
                            ->create([
                                'project_id' => $project->id,
                            ]);

                        $rules = AlertRule::factory()
                            ->count(5)
                            ->create([
                                'project_id' => $project->id,
                            ]);

                        WebhookSource::factory()
                            ->count(3)
                            ->create([
                                'project_id' => $project->id,
                            ]);

                        foreach ($subscribers as $subscriber) {

                            Notification::factory()
                                ->count(2)
                                ->create([
                                    'project_id' => $project->id,
                                    'subscriber_id' => $subscriber->id,
                                    'alert_rule_id' => $rules->random()->id,
                                ]);
                        }
                    });
            });
    }
}
