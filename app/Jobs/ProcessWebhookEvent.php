<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\WebhookSource;
use App\Pipeline\Pipeline;
use App\Pipeline\Handlers\DeduplicationHandler;
use App\Pipeline\Handlers\ValidationHandler;
use App\Pipeline\Handlers\SubscriberMatchHandler;
use App\Pipeline\Handlers\RuleEvaluationHandler;
use App\Pipeline\Handlers\NotificationDispatchHandler;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWebhookEvent implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(public WebhookSource $webhookSource, public array $payload)
    {
        //
    }

    public function uniqueId(): string
    {
        return $this->webhookSource->id . ':' . md5(json_encode($this->payload));
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // logger()->info('Webhook received', [
        //     'source_id' => $this->webhookSource->id,
        //     'payload' => $this->payload,
        // ]);
        // $dedupe = new DeduplicationHandler();
        // $validate = new ValidationHandler();
        // $subscriber = new SubscriberMatchHandler(app(\App\Services\SubscriberResolver::class));
        // $rules = new RuleEvaluationHandler();
        // $dispatch = new NotificationDispatchHandler();

        // $dedupe->setNext($validate)
        //     ->setNext($subscriber)
        //     ->setNext($rules)
        //     ->setNext($dispatch);

        // $pipeline = new Pipeline($dedupe);

        // $pipeline->process($this->payload, $this->webhookSource);
        $pipeline = new Pipeline(
            (new DeduplicationHandler())
                ->setNext(new ValidationHandler())
                ->setNext(new SubscriberMatchHandler(app(\App\Services\SubscriberResolver::class)))
                ->setNext(new RuleEvaluationHandler())
                ->setNext(new NotificationDispatchHandler())
        );

        $pipeline->process($this->payload, $this->webhookSource);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessWebhookEvent failed', [
            'source_id' => $this->webhookSource->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
