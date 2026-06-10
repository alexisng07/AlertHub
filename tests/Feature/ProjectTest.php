<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_can_list_only_its_projects(): void
    {
        $organization = Organization::factory()->create();

        Project::factory()->count(2)->create([
            'organization_id' => $organization->id,
        ]);

        Project::factory()->count(3)->create();

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $organization->api_token)
            ->getJson('/api/projects');

        $response->assertOk();

        $this->assertCount(2, $response->json('data'));
    }

    public function test_project_can_be_created(): void
    {
        $organization = Organization::factory()->create();

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $organization->api_token)
            ->postJson('/api/projects', [
                'name' => 'AlertHub API',
                'description' => 'Monitoring system',
            ]);

        $response->assertCreated();

        $this->assertDatabaseHas('projects', [
            'name' => 'AlertHub API',
            'organization_id' => $organization->id,
        ]);
    }

    public function test_project_creation_requires_name(): void
    {
        $organization = Organization::factory()->create();

        $response = $this
            ->withHeader('Authorization', 'Bearer ' . $organization->api_token)
            ->postJson('/api/projects', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }
}