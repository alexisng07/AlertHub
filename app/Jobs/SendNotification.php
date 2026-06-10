<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotification implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 5;
    public array $backoff = [10, 30, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(public Notification $notification) 
    {
        
    }

    public function uniqueId(): string
    {
        return (string) $this->notification->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // try {
        //     // Example: pretend to send email
        //     Log::info('Sending notification', [
        //         'notification_id' => $this->notification->id,
        //         'subscriber_id' => $this->notification->subscriber_id,
        //         'status' => $this->notification->status,
        //     ]);

        //     $this->notification->update([
        //         'status' => 'sent',
        //         'sent_at' => now(),
        //     ]);

        // } catch (\Throwable $e) {

        //     $this->notification->update([
        //         'status' => 'failed',
        //     ]);

        //     Log::error('Notification failed', [
        //         'notification_id' => $this->notification->id,
        //         'error' => $e->getMessage(),
        //     ]);
        // }
        try {
            // Simulated delivery (email/webhook/etc.)
            Log::info('Sending notification', [
                'notification_id' => $this->notification->id,
            ]);

            $this->notification->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

        } catch (\Throwable $e) {

            $this->notification->update([
                'status' => 'failed',
            ]);

            throw $e; // ensures retry behavior works
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendNotification failed', [
            'notification_id' => $this->notification->id,
            'error' => $exception->getMessage(),
        ]);

        $this->notification->update([
            'status' => 'failed',
        ]);
    }
}
