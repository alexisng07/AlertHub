<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SubscriberController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\AlertRuleController;
use App\Http\Controllers\Api\WebhookSourceController;
use App\Http\Controllers\Api\WebhookController;

Route::middleware('tenant')->group(function () {

    Route::apiResource('projects', ProjectController::class)
        ->only(['index','store','show','update']);

    Route::get(
        'projects/{project}/subscribers',
        [SubscriberController::class, 'index']
    );

    Route::post(
        'projects/{project}/subscribers',
        [SubscriberController::class, 'store']
    );

    Route::get(
        'projects/{project}/notifications',
        [NotificationController::class, 'index']
    );

    Route::get(
        'projects/{project}/alert-rules',
        [AlertRuleController::class, 'index']
    );

    Route::post(
        'projects/{project}/alert-rules',
        [AlertRuleController::class, 'store']
    );

    Route::post(
        'projects/{project}/webhook-sources',
        [WebhookSourceController::class, 'store']
    );
});

Route::post(
    '/webhooks/{project_uuid}/{source_key}',
    [WebhookController::class, 'handle']
);