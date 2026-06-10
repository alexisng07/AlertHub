<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_without_bearer_token_is_rejected(): void
    {
        $this->getJson('/api/projects')
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Missing bearer token.',
            ]);
    }

    public function test_request_with_invalid_token_is_rejected(): void
    {
        $this->withHeader('Authorization', 'Bearer invalid-token')
            ->getJson('/api/projects')
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Invalid API token.',
            ]);
    }
}