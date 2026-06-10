<?php

namespace App\Pipeline\Handlers;

use App\Models\WebhookSource;
use App\Pipeline\Handler;
use App\Pipeline\HandlerState;
use Illuminate\Support\Facades\Log;

class ValidationHandler extends Handler
{
    public function handle(array $payload, WebhookSource $source): HandlerState
    {
        $required = match ($source->source_type) {
            'github' => ['event'],
            'stripe' => ['id'],
            default => ['event'],
        };

        foreach ($required as $field) {
            if (!isset($payload[$field])) {
                Log::warning('Invalid webhook payload', [
                    'source_id' => $source->id,
                    'missing' => $field,
                ]);

                return HandlerState::QUIT;
            }
        }

        return HandlerState::CONTINUE;
    }
}