<?php

namespace App\Pipeline\Handlers;

use App\Jobs\SendNotification;
use App\Models\Notification;
use App\Models\WebhookSource;
use App\Pipeline\Handler;
use App\Pipeline\HandlerState;

class NotificationDispatchHandler extends Handler
{
    public function handle(array $payload, WebhookSource $source): HandlerState
    {
        $subscriber = app('current_subscriber');
        $rules = app('matched_rules');

        foreach ($rules as $rule) {

            $notification = Notification::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'project_id' => $source->project_id,
                'subscriber_id' => $subscriber->id,
                'alert_rule_id' => $rule->id,
                'channel' => 'email',
                'subject' => 'Alert Triggered',
                'body' => json_encode($payload),
                'payload' => $payload,
                'status' => 'pending',
            ]);

            SendNotification::dispatch($notification);
        }

        return HandlerState::CONTINUE;
    }
}