<?php

namespace App\Pipeline;

use App\Models\WebhookSource;
use App\Pipeline\Handler;

class Pipeline
{
    public function __construct(
        private Handler $firstHandler
    ) {}

    public function process(array $payload, WebhookSource $source): void
    {
        $current = $this->firstHandler;

        while ($current) {

            $result = $current->handle($payload, $source);

            if ($result === HandlerState::QUIT) {
                return;
            }

            if ($result === HandlerState::SKIP_TO_DISPATCH) {
                $this->dispatch($payload, $source);
                return;
            }

            $current = $current->next;
        }

        // If all handlers return CONTINUE → still dispatch
        $this->dispatch($payload, $source);
    }

    private function dispatch(array $payload, WebhookSource $source): void
    {
        /**
         * IMPORTANT:
         * Do NOT pass array into SendNotification expecting a model.
         * Notification is created inside NotificationDispatchHandler.
         */
    }
}