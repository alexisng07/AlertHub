<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\WebhookSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_endpoint_accepts_payload(): void
    {
        $project = Project::factory()->create();

        $payload = [
            'event' => 'deployment_failed',
            'message' => 'Deployment failed',
        ];

        $source = WebhookSource::factory()->create([
            'project_id' => $project->id,
            'signing_secret' => 'test-secret',
        ]);

        $json = json_encode($payload);

        $signature = hash_hmac(
            'sha256',
            $json,
            $source->signing_secret
        );

        $response = $this
            ->withHeader('X-Signature', $signature)
            ->postJson(
                "/api/webhooks/{$project->uuid}/{$source->source_key}",
                $payload
            );

        $response->assertSuccessful();
    }
}