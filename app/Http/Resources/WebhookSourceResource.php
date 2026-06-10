<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebhookSourceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'source_key' => $this->source_key,
            'source_type' => $this->source_type,
            'name' => $this->name,
            'event_mappings' => $this->event_mappings,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
        ];
    }
}