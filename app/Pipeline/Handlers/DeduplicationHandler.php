<?php

namespace App\Pipeline\Handlers;

use App\Models\WebhookSource;
use App\Pipeline\Handler;
use App\Pipeline\HandlerState;
use Illuminate\Support\Facades\Cache;

class DeduplicationHandler extends Handler
{
    public function handle(array $payload, WebhookSource $source): HandlerState
    {
        $hash = hash('sha256', json_encode($payload));

        $key = "webhook:dedupe:{$source->id}:{$hash}";

        if (Cache::has($key)) {
            return HandlerState::QUIT;
        }

        Cache::put($key, true, now()->addMinutes(5));

        return HandlerState::CONTINUE;
    }
}