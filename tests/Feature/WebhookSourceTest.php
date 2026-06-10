<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookSourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_source_can_be_created(): void
    {
        $organization = Organization::factory()->create();

        $project = Project::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $organization->api_token)
            ->postJson("/api/projects/{$project->id}/webhook-sources", [
                'source_key' => 'github',
                'source_type' => 'github',
                'name' => 'GitHub',
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('webhook_sources', [
            'project_id' => $project->id,
            'source_key' => 'github',
        ]);
    }
}