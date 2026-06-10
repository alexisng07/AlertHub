<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Subscriber;

class SubscriberResolver
{
    public function resolve(Project $project, array $payload): ?Subscriber
    {
        // Prefer external_id first (most stable)
        if (!empty($payload['external_id'])) {
            return Subscriber::query()
                ->where('project_id', $project->id)
                ->where('external_id', $payload['external_id'])
                ->first();
        }

        // Fallback to email
        if (!empty($payload['email'])) {
            return Subscriber::query()
                ->where('project_id', $project->id)
                ->where('email', $payload['email'])
                ->first();
        }

        return null;
    }
}