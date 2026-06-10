<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\NotificationCreated;

class CheckEscalation
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
        $notification = $event->notification;
        $subscriber = $notification->subscriber;

        // escalation rule (example: > 5 notifications in last 10 minutes)
        $threshold = 5;

        $recentCount = $subscriber->notifications()
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();

        if ($recentCount > $threshold) {
            $notification->update([
                'status' => 'escalated',
            ]);
        }
    }
}
