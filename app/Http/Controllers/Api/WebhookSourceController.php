<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Http\Resources\WebhookSourceResource;
use App\Http\Requests\StoreWebhookSourceRequest;

class WebhookSourceController extends Controller
{
    public function store(
        StoreWebhookSourceRequest $request,
        Project $project
    ) {
        $source = $project
            ->webhookSources()
            ->create(
                $request->validated()
            );

        return new WebhookSourceResource($source);
    }
}