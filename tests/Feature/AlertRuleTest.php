<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_alert_rule_can_be_created(): void
    {
        $organization = Organization::factory()->create();

        $project = Project::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $organization->api_token)
            ->postJson("/api/projects/{$project->id}/alert-rules", [
                'name' => 'High CPU',
                'source_type' => 'monitoring',
                'event_type' => 'cpu_high',
                'conditions' => [
                    'threshold' => 90,
                ],
                'action' => 'notify',
                'priority' => 'critical',
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('alert_rules', [
            'project_id' => $project->id,
            'name' => 'High CPU',
        ]);
    }
}