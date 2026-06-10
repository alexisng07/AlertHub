<?php

namespace App\Pipeline;

use App\Models\WebhookSource;

abstract class Handler
{
    public ?Handler $next = null;

    public function setNext(Handler $handler): Handler
    {
        $this->next = $handler;
        return $handler;
    }

    abstract public function handle(
        array $payload,
        WebhookSource $source
    ): HandlerState;
}