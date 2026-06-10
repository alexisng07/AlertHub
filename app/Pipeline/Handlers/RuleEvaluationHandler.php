<?php

namespace App\Pipeline\Handlers;

use App\Models\AlertRule;
use App\Models\WebhookSource;
use App\Pipeline\Handler;
use App\Pipeline\HandlerState;

class RuleEvaluationHandler extends Handler
{
    public function handle(array $payload, WebhookSource $source): HandlerState
    {
        $rules = AlertRule::query()
            ->where('project_id', $source->project_id)
            ->where('source_type', $source->source_type)
            ->where('event_type', $payload['event'] ?? null)
            ->where('is_active', true)
            ->get();

        if ($rules->isEmpty()) {
            return HandlerState::QUIT;
        }

        app()->instance('matched_rules', $rules);

        return HandlerState::SKIP_TO_DISPATCH;
    }
}