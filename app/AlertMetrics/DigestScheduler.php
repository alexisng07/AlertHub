<?php

namespace App\AlertMetrics;

use Illuminate\Support\Facades\Log;
use App\Models\Notification;
use App\Models\Subscriber;
use App\AlertMetrics\ProcessAlertDigest;

class DigestScheduler
{
    protected MetricsAggregator $aggregator;
    protected EngagementScorer $scorer;

    public function __construct(MetricsAggregator $aggregator, EngagementScorer $scorer)
    {
        $this->aggregator = $aggregator;
        $this->scorer = $scorer;
    }

    /**
     * Schedule digest processing for subscribers with pending alerts.
     *
     * This method finds subscribers who have undigested notifications
     * and dispatches ProcessAlertDigest jobs for each.
     *
     * @param  int     $projectId
     * @param  string  $date       Date in Y-m-d format
     * @param  string  $digestType "daily" or "weekly"
     * @return int     Number of digests scheduled
     */
    public function scheduleDigests(int $projectId, string $date, string $digestType = 'daily'): int
    {
        // $subscribers = \App\Models\Subscriber::where('project_id', $projectId)
        //     ->whereHas('notifications', function ($query) use ($date) {
        //         $query->whereDate('created_at', $date)
        //             ->where('status', 'sent');
        //     })
        //     ->get();
        $notificationsBySubscriber = Notification::where('project_id', $projectId)
            ->whereDate('created_at', $date)
            ->where('status', 'sent')
            ->get()
            ->groupBy('subscriber_id');

        if ($notificationsBySubscriber->isEmpty()) {
            Log::info('DigestScheduler: No notifications found', [
                'project_id' => $projectId,
                'date' => $date,
                'digest_type' => $digestType,
            ]);

            return 0;
        }

        $scheduled = 0;

        $subscribers = Subscriber::where('project_id', $projectId)
            ->whereIn('id', $notificationsBySubscriber->keys())
            ->get()
            ->keyBy('id');

        foreach ($notificationsBySubscriber as $subscriberId => $notifications) {
            // $alertIds = \App\Models\Notification::where('subscriber_id', $subscriber->id)
            //     ->whereDate('created_at', $date)
            //     ->where('status', 'sent')
            //     ->pluck('id')
            //     ->toArray();
            $subscriber = $subscribers->get($subscriberId);

            if (!$subscriber) {
                continue;
            }

            $alertIds = $notifications->pluck('id')->toArray();

            if (empty($alertIds)) {
                continue;
            }

            // Fire the DigestScheduled event for the listener pipeline
            event(new Events\DigestScheduled(
                subscriberId: $subscriber->id,
                projectId: $projectId,
                date: $date,
                alertIds: $alertIds,
                digestType: $digestType
            ));

            // Dispatch the digest processing job
            ProcessAlertDigest::dispatch(
                $subscriber->id,
                $date,
                $alertIds,
                $digestType
            );

            $scheduled++;
        }

        Log::info('DigestScheduler: Scheduled digests', [
            'project_id' => $projectId,
            'date' => $date,
            'digest_type' => $digestType,
            'count' => $scheduled,
        ]);

        return $scheduled;
    }

    /**
     * Get digest statistics for a project.
     *
     * @param  int     $projectId
     * @param  string  $date
     * @return array
     */
    public function getDigestStats(int $projectId, string $date): array
    {
        return [
            'date' => $date,
            'total_alerts' => $this->aggregator->getDailyAlertCount($projectId, $date),
            'hourly_breakdown' => $this->aggregator->getHourlyBreakdown($projectId, $date),
        ];
    }
}
