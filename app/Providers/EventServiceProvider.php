<?php

namespace App\Providers;

use App\Events\NotificationCreated;
use App\Listeners\UpdateSubscriberStats;
use App\Listeners\CheckEscalation;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NotificationCreated::class => [
            UpdateSubscriberStats::class,
            CheckEscalation::class, // MUST come after stats update
        ],
    ];
}