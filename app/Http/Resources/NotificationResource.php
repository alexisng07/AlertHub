<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'channel' => $this->channel,
            'subject' => $this->subject,
            'body' => $this->body,
            'payload' => $this->payload,
            'status' => $this->status,
            'sent_at' => $this->sent_at,
            'created_at' => $this->created_at,

            'subscriber' => new SubscriberResource(
                $this->whenLoaded('subscriber')
            ),

            'alert_rule' => new AlertRuleResource(
                $this->whenLoaded('alertRule')
            ),
        ];
    }
}