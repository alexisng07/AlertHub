<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\NotificationCreated;

class UpdateSubscriberStats
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationCreated $event): void
    {
        $subscriber = $event->notification->subscriber;

        $subscriber->increment('notification_count');

        $subscriber->update([
            'last_notified_at' => now(),
        ]);
    }
}
