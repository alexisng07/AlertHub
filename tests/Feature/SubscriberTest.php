<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriberTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscriber_can_be_added_to_project(): void
    {
        $organization = Organization::factory()->create();

        $project = Project::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $organization->api_token)
            ->postJson("/api/projects/{$project->id}/subscribers", [
                'email' => 'test@example.com',
                'name' => 'John Doe',
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('subscribers', [
            'email' => 'test@example.com',
            'project_id' => $project->id,
        ]);
    }
}