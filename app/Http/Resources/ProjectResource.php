<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\SubscriberResource;
use App\Http\Resources\AlertRuleResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\WebhookSourceResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,

            'subscribers' => SubscriberResource::collection(
                $this->whenLoaded('subscribers')
            ),

            'alert_rules' => AlertRuleResource::collection(
                $this->whenLoaded('alertRules')
            ),

            'notifications' => NotificationResource::collection(
                $this->whenLoaded('notifications')
            ),

            'webhook_sources' => WebhookSourceResource::collection(
                $this->whenLoaded('webhookSources')
            ),
        ];
    }
}