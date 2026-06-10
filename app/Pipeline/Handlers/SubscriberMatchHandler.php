<?php

namespace App\Pipeline\Handlers;

use App\Models\WebhookSource;
use App\Models\Subscriber;
use App\Pipeline\Handler;
use App\Pipeline\HandlerState;
use App\Services\SubscriberResolver;

class SubscriberMatchHandler extends Handler
{
    public function __construct(
        private SubscriberResolver $resolver
    ) {}

    public function handle(array $payload, WebhookSource $source): HandlerState
    {
        $subscriber = $this->resolver->resolve(
            $source->project,
            $payload
        );

        if (!$subscriber) {
            $subscriber = Subscriber::create([
                'project_id' => $source->project_id,
                'email' => $payload['email'] ?? null,
                'external_id' => $payload['external_id'] ?? null,
            ]);
        }

        app()->instance('current_subscriber', $subscriber);

        return HandlerState::CONTINUE;
    }
}